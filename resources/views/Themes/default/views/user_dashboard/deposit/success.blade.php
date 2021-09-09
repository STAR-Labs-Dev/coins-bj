@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h3>@lang('message.dashboard.deposit.title')</h3>
                    </div>

                    <div class="card-body">
                        <div class="text-center">
                            <div class="confirm-btns"><i class="fa fa-check"></i></div>
                        </div>

                        <div class="text-center">
                            <div class="h3 mt6 text-success"> @lang('message.dashboard.deposit.success')!</div>
                        </div>

                        <div class="text-center"><p><strong> @lang('message.dashboard.deposit.completed-success')</strong></p></div>
                        <h5 class="text-center mt10">@lang('message.dashboard.deposit.deposit-amount') : {{  moneyFormat($transaction->currency->symbol, formatNumber($transaction->subtotal)) }}</h5>

                        <div class="mt-4">
                            <div class="text-center">
                                <a href="{{url('deposit-money/print')}}/{{$transaction->id}}" target="_blank" class="btn btn-grad mr-2 mt-4"><strong>@lang('message.dashboard.vouchers.success.print')</strong></a>
                                <a href="{{url('deposit')}}" class="btn btn-grad ml-2 mt-4"><strong>@lang('message.dashboard.deposit.deposit-again')</strong></a>
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
</script>
@endsection
