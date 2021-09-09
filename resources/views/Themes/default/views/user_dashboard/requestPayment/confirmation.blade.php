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
						<p class="mb-4">@lang('message.dashboard.send-request.request.confirmation.request-money-from')&nbsp;&nbsp;<strong>{{ isset($transInfo['email']) ? $transInfo['email'] : '' }}</strong></p>
						<div class="row m-0 justify-content-between mt-4">
							<div><strong>@lang('message.dashboard.send-request.request.confirmation.requested-amount')</strong></div>
							<div><strong>{{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</strong></div>
						</div>

						<div class="mt-5">
							<div class="text-center">
								<a href="#" class="request-create-confirm-back-link">
									<button class="btn btn-grad float-left request-create-confirm-back-btn"><strong><i class="fa fa-angle-left"></i>&nbsp;&nbsp;@lang('message.dashboard.button.back')</strong></button>
								</a>

								<a href="{{url('request-money-confirm')}}" class="request-create-confirm-submit-link">
									<button class="btn btn-grad  float-right request-create-confirm-submit-btn">
										<i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
										<strong>
											<span class="request-create-confirm-submit-btn-txt">
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

	$(document).on('click', '.request-create-confirm-submit-btn', function (e)
    {
    	$(".fa-spin").show();
    	$('.request-create-confirm-submit-btn-txt').text("{{ __('Confirming...') }}");
    	$(this).attr("disabled", true);
    	$('.request-create-confirm-submit-link').click(function (e) {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.request-create-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
        $('.request-create-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.request-create-confirm-back-btn', function (e)
    {
        e.preventDefault();
        window.history.back();
    });

</script>

@endsection
