@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-8 col-xs-12">
				@include('user_dashboard.layouts.common.alert')

				<form method="POST" action="{{url('transfer')}}" id="transfer_form" accept-charset='UTF-8'>
					<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
					<div class="card">
						<div class="card-header">
							<h3 class="active">@lang('message.dashboard.right-table.crypto-receive') {{ strtoupper($walletCurrencyCode) }}</h3>
						</div>

						<div class="card-body">
							<div class="text-center">
								<h3>@lang('message.dashboard.crypto.receive.address-qr-code-head-text')</h3>
								<div class="mt-5 d-flex justify-content-center" id="wallet-address"></div>
								<br>
								<small class="form-text text-muted"><b>@lang('message.dashboard.crypto.receive.address-qr-code-foot-text-1') {{ strtoupper($walletCurrencyCode) }} @lang('message.dashboard.crypto.receive.address-qr-code-foot-text-2')</b>, @lang('message.dashboard.crypto.receive.address-qr-code-foot-text-3').</small>
								<br>
							</div>

                            <div class="mt-4 pl-5 mb-4">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">@lang('message.dashboard.crypto.receive.address-input-label-text')</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" id="wallet-address-input" value="{{ decrypt($address) }}" readonly >
                                        <div class="input-group-append wallet-address-copy-btn">
                                          <span class="input-group-text btn-grad-copy " >@lang('message.dashboard.crypto.receive.address-input-copy-text')</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
@endsection

@section('js')

<!-- jquery-qrcode -->
<script src="{{theme_asset('public/js/jquery-qrcode/jquery.qrcode.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/jquery-qrcode/qrcode.js')}}" type="text/javascript"></script>
<!-- sweetalert -->
<script src="{{theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js')}}" type="text/javascript"></script>


<script>
    $(window).on('load', function (e)
    {
        jQuery('#wallet-address').qrcode({
            text : '{{ decrypt($address) }}'
        });
    });

    $(document).on('click','.wallet-address-copy-btn',function ()
    {
        $('#wallet-address-input').select();
        document.execCommand('copy');

        // TODO: translation
        swal({
            title: "{{ __('Copied') }}!",
            text: "{{ __('Address Copied') }}!",
            type: "info",
            icon: "success",
            closeOnClickOutside: false,
            closeOnEsc: false,
        });
    })
</script>

@endsection
