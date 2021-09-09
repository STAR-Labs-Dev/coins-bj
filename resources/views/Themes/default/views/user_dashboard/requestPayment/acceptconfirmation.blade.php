@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-xs-12">
				<div class="card">
					<div class="card-header">
						<h3>@lang('message.dashboard.send-request.request.success.title')</h3>
					</div>

					<div class="card-body">
						<div class="h5"><strong>@lang('message.dashboard.confirmation.details')</strong></div>
						<div class="row mt20">
							<div class="col-md-12">
								<div class="pull-left">@lang('message.dashboard.send-request.request.success.accept-amount')</div>
								<div class="pull-right"><strong>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</strong></div>
							</div>
						</div>

						<div class="row mt10">
							<div class="col-md-12">
								<div class="pull-left">@lang('message.dashboard.confirmation.fee')</div>
								<div class="pull-right"><strong>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'])) }}</strong></div>
							</div>
						</div>
						<hr />

						<div class="row">
							<div class="col-md-12">
								<div class="pull-left h6"><strong>@lang('message.dashboard.confirmation.total')</strong></div>
								<div class="pull-right"><strong>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'])) }}</strong></div>
							</div>
						</div>

						<div class="mt-5">
							<div class="text-center">
								<a href="{{url('request_payment/accept/'.$requestPaymentId)}}" class="request-accept-confirm-back-link">
									<button class="btn btn-grad float-left request-accept-confirm-back-btn"><strong><i class="fa fa-angle-left"></i>&nbsp;&nbsp;@lang('message.dashboard.button.back')</strong></button>
								</a>

								<a href="{{url('request_payment/accept-money-confirm')}}" class="request-accept-confirm-submit-link">
								<button class="btn btn-grad float-right request-accept-confirm-submit-btn">
									<i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
									<strong>
										<span class="request-accept-confirm-submit-btn-txt">
											@lang('message.dashboard.button.confirm') &nbsp; <i class="fa fa-angle-right"></i>
										</span>
									</strong>
								</button>
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
	$(document).on('click', '.request-accept-confirm-submit-btn', function (e)
    {
    	$(".fa-spin").show()
    	$('.request-accept-confirm-submit-btn-txt').text("{{ __('Confirming...') }}");
    	$(this).attr("disabled", true);
    	$('.request-accept-confirm-submit-link').click(function (e)
        {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
    	$('.request-accept-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
    	$('.request-accept-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.request-accept-confirm-back-btn', function (e)
    {
    	e.preventDefault();
        window.history.back();
    });
</script>

@endsection
