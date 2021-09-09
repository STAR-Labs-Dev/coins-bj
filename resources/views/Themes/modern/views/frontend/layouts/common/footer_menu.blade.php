<?php
    $socialList = getSocialLink();
    $menusFooter = getMenuContent('Footer');
    $logo = getCompanyLogoWithoutSession(); //direct query
?>

<section class="bg-image footer text-white mt-5">
    <div class="bg-dark">
        <div class="container pt-60 pb-3">
            <div class="row  justify-content-between">
                <div class="col-xl-5">
                    <div>
                        <img src="{{theme_asset('public/images/logos/'.$logo)}}" class="mw-200" alt="logo">
                    </div>

                    <p class="mt-3">{{ getCompanyName() . __(', a secured online payment gateway that allows payment in multiple currencies easily, safely and securely.') }}</p>
                </div>

                <div class="col-xl-7 pt-3">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div>
                            <h4>{{ __('Quick Links') }}</h4>
                            <ul class="link mt-3">
                                <li class="mt-2"><a href="{{ url('/') }}" class="text-white">@lang('message.home.title-bar.home')</a></li>
                                <li class="mt-2"><a href="{{ url('/send-money') }}" class="text-white">@lang('message.home.title-bar.send')</a></li>
                                <li class="mt-2"><a href="{{ url('/request-money') }}" class="text-white">@lang('message.home.title-bar.request')</a></li>
                                <li class="mt-2"><a href="{{ url('/developer') }}" class="text-white">@lang('message.home.title-bar.developer')</a></li>
                            </ul>
                        </div>

                        <div>
                            <ul class="link mt-4 pt-1">
                                @if(!empty($menusFooter))
                                    @foreach($menusFooter as $footer_navbar)
                                        <li class="mt-2"><a href="{{url($footer_navbar->url)}}" class="text-white">{{ $footer_navbar->name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between">
                <div>
                    <div class="d-flex justify-content-center">
                        <div class="d-flex flex-wrap social-icons mt-5">
                            @foreach($socialList as $social)
                                @if (!empty($social->url))
                                    <div class="p-2">
                                        <a href="{{ $social->url }}">{!! $social->icon !!}</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <div class="d-flex justify-content-center pt-4">

                        @foreach(getAppStoreLinkFrontEnd() as $app)
                            @if (!empty($app->logo))
                                <div class="p-2 pl-4 pr-4">
                                    <a href="{{$app->link}}"><img src="{{url('public/uploads/app-store-logos/'.$app->logo)}}" class="img-responsive" width="125" height="50"/></a>
                                </div>
                            @else
                                <div class="p-2 pl-4 pr-4">
                                    <a href="#"><img src='{{ url('public/uploads/app-store-logos/default-logo.jpg') }}' class="img-responsive" width="120" height="90"/></a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="mb-2">
            <div class="d-flex justify-content-between">
                <div>
                    <?php
                        $company_name = getCompanyName();
                    ?>
                    <p class="copyright mt-0">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
                </div>

                <div>
                    <div class="container-select d-flex">
                        <div>
                            <i class="fa fa-globe"></i>
                        </div>

                        <div>
                            <select class="select-custom mt-0" id="lang">
                                @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                                <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }} value='{{ $lang->short_name }}'> {{ $lang->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>
