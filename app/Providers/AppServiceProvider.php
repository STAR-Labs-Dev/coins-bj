<?php

namespace App\Providers;

use App\Models\TransactionType;
use App\Models\Preference;
use Illuminate\Support\Facades\{Schema, Config, View};
use Illuminate\Support\ServiceProvider;
use TechVill\Theme\Facades\Theme;

class AppServiceProvider extends ServiceProvider
{
    
    public function boot()
    {
        header('x-powered-by:');

        Theme::set(env('THEME', 'modern'));

        
        if (!defined('BLOCKIO_API_VERSION')) define('BLOCKIO_API_VERSION', 2);

        if (env('APP_INSTALL') == true) {
            
            $transactionTypes = TransactionType::all()->toArray();
            foreach ($transactionTypes as $transactionType) {
                if (!defined($transactionType['name'])) define($transactionType['name'], $transactionType['id']);
            }

            //Admin Prefix - starts
            $adminUrlPrefix = Preference::where(['category' => 'preference', 'field' => 'admin_url_prefix'])->first(['value']);

            if (!empty($adminUrlPrefix)) {
                Config::set(['adminPrefix' => $adminUrlPrefix->value]);
                View::share('adminPrefix', $adminUrlPrefix->value);
            }

        }
        Schema::defaultStringLength(191);
    }

    public function register()
    {
    }
}
