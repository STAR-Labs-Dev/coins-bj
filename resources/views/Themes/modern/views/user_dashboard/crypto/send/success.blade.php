@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="my-3">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3>@lang('message.dashboard.right-table.crypto-send') {{ $walletCurrencyCode }}</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">Success</h3>
                        <p class="text-gray-500 text-16">{{ __('Coin has been transferred to the address. Amount will be added to the user wallet after 1 confirmation.') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-xl-10">

                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active text-white">Create</li>
                                    <li class="breadcrumb-first text-white">Confirmation</li>
                                    <li class="breadcrumb-success text-white">Success</li>
                                </ol>
                            </div>


                            <div class="bg-secondary rounded mt-5 shadow py-4 p-35">
                                <div class="card-body">
                                    <div class="d-flex justify-content-center">
                                        <div class="confirm-check"><i class="fa fa-check"></i></div>
                                    </div>

                                    <div class="text-center" style="margin-top: 20px;">
                                        <div class="h3 sub-title">@lang('message.dashboard.send-request.request.confirmation.success')!</div>
                                    </div>

                                    <div class="text-center">
                                        <p><strong>{{ $walletCurrencyCode }} @lang('message.dashboard.crypto.send.success.sent-successfully').</strong></p>
                                    </div>

                                    <div class="text-center" style="margin-top: 10px;">
                                        <p><strong>@lang('message.dashboard.crypto.send.success.amount-added') {{ $confirmations }} @lang('message.dashboard.crypto.send.success.confirmations').</strong></p>
                                    </div>

                                    <div class="text-center" style="margin-top: 10px;">
                                        <h6>
                                            @lang('message.dashboard.crypto.send.success.address'):
                                        </h6>
                                        <strong>
                                            {!! $receiverAddress !!}
                                        </strong>
                                    </div>

                                    <h5 class="text-center" style="margin-top: 10px;">
                                        <p>@lang('message.dashboard.crypto.send.confirm.sent-amount'): {{ moneyFormat($currencySymbol, number_format($amount, 8, '.', '')) }}</p>
                                    </h5>


                                    <div class="mt-5">
                                        <div class="text-center">
                                            <a href="{{ url("transactions/crypto-sent-received-print/".encrypt($transactionId)) }}" target="_blank" class="btn btn-grad mr-2 mt-4">
                                                <strong>@lang('message.dashboard.vouchers.success.print')</strong>
                                            </a>
                                            <a href="{{ url("/crpto/send/".encrypt($walletCurrencyCode)."/".encrypt($walletId)) }}" class="btn btn-primary ml-2 mt-4">
                                                <strong>@lang('message.dashboard.right-table.crypto-send') {{ $walletCurrencyCode }} @lang('message.dashboard.crypto.send.success.again')</strong>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script type="text/javascript">
    function printFunc() {
        window.print();
    }
    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });

    //disabling F5
    function disable_f5(e) {
        if ((e.which || e.keyCode) == 116) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_f5);
    });

    //disabling ctrl+r
    function disable_ctrl_r(e) {
        if (e.keyCode == 82 && e.ctrlKey) {
            e.preventDefault();
        }
    }
    $(document).ready(function() {
        $(document).bind("keydown", disable_ctrl_r);
    });
</script>
@endsection