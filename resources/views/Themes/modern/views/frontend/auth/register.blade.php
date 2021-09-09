@extends('frontend.layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/intl-tel-input-13.0.0/build/css/intlTelInput.css')}}">
@endsection

@section('content')

<!--Start banner Section-->
<section class="bg-image">
    <div class="bg-dark">
        <div class="container">
            <div class="row py-5">
                <div class="col-md-12">
                    <h2 class="text-white font-weight-bold text-28">@lang('message.registration.title')</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!--End banner Section-->

<!--Start Section-->
<section class="sign-up padding-30">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-8 mt-5">
                        <!-- form card login -->
                        <div class="card p-4 rounded-0">
                            <div>
                                <h3 class="mb-0 font-weight-bold">@lang('message.registration.form-title')</h3>
                                <p class="mt-2 text-14">
                                    <span>@lang('message.registration.new-account-question') &nbsp;</span>
                                    <a href="{{url('login')}}" class="text-active">@lang('message.registration.sign-here')</a>.
                                </p>
                            </div>

                            <div class="mt-4 mb-3">
                                @include('frontend.layouts.common.alert')

                                <form action="{{ url('register/store') }}" class="form-horizontal" id="register_form" method="POST">
                                    {{ csrf_field() }}

                                    <input type="hidden" name="has_captcha" value="{{ isset($setting['has_captcha']) && ($setting['has_captcha'] == 'registration' || $setting['has_captcha'] == 'login_and_registration') ? 'registration' : 'Disabled' }}">

                                    <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                                    <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">

                                    <input type="hidden" name="formattedPhone" id="formattedPhone" class="form-control">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.first-name')<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="{{old('first_name')}}">
                                                @if($errors->has('first_name'))
                                                <span class="error">
                                                    {{ $errors->first('first_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.last-name')<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="{{old('last_name')}}">

                                                @if($errors->has('last_name'))
                                                <span class="error">
                                                    {{ $errors->first('last_name') }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.email')<span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}">
                                                @if($errors->has('email'))
                                                <span class="error">
                                                    {{ $errors->first('email') }}
                                                </span>
                                                @endif
                                                <span id="email_error"></span>
                                                <span id="email_ok" class="text-success"></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputAddress">@lang('message.registration.phone')</span></label>
                                                <br>
                                                <input type="tel" class="form-control" id="phone" name="phone">
                                                <span id="phone-error"></span>
                                                <span id="tel-error"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="inputEmail4">@lang('message.registration.password')<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" id="password">
                                            @if($errors->has('password'))
                                                <span class="error">
                                                    {{ $errors->first('password') }}
                                                </span>
                                            @endif
                                        </div>
                                        </div>
                                        <div class=" col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="inputPassword4">@lang('message.registration.confirm-password')<span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        </div>
                                    </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputAddress">@lang('message.registration.type-title')<span class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control" name="type" id="type">
                                          <option value=''>@lang('message.registration.select-user-type')</option>
                                          @if (!empty($checkUserRole))
                                            <option value='user'>@lang('message.registration.type-user')</option>
                                          @endif
                                          @if (!empty($checkMerchantRole))
                                            <option value='merchant'>@lang('message.registration.type-merchant')</option>
                                          @endif
                                        </select>
                                    </div>
                                    @if (isset($setting['has_captcha']) && ($setting['has_captcha'] == 'registration' || $setting['has_captcha'] == 'login_and_registration'))
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! app('captcha')->display() !!}

                                                @if ($errors->has('g-recaptcha-response'))
                                                    <span class="error">
                                                        <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                                    </span>
                                                    <br>
                                                @endif
                                                <br>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
                                            <div class="checkbox">
                                              <p class="text-14">@lang('message.registration.terms')</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-sm-12 mt20">
                                            <button type="submit" class="btn btn-grad mt-4" id="users_create"><i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="users_create_text">@lang('message.form.button.sign-up')</span></button>
                                        </div>
                                    </div>
                                </form>
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
<script src="{{ theme_asset('public/js/intl-tel-input-13.0.0/build/js/intlTelInput.js')}}" type="text/javascript"></script>
<!-- isValidPhoneNumber -->
<script src="{{ theme_asset('public/js/isValidPhoneNumber.js') }}" type="text/javascript"></script>

<script>
    // flag for button disable/enable
    var hasPhoneError = false;
    var hasEmailError = false;

        //jquery validation
    $.validator.setDefaults({
    highlight: function(element) {
        $(element).parent('div').addClass('has-error');
    },
    unhighlight: function(element) {
        $(element).parent('div').removeClass('has-error');
    },
    errorPlacement: function (error, element) {
            error.insertAfter(element);
        }
    });

    jQuery.extend(jQuery.validator.messages, {
        required: "{{ __('This field is required.') }}",
        email: "{{ __("Please enter a valid email address.") }}",
        equalTo: "{{ __("Please enter the same value again.") }}",
        minlength: $.validator.format( "{{ __("Please enter at least") }}"+" {0} "+"{{ __("characters.") }}" ),
        password_confirmation: {
            equalTo: "{{ __("Please enter same value as the password field!") }}",
        },
    })

    $('#register_form').validate({
        rules: {
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
                minlength: 6,
            },
            password_confirmation: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            type: {
                required: true,
            },
        },
        messages: {
            password_confirmation: {
                equalTo: "{{ __("Please enter same value as the password field!") }}",
            },
        },
        submitHandler: function(form)
        {
            $("#users_create").attr("disabled", true).click(function (e)
            {
                e.preventDefault();
            });
            $(".spinner").show();
            $("#users_create_text").text("{{ __('Signing Up...') }}");
            form.submit();
        }
    });
/*
intlTelInput
 */
    $(document).ready(function()
    {
        var countryShortCode = '{{ getDefaultCountry() }}';

        $("#phone").intlTelInput({
            separateDialCode: true,
            nationalMode: true,
            preferredCountries: [countryShortCode],
            autoPlaceholder: "polite",
            placeholderNumberType: "MOBILE",
            utilsScript: "{{ theme_asset('public/js/intl-tel-input-13.0.0/build/js/utils.js') }}"
        });

        let countryData = $("#phone").intlTelInput("getSelectedCountryData");
        $('#defaultCountry').val(countryData.iso2);
        $('#carrierCode').val(countryData.dialCode);

        $("#phone").on("countrychange", function(e, countryData)
        {
            formattedPhone();

            // log(countryData);
            $('#defaultCountry').val(countryData.iso2);
            $('#carrierCode').val(countryData.dialCode);

            if ($.trim($(this).val()) !== '')
            {
                //Invalid Number Validation - Add
                if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                {
                    // alert('invalid');
                    $('#tel-error').addClass('error').html("{{ __('Please enter a valid International Phone Number.') }}");
                    hasPhoneError = true;
                    enableDisableButton();
                    $('#phone-error').hide();
                }
                else
                {
                    $('#tel-error').html('');

                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: SITE_URL+"/register/duplicate-phone-number-check",
                        dataType: "json",
                        cache: false,
                        data: {
                            'phone': $.trim($(this).val()),
                            'carrierCode': $.trim(countryData.dialCode),
                        }
                    })
                    .done(function(response)
                    {
                        if (response.status == true)
                        {
                            $('#tel-error').html('');
                            $('#phone-error').show();

                            $('#phone-error').addClass('error').html(response.fail);
                            hasPhoneError = true;
                            enableDisableButton();
                        }
                        else if (response.status == false)
                        {
                            $('#tel-error').show();
                            $('#phone-error').html('');

                            hasPhoneError = false;
                            enableDisableButton();
                        }
                    });
                }
            }
            else
            {
                $('#tel-error').html('');
                $('#phone-error').html('');
                hasPhoneError = false;
                enableDisableButton();
            }
        });
    });
