@extends('user_dashboard.layouts.app')
@section('content')
  <section class="section-06 history padding-30  min-vh-100">
	<div class="container mt-4">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-xs-12 mb20 marginTopPlus">
				<div class="card">
					<div class="card-header">
						<h3>@lang('message.dashboard.send-request.send.confirmation.title')</h3>
					</div>

					<div class="card-body">
						<div class="text-center">
							<div class="confirm-btns"><i class="fa fa-check"></i></div>
						</div>

						<div class="text-center">
							<div class="h3 mt6 text-success">@lang('message.dashboard.send-request.request.confirmation.success')!</div>
						</div>

						<!-- check mail error message-->
						@if (!empty($errorMessage))
							<div class="text-center"><p><strong>@lang('message.dashboard.send-request.send.confirmation.money-send') @lang('message.dashboard.mail-not-sent').</strong></p></div>
						@else
							<div class="text-center"><p><strong>@lang('message.dashboard.send-request.send.confirmation.money-send').</strong></p></div>
						@endif

						<div class="text-center mt10 img-success">
							@if(!empty($userPic))
								<img style="" src="{{url('public/user_dashboard/profile')}}/{{$userPic}}">
							@else
								<img src="{{url('public/user_dashboard/images/avatar.jpg')}}">
							@endif
						</div>

						<h4 class="text-center mt10"><strong>{{!empty($receiverName)?$receiverName:$transInfo['receiver']}}</strong></h4>
						<h5 class="text-center mt10">@lang('message.dashboard.send-request.send.confirmation.transfer-amount') : {{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</h5>
						<div class="mt-5">
							<div class="text-center">
								<a href="{{url('moneytransfer/print')}}/{{$transInfo['trans_id']}}" target="_blank" class="btn btn-grad mr-2"><strong>@lang('message.dashboard.vouchers.success.print')</strong></a>
								<a href="{{url('moneytransfer')}}" class="btn btn-grad ml-2"><strong>@lang('message.dashboard.send-request.send.confirmation.send-again')</strong></a>
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
    //window.history.forward(1);
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
