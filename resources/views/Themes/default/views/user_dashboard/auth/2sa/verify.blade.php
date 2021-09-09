
@extends('user_dashboard.layouts.app')
@section('css')
    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/iCheck/square/blue.css') }}">
@endsection

@section('content')
 <!--Start Section-->
 <section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                @include('user_dashboard.layouts.common.alert')
					<div class="card">
                        <div class="card-header">
                            <h3 class="text-center">@lang('message.2sa.title-text')</h3>
                        </div>

                        <div class="p-4">
							<form class="form-horizontal" method="POST" id="2sa_form"><!--submitting via ajax-->
								<input type="hidden" name="fingerprint" id="fingerprint" class="form-control">
								<div class="form-group">
									<label class="col-md-12 control-label">
										<p class="text-left">
											@lang('message.2sa.extra-step')
										</p>
										<br>
										<p class="text-left">
											@lang('message.2sa.confirm-message')
											@if (auth()->user()->user_detail->two_step_verification_type == 'phone')
												{{ str_pad(substr(auth()->user()->phone, -2), strlen(auth()->user()->phone), '*', STR_PAD_LEFT) }}.
											@elseif (auth()->user()->user_detail->two_step_verification_type == 'email')
												{{ auth()->user()->email }}
											@endif
										</p>
									</label>
								</div>

								<div class="form-group {{ $errors->has('two_step_verification_code') ? ' has-error' : '' }}">
									<div class="col-md-6">
										<input id="two_step_verification_code" class="form-control" placeholder="Enter the 6-digit code" name="two_step_verification_code"
										oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" type = "number" maxlength = "6" required autofocus/>

										@if ($errors->has('two_step_verification_code'))
											<span class="error">
												<strong>{{ $errors->first('two_step_verification_code') }}</strong>
											</span>
										@endif
									</div>
								</div>

								<div class="form-group">
									<div class="checkbox icheck" style="margin-left: 15px;">
										<label>
											<input type="checkbox" name="remember_me" id="remember_me">
											<span style="font-size: 16px; font-weight: 600; color: #181818;">&nbsp;&nbsp;&nbsp;@lang('message.2sa.remember-me-checkbox')</span>
										</label>
									</div>
								</div>

								<div class="form-group">
									<div class="col-md-6 col-md-offset-6">
										<button type="submit" class="btn btn-grad verify_code" id="verify_code">@lang('message.2sa.verify')</button>
									</div>
								</div>
							</form>
						</div>
					</div>
            </div>
        </div>
    </div>
</section>
@endsection



@section('js')
<script src="{{ theme_asset('public/js/iCheck/icheck.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/fpjs2/fpjs2.js') }}" type="text/javascript"></script>


<script>
		    //verifying script - start
		    $(function () {
			    $('input').iCheck({
			        checkboxClass: 'icheckbox_square-blue',
			        radioClass: 'iradio_square-blue',
			        increaseArea: '20%' // optional
			    });
			});

			//verifying on submit
			$('#2sa_form').submit(function(event)
			{
			    event.preventDefault();

			    var token = '{{csrf_token()}}';
			    var two_step_verification_code = $("#two_step_verification_code").val();
			    var remember_me = $("#remember_me").is(':checked');

				//Fingerprint2
				new Fingerprint2().get(function(result, components)
				{
				   $.ajax({
			        method: "POST",
			        url: SITE_URL + "/2fa/verify",
			        cache: false,
			        dataType:'json',
			        data: {
				            "_token": token,
				     		'two_step_verification_code': two_step_verification_code,
				            'remember_me': remember_me,
				            'browser_fingerprint': result,
			        	}
				    })
				    .done(function(data)
				    {
				       	if (data.status == false || data.status == 404) {
				        	//failure
				            $('#message').css('display', 'block');
				            $('#message').html(data.message);
				            $('#message').addClass(data.error);
				        } else {
				        	//success
				            $('#message').removeClass('alert-danger');
				            $('#message').hide();
				            window.location.href="{{ url('dashboard') }}";
				        }
				    });
				});
			});
</script>
@endsection
