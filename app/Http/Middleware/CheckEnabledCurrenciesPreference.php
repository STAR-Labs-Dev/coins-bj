<?php

namespace App\Http\Middleware;

use App\Repositories\CryptoCurrencyRepository;
use Closure;
use Illuminate\Support\Facades\Config;

class CheckEnabledCurrenciesPreference
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

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $getCurrenciesPreference = $this->cryptoCurrency->getCurrenciesPreference();
        if ($getCurrenciesPreference->value == "fiat")
        {
            $prefix = str_replace('/', '', request()->route()->getPrefix());
            // Check crypto preference in admin panel
            if ($prefix == Config::get('adminPrefix'))
            {
                return redirect(Config::get('adminPrefix').'/crypto/preference-disabled');
            }
            else
            {
                // Check crypto preference in user panel
                return redirect('user/crypto/preference-disabled');
            }
        }
        return $next($request);
    }
}
