
<?php
    $user = Auth::user();
    $socialList = getSocialLink();
    $menusHeader = getMenuContent('Header');
    $logo = getCompanyLogoWithoutSession(); //direct query


?>

<header id="js-header-old">
    <nav class="navbar navbar-expand-lg">
        <div class="container pl-0">
            @if($logo)
                <a style="width: 200px;overflow: hidden;"  class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                    <img src="{{asset('public/images/logos/'.$logo)}}" alt="logo" class="img-fluid">
                </a>
            @else
                <a style="width: 157px;overflow: hidden;"  class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                    <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="img-responsive" width="80" height="50">
                </a>
            @endif

            @if (request()->path() != 'merchant/payment')
                <button aria-label="navbar" class="navbar-toggler text-28" data-toggle="modal" data-target="#left_modal">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse navbar-toggler-right" id="navbarSupportedContent">
                    <ul class="navbar-nav  my-navbar">
                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'home' ) ? 'nav_active': '' ?>"><a href="{{url('/')}}" class="nav-link">@lang('message.home.title-bar.home') </a></li>
                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'send-money' ) ? 'nav_active': '' ?>"><a href="{{url('/send-money')}}" class="nav-link">@lang('message.home.title-bar.send')</a></li>
                        <li class="nav-item <?= isset( $menu ) && ( $menu == 'request-money' ) ? 'nav_active': '' ?>"><a href="{{url('/request-money')}}" class="nav-link">@lang('message.home.title-bar.request')</a></li>
                     @if(!empty($menusHeader))
                        @foreach($menusHeader as $top_navbar)
                            <li class="nav-item <?= isset( $menu ) && ( $menu == $top_navbar->url ) ? 'nav_active': '' ?>"><a href="{{url($top_navbar->url)}}" class="nav-link"> {{ $top_navbar->name }}</a></li>
                        @endforeach
                    @endif
                        @if( !Auth::check() )
                            <li class="nav-item auth-menu"> <a href="{{url('/login')}}" class="nav-link">@lang('message.home.title-bar.login')</a></li>
                            <li class="nav-item auth-menu"> <a href="{{url('/register')}}" class="nav-link">@lang('message.home.title-bar.register')</a></li>
                        @else
                            <li class="nav-item auth-menu"> <a href="{{url('/dashboard')}}" class="nav-link">@lang('message.home.title-bar.dashboard')</a> </li>
                            <li class="nav-item auth-menu"> <a href="{{url('/logout')}}" class="nav-link">@lang('message.home.title-bar.logout')</a> </li>
                        @endif
                    </ul>
                </div>
                
            @endif

            <div id="quick-contact" class="collapse navbar-collapse">
                <ul class="ml-auto">
                    @if( !Auth::check())
                        @if (request()->path() == 'merchant/payment')
                            {{-- @php
                                $grandId = $_GET['grant_id'];
                                $urlToken = $_GET['token'];
                            @endphp
                            <li> <a href="{{ url("merchant/payment?grant_id=$grandId&token=$urlToken") }}">@lang('message.home.title-bar.login')</a> </li> --}}
                        @else
                            <li> <a href="{{url('/login')}}">@lang('message.home.title-bar.login')</a> </li>
                            <li> <a href="{{url('/register')}}">@lang('message.home.title-bar.register')</a> </li>
                        @endif
                    @else
                        <li><a href="{{url('/dashboard')}}">@lang('message.home.title-bar.dashboard')</a> </li>
                        <li><a href="{{url('/logout')}}">@lang('message.home.title-bar.logout')</a> </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>



    <!-- Modal Window -->
    <div class="modal left fade" id="left_modal" tabindex="-1" role="dialog" aria-labelledby="left_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 m-nav-bg"> 
                    @if(Auth::check())
                        <div class="row justify-content-center">
                            <div>
                                @if(Auth::user()->picture)
                                    <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}"
                                        class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                                @else
                                    <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" class="rounded-circle rounded-circle-custom" id="profileImageHeader">
                                @endif
                                
                            </div>

                            <div>
                                @php
                                    $fullName = strlen($user->first_name.' '.$user->last_name) > 20 ? substr($user->first_name.' '.$user->last_name,0,20)."..." : $user->first_name.' '.$user->last_name; //change in pm_v2.1
                                @endphp
                                <p  class="text-white ml-1 mt-2"> {{ $fullName }}</p>
                            </div>
                        </div>
                    @else
                        <div>
                            @if($logo)
                            <a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                                <img src="{{asset('public/images/logos/'.$logo)}}" alt="logo" class="logo">
                            </a>
                            @else
                                <a class="navbar-brand" href="@if (request()->path() != 'merchant/payment') {{ url('/') }} @else {{ '#' }} @endif">
                                    <img src="{{ url('public/uploads/userPic/default-logo.jpg') }}" class="logo">
                                </a>
                            @endif
                            
                        </div>
                    @endif

                    <button type="button" class="close text-28" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <ul class="mobile-side">
                        <li><a href="{{url('/')}}">@lang('message.home.title-bar.home')</a></li>
                        <li><a href="{{url('/send-money')}}">@lang('message.home.title-bar.send')</a></li>
                        <li><a href="{{url('/request-money')}}">@lang('message.home.title-bar.request')</a></li>
                     @if(!empty($menusHeader))
                        @foreach($menusHeader as $top_navbar)
                            <li><a href="{{url($top_navbar->url)}}"> {{ $top_navbar->name }}</a></li>
                        @endforeach
                    @endif
                        @if( !Auth::check() )
                            <li> <a href="{{url('/login')}}">@lang('message.home.title-bar.login')</a></li>
                            <li> <a href="{{url('/register')}}">@lang('message.home.title-bar.register')</a></li>
                        @else
                            <li> <a href="{{url('/dashboard')}}">@lang('message.home.title-bar.dashboard')</a> </li>
                            <li> <a href="{{url('/logout')}}">@lang('message.home.title-bar.logout')</a> </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>