/*
intlTelInput
 */


    // Validate phone via Ajax
    $(document).ready(function()
    {
        $("input[name=phone]").on('blur', function(e)
        {
            formattedPhone();


            if ($.trim($(this).val()) !== '')
            {
                if (!$(this).intlTelInput("isValidNumber") || !isValidPhoneNumber($.trim($(this).val())))
                {
                    // alert('invalid');
                    $('#tel-error').addClass('error').html("{{ __('Please enter a valid International Phone Number.') }}");
                    hasPhoneError = true;
                    enableDisableButton();
                    $('#phone-error').hide();
                }
                else
                {
                    var phone = $(this).val().replace(/-|\s/g,""); //replaces 'whitespaces', 'hyphens'
                    var phone = $(this).val().replace(/^0+/,"");  //replaces (leading zero - for BD phone number)

                    var pluginCarrierCode = $('#phone').intlTelInput('getSelectedCountryData').dialCode;
                    $.ajax({
                        headers:
                        {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: "POST",
                        url: SITE_URL+"/register/duplicate-phone-number-check",
                        dataType: "json",
                        data: {
                            'phone': phone,
                            'carrierCode': pluginCarrierCode,
                        }
                    })
                    .done(function(response)
                    {
                        if (response.status == true)
                        {
                            if(phone.length == 0)
                            {
                                $('#phone-error').html('');
                            }
                            else{
                                $('#phone-error').addClass('error').html(response.fail);
                                hasPhoneError = true;
                                enableDisableButton();
                            }
                        }
                        else if (response.status == false)
                        {
                            $('#phone-error').html('');
                            hasPhoneError = false;
                            enableDisableButton();
                        }
                    });
                    $('#tel-error').html('');
                    $('#phone-error').show();
                    hasPhoneError = false;
                    enableDisableButton();
                }
            }
            else
            {
                $('#tel-error').html('');
                $('#phone-error').html('');
                hasPhoneError = false;
                enableDisableButton();
            }
        });
    });

    function formattedPhone()
    {
        if ($('#phone').val != '')
        {
            var p = $('#phone').intlTelInput("getNumber").replace(/-|\s/g,"");
            $("#formattedPhone").val(p);
        }
    }

    // Validate Emal via Ajax
    $(document).ready(function()
    {
        $("#email").on('input', function(e)
        {
            var email = $('#email').val();
            $.ajax({
                headers:
                {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: SITE_URL+"/user-registration-check-email",
                dataType: "json",
                data: {
                    'email': email,
                }
            })
            .done(function(response)
            {
                // console.log(response);
                if (response.status == true)
                {
                    emptyEmail();
                    if (validateEmail(email))
                    {
                        $('#email_error').addClass('error').html(response.fail);
                        $('#email_ok').html('');
                        hasEmailError = true;
                        enableDisableButton();
                    } else {
                        $('#email_error').html('');
                    }
                }
                else if (response.status == false)
                {
                    emptyEmail();
                    if (validateEmail(email))
                    {
                        $('#email_error').html('');
                    } else {
                        $('#email_ok').html('');
                    }
                    hasEmailError = false;
                    enableDisableButton();
                }

                /**
                 * [validateEmail description]
                 * @param  {null} email [regular expression for email pattern]
                 * @return {null}
                 */
                function validateEmail(email) {
                  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                  return re.test(email);
                }

                /**
                 * [checks whether email value is empty or not]
                 * @return {void}
                 */
                function emptyEmail() {
                    if( email.length === 0 )
                    {
                        $('#email_error').html('');
                        $('#email_ok').html('');
                    }
                }
            });
        });
    });

    /**
    * [check submit button should be disabled or not]
    * @return {void}
    */
    function enableDisableButton()
    {
        if (!hasPhoneError && !hasEmailError) {
            $('form').find("button[type='submit']").prop('disabled',false);
        } else {
            $('form').find("button[type='submit']").prop('disabled',true);
        }
    }
</script>
@endsection
