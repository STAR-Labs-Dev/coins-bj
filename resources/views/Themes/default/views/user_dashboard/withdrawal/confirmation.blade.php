@extends('user_dashboard.layouts.app')

@section('css')
    <style>
        @media only screen and (max-width: 240px) {
            .pull-right {
            	float: left;
            }
        }
    </style>
@endsection

@section('content')
    <section class="min-vh-100">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-7 col-xs-12">
                    @include('user_dashboard.layouts.common.alert')
                    <div class="card">
                        <div class="card-header">
                            <h3>@lang('message.dashboard.nav-menu.payout')</h3>
                        </div>

                        <div class="card-body">
                            <p class="mb20">
                                @lang('message.dashboard.payout.new-payout.withdraw-via')
                                <span class="font-weight-600 text-uppercase">{{ strtolower($transInfo['payout_setting']->paymentMethod->name) }}</span>
                            </p>

                            @if ( isset($transInfo['payout_setting']->paymentMethod) && $transInfo['payout_setting']->paymentMethod->name == 'Bank')
                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.bank-account-holder-name')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->account_name }}</b></p>
                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.account-number')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->account_number }}</b></p>
                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.swift-code')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->swift_code }}</b></p>
                                <p class="mb20"> @lang('message.dashboard.payout.payout-setting.modal.bank-name')&nbsp;&nbsp;: <b>{{ $transInfo['payout_setting']->bank_name }}</b></p>
                            @endif


                            <div>@lang('message.dashboard.confirmation.details')</div>

                            <div class="row m-0 mt-2 justify-content-between">
                                <div>
                                    @lang('message.dashboard.left-table.withdrawal.withdrawan-amount')
                                </div>

                                <div>
                                   {{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount']))  }}
                                </div>
                            </div>


                            <div class="row justify-content-between m-0 mt-2">
                                <div>@lang('message.dashboard.confirmation.fee')</div>
                                <div>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'])) }}</div>
                            </div>
                            <hr class="my-2" />
                            <div class="row justify-content-between m-0">
                                <div><strong>@lang('message.dashboard.confirmation.total')</strong></div>
                                <div><strong>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'])) }}</strong></div>
                            </div>

                            <div class="mt-4">
                                <div class="text-center">
                                    <a href="#" class="withdrawal-confirm-back-link">
                                        <button class="btn btn-grad float-left withdrawal-confirm-back-btn mt-2">
                                            <strong>@lang('message.dashboard.button.back')</strong>
                                        </button>
                                    </a>
                                    <a href="{{url('withdrawal/confirm-transaction')}}" class="withdrawal-confirm-submit-link">
                                        <button class="btn btn-grad float-right withdrawal-confirm-submit-btn mt-2">
                                            <i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
                                            <strong>
                                                <span class="withdrawal-confirm-submit-btn-txt">
                                                    @lang('message.dashboard.button.confirm')
                                                </span>
                                            </strong>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
                <!--/col-->
            </div>
            <!--/row-->
        </div>
    </section>
@endsection

@section('js')

<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script>
    function payoutBack()
    {
        localStorage.setItem("payoutConfirmPreviousUrl",document.URL);
        window.history.back();
    }

    $(document).on('click', '.withdrawal-confirm-submit-btn', function (e)
    {
        $(".fa-spin").show()
        $('.withdrawal-confirm-submit-btn-txt').text("{{ __('Confirming...') }}");
        $(this).attr("disabled", true);
        $('.withdrawal-confirm-submit-link').click(function (e) {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.withdrawal-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
        $('.withdrawal-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.withdrawal-confirm-back-btn', function (e)
    {
        e.preventDefault();
        payoutBack();
    });
</script>

@endsection
