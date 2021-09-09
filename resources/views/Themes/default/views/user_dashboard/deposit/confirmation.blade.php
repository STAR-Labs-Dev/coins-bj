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
						<p>
							@lang('message.dashboard.deposit.deposit-via')
							<span class="font-weight-600 text-uppercase">{{ strtolower($transInfo['payment_name']) }}</span>
						</p>

						<div class="mt-4"><strong>@lang('message.dashboard.confirmation.details')</strong></div>

						<div class="row m-0 justify-content-between mt-4">
							<div>@lang('message.dashboard.deposit.deposit-amount')</div>
							<div>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</div>
						</div>

						<div class="row justify-content-between m-0 mt-2">
							<div>@lang('message.dashboard.confirmation.fee')</div>
							<div>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['fee'])) }}</div>
						</div>
						<hr class="mb-2" />

						<div class="row m-0 justify-content-between">
							<div><strong>@lang('message.dashboard.confirmation.total')</strong></div>
							<div><strong>{{ moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['totalAmount'])) }}</strong></div>
						</div>

						<div class="row m-0 mt-4 justify-content-between">
							<div>
								<a href="#" class="deposit-confirm-back-link">
									<button class="btn btn-grad deposit-confirm-back-btn mt-2"><strong>@lang('message.dashboard.button.back')</strong></button>
								</a>
							</div>
							<div>
								<form action="{{url('deposit/store')}}" style="display: block;" method="POST" accept-charset="UTF-8" id="deposit_form" novalidate="novalidate" enctype="multipart/form-data">
									<input value="{{csrf_token()}}" name="_token" id="token" type="hidden">
									<input value="{{$transInfo['payment_method']}}" name="method" id="method" type="hidden">
									<input value="{{$transInfo['totalAmount']}}" name="amount" id="amount" type="hidden">
									<button type="submit" class="btn btn-grad mt-2" id="deposit-money-confirm">
										<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposit-money-confirm-text">@lang('message.dashboard.button.confirm')</span>
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@include('user_dashboard.layouts.common.help')
@endsection

@section('js')
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>

<script>

    function depositBack()
    {
        window.localStorage.setItem("depositConfirmPreviousUrl",document.URL);
        window.history.back();
    }

    jQuery.extend(jQuery.validator.messages, {
        required: "{{ __('This field is required.') }}",
    })

    $('#deposit_form').validate({
        rules: {
            amount: {
                required: false,
            },
            method: {
                required: false,
            },
        },
        submitHandler: function(form)
        {
            $("#deposit-money-confirm").attr("disabled", true);
            $(".spinner").show();
            var pretext=$("#deposit-money-confirm-text").text();
            $("#deposit-money-confirm-text").text("{{__('Confirming...')}}");

            //Make back button disabled and prevent click
            $('.deposit-confirm-back-btn').attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });

            //Make back anchor prevent click
            $('.deposit-confirm-back-link').click(function (e)
            {
                e.preventDefault();
            });

            form.submit();

            setTimeout(function(){
                $("#deposit-money-confirm").removeAttr("disabled");
                $(".spinner").hide();
                $("#deposit-money-confirm-text").text(pretext);
            },10000);
        }
    });

    //Only go back by back button, if submit button is not clicked
    $(document).on('click', '.deposit-confirm-back-btn', function (e)
    {
        e.preventDefault();
        depositBack();
    });

</script>
@endsection
