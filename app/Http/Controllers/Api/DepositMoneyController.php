<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{
    DB, 
    Validator
};
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{PaymentMethod,
    CurrencyPaymentMethod,
    Transaction,
    FeesLimit,
    Currency,
    Setting,
    Deposit,
    Wallet,
    Bank,
    File
};
use Exception;
use App\Repositories\{StripeRepository};
class DepositMoneyController extends Controller
{
    public $successStatus      = 200;
    public $unauthorisedStatus = 401;
    protected $helper;
    protected $stripeRepository;

    public function __construct()
    {
        $this->helper  = new Common();
        $this->stripeRepository = new StripeRepository();
    }

    //Deposit Money Starts here
    public function getDepositCurrencyList()
    {
        $activeCurrency                     = Currency::where(['status' => 'Active'])->get(['id', 'code', 'status']);
        $feesLimitCurrency                  = FeesLimit::where(['transaction_type_id' => Deposit, 'has_transaction' => 'Yes'])->get(['currency_id', 'has_transaction']);

        //Set default wallet as selected - starts
        $defaultWallet                      = Wallet::where(['user_id' => request('user_id'), 'is_default' => 'Yes'])->first(['currency_id']);
        $success['defaultWalletCurrencyId'] = $defaultWallet->currency_id;
        //Set default wallet as selected - ends

        $success['currencies']              = $this->currencyList($activeCurrency, $feesLimitCurrency);
        $success['status']                  = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    //Extended function - 1
    public function currencyList($activeCurrency, $feesLimitCurrency)
    {
        $selectedCurrency = [];
        foreach ($activeCurrency as $aCurrency)
        {
            foreach ($feesLimitCurrency as $flCurrency)
            {
                if ($aCurrency->id == $flCurrency->currency_id && $aCurrency->status == 'Active' && $flCurrency->has_transaction == 'Yes')
                {
                    $selectedCurrency[$aCurrency->id]['id']   = $aCurrency->id;
                    $selectedCurrency[$aCurrency->id]['code'] = $aCurrency->code;
                }
            }
        }
        return $selectedCurrency;
    }

    //getMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods
    public function getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods(Request $request)
    {
        $feesLimits = FeesLimit::whereHas('currency', function($q)
        {
            $q->where('status','=','Active');
        })
        ->whereHas('payment_method', function($q)
        {
            $q->whereIn('name', ['Stripe', 'Paypal', 'Bank'])->where('status','=','Active');
        })
        ->where(['transaction_type_id' => $request->transaction_type_id, 'has_transaction' => 'Yes', 'currency_id' => $request->currency_id])
        ->get(['payment_method_id']);

        $currencyPaymentMethods                       = CurrencyPaymentMethod::where('currency_id', $request->currency_id)->where('activated_for', 'like', "%deposit%")->get(['method_id']);
        $currencyPaymentMethodFeesLimitCurrenciesList = $this->currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods);
        $success['paymentMethods']                    = $currencyPaymentMethodFeesLimitCurrenciesList;
        $success['status']                            = $this->successStatus;
        return response()->json(['success' => $success], $this->successStatus);
    }

    //Extended function - 2
    public function currencyPaymentMethodFeesLimitCurrencies($feesLimits, $currencyPaymentMethods)
    {
        $selectedCurrencies = [];
        foreach ($feesLimits as $feesLimit)
        {
            foreach ($currencyPaymentMethods as $currencyPaymentMethod)
            {
                if ($feesLimit->payment_method_id == $currencyPaymentMethod->method_id)
                {
                    $selectedCurrencies[$feesLimit->payment_method_id]['id']   = $feesLimit->payment_method_id;
                    $selectedCurrencies[$feesLimit->payment_method_id]['name'] = $feesLimit->payment_method->name;
                }
            }
        }
        return $selectedCurrencies;
    }

    public function getDepositDetailsWithAmountLimitCheck()
    {
        $user_id         = (int )request('user_id');
        $amount          = (double) request('amount');
        $currency_id     = request('currency_id');
        $paymentMethodId = (int) request('paymentMethodId');
        $success['paymentMethodName'] = PaymentMethod::where('id', $paymentMethodId)->first(['name'])->name;
        $wallets                      = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['balance']);
        
