@extends('user_dashboard.layouts.app')

@section('content')
	<section class="min-vh-100">
		<div class="container mt-5">
			<div class="row justify-content-center">
				<div class="col-lg-6 col-xs-12">
					<div class="card">
						<div class="card-header">
							<h3>@lang('message.dashboard.send-request.send.confirmation.title')</h3>
						</div>

						<div class="card-body">
							<p class="mb20">@lang('message.dashboard.send-request.send.confirmation.send-to')&nbsp;&nbsp;<strong>{{ isset($transInfo['receiver']) ? $transInfo['receiver'] : '' }}</strong></p>
							<div class="h5">@lang('message.dashboard.confirmation.details')</div>
							
							<div class="row m-0 justify-content-between mt-4">
								<div>@lang('message.dashboard.send-request.send.confirmation.transfer-amount')</div>
								<div>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</div>
							</div>

							<div class="row m-0 justify-content-between mt-2">
								<div>@lang('message.dashboard.confirmation.fee')</div>
								<div>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'])) }}</div>
							</div>
							<hr class="mb-2" />

							<div class="row m-0 justify-content-between">
								<div><strong>@lang('message.dashboard.confirmation.total')</strong></div>
								<div><strong>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'])) }}</strong></div>
							</div>

							<div class="mt-4">
								<div class="text-center">
									<a href="#" class="send-money-confirm-back-link">
										 <button class="btn btn-grad mr-2 float-left send-money-confirm-back-button"><strong>@lang('message.dashboard.button.back')</strong></button>
									</a>
									<a href="{{url('send-money-confirm')}}" class="sendMoneyPaymentConfirmLink">
										<button class="btn btn-grad ml-2 float-right sendMoneyConfirm">
											<i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
											<strong>
												<span class="sendMoneyConfirmText">
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
			</div>
		</div>
	</section>
@endsection

@section('js')
<script type="text/javascript">

	$(document).on('click', '.sendMoneyConfirm', function (e)
    {
    	$(".fa-spin").show()
    	$('.sendMoneyConfirmText').text("{{ __('Confirming...') }}");
    	$(this).attr("disabled", true);
    	$('.sendMoneyPaymentConfirmLink').click(function (e)
        {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.send-money-confirm-back-button').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
    	$('.send-money-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

	//Only go back by back button, if submit button is not clicked
    $(document).on('click', '.send-money-confirm-back-button', function (e)
    {
    	e.preventDefault();
        window.history.back();
    });

</script>
@endsection
