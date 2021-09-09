@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-xs-12">
				<div class="card">
					<div class="card-header">
						<h3>@lang('message.dashboard.send-request.request.confirmation.title')</h3>
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
							<div class="text-center"><p><strong>@lang('message.dashboard.send-request.request.confirmation.success-send') @lang('message.dashboard.mail-not-sent').</strong></p></div>
						@else
							<div class="text-center"><p><strong>@lang('message.dashboard.send-request.request.confirmation.success-send').</strong></p></div>
						@endif
						<div class="text-center mt10 img-success">
							@if(!empty($userPic))
								<img style="" src="{{url('public/user_dashboard/profile')}}/{{$userPic}}">
							@else
								<img src="{{url('public/user_dashboard/images/avatar.jpg')}}">
							@endif
						</div>

						<h4 class="text-center mt10"><strong>{{!empty($receiverName)?$receiverName:$transInfo['email']}}</strong></h4>
						<h5 class="text-center mt10">@lang('message.dashboard.send-request.request.confirmation.request-amount') : {{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</h5>
						<div class="mt-5">
							<div class="text-center">
								<a href="{{url('request-payment/print')}}/{{$transInfo['trans_id']}}" target="_blank" class="btn btn-grad mr-2">
									<strong>@lang('message.dashboard.vouchers.success.print')</strong>
								</a>

								<a href="{{url('request_payment/add')}}" class="btn btn-grad ml-2">
									<strong>@lang('message.dashboard.send-request.request.confirmation.request-again')</strong>
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
    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });
</script>
@endsection