        $feesDetails = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currency_id, 'payment_method_id' => $paymentMethodId])
            ->first(['charge_percentage', 'charge_fixed', 'min_limit', 'max_limit', 'currency_id']);
        if (@$feesDetails->max_limit == null) {
            $success['status'] = 200;
            if ((@$amount < @$feesDetails->min_limit)) {
                $success['reason']   = 'minLimit';
                $success['minLimit'] = @$feesDetails->min_limit;
                $success['message']  = 'Minimum amount ' . formatNumber(@$feesDetails->min_limit);
                $success['status']   = '401';
                return response()->json(['success' => $success]);
            }
        } else {
            $success['status'] = 200;
            if ((@$amount < @$feesDetails->min_limit) || (@$amount > @$feesDetails->max_limit)) {
                $success['reason']   = 'minMaxLimit';
                $success['minLimit'] = @$feesDetails->min_limit;
                $success['maxLimit'] = @$feesDetails->max_limit;
                $success['message']  = 'Minimum amount ' . formatNumber(@$feesDetails->min_limit) . ' and Maximum amount ' . formatNumber(@$feesDetails->max_limit);
                $success['status']   = '401';
                return response()->json(['success' => $success]);
            }
        }
        //Code for Amount Limit ends here

        //Code for Fees Limit Starts here
        if (empty($feesDetails)) {
            $success['message'] = "ERROR";
            $success['status']  = 401;
        } else {
            $feesPercentage            = $amount * ($feesDetails->charge_percentage / 100);
            $feesFixed                 = $feesDetails->charge_fixed;
            $totalFess                 = $feesPercentage + $feesFixed;
            $totalAmount               = $amount + $totalFess;
            $success['feesPercentage'] = $feesPercentage;
            $success['feesFixed']      = $feesFixed;
            $success['amount']         = $amount;
            $success['totalFees']      = $totalFess;
            $success['totalFeesHtml']  = formatNumber($totalFess);
            $success['currency_id']    = $feesDetails->currency_id;
            $success['currSymbol']     = $feesDetails->currency->symbol;
            $success['currCode']       = $feesDetails->currency->code;
            $success['totalAmount']    = $totalAmount;
            $success['pFees']          = $feesDetails->charge_percentage;
            $success['fFees']          = $feesDetails->charge_fixed;
            $success['min']            = $feesDetails->min_limit;
            $success['max']            = $feesDetails->max_limit;
            $success['balance']        = @$wallets->balance ? @$wallets->balance : 0;
            $success['status']         = 200;
        }
        return response()->json(['success' => $success]);
    }

    public function stripeMakePayment(Request $request)
    {
        $data = [];
        $data['status']  = 200;
        $data['message'] = "Success";
        $validation = Validator::make($request->all(), [
            'cardNumber'  => 'required',
            'month'       => 'required|digits_between:1,12|numeric',
            'year'        => 'required|numeric',
            'cvc'         => 'required|numeric',
            'amount'      => 'required|numeric',
            'totalAmount' => 'required|numeric',
            'currency_id' => 'required',
            'payment_method_id' => 'required',
        ]);
        if ($validation->fails()) {
            $data['message'] = $validation->errors()->first();
            $data['status']  = 401;
            return response()->json(['success' => $data]);
        }
        $sessionValue['totalAmount'] = (double) request('totalAmount');
        $sessionValue['amount']      = (double) request('amount');
        $amount            = (double) $sessionValue['totalAmount'];
        $payment_method_id = $method_id = (int) request('payment_method_id');
        $currencyId        = (int) request('currency_id');
        $currency          = Currency::find($currencyId, ["id", "code"]);
        $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
        $methodData        = json_decode($currencyPaymentMethod->method_data);
        $secretKey         = $methodData->secret_key;
        if (!isset($secretKey)) {
            $data['message']  = __("Payment gateway credentials not found!");
            $data['status']  = 401;
            return response()->json(['success' => $data]);
        }
        $response = $this->stripeRepository->makePayment($secretKey, round($amount, 2), strtolower($currency->code), $request->cardNumber, $request->month, $request->year, $request->cvc);
        if ($response->getData()->status != 200) {
            $data['status']  = $response->getData()->status;
            $data['message'] = $response->getData()->message;
        } else {
            $data['paymentIntendId'] = $response->getData()->paymentIntendId;
            $data['paymentMethodId'] = $response->getData()->paymentMethodId;
        }
        return response()->json(['success' => $data]);
    }
    
    public function stripeConfirm(Request $request)
    {
        $data = [];
        $data['status']  = 401;
        $data['message'] = "Fail";
        try {
            DB::beginTransaction();
            $validation = Validator::make($request->all(), [
                'paymentIntendId'   => 'required',
                'paymentMethodId'   => 'required',
                'amount'            => 'required',
                'totalAmount'       => 'required',
                'currency_id'       => 'required',
                'payment_method_id' => 'required',
            ]);
            if ($validation->fails()) {
                $data['message'] = $validation->errors()->first();
                return response()->json(['success' => $data]);
            }
            $sessionValue['totalAmount'] = (double) request('totalAmount');
            $sessionValue['amount']      = (double) request('amount');
            $amount            = (double) $sessionValue['totalAmount'];
            $payment_method_id = $method_id = (int) request('payment_method_id');
            $currencyId        = (int) request('currency_id');
            $currency          = Currency::find($currencyId, ["id", "code"]);
            $currencyPaymentMethod     = CurrencyPaymentMethod::where(['currency_id' => $currencyId, 'method_id' => $method_id])->where('activated_for', 'like', "%deposit%")->first(['method_data']);
            $methodData        = json_decode($currencyPaymentMethod->method_data);
            $secretKey         = $methodData->secret_key;
            if (!isset($secretKey)) {
                $data['message']  = __("Payment gateway credentials not found!");
                return response()->json([
                    'data' => $data
                ]);
            }
            $response = $this->stripeRepository->paymentConfirm($secretKey, $request->paymentIntendId, $request->paymentMethodId);
            if ($response->getData()->status != 200) {
                $data['message'] = $response->getData()->message;
                return response()->json([
                    'data' => $data
                ]);
            }
            $user_id           = request('user_id');
            $wallet            = Wallet::where(['currency_id' => $currencyId, 'user_id' => $user_id])->first(['id', 'currency_id']);
            if (empty($wallet)) {
                $walletInstance = Wallet::createWallet($user_id, $sessionValue['currency_id']);
            }
            $currencyId = isset($wallet->currency_id) ? $wallet->currency_id : $walletInstance->currency_id;
            $currency   = Currency::find($currencyId, ['id', 'code']);

            $depositConfirm      = Deposit::success($currencyId, $payment_method_id, $user_id, $sessionValue);
            DB::commit();
            $response            = $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $depositConfirm['deposit']]);
            $data['status']      = 200;
            $data['message']     = "Success";
            return response()->json(['success' => $data]);
        } catch (Exception $e) {
            DB::rollBack();
            $data['message'] =  $e->getMessage();
            return response()->json(['success' => $data]);
        }
    }
    /**
     * Stripe Ends
     * @return [type] [description]
     */

    /**
     * Paypal Starts
     * @return [type] [description]
     */
    //Get Paypal Info
    public function getPeypalInfo()
    {
        $currencyPaymentMethod = CurrencyPaymentMethod::where(['currency_id' => request('currency_id'), 'method_id' => request('method_id')])
            ->where('activated_for', 'like', "%deposit%")
            ->first(['method_data']);

        if (empty($currencyPaymentMethod))
        {
            $success['message'] = __('Payment gateway credentials not found!');
            $success['status']  = 401;
        }
        else
        {
            $success['method_info'] = json_decode($currencyPaymentMethod->method_data);
            $success['status']      = 200;
            return response()->json(['success' => $success]);
        }
    }

    public function paypalSetup()
    {
        $numarr = func_num_args();
        if ($numarr > 0)
        {
            $clientID   = func_get_arg(0);
            $secret     = func_get_arg(1);
            $mode       = func_get_arg(2);
            $apicontext = new ApiContext(new OAuthTokenCredential($clientID, $secret));
            $apicontext->setConfig([
                'mode' => $mode,
            ]);
        }
        else
        {
            $credentials = Setting::where(['type' => 'PayPal'])->get();
            $clientID    = $credentials[0]->value;
            $secret      = $credentials[1]->value;
            $apicontext  = new ApiContext(new OAuthTokenCredential($clientID, $secret));
            $apicontext->setConfig([
                'mode' => $credentials[3]->value,
            ]);
        }

        return $apicontext;
    }

    //Deposit Confirm Post via Paypal
    public function paypalPaymentStore()
    {
        try {
            DB::beginTransaction();
            if (request('details')['status'] != "COMPLETED") {
                $success['status']  = 401;
                $success['message'] = __('Unsuccessful Transaction');
                return response()->json(['success' => $success]);
            }
            $amount            = (double) request('amount');
            $currency_id       = (int) request('currency_id');
            $payment_method_id = (int) request('paymentMethodId');
            $user_id           = (int) request('user_id');
            $uuid              = unique_code();
            $wallet            = Wallet::where(['currency_id' => $currency_id, 'user_id' => $user_id])->first(['id', 'balance']);
            if (empty($wallet)) {
                $walletInstance = Wallet::createWallet($user_id, $currency_id);
            }
            $calculatedFee = $this->getDepositDetailsWithAmountLimitCheck();
            $sessionValue['amount']      = $amount;
            $sessionValue['totalAmount'] = $amount + $calculatedFee->getData()->success->totalFees;
            $depositConfirm              = Deposit::success($currency_id, $payment_method_id, $user_id, $sessionValue);
            DB::commit();
            $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $depositConfirm['deposit']]);
            $success['transaction'] = $depositConfirm['transaction'];
            $success['status']      = 200;
            return response()->json(['success' => $success]);
        } catch (Exception $e) {
            DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage();
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }

    /**
     * Paypal Ends
     * @return [type] [description]
     */

    /**
     * Bank Starts
     * @return [type] [description]
     */
    public function getDepositBankList()
    {
        $banks                  = Bank::where(['currency_id' => request('currency_id')])->get(['id', 'bank_name', 'is_default', 'account_name', 'account_number']);
        $currencyPaymentMethods = CurrencyPaymentMethod::where('currency_id', request('currency_id'))
            ->where('activated_for', 'like', "%deposit%")
            ->where('method_data', 'like', "%bank_id%")
            ->get(['method_data']);

        $bankList = $this->bankList($banks, $currencyPaymentMethods);
        if (empty($bankList))
        {
            $success['status']  = 401;
            $success['message'] = __('Banks Does Not Exist For Selected Currency!');
        }
        else
        {
            $success['status'] = $this->successStatus;
            $success['banks']  = $bankList;
        }
        return response()->json(['success' => $success], $this->successStatus);
    }

    public function bankList($banks, $currencyPaymentMethods)
    {
        $selectedBanks = [];
        $i             = 0;
        foreach ($banks as $bank)
        {
            foreach ($currencyPaymentMethods as $cpm)
            {
                if ($bank->id == json_decode($cpm->method_data)->bank_id)
                {
                    $selectedBanks[$i]['id']             = $bank->id;
                    $selectedBanks[$i]['bank_name']      = $bank->bank_name;
                    $selectedBanks[$i]['is_default']     = $bank->is_default;
                    $selectedBanks[$i]['account_name']   = $bank->account_name;
                    $selectedBanks[$i]['account_number'] = $bank->account_number;
                    $i++;
                }
            }
        }
        return $selectedBanks;
    }

    public function getBankDetails()
    {
        $bank = Bank::with('file:id,filename')->where(['id' => request('bank')])->first(['account_name', 'account_number', 'bank_name', 'file_id']);
        if ($bank)
        {
            $success['status'] = 200;
            $success['bank']   = $bank;
            if (!empty($bank->file_id))
            {
                $success['bank_logo'] = $bank->file->filename;
            }
        }
        else
        {
            $success['status'] = 401;
            $success['bank']   = "Bank Not Found!";
        }
        return response()->json(['success' => $success], $this->successStatus);
    }

    //Deposit Confirm Post via Bank
    public function bankPaymentStore()
    {
        try {
            DB::beginTransaction();
            $uid                  = (int)request('user_id');
            $uuid                 = unique_code();
            $deposit_payment_id   = (int) request('deposit_payment_id');
            $deposit_payment_name = request('deposit_payment_name');
            $currency_id          = (int) request('currency_id');
            $amount               = $sessionValue['amount'] = (double) request('amount');
            $bank_id              = (int) request('bank_id');
            $totalAmount          = $sessionValue['totalAmount'] = (double) request('amount') + (double) request('totalFees');
            $feeInfo              = FeesLimit::where(['transaction_type_id' => Deposit, 'currency_id' => $currency_id, 'payment_method_id' => $deposit_payment_id])->first(['charge_percentage', 'charge_fixed']);
            $feePercentage        = $amount * ($feeInfo->charge_percentage / 100);
            if ($deposit_payment_name == 'Bank') {
                if (request()->hasFile('file')) {
                    $fileName     = request()->file('file');
                    $originalName = $fileName->getClientOriginalName();
                    $uniqueName   = strtolower(time() . '.' . $fileName->getClientOriginalExtension());
                    $file_extn    = strtolower($fileName->getClientOriginalExtension());
                    $path         = 'uploads/files/bank_attached_files';
                    $uploadPath   = public_path($path);
                    $fileName->move($uploadPath, $uniqueName);

                    $file               = new File();
                    $file->user_id      = $uid;
                    $file->filename     = $uniqueName;
                    $file->originalname = $originalName;
                    $file->type         = $file_extn;
                    $file->save();
                }
            }
            $depositConfirm = Deposit::success($currency_id, $deposit_payment_id, $uid, $sessionValue, "Pending", "bank", $file->id, $bank_id);
            DB::commit();
            $this->helper->sendTransactionNotificationToAdmin('deposit', ['data' => $depositConfirm['deposit']]);
            $success['status'] = $this->successStatus;
            return response()->json(['success' => $success], $this->successStatus);
        } catch (Exception $e) {
            DB::rollBack();
            $success['status']  = $this->unauthorisedStatus;
            $success['message'] = $e->getMessage(); 
            return response()->json(['success' => $success], $this->unauthorisedStatus);
        }
    }
    /**
     * Bank Ends
     * @return [type] [description]
     */

    //Deposit Money Ends here
}
