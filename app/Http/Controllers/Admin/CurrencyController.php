<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\CurrenciesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Repositories\CryptoCurrencyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,
    Storage
};
use Intervention\Image\Facades\Image;
use App\Models\{CryptoapiLog,
    CryptoCurrenciesSetting,
    Transaction,
    Currency,
    Wallet,
    User
};
use Illuminate\Support\Facades\Config;
use Exception;

class CurrencyController extends Controller
{

    protected $helper;
    protected $currency;
    /**
     * The CryptoCurrency repository instance.
     *
     * @var CryptoCurrencyRepository
     */
    protected $cryptoCurrency;

    public function __construct()
    {
        $this->helper         = new Common();
        $this->currency       = new Currency();
        $this->cryptoCurrency = new CryptoCurrencyRepository();
    }

    public function index(CurrenciesDataTable $dataTable)
    {
        $data['menu'] = 'currency';
        return $dataTable->render('admin.currencies.view', $data);
    }

    public function getActiveBlockIoCrytoCurrencySettings(Request $request)
    {
        try
        {
            $cryptoCurrenciesSettings = $this->cryptoCurrency->getActiveCryptoCurrenciesSettingsNetwork();
            return response()->json([
                'cryptoCurrenciesSettings' => $cryptoCurrenciesSettings,
            ]);
        }
        catch (Exception $e)
        {
            return response()->json([
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function add(Request $request)
    {
        if (!$request->isMethod('post'))
        {
            $data['menu'] = 'currency';

            // Check enabled currencies in preference
            $data['getCurrenciesPreference'] = $getCurrenciesPreference = $this->cryptoCurrency->getCurrenciesPreference();
            if ($request->currency_type == 'crypto') {
                return view('admin.currencies.add_crypto', $data);
            }
            return view('admin.currencies.add', $data);
        }
        else if ($request->isMethod('post'))
        {
            if (checkDemoEnvironment() == true && $request->currency_type == 'crypto')
            {
                $this->helper->one_time_message('error', 'CryptoCurrency Settings cannot be updated on demo site.');
                return redirect()->back();
            }

            if ($request->type == 'fiat') {

                $getFiatCurrency = $this->currency->getCurrency(['type' => 'fiat', 'code' => $request->code], ['code']);

                if (!empty($getFiatCurrency) && $getFiatCurrency->code === $request->code) {
                    $this->helper->one_time_message('error', 'The currency already exists!');
                    return redirect(Config::get('adminPrefix').'/settings/currency');
                }

                $this->validate($request, [
                    'name'   => 'required',
                    'code'   => 'required|unique:currencies,code',
                    'symbol' => 'required',
                    'rate'   => 'required|numeric|min:0.0001',
                    'logo'   => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
                ], [
                    'rate.min'    => 'Please enter values greater than 0.0001!',
                    'code.unique' => 'The currency already exists!',
                ]);

            } else {

                $this->validate($request, [
                    'logo' => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
                    'code' => 'unique:currencies,code',
                    'api_key' => 'required',
                    'pin'     => 'required',
                    'address' => 'required',
                    'status'  => 'required',
                ], [
                    'code.unique' => 'The currency already exist',
                ]);

                $getCryptoCurrency = $this->currency->getCurrency(['type' => 'crypto', 'code' => $request->network], ['code']);
                if (!empty($getCryptoCurrency) && $getCryptoCurrency->code === $request->network) {
                    $this->helper->one_time_message('error', 'The currency ' . $request->network . ' already exists!');
                    return redirect(Config::get('adminPrefix').'/settings/currency');
                }

                //Custom Back-end Crypto Validations
                $customCryptoValidations = $this->customCryptoValidations($request);
                if (!$customCryptoValidations && $customCryptoValidations != null) {
                    return redirect(Config::get('adminPrefix').'/settings/currency');
                }
            }

            try
            {
                DB::beginTransaction();

                $currency         = new Currency();
                $currency->type   = $request->type;
                $currency->name   = $request->name;
                $currency->symbol = $request->symbol;

                if ($request->type == 'fiat') {
                    $currency->code          = $request->code;
                    $currency->rate          = $request->rate;
                    $currency->exchange_from = $request->exchange_from;
                    $currency->status        = $request->status;
                } else {
                    $currency->code = $request->network;

                    if (isset($request->network_address) && $request->network_address == 'on') {
                        $currency->allow_address_creation = 'Yes';
                    } else {
                        $currency->allow_address_creation = 'No';
                    }

                    $currency->status  = $request->status == 'active' ? 'Active' : 'Inactive';
                }

                $currency->default = '0';
                $filename       = $this->processCurrencyLogo('add', $request, null);
                $currency->logo = $filename;

                if ($currency->save()) {

                    if ($request->type == 'crypto') {

                        $network = $request->network;
                        $api_key = $request->api_key;
                        $pin     = $request->pin;
                        $address = $request->address;

                        $checkMerchantNetworkAddress = $this->cryptoCurrency->checkMerchantNetworkAddressValidity($api_key, $pin, $address);

                        if (! $checkMerchantNetworkAddress['status']) {
                            $this->helper->one_time_message('error', 'Invalid merchant address');
                            return back();
                        } else {

                            $getCryptoCurrencySetting = $this->cryptoCurrency->getCryptoCurrenciesSetting($network, 'All', ['*']);

                            $cryptoCurrenciesSetting                      = !empty($getCryptoCurrencySetting) ? $getCryptoCurrencySetting : new cryptoCurrenciesSetting();
                            $cryptoCurrenciesSetting->payment_method_id   = 9;
                            $cryptoCurrenciesSetting->network             = $this->cryptoCurrency->getCryptoCurrencyNetwork($api_key, $pin);
                            $blockIoBtcArr                                = [];
                            $blockIoBtcArr['api_key']                     = $api_key;
                            $blockIoBtcArr['pin']                         = $pin;
                            $blockIoBtcArr['address']                     = $address;
                            $cryptoCurrenciesSetting->network_credentials = json_encode($blockIoBtcArr);
                            $cryptoCurrenciesSetting->status              = $request->status;

                            if ($cryptoCurrenciesSetting->save()) {
                                $this->cryptoCurrency->createNotification($network);
                            }
                        }
                    }
                }

                // Create Users Wallet Addresses
                $createUsersNetworkAddressError = false;
                if ($request->type == 'crypto' && isset($request->network_address) && $request->network_address == 'on')
                {
                    // .env - APP_DEMO - check
                    if (checkDemoEnvironment() == true)
                    {
                        $this->helper->one_time_message('error', 'Users wallet address creation is not possible on demo site.');
                        return redirect(Config::get('adminPrefix').'/settings/currency');
                    }

                    $createUsersNetworkAddresses = $this->createUsersNetworkAddress($currency->code, $currency->id);
                    foreach ($createUsersNetworkAddresses as $createUsersNetworkAddress)
                    {
                        if ($createUsersNetworkAddress['status'] == 401)
                        {
                            $createUsersNetworkAddressError = true;
                        }
                    }
                }
                DB::commit();

                if ($createUsersNetworkAddressError == false) {
                    $this->helper->one_time_message('success', 'Currency Added Successfully');
                } else {
                    $this->helper->one_time_message('error', $createUsersNetworkAddress['message']);
                }
                return redirect(Config::get('adminPrefix').'/settings/currency');
            }
            catch (Exception $e)
            {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect(Config::get('adminPrefix').'/settings/currency');
            }
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->isMethod('post')) {
            $data['menu'] = 'currency';

            $id             = decrypt($request->id);
            $data['result'] = $result = Currency::find($id);

            if ($result->type == 'crypto') {
                $data['cryptoCurrencyStatus'] = $this->cryptoCurrency->getCryptoCurrencyStatus($result->code);
                $data['getCurrenciesPreference'] = $this->cryptoCurrency->getCurrenciesPreference();
                $data['getCurrenciesSettings'] = $getCurrenciesSettings = ($this->cryptoCurrency->getCryptoCurrenciesSetting($result->code, 'All', ['*']))->network_credentials;
                
                return view('admin.currencies.edit_crypto', $data);
            }
            return view('admin.currencies.edit', $data);

        } else if ($request->isMethod('post')) {

            if ($request->type != 'fiat') {

                $network = $request->network;
                $api_key = $request->api_key;
                $pin     = $request->pin;
                $address = $request->address;

                if (checkDemoEnvironment() == true) {
                    $this->helper->one_time_message('error', 'CryptoCurrency Settings cannot be updated on demo site.');
                    return redirect()->back();
                }

                $rules = array(
                    'api_key' => 'required',
                    'pin'     => 'required',
                    'address' => 'required',
                    'status'  => 'required',
                );
                $fieldNames = array(
                    'api_key' => 'Api Key',
                    'pin'     => 'Pin',
                    'address' => 'Address',
                    'status'  => 'Status',
                );

                $validator = \Validator::make($request->all(), $rules);
                $validator->setAttributeNames($fieldNames);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }  
            }

            if ($request->type == 'fiat') {
                $getFiatCurrency = Currency::where('id', '!=', $id)->where(['type' => 'fiat', 'code' => $request->code])->first(['code']);
                if (!empty($getFiatCurrency) && $getFiatCurrency->code === $request->code) {
                    $this->helper->one_time_message('error', 'The currency ' . $request->code . ' already exists!');
                    return redirect(Config::get('adminPrefix').'/settings/currency');
                }
                $this->validate($request, [
                    'name'   => 'required',
                    'code'   => 'required',
                    'symbol' => 'required',
                    'rate'   => 'required|numeric|min:0.0001',
                    'logo'   => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
                ], [
                    'rate.min' => 'Please enter values greater than 0.0001!',
                ]);
            } else {
                $this->validate($request, [
                    'logo' => 'mimes:png,jpg,jpeg,gif,bmp|max:10000',
                ]);
                //Custom Back-end Crypto Validations
                $customCryptoValidations = $this->customCryptoValidations($request);
                if (!$customCryptoValidations && $customCryptoValidations != null) {
                    return redirect(Config::get('adminPrefix').'/settings/currency');
                }
            }

            try {

                DB::beginTransaction();

                $currency         = Currency::find($id);
                $currency->type   = $request->type;
                $currency->name   = $request->name;
                $currency->symbol = $request->symbol;
                $currency->code   = $request->code;
                if ($request->type == 'fiat') {
                    $currency->rate          = $request->rate;
                    $currency->exchange_from = $request->exchange_from;
                } else {
                    if (isset($request->network_address) && $request->network_address=='on') {
                        $currency->allow_address_creation = 'Yes';
                    } else {
                        $currency->allow_address_creation = 'No';
                    }
                    $currency->code = $request->network;
                }
                if ($request->default_currency == 1) {
                    $currency->status  = 'Active';
                    $currency->default = 1;
                } else {
                    $currency->status  = $request->status;
                    $currency->default = 0;
                }

                if (isset($request->logo)) {
                    $filename       = $this->processCurrencyLogo('edit', $request, $currentLogo = $currency->logo);
                    $currency->logo = $filename;
                }

                if ($currency->save()) {

                    if ($request->type == 'crypto') {

                        $network = $request->network;
                        $api_key = $request->api_key;
                        $pin     = $request->pin;
                        $address = $request->address;

                        $checkMerchantNetworkAddress = $this->cryptoCurrency->checkMerchantNetworkAddressValidity($api_key, $pin, $address);

                        if (! $checkMerchantNetworkAddress['status']) {
                            $this->helper->one_time_message('error', 'Invalid merchant address');
                            return back();
                        } else {

                            $cryptoCurrenciesSetting = $this->cryptoCurrency->getCryptoCurrenciesSetting($network, 'All', ['*']);

                            if (! empty($cryptoCurrenciesSetting)) {

                                $cryptoCurrenciesSetting->payment_method_id   = 9;
                                $cryptoCurrenciesSetting->network             = $this->cryptoCurrency->getCryptoCurrencyNetwork($api_key, $pin);
                                $blockIoBtcArr                                = [];
                                $blockIoBtcArr['api_key']                     = $api_key;
                                $blockIoBtcArr['pin']                         = $pin;
                                $blockIoBtcArr['address']                     = $address;
                                $cryptoCurrenciesSetting->network_credentials = json_encode($blockIoBtcArr);
                                $cryptoCurrenciesSetting->status              = $request->status;

                                if ($cryptoCurrenciesSetting->save()) {
                                    $this->cryptoCurrency->createNotification($network);
                                }
                            }
                        }
                    }
                }


                // Create Users Wallet Addresses
                $createUsersNetworkAddressError = false;
                if ($request->type == 'crypto' && isset($request->network_address) && $request->network_address == 'on')
                {
                    // .env - APP_DEMO - check
                    if (checkDemoEnvironment() == true)
                    {
                        $this->helper->one_time_message('error', 'Users wallet address creation is not possible on demo site.');
                        return redirect(Config::get('adminPrefix').'/settings/currency');
                    }

                    $createUsersNetworkAddresses = $this->createUsersNetworkAddress($currency->code, $currency->id);
                    foreach ($createUsersNetworkAddresses as $createUsersNetworkAddress)
                    {
                        if ($createUsersNetworkAddress['status'] == 401)
                        {
                            $createUsersNetworkAddressError = true;
                        }
                    }
                }
                DB::commit();

                if ($createUsersNetworkAddressError == false)
                {
                    $this->helper->one_time_message('success', 'Currency Updated Successfully');
                }
                else
                {
                    $this->helper->one_time_message('error', $createUsersNetworkAddress['message']);
                }
                return redirect(Config::get('adminPrefix').'/settings/currency');
            }
            catch (Exception $e)
            {
                DB::rollBack();
                $this->helper->one_time_message('error', $e->getMessage());
                return redirect(Config::get('adminPrefix').'/settings/currency');
            }
        }
    }

    protected function processCurrencyLogo($type, $request, $currentLogo)
    {
        if ($request->hasFile('logo'))
        {
            $logo = $request->file('logo');
            if (isset($logo))
            {
                $filename  = time() . '.' . $logo->getClientOriginalExtension();
                $extension = strtolower($logo->getClientOriginalExtension());
                $location  = public_path('uploads/currency_logos/' . $filename);
                if (file_exists($location))
                {
                    unlink($location);
                }
                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif' || $extension == 'bmp')
                {
                    Image::make($logo)->resize(64, 64)->save($location);

                    if ($type == 'edit')
                    {
                        //Old file assigned to a variable
                        $oldfilename = $currentLogo;

                        //Delete old photo
                        Storage::delete($oldfilename);

                        return $filename;
                    }
                    else
                    {
                        return $filename;
                    }
                }
                else
                {
                    $this->helper->one_time_message('error', 'Invalid Image Format!');
                }
            }
        }
    }

    protected function customCryptoValidations($request)
    {
        $network = trim($request->network);
        $name    = trim($request->name);
        $symbol  = trim($request->symbol);
        switch ($network)
        {
            case 'BTC':
                //Check if crypto currency name is changed from front-end
                if ($name != 'Bitcoin')
                {
                    $this->helper->one_time_message('error', 'Bitcoin name cannot be changed!');
                    return false;
                }
                //Check if crypto currency symbol is changed from front-end
                if ($symbol != '฿')
                {
                    $this->helper->one_time_message('error', 'Bitcoin symbol cannot be changed!');
                    return false;
                }
                break;
            case 'BTCTEST':
                if ($name != 'Bitcoin (TESTNET!)')
                {
                    $this->helper->one_time_message('error', 'Bitcoin (TESTNET!) name cannot be changed!');
                    return false;
                }
                if ($symbol != '฿')
                {
                    $this->helper->one_time_message('error', 'Bitcoin (TESTNET!) symbol cannot be changed!');
                    return false;
                }
                break;
            case 'LTC':
                if ($name != 'Litecoin')
                {
                    $this->helper->one_time_message('error', 'Litecoin name cannot be changed!');
                    return false;
                }
                if ($symbol != 'Ł')
                {
                    $this->helper->one_time_message('error', 'Litecoin symbol cannot be changed!');
                    return false;
                }
                break;
            case 'LTCTEST':
                //Check if crypto currency name is changed from front-end
                if ($name != 'Litecoin (TESTNET!)')
                {
                    $this->helper->one_time_message('error', 'Litecoin (TESTNET!) name cannot be changed!');
                    return false;
                }
                //Check if crypto currency symbol is changed from front-end
                if ($symbol != 'Ł')
                {
                    $this->helper->one_time_message('error', 'Litecoin (TESTNET!) symbol cannot be changed!');
                    return false;
                }
                break;
            case 'DOGE':
                if ($name != 'Dogecoin')
                {
                    $this->helper->one_time_message('error', 'Dogecoin name cannot be changed!');
                    return false;
                }
                if ($symbol != 'Ð')
                {
                    $this->helper->one_time_message('error', 'Dogecoin symbol cannot be changed!');
                    return false;
                }
                break;
            case 'DOGETEST':
                if ($name != 'Dogecoin (TESTNET!)')
                {
                    $this->helper->one_time_message('error', 'Dogecoin (TESTNET!) name cannot be changed!');
                    return false;
                }
                if ($symbol != 'Ð')
                {
                    $this->helper->one_time_message('error', 'Dogecoin (TESTNET!) symbol cannot be changed!');
                    return false;
                }
                break;
            default:
                return true;
                break;
        }
    }

    protected function createUsersNetworkAddress($network, $currencyId)
    {
        $users = User::with(['wallets' => function ($q) use ($currencyId)
        {
            $q->where(['currency_id' => $currencyId]);
        }])
        ->where(['status' => 'Active'])
        ->get(['id', 'email']);

        $getCryptoApiLogOfWallets = [];
        if (!empty($users))
        {
            foreach ($users as $user)
            {
                $getWalletObject = $this->helper->getUserWallet([], ['user_id' => $user->id, 'currency_id' => $currencyId], ['id']);
                if (empty($getWalletObject) && count($user->wallets) == 0)
                {
                    // Create new wallet of current currency
                    $wallet              = new Wallet();
                    $wallet->user_id     = $user->id;
                    $wallet->currency_id = $currencyId;
                    $wallet->is_default  = 'No';
                    $wallet->save();

                    // Get Crypto Api Logs of current wallet id
                    $getCryptoApiLogOfWallets[] = $this->cryptoCurrency->getCryptoApiLogOfWallet($wallet->id, $network, $user);
                }
            }
            return $getCryptoApiLogOfWallets;
        }
    }

    public function deleteCurrencyLogo(Request $request)
    {
        $logo = $request->logo;

        if (isset($logo))
        {
            $currency = Currency::where(['id' => $request->currency_id, 'logo' => $request->logo])->first();

            if ($currency)
            {
                Currency::where(['id' => $request->currency_id, 'logo' => $request->logo])->update(['logo' => null]);

                if ($logo != null)
                {
                    $dir = public_path('uploads/currency_logos/' . $logo);
                    if (file_exists($dir))
                    {
                        unlink($dir);
                    }
                }
                $data['success'] = 1;
                $data['message'] = 'Logo has been successfully deleted!';
            }
            else
            {
                $data['success'] = 0;
                $data['message'] = "No Record Found!";
            }
        }
        echo json_encode($data);
        exit();
    }

    public function delete(Request $request)
    {
        $id = decrypt($request->id);

        $currency = Currency::find($id);

        $transaction = Transaction::where(['currency_id' => $currency->id])->first();

        if (isset($transaction)) {
            $this->helper->one_time_message('error', 'Sorry, You can\'t delete this currency, it\'s transaction exists!');
        } elseif (isset($currency) && $currency->default == 1) {
            $this->helper->one_time_message('error', 'Sorry, You can\'t delete this currency, it\'s default currency!');
        } else {
            if (isset($currency->id)) {

                $cryptoCurrenciesSetting = CryptoCurrenciesSetting::where('network', $currency->code)->first();
                if (!empty($cryptoCurrenciesSetting)) {

                    $notification = $this->cryptoCurrency->getNotificationStatus($currency->code);
                    if ($notification['status'] == true) {
                        $this->cryptoCurrency->deleteNotification($currency->code);
                    }
                    
                    $cryptoCurrenciesSetting->delete();
                }
                $currency->delete();
                Storage::delete($request->image);
            }
            $this->helper->one_time_message('success', 'Currency Deleted Successfully');
        }
        return redirect(Config::get('adminPrefix').'/settings/currency');
    }
}


