@extends('frontend.layouts.app')
@section('content')
<section>
    <div class="banner">
      <div class="container py-5">
          <div class="row py-5">
              <div class="col-md-7 py-5">
                  <div>
                      <h2 class="text-36">{{ __('Make Your Transaction') }} </h2> 
                      <p class="text-20 text-gray-300">{{ __('Easy cheapest and secure') }}</p>

                  </div>

                  <div class="mt-4 mw-450">
                      <p class="head-sub-title">{{ __('Sending money globally with multiple currencies to your beloved one easily, safely & securely with low fees in just few minutes.') }}</p>
                  </div>

                    @if( !Auth::check() )
                        <div class="mt-5">
                            <a href="{{ url('/register') }}">
                                <button class="btn btn-primary rounded">{{ __('Create an account') }}</button>
                            </a>
                        </div>
                    @endif
              </div>

              <div class="col-md-5">
                  <div class="p-2">
                      <img src="{{ theme_asset('public/images/banner/bannerone.png') }}" class="img-fluid">
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

<section class="mt-60 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <img src="{{ theme_asset('public/images/banner/bannertwo.png') }}" alt="Phone Image" class="img-responsive img-fluid" />
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="text-28 title">@lang('message.home.choose-us.title')</h2>
                <hr class="p-2" style="width: 25px;border-top: 10px groove #635bff;border-top-left-radius: 25px;">

                <div class="row  d-flex flex-wrap mt-4">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Low Cost') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('A built in system with the lowest possible cost that energizes customer to grab it.') }} </p>
                    </div>
                </div>

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Easy Process') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Easily processable and maintainable system that allows you to process and track records.') }} </p>
                    </div>
                </div>

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Faster Payments') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Make payment from one corner of the world to another in just a few seconds. Making payment is very easy and fast.') }} </p>
                    </div>
                </div>


                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Secure and Safe') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Customer\'s data security is the first priority. Make your transactions safe, sound and secure.') }} </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mt-60 bg-white">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div>
                <h2 class="text-28 title text-center">{{ __('What can you do?') }}</h2>
            <hr class="p-2 text-center" style="width: 25px;border-top: 10px groove #635bff;border-top-left-radius: 25px;">
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="row mr-4">
                    <div class="col-md-6">
                        <div class="card p-4">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <img src="{{ theme_asset('public/images/icon/deposit.png') }}" class="choose-img" alt="icon" >
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-center">{{ __('Payment API') }}</h4>
                                <p class="mt-4 text-center text-gray-400">
                                    {{ __('It will manage customer\'s Pay Money experience by integrating our seamless API interface within your website.') }}
                                </p>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 mt-5">
                        <div class="card p-4">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <img src="{{ theme_asset('public/images/icon/receipt.png') }}" class="choose-img" alt="icon" >
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-center">{{ __('Online Payments') }}</h4>
                                <p class="mt-4 text-center text-gray-400">
                                    {{ __('Whether it is credit, debit or bank account you can pay by your preferred way.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="card p-4">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <img src="{{ theme_asset('public/images/icon/transaction.png') }}" class="choose-img" alt="icon" >
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-center">{{ __('Currency Exchange') }}</h4>
                                <p class="mt-4 text-center text-gray-400">
                                    {{ __('Default currency to another, you can change it easily.') }}
                                </p>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6 mt-5">
                        <div class="card  p-4">
                            <div class="d-flex justify-content-center">
                                <div>
                                    <img src="{{ theme_asset('public/images/icon/cash-payment.png') }}" class="choose-img" alt="icon" >
                                </div>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-center">{{ __('Payment Request') }}</h4>
                                <p class="mt-4 text-center text-gray-400">
                                    {{ __('By these systems now you can request for payment from one person to another, within seconds.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-4">
                    <img src="{{ theme_asset('public/images/banner/bannerthree.png') }}" alt="Phone Image" class="img-responsive img-fluid" />
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Where you can use our services -->

<section class="bg-image">
    <div class="mt-60 py-5 bg-dark services text-white">
        <div class="container py-5">
            <div class="row d-flex justify-content-center">
                <div>
                    <h2 class="text-28 title text-center text-white">{{ __('Where can you use our services?') }}</h2>
                    <hr class="p-2 text-center" style="width: 25px;border-top: 10px groove #635bff;border-top-left-radius: 25px;">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 mt-4">
                    <div class="h-100  p-4">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-cart-plus text-24"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-service">{{ __('E-commerce') }}</h4>
                            <p class="mt-3 text-gray-500">{{ __('Easily create your own store and add products. A complete e-commerce with maintainability and efficiency to make an organized store for you.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mt-4">
                    <div class="h-100  p-4">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-calendar-alt text-24"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-service">{{ __('Events') }}</h4>
                            <p class="mt-3 text-gray-500">{{ __('Do not hesitate to compete with a lot of events. A beautiful and easily trackable event management is provided for making your tasks get done easier.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mt-4">
                    <div class="h-100  p-4">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-mobile-alt text-24"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-service">{{ __('Mobile Recharge') }}</h4>
                            <p class="mt-3 text-gray-500">{{ __('Easily top-up airtime and data at the world\'s leading mobile operators and makes payments using any of their wallets on the system.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mt-4">
                    <div class="h-100  p-4">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-dollar-sign text-24"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-service">{{ __('Instant Onboarding') }}</h4>
                            <p class="mt-3 text-gray-500">{{ __('Merchants can get payments instantly from anywhere, anytime without any hassle. A simple and better way to expand your business.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mt-4">
                    <div class="h-100  p-4">
                        <div class="d-flex">
                            <div>
                                <i class="fas fa-calendar-check text-24"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-service">{{ __('E-booking') }}</h4>
                            <p class="mt-3 text-gray-500">{{ __('Allow your customers to make payment for bookings or appointments in a quick, easy and secured process that suits them the most.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mt-4">
                    <div class="h-100  p-4">
                        <div class="d-flex">
                            <div>
                                <i class="fab fa-btc text-24"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h4 class="text-service">{{ __('Crypto Payment') }}</h4>
                            <p class="mt-3 text-gray-500">{{ __('A powerful solution that allows your customers to make payment using crypto coins including Bitcoin, Litecoin, Dogecoin.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- How it work section -->

<section class="mt-60 bg-white">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="p-4">
                    <img src="{{ theme_asset('public/images/banner/bannerfour.png') }}" alt="Phone Image" class="img-responsive img-fluid" />
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="text-28 title">{{ __('How does it work?') }}</h2>
                <hr class="p-2" style="width: 25px;border-top: 10px groove #635bff;border-top-left-radius: 25px;">

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Create Account') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Provide your credentials, create your own account and explore. Creating account is so easy.') }} </p>
                    </div>
                </div>

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Send/Request Amount') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Send or request any amount to your preferred one within seconds. Just search the desired one and send or request for money.') }} </p>
                    </div>
                </div>


                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Select Payment Method') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('Providing you multiple options to pay according to your desired payment method such as PayPal, Stripe, CoinPayments and many more.') }}</p>
                    </div>
                </div>

                <div class="row  d-flex flex-wrap">
                    <div class="p-2">
                        <i class="fas fa-check text-primary choose-img"></i>
                    </div>

                    <div class="mw-450 p-2">
                        <h3>{{ __('Confirmation') }}</h3>
                        <p class="mt-2 text-gray-400">{{ __('After all the steps done above just confirm with your preference and that\'s it. Welcome to successfull transaction.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- payment processor -->

<section class="mt-60 mb-120 bg-white">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div>
                <h2 class="text-28 title text-center">{{ __('Payment Processors') }}</h2>
            <hr class="p-2 text-center" style="width: 25px;border-top: 10px groove #635bff;border-top-left-radius: 25px;">
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-2 mt-4">
                <div class="card p-4">
                    <div class="d-flex justify-content-center">
                        <div>
                            <img src="{{theme_asset('public/images/gateway/paypal.png')}}" class="payment-img" alt="icon" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mt-4">
                <div class="card p-4">
                    <div class="d-flex justify-content-center">
                        <div>
                            <img src="{{theme_asset('public/images/gateway/visa.png')}}" class="payment-img" alt="icon" >
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-2 mt-4">
                <div class="card  p-4">
                    <div class="d-flex justify-content-center">
                        <div>
                            <img src="{{theme_asset('public/images/gateway/mastercard.png')}}" class="payment-img" alt="icon" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mt-4">
                <div class="card  p-4">
                    <div class="d-flex justify-content-center">
                        <div>
                            <img src="{{theme_asset('public/images/gateway/twocheckout.png')}}" class="payment-img" alt="icon" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mt-4">
                <div class="card  p-4">
                    <div class="d-flex justify-content-center">
                        <div>
                            <img src="{{theme_asset('public/images/gateway/coinpaymentlogo.png')}}" class="payment-img" alt="icon" >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 mt-4">
                <div class="card  p-4">
                    <div class="d-flex justify-content-center">
                        <div>
                            <img src="{{theme_asset('public/images/gateway/stripe.png')}}" class="payment-img" alt="icon" >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
