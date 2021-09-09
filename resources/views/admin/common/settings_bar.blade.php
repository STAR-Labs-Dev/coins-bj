<div class="box box-info box_info">
    <div class="panel-body">
        <h4 class="all_settings">
            Manage Settings
        </h4>
        <ul class="nav navbar-pills nav-tabs nav-stacked no-margin" role="tablist">

            {{-- @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_general_setting')) --}}
                <li class="{{ (Route::current()->uri() == \Config::get('adminPrefix') . '/settings') ? 'active' : '' }}">
                    <a data-group="settings" href="{{ url(\Config::get('adminPrefix').'/settings') }}">
                        <i class="glyphicon glyphicon-cog">
                        </i>
                        <span>
                            General
                        </span>
                    </a>
                </li>
            {{-- @endif --}}

            <li <?= $settings_menu == 'admin-security-settings' ? ' class="treeview active"' : 'treeview'?>>
                <a href="{{ url(\Config::get('adminPrefix').'/settings/admin-security-settings') }}">
                    <i class="fa fa-shield">
                    </i>
                    <span>
                        Admin Security
                    </span>
                </a>
            </li>

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_social_links'))
                <li <?= $settings_menu == 'social_links' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/social_links') }}">
                        <i class="fa fa-share-alt">
                        </i>
                        <span>
                            Social Links
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_social_links'))
                <li <?= $settings_menu == 'themes' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/themes') }}">
                        <i class="fa fa-newspaper-o">
                        </i>
                        <span>
                            Frontend Theme
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_api_credentials'))
                <li <?= $settings_menu == 'api_informations' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/api_informations') }}">
                        <i class="fa fa-key">
                        </i>
                        <span>
                            Google reCaptcha
                        </span>
                    </a>
                </li>
            @endif



            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_appstore_credentials'))
                <li <?= $settings_menu == 'app-store-credentials' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/app-store-credentials') }}">
                        <i class="fa fa-key">
                        </i>
                        <span>
                            App Store Credentials
                        </span>
                    </a>
                </li>
            @endif


            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_email_setting'))
                <li <?= $settings_menu == 'email' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/email') }}">
                        <i class="fa fa-envelope">
                        </i>
                        <span>
                            Email Settings
                        </span>
                    </a>
                </li>
            @endif


            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_sms_setting'))
                <li <?= $settings_menu == 'sms' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/sms/twilio') }}">
                        <i class="glyphicon glyphicon-phone"></i>
                        <span>
                            SMS Settings
                        </span>
                    </a>
                </li>
            @endif

            {{-- Notification Settings --}}
            <li <?= $settings_menu == 'notification-settings' ? ' class="treeview active"' : 'treeview'?>>
                <a href="{{ url(\Config::get('adminPrefix').'/settings/notification-types') }}">
                    <i class="fa fa-bell">
                    </i>
                    <span>
                        Notification Settings
                    </span>
                </a>
            </li>




            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_country'))
                <li <?= $settings_menu == 'country' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/country') }}">
                        <i class="fa fa-flag">
                        </i>
                        <span>
                            Countries
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_language'))
                <li <?= $settings_menu == 'language' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="language" href="{{ url(\Config::get('adminPrefix').'/settings/language') }}">
                        <i class="fa fa-language">
                        </i>
                        <span>
                            Languages
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_merchant_group'))
                <li <?= $settings_menu == 'merchant_group' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/merchant-group') }}">
                        <i class="fa fa-user-secret"></i>
                        <span>
                            Merchant Packages
                        </span>
                    </a>
                </li>
            @endif


            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_group'))
                <li <?= $settings_menu == 'user_role' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/user_role') }}">
                        <i class="fa fa-object-group"></i>
                        <span>
                            User Groups
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_role'))
                <li <?= $settings_menu == 'role' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="permissions_roles" href="{{ url(\Config::get('adminPrefix').'/settings/roles') }}">
                        <i class="fa fa-key"></i>
                        <span>
                            Roles &amp; Permissions
                        </span>
                    </a>
                </li>
            @endif

            <!-- @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_fees'))
                <li <?= $settings_menu == 'fee' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/fees') }}">
                        <i class="fa fa-calculator">
                        </i>
                        <span>
                            Fees
                        </span>
                    </a>
                </li>
            @endif -->

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_database_backup'))
                <li <?= $settings_menu == 'backup' ? ' class="treeview active"' : 'treeview'?>>
                    <a href="{{ url(\Config::get('adminPrefix').'/settings/backup') }}">
                        <i class="fa fa-database">
                        </i>
                        <span>
                            Database Backup
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_meta'))
                <li <?= $settings_menu == 'meta' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="metas" href="{{ url(\Config::get('adminPrefix').'/settings/metas') }}">
                        <i class="glyphicon glyphicon-info-sign">
                        </i>
                        <span>
                            Metas
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_page'))
                <li <?= $settings_menu == 'pages' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="metas" href="{{ url(\Config::get('adminPrefix').'/settings/pages') }}">
                        <i class="fa fa-pagelines"></i>
                        <span>
                            Pages
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_enable_woocommerce'))
                <li <?= $settings_menu == 'enablewoocommerce' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="preference" href="{{ url(\Config::get('adminPrefix').'/settings/enable-woocommerce') }}">
                        <i class="fa fa-shopping-cart"></i>
                        <span>
                            Enable WooCommerce
                        </span>
                    </a>
                </li>
            @endif

            @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'view_preference'))
                <li <?= $settings_menu == 'preference' ? ' class="treeview active"' : 'treeview'?>>
                    <a data-group="preference" href="{{ url(\Config::get('adminPrefix').'/settings/preference') }}">
                        <i class="fa fa-cogs">
                        </i>
                        <span>
                            Preferences
                        </span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</div>
