<?php

namespace App\Http\Controllers\Users;

use App\Repositories\CryptoCurrencyRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB,
    Auth
};
use Illuminate\Http\Request;
use App\Models\{Setting,
    Transaction,
    Transfer,
    Wallet
};

class UserTransactionController extends Controller
{
    /**
     * The CryptoCurrency repository instance.
     *
     * @var CryptoCurrencyRepository
     */
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->cryptoCurrency = new CryptoCurrencyRepository();
    }

    public function index()
    {
        $transaction      = new Transaction();
        $data['menu']     = 'transactions';
        $data['sub_menu'] = 'transactions';

        $data['from']     = $from   = isset(request()->from) ? setDateForDb(request()->from) : null;
        $data['to']       = $to     = isset(request()->to ) ? setDateForDb(request()->to) : null;
        $data['status']   = $status = isset(request()->status) ? request()->status : 'all';
        $data['type']     = $type   = isset(request()->type) ? request()->type : 'all';
        $data['wallet']   = $wallet = isset(request()->wallet) ? request()->wallet : 'all';

        $data['transactions'] = $transaction->getTransactions($from, $to, $type, $wallet, $status);

        $data['wallets']      = Wallet::with(['currency:id,code'])->where(['user_id' => Auth::user()->id])->get(['currency_id']);
        if ($type == Deposit || $type == Withdrawal || $type == 'all')
        {
            $data['type'] = $type;
        }
        else
        {
            switch ($type)
            {
                case 'sent':
                    $data['type'] = 'sent';
                    break;

                case 'request':
                    $data['type'] = 'request';
                    break;

                case 'received':
                    $data['type'] = 'received';
                    break;

                case 'exchange':
                    $data['type'] = 'exchange';
                    break;

                case 'crypto_sent':
                    $data['type'] = 'crypto_sent';
                    break;

                case 'crypto_received':
                    $data['type'] = 'crypto_received';
                    break;
            }
        }
        return view('user_dashboard.transactions.index', $data);
    }

    public function getTransaction(Request $request)
    {
        $data['status'] = 0;

        $transaction = Transaction::with([
            'payment_method:id,name',
            'transaction_type:id,name',
            'currency:id,code,symbol',
            'transfer:id,sender_id,receiver_id',
            'transfer.sender:id,first_name,last_name',
            'end_user:id,first_name,last_name,email,formattedPhone',
            'merchant:id,business_name',
            'cryptoapi_log:id,object_id,payload,confirmations',
        ])->find($request->id);

        // Get crypto api log details for Crypto_Sent & Crypto_Received (via custom relationship)
        if (!empty($transaction->cryptoapi_log))
        {
            $getCryptoDetails = $this->cryptoCurrency->getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoapi_log->payload, $transaction->cryptoapi_log->confirmations);
            if (count($getCryptoDetails) > 0)
            {
                // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                if (isset($getCryptoDetails['senderAddress']))
                {
                    $senderAddress   = $getCryptoDetails['senderAddress'];
                }
                if (isset($getCryptoDetails['receiverAddress']))
                {
                    $receiverAddress = $getCryptoDetails['receiverAddress'];
                }
                $network_fee = isset($getCryptoDetails['network_fee']) ? $getCryptoDetails['network_fee'] : 0.00000000;

                $confirmations   = $getCryptoDetails['confirmations'];
            }
        }

        if ($transaction->count() > 0)
        {
            switch ($transaction->transaction_type_id)
            {
                case Deposit:
                    if ($transaction->payment_method->name == 'Mts')
                    {
                        $pm = getCompanyName();
                    }
                    else
                    {
                        $pm = $transaction->payment_method->name;
                    }
                    $data['html'] ="<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div> <p>" . __('message.dashboard.left-table.deposit.deposited-to') . "</p></div>" .
                                        "<div> <p>" . $transaction->currency->code . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div> <p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div> <p>" . __('message.dashboard.left-table.deposit.payment-method') . "</p></div>" .
                                        "<div><p>" . $pm . "</p></div>" .
                                    "</div>" .

                                   
                                    "<h4  class='text-18 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .
                                
                                    "<div class='d-flex justify-content-between flex-wrap  mt-4'>" .
                                        "<div> <p>" . __('message.dashboard.left-table.deposit.deposited-amount') . "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</p></div>" .
                                    "</div>";

                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0)
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div> <p>" . __('message.dashboard.left-table.fee') . "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total - $transaction->subtotal)) . "</p></div>" .
                                        "</div>".
                                        "<hr class='mt-0 mb-2'>" .

                                        "<div class='d-flex justify-content-between flex-wrap'>" .
                                            "<div> <p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                            "<div>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</div>" .
                                        "</div>";
                                   
                    }
                    else
                    {
                        $data['html'] .=  "<hr class='mt-0 mb-2'>" .
                                         "<div class='d-flex justify-content-between flex-wrap'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</p></div>" .
                                        "</div>";
                        
                    }

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>". __('message.dashboard.left-table.deposit.deposited-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></h1>
                                        </div>" .
            
                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('deposit-money/print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a>" .
                                        "</div>";
                    break;

                case Withdrawal:
                    if ($transaction->payment_method->name == 'Mts')
                    {
                        $pm = getCompanyName();
                    }
                    else
                    {
                        $pm = $transaction->payment_method->name;
                    }
                    $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.withdrawal.withdrawan-with') . "</p></div>" .
                                        "<div><p>" . $pm . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .

                                  
                                    "<h4  class='text-18 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div> <p>" . __('message.dashboard.left-table.withdrawal.withdrawan-amount') . "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .     
                                    "</div>" ;
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0)
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.fee') . "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) . "</p></div>" .
                                        "</div>" .

                                        "<hr class='mt-0 mb-2'>" .

                                        "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                        "</div>" ;
                    }
                    else
                    {
                        $data['html'] .=    "<hr class='mt-0 mb-2'>" . 
                                            "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                "<div><p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                                "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                            "</div>" ;
                                        
                    }

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.left-table.withdrawal.withdrawan-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('withdrawal-money/print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";

                    break;

                case Transferred:

                    $receiverEmailorPhone = '';
                    $receiverName         = '';
                    if (isset($transaction->email) && ($transaction->user_type == 'registered')) {
                        $receiverEmailorPhone = $transaction->end_user->email;
                        $receiverName = $transaction->transfer->receiver->first_name . ' ' . $transaction->transfer->receiver->last_name;
                    } else if (isset($transaction->phone) && ($transaction->user_type == 'registered')) {
                        $receiverEmailorPhone = $transaction->end_user->formattedPhone;
                        $receiverName = $transaction->transfer->receiver->first_name . ' ' . $transaction->transfer->receiver->last_name;

                    }

                    if ($transaction->user_type == 'unregistered')
                    {
                        if (! empty($transaction->email)) {
                            $unregisteredEmailOrPhone = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                            "<div><p>".  __('message.dashboard.left-table.transferred.transferred-to') . "</p></div>" .
                                                            "<div><p>" . $transaction->email . "</p></div>" .
                                                        "</div>" ;
                        } else {
                            $unregisteredEmailOrPhone = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                            "<div><p>".  __('message.dashboard.left-table.transferred.transferred-to') . "</p></div>" .
                                                            "<div><p>" . $transaction->phone . "</p></div>" .
                                                        "</div>" ;
                        }
                    }
                    else
                    {
                        $unregisteredEmailOrPhone ="<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                        "<div><p>".  __('message.dashboard.left-table.transferred.transferred-to') . "</p></div>" .
                                                        "<div><p>" .  $receiverName . " <strong>(" . $receiverEmailorPhone . ")</strong>" . "</p></div>" .
                                                    "</div>" ;
                    }

                    $data['html'] = $unregisteredEmailOrPhone .
                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .

                                    "<h4 class='text-18 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .
                                
                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transferred.transferred-amount') .  "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .
                                    "</div>";



                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0)
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.fee') .  "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) . "</p></div>" .
                                        "</div>".

                                        "<hr class='mb-2 mt-0'>" .
                                    
                                        "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') .  "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                        "</div>".
                                
                                        "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.transferred.note') . "</h4>" .
                                        "<div  class='act-detail-font'>" . $transaction->note . "</div>" ;
                    }
                    else
                    {
                        $data['html'] .= "<hr class='mb-2 mt-0'>" .
                      
                                        "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') .  "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                        "</div>".
                                    
                                        "<h4  class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.transferred.note') . "</h4>" .
                                        "<div  class='act-detail-font'>" . $transaction->note . "</div>" ;
                        
                    }


                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.left-table.transferred.transferred-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></h1>
                                        </div>" .
                  
                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('moneytransfer/print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Received:

                    $senderEmailOrPhone = '';
                    if (isset($transaction->email)) {
                        $senderEmailOrPhone = $transaction->end_user->email;
                    } else if (isset($transaction->phone)) {
                        $senderEmailOrPhone = $transaction->end_user->formattedPhone;
                    }

                    $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.received.received-from') . "</p></div>" .
                                        "<div><p>" . $transaction->transfer->sender->first_name . ' ' . $transaction->transfer->sender->last_name . " <strong>(" . $senderEmailOrPhone . ")</strong>" . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .


                                    "<h4 class='mt-4'>". __('message.dashboard.left-table.details') ."</h4>".

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.received.received-amount') .  "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</p></div>" .
                                    "</div>" .
                                    "<hr>".

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.total') .  "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</p></div>" .
                                    "</div>" .

                                    "<h4 class='mt-4'>" . __('message.dashboard.left-table.transferred.note') . "</h4>" .
                                    "<div class='act-detail-font'>" . $transaction->note . "</div>" ;
                                  
                    $data['total'] =    "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.left-table.transferred.transferred-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('moneytransfer/print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Exchange_From:
                    $data['html'] ="<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.exchange-from.from-wallet') . "</p></div>" .
                                        "<div><p>" . $transaction->currency->code . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .

                                    "<h4 class='text-18 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>" . __('message.dashboard.left-table.exchange-from.exchange-from-amount') . "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .
                                    "</div>";

                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0)
                    {
                        $data['html'] .=    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                "<div><p>" . __('message.dashboard.left-table.fee') . "</p></div>" .
                                                "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($fee)) .  "</p></div>" .
                                            "</div>".
    
                                            "<hr class='mt-0 mb-2'>" .
                                            "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                "<div><h4 class='text-16 font-weight-600'>" . __('message.dashboard.left-table.total') . "</h4></div>" .
                                                "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                            "</div>" ;
                    }
                    else
                    {
                        $data['html'] .= "<hr class='mt-0 mb-2'>" .
                                        "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><h4 class='text-16 font-weight-600'>" . __('message.dashboard.left-table.total') . "</h4></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                        "</div>" ;      
                    }

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" .  __('message.dashboard.left-table.received.received-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-4 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('transactions/exchangeTransactionPrintPdf/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Exchange_To:
                    $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.exchange-to.to-wallet') . "<p></div>" .
                                        "<div><p>" . $transaction->currency->code . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .

                    
                                    "<h4 class='text-18 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .
                                
                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>" . __('message.dashboard.left-table.exchange-from.exchange-from-amount') . "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .
                                    "</div>" .
            
                                    "<hr class='mt-0 mb-2'>" .
            
                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</p></div>" .
                                    "</div>" ;
                                   

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.left-table.exchange-from.exchange-from-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('transactions/exchangeTransactionPrintPdf/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Request_From:
                    $conditionForRequestToPhoneAndEMail = !empty($transaction->email) ? $transaction->email : $transaction->phone;
                    $cancel_btn                         = '';
                    if ($transaction->status == 'Pending')
                    {
                        $cancel_btn = "<button class='btn btn-cancel mt-4 btn-sm trxnreqfrom' data-notificationType='{$conditionForRequestToPhoneAndEMail}' data='{$transaction->id}' data-type='{$transaction->transaction_type_id}' id='btn_{$transaction->id}'>" . __('message.form.cancel') . "</button>";
                    }
                    if ($transaction->user_type == 'registered')
                    {
                        $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.form.request_to') . "</p></div>" .
                                            "<div><p>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . '<strong> (' . (! empty($transaction->email) ? $transaction->email : $transaction->phone) . ')</strong>' ."</p></div>" .
                                        "</div>";  
                    }
                    else
                    {
                        if (! empty($transaction->email)) {
                            $data['html'] ="<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                "<div><p>" . __('message.form.request_to') . "</p></div>" .
                                                "<div><p>" . $transaction->email . "</p></div>" .
                                            "</div>";  
                        } else {
                            $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                "<div><p>" . __('message.form.request_to') . "</p></div>" .
                                                "<div><p>" . $transaction->phone . "</p></div>" .
                                            "</div>"; 
                        }

                    }
                    $data['html'] .="<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>".__('message.dashboard.left-table.transaction-id') ."</p> </div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .

                                    "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .
                                    
                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>".__('message.dashboard.send-request.request.confirmation.requested-amount') ."</p> </div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .
                                    "</div>" .
                
                                    "<hr class= 'mb-2'>" .
                
                                    "<div class='d-flex justify-content-between flex-wrap'>" .
                                        "<div><p>".__('message.dashboard.left-table.total') ."</p> </div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</p></div>" .
                                    "</div>" .
                
                                    "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.transferred.note') . "</h4>" .
                                    "<div  class='act-detail-font'>" . $transaction->note . "</div>" .$cancel_btn  ;
                                  

                    $data['total'] =    "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.send-request.request.confirmation.requested-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('request-payment/print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Request_To:
                    $conditionForRequestToPhoneAndEMail = !empty($transaction->email) ? $transaction->email : $transaction->phone;
                    $twoButtons                         = '';
                    //
                    if ($transaction->status == 'Pending')
                    {
                        $twoButtons = "<button class='btn btn-cancel mr-2 mt-4  btn-sm trxn' data-notificationType='{$conditionForRequestToPhoneAndEMail}' data='{$transaction->id}' data-type='{$transaction->transaction_type_id}'
                        id='btn_{$transaction->id}'>" . __('message.form.cancel') . "</button>";

                        $twoButtons .= " <button class='btn btn-primary ml-2 mt-4 btn-sm trxn_accept' data-rel='" . $transaction->transaction_reference_id . "' data='" . $transaction->id . "' id='acceptbtn_" . $transaction->id . "'> " . __('message.dashboard.left-table.request-to.accept') . " </button>";
                    }
                    //
                    if ($transaction->user_type == 'registered')
                    {
                        $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.form.request_from') . "</p></div>" .
                                            "<div><p>" . $transaction->end_user->first_name . ' ' . $transaction->end_user->last_name . ' <strong>(' . (! empty($transaction->email) ? $transaction->end_user->email : $transaction->end_user->formattedPhone) . ')</strong>' ."</p></div>" .
                                        "</div>";
                    }
                    else
                    {
                        $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div class='text-16 font-weight-600'>" . __('message.dashboard.left-table.transferred.email')  . "</div>" .
                                            "<div>" . $transaction->email ."</div>" .
                                        "</div>";
                    }
                    $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div class='text-16 font-weight-600'>" . __('message.dashboard.left-table.transaction-id') . "</div>" .
                                        "<div>" . $transaction->uuid . "</div>" .
                                    "</div>" .

                                    "<h4 class='text-18 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>" . __('message.dashboard.send-request.request.confirmation.requested-amount') . "</p></div>" .
                                        "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>". 
                                    "</div>";
                                   
                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0)
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.fee') .  "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) . "</p></div>" .
                                        "</div>" .
    
                                        "<hr class='mt-0 mb-2'>" .

                                        "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') .   "</p></div>" .
                                            "<div><p>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                        "</div>" .
    
                                        
                                        "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.transferred.note') . "</h4>" .
                                        "<div class='act-detail-font mt-4'>" . $transaction->note . "</div>" ;
                                        
                    }
                    else
                    {
                        $data['html'] .= "<hr class='mt-0 mb-2'>" .
                                        "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div class='text-16 font-weight-600'>" . __('message.dashboard.left-table.total') .   "</div>" .
                                            "<div>" . moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</div>" .
                                        "</div>" .
                       
                                       
                                        "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.transferred.note') . "</h4>" .
                                        "<div class='act-detail-font mt-4'>" . $transaction->note . "</div>" ;
                    }

                    $data['html'] .= $twoButtons;

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.send-request.request.confirmation.requested-amount') . "</h2>
                                            <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('request-payment/print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Payment_Sent:
                    $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.merchant.payment.merchant') . "</p></div>" .
                                        "<div><p>" . $transaction->merchant->business_name . "</p></div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" . __('message.dashboard.left-table.transaction-id') . "</p></div>" .
                                        "<div><p>" . $transaction->uuid . "</p></div>" .
                                    "</div>" .


                                    
                                    "<h4 class='mt-4'>" . __('message.dashboard.left-table.details') . "</h4>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>" .__('message.dashboard.left-table.payment-Sent.payment-amount') . "</p></div>" .
                                        "<div><p>" .  moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .
                                    "</div>" .
                                    
                                    "<hr/>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div><p>" .__('message.dashboard.left-table.total') . "</p></div>" .
                                        "<div><p>" .  moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</p></div>" .
                                    "</div>" ;


                    $data['total'] =  "<div class='text-center'>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.left-table.payment-Sent.payment-amount')  . "</h2>
                                            <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->total))) . "</strong></h1>
                                        </div>" .

                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('transactions/merchant-payment-print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Payment_Received:
                    $data['html'] = "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div>" . __('message.dashboard.left-table.transaction-id') . "</div>" .
                                        "<div>" . $transaction->uuid . "</div>" .
                                    "</div>" .

                                    "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div><p>" . __('message.dashboard.left-table.payment-Sent.payment-amount') . "</p></div>" .
                                        "<div><p>" .  moneyFormat($transaction->currency->symbol, formatNumber(abs($transaction->subtotal))) . "</p></div>" .
                                    "</div>" ;

                                   

                    $fee = abs($transaction->total) - abs($transaction->subtotal);
                    if ($fee > 0)
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                            "<div><p>" . __('message.dashboard.left-table.fee') . "</p></div>" .
                                            "<div><p>" .  moneyFormat($transaction->currency->symbol, formatNumber($transaction->charge_percentage + $transaction->charge_fixed)) . "</p></div>" .
                                        "</div>" .
                                        "<hr/>" .

                                        "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                            "<div><p>" .  moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</p></div>" .
                                        "</div>" ;
                    }
                    else
                    {
                        $data['html'] .= "<hr/>" .
                                        "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                            "<div><p>" . __('message.dashboard.left-table.total') . "</p></div>" .
                                            "<div><p>" .  moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</p></div>" .
                                        "</div>";
                        
                    }

                        $data['total'] =  "<div class='text-center '>
                                                <h2 class='text-white text-center font-weight-700 text-20'>" . __('message.dashboard.left-table.payment-Sent.payment-amount')  . "</h2>
                                                <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, formatNumber($transaction->total)) . "</strong></h1>
                                            </div>" .
            
                                            "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                            "<div class='form-group mt-5 text-center'>" .
                                                "<a href='" . url('transactions/merchant-payment-print/' . $transaction->id) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                            "</div>";
                    break;

                case Crypto_Sent:
                    $data['html'] = "";
                    if (isset($receiverAddress))
                    {
                        $data['html'] .=  "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div>" . __('message.dashboard.crypto.transactions.receiver-address') . "</div>" .
                                            "<div>" . $receiverAddress . "</div>" .
                                        "</div>" ;
                    }
                    if (isset($confirmations))
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div>" . __('message.dashboard.crypto.transactions.confirmations') . "</div>" .
                                            "<div>" . $confirmations . "</div>" .
                                        "</div>" ;
                    }
                    $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div>" . __('message.dashboard.left-table.transaction-id') . "</div>" .
                                        "<div>" . $transaction->uuid . "</div>" .
                                    "</div>" .

                                  
                                    "<h4 class='text-16 mt-4 font-weight-600'>" . __('message.dashboard.left-table.details') . "</h4>" .
                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div>" . __('message.dashboard.crypto.send.confirm.sent-amount') . "</div>" .
                                        "<div>" . moneyFormat($transaction->currency->symbol, $transaction->subtotal) . "</div>" .
                                    "</div>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div>" . __('message.dashboard.crypto.send.confirm.network-fee') . "</div>" .
                                        "<div>" . moneyFormat($transaction->currency->symbol, $network_fee) . "</div>" .
                                    "</div>" .
                                    
                                    "<hr/>" .
                                    "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div>" . __('message.dashboard.left-table.total') . "</div>" .
                                        "<div>" . moneyFormat($transaction->currency->symbol, ( number_format($transaction->total - $network_fee, 8, '.', ''))) . "</div>" .
                                    "</div>" ;

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" .  __('message.dashboard.crypto.send.confirm.sent-amount')  . "</h2>
                                            <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, $transaction->total). "</strong></h1>
                                        </div>" .
        
                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('transactions/crypto-sent-received-print/' . encrypt($transaction->id)) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                case Crypto_Received:
                    $data['html'] = "";
                    // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                    if (isset($senderAddress))
                    {
                        $data['html'] .=  "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                                "<div>" . __('message.dashboard.crypto.transactions.sender-address') . "</div>" .
                                                "<div>" . $senderAddress . "</div>" .
                                            "</div>" ;
                    }
                    if (isset($confirmations))
                    {
                        $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                            "<div>" . __('message.dashboard.crypto.transactions.confirmations') . "</div>" .
                                            "<div>" . $confirmations . "</div>" .
                                        "</div>" ;
                    }
                    $data['html'] .= "<div class='d-flex justify-content-between flex-wrap mt-2'>" .
                                        "<div>" . __('message.dashboard.left-table.transaction-id') . "</div>" .
                                        "<div>" . $transaction->uuid . "</div>" .
                                    "</div>" .

                                    "<h4 class='text-16 font-weight-600 mt-4'>" . __('message.dashboard.left-table.details') . "</h4>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div>" . __('message.dashboard.left-table.received.received-amount') . "</div>" .
                                        "<div>" .  moneyFormat($transaction->currency->symbol, $transaction->subtotal). "</div>" .
                                    "</div>" .
                                    "<hr/>" .

                                    "<div class='d-flex justify-content-between flex-wrap mt-4'>" .
                                        "<div>" . __('message.dashboard.left-table.total') . "</div>" .
                                        "<div>" .  moneyFormat($transaction->currency->symbol, $transaction->subtotal). "</div>" .
                                    "</div>" ;

                    $data['total'] =  "<div class='text-center '>
                                            <h2 class='text-white text-center font-weight-700 text-20'>" .  __('message.dashboard.left-table.received.received-amount')  . "</h2>
                                            <h1 class='text-white mt-4'><strong>". moneyFormat($transaction->currency->symbol, $transaction->subtotal). "</strong></h1>
                                        </div>" .
                    
                                        "<h4 class='mt-2 text-center text-white text-16'>". $transaction->created_at->format('jS F Y') . "</h4>" .
                                        "<div class='form-group mt-5 text-center'>" .
                                            "<a href='" . url('transactions/crypto-sent-received-print/' . encrypt($transaction->id)) . "' target='_blank' class='btn btn-light pl-4 pr-4 btn-sm'>" . __('message.dashboard.vouchers.success.print') . "</a> &nbsp;&nbsp;" .
                                        "</div>";
                    break;

                
            }
        }
        return json_encode($data);
    }

    /**
     * Generate pdf print for exchangeTransaction entries
     */
    public function exchangeTransactionPrintPdf($id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        // $data['transaction'] = $transaction = Transaction::where(['id' => $id])->first();
        $data['transaction'] = $transaction = Transaction::with([
            'currency:id,code,symbol',
        ])->where(['id' => $id])->first();

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.exchangeTransactionPrintPdf', $data));
        $mpdf->Output('exchange_' . time() . '.pdf', 'I'); // this will output data
    }

    /**
     * Generate pdf print for merchant payment entries
     */
    public function merchantPaymentTransactionPrintPdf($id)
    {
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);

        $data['transaction'] = $transaction = Transaction::with([
            'merchant:id,business_name',
            'currency:id,symbol',
        ])->where(['id' => $id])->first();

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.merchantPaymentTransactionPrintPdf', $data));
        $mpdf->Output('merchant-payment_' . time() . '.pdf', 'I'); // this will output data
    }

    /**
     * Generate pdf print for crypto sent & received
     */
    public function cryptoSentReceivedTransactionPrintPdf($id)
    {
        $id                  = decrypt($id);
        $data['companyInfo'] = Setting::where(['type' => 'general', 'name' => 'logo'])->first(['value']);
        $data['transaction'] = $transaction = Transaction::with(['currency:id,symbol', 'cryptoapi_log:id,object_id,payload,confirmations'])->where(['id' => $id])->first();

        // Get crypto api log details for Crypto_Sent & Crypto_Received (via custom relationship)
        if (!empty($transaction->cryptoapi_log))
        {
            $getCryptoDetails = $this->cryptoCurrency->getCryptoPayloadConfirmationsDetails($transaction->transaction_type_id, $transaction->cryptoapi_log->payload, $transaction->cryptoapi_log->confirmations);
            if (count($getCryptoDetails) > 0)
            {
                // For "Tracking block io account receiver address changes, if amount is sent from other payment gateways like CoinBase, CoinPayments, etc"
                if (isset($getCryptoDetails['senderAddress']))
                {
                    $data['senderAddress']   = $getCryptoDetails['senderAddress'];
                }
                if (isset($getCryptoDetails['receiverAddress']))
                {
                    $data['receiverAddress'] = $getCryptoDetails['receiverAddress'];
                }
                
                $data['network_fee']     = isset($getCryptoDetails['network_fee']) ? $getCryptoDetails['network_fee'] : 0.00000000;
                
                $data['confirmations']   = $getCryptoDetails['confirmations'];
            }
        }

        $mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);
        $mpdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => 'A3',
            'orientation' => 'P',
        ]);
        $mpdf->autoScriptToLang         = true;
        $mpdf->autoLangToFont           = true;
        $mpdf->allow_charset_conversion = false;
        $mpdf->SetJS('this.print();');
        $mpdf->WriteHTML(view('user_dashboard.transactions.crypto_sent_received', $data));
        $mpdf->Output('crypto-sent-received_' . time() . '.pdf', 'I'); // this will output data
    }
}

//DB transaction template below
///////////////////////////////////////////////////////////////
/* WITH MAIL ROLLBACK
try
{
DB::beginTransaction();

//Save to tables

//Mail or SMS try catch
try
{
//send mail or sms
}
catch (Exception $e)
{
DB::rollBack();
clearActionSession();
$this->helper->one_time_message('error', $e->getMessage());
return redirect('');
}

DB::commit();
// return;
}
catch (Exception $e)
{
DB::rollBack();
$this->helper->one_time_message('error', $e->getMessage());
return redirect('');
}
 */

///////////////////////////////////////////////////////////////
/* USUAL APPROACH
try
{
DB::beginTransaction();
DB::commit();
}
catch (Exception $e)
{
DB::rollBack();
$this->helper->one_time_message('error', $e->getMessage());
return redirect('');
}
 */
