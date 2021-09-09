@extends('user_dashboard.layouts.app')

@section('content')

<section class="section-06 history padding-30 min-vh-100">
	<div class="container mt-4">
		<div class="row justify-content-center">
			<div class="col-lg-6 col-xs-12">
				<div class="card">
					<div class="card-header">
						<h3>@lang('message.dashboard.exchange.confirm.title')</h3>
					</div>
					<div class="card-body">
						<p class="mb20">@lang('message.dashboard.exchange.confirm.exchanging') <strong>{{ $fromCurrency->code }}</strong>
							@lang('message.dashboard.exchange.confirm.of') <strong>{{ isset($transInfo['defaultAmnt']) ? formatNumber($transInfo['defaultAmnt']) : 0.00 }}</strong>
							@lang('message.dashboard.exchange.confirm.equivalent-to') <strong>{{ isset($transInfo['finalAmount']) ? formatNumber($transInfo['finalAmount']) : 0.00 }} {{ $transInfo['currCode'] }}</strong>
						</p>
						<p>@lang('message.dashboard.exchange.confirm.exchange-rate'):  &nbsp;<strong>1 {{$fromCurrency->code}} </strong>= <strong>
							{{ ($transInfo['dCurrencyRate']) }} {{ $transInfo['currCode'] }}</strong></p>

						<div class="h5 mt-2"><strong>@lang('message.dashboard.confirmation.details')</strong></div>
						<div class="mt-4">
							<div class="row m-0 justify-content-between mt-2">
								<div>@lang('message.dashboard.exchange.confirm.amount')</div>
								<div>{{  moneyFormat($fromCurrency->symbol, isset($transInfo['defaultAmnt']) ? formatNumber($transInfo['defaultAmnt']) : 0.00) }}</div>
							</div>

							<div class="row justify-content-between m-0 mt-2">
								<div>@lang('message.dashboard.confirmation.fee')</div>
								<div>{{  moneyFormat($fromCurrency->symbol, isset($transInfo['fee']) ? formatNumber($transInfo['fee']) : 0.00) }}</div>
							</div>
							<hr class="my-2" />
							<div class="row m-0 justify-content-between">
								<div><strong>@lang('message.dashboard.confirmation.total')</strong></div>
								<div><strong>{{  moneyFormat($fromCurrency->symbol, isset($transInfo['totalAmount']) ? formatNumber($transInfo['totalAmount']) : 0.00) }}</strong></div>
							</div>
						</div>

						<div class="mt-5">
							<div class="text-center">
								<a href="#" class="exchange-confirm-back-link">
									<button class="btn btn-grad float-left exchange-confirm-back-btn mt-2"><strong>@lang('message.dashboard.button.back')</strong></button>
								</a>
								<a href="{{url('exchange-of-money-success')}}" class="exchange-confirm-submit-link">
									<button class="btn btn-grad float-right exchange-confirm-submit-btn mt-2">
										<i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
										<strong>
											<span class="exchange-confirm-submit-btn-txt">
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

	function exchangeBack()
	{
		localStorage.setItem("previousUrl",document.URL);
		window.history.back();
	}

	$(document).on('click', '.exchange-confirm-submit-btn', function (e)
    {
    	$(".fa-spin").show();
    	$('.exchange-confirm-submit-btn-txt').text("{{__('Confirming...')}}");
    	$(this).attr("disabled", true);
    	$('.exchange-confirm-submit-link').click(function (e) {
            e.preventDefault();
        });

        //Make back button disabled and prevent click
        $('.exchange-confirm-back-btn').attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });

        //Make back anchor prevent click
        $('.exchange-confirm-back-link').click(function (e)
        {
            e.preventDefault();
        });
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.exchange-confirm-back-btn', function (e)
    {
        e.preventDefault();
        exchangeBack();
    });

</script>

@endsection