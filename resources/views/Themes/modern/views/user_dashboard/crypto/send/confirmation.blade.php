@php
$amount = number_format($cryptoTrx['amount'], 8, '.', '');
$networkFee = $cryptoTrx['networkFee'];
$total = number_format($cryptoTrx['amount'] + $cryptoTrx['networkFee'], 8, '.', '');
@endphp

@extends('user_dashboard.layouts.app')

@section('css')
<style>
	@media only screen and (max-width: 260px) {
		.pull-right {
			float: left;
		}
	}

	@media only screen and (max-width: 320px) {
		.crypto-send-confirm-text {
			font-size: 12px !important;
		}
	}
</style>
@endsection

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
						<h3 class="sub-title">Confirmation</h3>
						<p class="text-gray-500 text-16">{{ __('Take a look before you send. Once the coin sent to this address, it can not be undone.') }}</p>
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
									<li class="active">Success</li>
								</ol>
							</div>
							

							<div class="bg-secondary rounded mt-5 shadow py-4 p-35">
								<div>

									<div class="card-body" style="padding: 0px;">
										<p>
											<div style="float: left;">
												@lang('message.dashboard.crypto.send.confirm.about-to-send-text-1') {!! $walletCurrencyCode !!} @lang('message.dashboard.crypto.send.confirm.about-to-send-text-2') &nbsp;
											</div>
											<div style="word-break: break-word !important;">
												<strong>{!! $cryptoTrx['receiverAddress'] !!}</strong>
											</div>
										</p>
									</div>

									<div class="mt-4">
										<p class="sub-title">@lang('message.dashboard.confirmation.details')</p>
									</div>

									<div>
										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>@lang('message.dashboard.crypto.send.confirm.sent-amount')</p>
											</div>

											<div class="pl-2">
												<p>{{ moneyFormat($cryptoTrx['currencySymbol'], $amount) }}</p>
											</div>
										</div>

										<div class="d-flex flex-wrap justify-content-between mt-2">
											<div>
												<p>@lang('message.dashboard.crypto.send.confirm.network-fee')</p>
											</div>

											<div class="pl-2">
												<p>{{ moneyFormat($cryptoTrx['currencySymbol'], $networkFee) }}</p>
											</div>
										</div>
									</div>
									<hr class="mb-2">

									<div class="d-flex flex-wrap justify-content-between">
										<div>
											<p class="font-weight-600">@lang('message.dashboard.confirmation.total')</p>
										</div>

										<div class="pl-2">
											<p class="font-weight-600">{{ moneyFormat($cryptoTrx['currencySymbol'], $total) }}</p>
										</div>
									</div>


									<div class="row m-0 mt-4 justify-content-between">
										<div>
											<a href="#" class="crypto-send-confirm-back-link">
												<p class="text-active text-underline crypt-send-confirm-back-button mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
											</a>
										</div>


										<div>
											<a href="{{url('crpto/send/success')}}" class="crypto-send-confirm-link">
												<button class="btn btn-primary px-4 py-2 ml-2 float-right crypto-send-confirm">
													<i class="fa fa-spinner fa-spin" style="display: none;" id="spinner"></i>
													<strong>
														<span class="crypto-send-confirm-text">
															@lang('message.dashboard.button.confirm') &nbsp;
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
			</div>
		</div>
	</div>
</section>
@endsection

@section('js')

<script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">
	function userCryptoSendConfirmBack() {
		window.localStorage.setItem("previousUserCrytoSentUrl", document.URL);
		var urlToUserCryptoSendCreate = '{{ url("/crpto/send/".encrypt(strtolower($walletCurrencyCode))."/".encrypt($walletId)) }}';
		window.location.replace(urlToUserCryptoSendCreate);
	}

	//Only go back by back button, if submit button is not clicked
	$(document).on('click', '.crypt-send-confirm-back-button', function(e) {
		e.preventDefault();
		userCryptoSendConfirmBack();
	});


	$(document).on('click', '.crypto-send-confirm', function(e) {
		window.localStorage.removeItem('user-crypto-sent-amount');
		window.localStorage.removeItem('user-crypto-receiver-address');

		$(".fa-spin").show()
		$('.crypto-send-confirm-text').text("{{ __('Confirming...') }}");
		$(this).attr("disabled", true);
		$('.crypto-send-confirm-link').click(function(e) {
			e.preventDefault();
		});

		//Make back button disabled and prevent click
		$('.crypt-send-confirm-back-button').attr("disabled", true).click(function(e) {
			e.preventDefault();
		});

		//Make back anchor prevent click
		$('.send-money-confirm-back-link').click(function(e) {
			e.preventDefault();
		});
	});
</script>

@endsection