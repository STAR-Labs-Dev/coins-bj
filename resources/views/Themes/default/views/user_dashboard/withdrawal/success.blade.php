@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h3>@lang('message.dashboard.nav-menu.payout')</h3>
                    </div>

                    <div class="card-body">
                        <div class="text-center">
                            <div class="confirm-btns"><i class="fa fa-check"></i></div>
                        </div>
                        <div class="text-center">
                            <div class="h3 mt6 text-success">@lang('message.dashboard.payout.new-payout.success')!</div>
                        </div>
                        <div class="text-center"><p><strong>@lang('message.dashboard.payout.new-payout.payout-success')</strong></p></div>

                        <h5 class="text-center mt10">@lang('message.dashboard.payout.new-payout.amount') : {{  moneyFormat($currencySymbol, formatNumber($amount)) }}</h5>
                        <div class="mt-5">
                            <div class="text-center">
                                <a href="{{url('withdrawal-money/print')}}/{{$transactionId}}" target="_blank" class="btn btn-grad mr-2 mt-2"><strong>@lang('message.dashboard.vouchers.success.print')</strong>
                                </a>

                                <a href="{{url('payout')}}" class="btn btn-grad payout-again ml-2 mt-2">
                                    <strong>@lang('message.dashboard.payout.new-payout.payout-again')</strong>
                                </a>
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

        function printFunc(){
            window.print();
        }
        $(document).ready(function() {
            window.history.pushState(null, "", window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, "", window.location.href);
            };
        });

        //disabling F5
        function disable_f5(e)
        {
          if ((e.which || e.keyCode) == 116)
          {
              e.preventDefault();
          }
        }
        $(document).ready(function(){
            $(document).bind("keydown", disable_f5);
        });

        //disabling ctrl+r
        function disable_ctrl_r(e)
        {
          if(e.keyCode == 82 && e.ctrlKey)
          {
            e.preventDefault();
          }
        }
        $(document).ready(function(){
            $(document).bind("keydown", disable_ctrl_r);
        });

        //Clear withdrawal local storage values on click - payout again
        $(document).on('click', '.payout-again', function()
        {
            window.localStorage.removeItem('payoutConfirmPreviousUrl');
            window.localStorage.removeItem('payoutPaymentMethodId');
            window.localStorage.removeItem('currency_id');
            window.localStorage.removeItem('pFees');
            window.localStorage.removeItem('fFees');
            window.localStorage.removeItem('total_fees_html');
        });
    </script>
@endsection
