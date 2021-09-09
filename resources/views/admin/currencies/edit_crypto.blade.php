@extends('admin.layouts.master')
@section('title', 'Add Currency')

@section('head_style')
    <!-- bootstrap-toggle -->
    <link rel="stylesheet" href="{{ asset('public/backend/bootstrap-toggle/css/bootstrap-toggle.min.css') }}">
@endsection

@section('page_content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Edit Crypto Currency</h3>
                </div>

                <form action="{{ url(\Config::get('adminPrefix').'/settings/edit_currency/'.$result->id) }}" method="post" class="form-horizontal" enctype="multipart/form-data" id="add_currency_form">
                    {!! csrf_field() !!}

                    <!-- box-body -->
                    <div class="box-body">
                        <!-- Type -->
                        <input type="hidden" value="{{ $result->id }}" name="id" id="id">
                        <input type="hidden" value="{{ $result->allow_address_creation }}" name="allow_address_creation" id="allow_address_creation">
                        <input type="hidden" value="{{ $result->type }}" name="type" id="type">

                        <div class="form-group" id="crypto-provider-div">
                            <label class="col-sm-3 control-label" for="inputEmail3">Coin/Network Provider</label>
                            <div class="col-sm-6">
                              <select class="form-control provider" name="provider" id="provider">
                                <option value='blockio'>BlockIO</option>
                              </select>
                            </div>
                        </div>

                        <div class="form-group" id="crypto-networks-div">
                            <label class="col-sm-3 control-label" for="inputEmail3">Coin/Network</label>
                            <div class="col-sm-6">
                               <input type="text" name="network" class="form-control" value="{{ $result->code }}" placeholder="Network" id="network" readonly="readonly" aria-required="true" aria-invalid="false">
                            </div>
                        </div>

                        <div class="form-group" id="name-div">
                            <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-6">
                                <input type="text" name="name" class="form-control" value="{{ $result->name }}" placeholder="Name" id="name" readonly="readonly" aria-required="true" aria-invalid="false" readonly>
                            </div>
                        </div>

                        <div class="form-group d-none" id="symbol-div">
                            <label for="inputEmail3" class="col-sm-3 control-label">Symbol</label>
                            <div class="col-sm-6">
                                <input type="text" name="symbol" class="form-control" value="{{ $result->symbol }}" placeholder="Symbol" id="symbol" readonly="readonly" aria-required="true" aria-invalid="false" readonly>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="form-group" id="logo-div">
                            <label for="inputEmail3" class="col-sm-3 control-label">Logo</label>
                            <div class="col-sm-4">
                              <input type="file" name="logo" class="form-control input-file-field" id="currency-logo">
                              <div class="clearfix"></div>
                              <small class="form-text text-muted"><strong>{{ allowedImageDimension(64,64) }}</strong></small>
                            </div>
                            @if (!empty($result->logo)) 
                                <div class="col-sm-2">
                                    <div class="pull-right setting-img">
                                        <img src='{{ url('public/uploads/currency_logos/'.$result->logo) }}' width="64" height="64" id="currency-demo-logo-preview">
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-2">
                                    <div class="pull-right setting-img">
                                        <img src='{{ url('public/user_dashboard/images/favicon.png') }}' width="64" height="64" id="currency-demo-logo-preview">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- API Key -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="api_key">API Key</label>
                            <div class="col-sm-6">
                                <input class="form-control api_key" name="api_key" type="text" placeholder="Please enter valid api key" value="{{ (json_decode($getCurrenciesSettings))->api_key }}" id="api_key"
                                onkeyup="this.value = this.value.replace(/\s/g, '')">
                                @if ($errors->has('api_key'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('api_key') }}</strong>
                                    </span>
                                @endif
                                <div class="clearfix"></div>
                                <small class="form-text text-muted"><strong>*Network/Crypto Currency is generated according to api key.</strong></small>
                                <div class="clearfix"></div>
                                <small class="form-text text-muted"><strong>*Updating API key will update corresponding crypto currency.</strong></small>
                            </div>
                        </div>

                        <!-- PIN -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="pin">PIN</label>
                            <div class="col-sm-6">
                                <input class="form-control pin" name="pin" type="text" placeholder="Please enter valid pin"
                                value="{{ (json_decode($getCurrenciesSettings))->pin }}" id="pin"
                                onkeyup="this.value = this.value.replace(/\s/g, '')">
                                @if ($errors->has('pin'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pin') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="address">Merchant Address</label>
                            <div class="col-sm-6">
                                <input class="form-control address" name="address" type="text" placeholder="Please enter valid merchant address"
                                value="{{ (json_decode($getCurrenciesSettings))->address }}" id="address"
                                onkeyup="this.value = this.value.replace(/\s/g, '')">
                                <span class="address-validation-error" style="color: red;font-weight: bold"></span>
                                @if ($errors->has('address'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Exchange fee -->
                        <div class="form-group" id="create-network-address-div">
                            <label class="col-sm-3 control-label" for="inputEmail3">Create Addresses</label>
                            <div class="col-sm-6">
                                <input type="checkbox" data-toggle="toggle" name="network_address" id="network-address">
                                <div class="clearfix"></div>
                                <small class="form-text text-muted"><strong>*If On, <span class="network-name"></span> wallet addresses will be created for all registered users.</strong></small>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="status">Status</label>
                            <div class="col-sm-6">
                                <select class="form-control status" name="status" id="status">
                                    <option value='Active' {{ $result->status == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value='Inactive' {{ $result->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <div class="clearfix"></div>
                                <small class="form-text text-muted"><strong>*Updating status will update corresponding crypto currency.</strong></small>
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <!-- box-footer -->
                        <div class="box-footer">
                            <a class="btn btn-theme-danger" href="{{ url(\Config::get('adminPrefix').'/settings/currency') }}" >Cancel</a>
                            <button type="submit" class="btn btn-theme pull-right" id="crypto-currency-settings-submit-btn">
                                <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="crypto-currency-settings-submit-btn-text">Submit</span>
                            </button>
                        </div>
                        <!-- /.box-footer -->
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('extra_body_scripts')
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/backend/bootstrap-toggle/js/bootstrap-toggle.min.js') }}" type="text/javascript"></script>
@include('common.read-file-on-change')
@include('common.restrict_number_to_eight_decimal')


<script type="text/javascript">


    $(document).on('change','#currency-logo', function() {
        let orginalSource = '{{ url('public/user_dashboard/images/favicon.png') }}';
        readFileOnChange(this, $('#currency-demo-logo-preview'), orginalSource);
    });

    var addressErrorFlag = false;
    function checkSubmitBtn()
    {
        if (!addressErrorFlag)
        {
            $('#crypto-currency-settings-submit-btn').attr("disabled", false);
        }
        else
        {
            $('#crypto-currency-settings-submit-btn').attr("disabled", true);
        }
    }

    function checkMerchantAddress()
    {
        var api_key = $('#api_key').val().trim();
        var pin = $('#pin').val().trim();
        var address = $('#address').val().trim();

        if (api_key.length > 0 && pin.length > 0 && address.length > 0)
        {
            $.ajax(
            {
                method: "GET",
                url: SITE_URL+"/"+ADMIN_PREFIX+"/settings/crypto-currencies-settings/check-merhant-network-address",
                dataType: "json",
                data:
                {
                    'api_key': api_key,
                    'pin': pin,
                    'address': address,
                },
            })
            .done(function(res)
            {
                if (res.status == 400)
                {
                    $('.address-validation-error').text(res.message);
                    addressErrorFlag = true;
                }
                else
                {
                    $('.address-validation-error').text('');
                    addressErrorFlag = false;

                    // Update existing network value (THIS IS NEEDED WHEN SWITCHING BETWEEN NORMAL & TESTNET)
                    // $('#network').val(res.network)
                }
                checkSubmitBtn();
            })
            .fail(function(err)
            {
                console.log(err);
            });
        }
        else
        {
            $('.address-validation-error').text('');
            addressErrorFlag = false;
            checkSubmitBtn();
        }
    }

    // Check Merchant Api Key
    $(document).on('blur', '#api_key', function ()
    {
        checkMerchantAddress();
    });

    // Check Merchant Pin
    $(document).on('blur', '#pin', function ()
    {
        checkMerchantAddress();
    });

    // Check Merchant Network Address
    $(document).on('blur', '#address', function ()
    {
        checkMerchantAddress();
    });

    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
    });

    $('#add_currency_form').validate({
        rules: {
            api_key:{
              required: true,
            },
            pin:{
              required: true,
            },
            address:{
              required: true,
            },
            rate: {
                required: true,
                number: true,
                min: 0.0001,
            },
            logo: {
                extension: "png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
          rate: {
            min: "Please enter values greater than 0.0001!"
          },
          logo: {
            extension: "Please select (png, jpg, jpeg, gif or bmp) file!"
          }
        },
        submitHandler: function(form)
        {
            $("#crypto-currency-settings-submit-btn").attr("disabled", true);
            $('#cancel-link').attr("disabled", true);
            $(".fa-spin").show();
            $("#crypto-currency-settings-submit-btn-text").text('Submitting...');
            $('#crypto-currency-settings-submit-btn').click(function (e) {
                e.preventDefault();
            });
            $('#cancel-link').click(function (e) {
                e.preventDefault();
            });
            form.submit();
        }
    });

</script>

@endpush
