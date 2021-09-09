@extends('admin.layouts.master')
@section('title', 'General Settings')

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/tagify/tagify.css') }}">
@endsection

@section('page_content')

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.settings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Admin Security Form</h3>
                </div>

                <form action="{{ url(\Config::get('adminPrefix').'/settings/admin-security-settings') }}" method="post" class="form-horizontal" id="">
                    {!! csrf_field() !!}
                    <div class="box-body">

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Admin URL Prefix</label>
                            <div class="col-sm-6">
                                <input type="text" name="admin_url_prefix" class="form-control" id="admin-url-prefix" value="{{ !empty($prefData['preference']['admin_url_prefix']) ? $prefData['preference']['admin_url_prefix'] : '' }}" placeholder="Admin URL Prefix">
                            </div>
                        </div>

                        {{-- next version 3.3 --}}
                        {{-- <div class="form-group">
                            <label class="col-sm-4 control-label" for="exampleFormControlInput1">Admin 2Fa Login</label>
                            <div class="col-sm-6">
                                <select class="form-control admin_2fa" name="admin_2fa" id="admin_2fa">
                                    <option value='Enabled' {{ $result['admin_2fa'] == 'Enabled' ? 'selected' : '' }}>Enabled</option>
                                    <option value='Disabled' {{ $result['admin_2fa'] == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="exampleFormControlInput1">Admin Panel IPaccess</label>
                            <div class="col-sm-6">
                                <select class="form-control admin_access_ip_setting" name="admin_access_ip_setting"
                                    id="admin_access_ip_setting">
                                    <option value='Enabled'
                                        {{ $result['admin_access_ip_setting'] == 'Enabled' ? 'selected' : '' }}>Enabled
                                    </option>
                                    <option value='Disabled'
                                        {{ $result['admin_access_ip_setting'] == 'Disabled' ? 'selected' : '' }}>Disabled
                                    </option>
                                </select>
                                <small class="form-text text-muted"><strong>*If enable, don't forget to put the IPs</strong></small>
                            </div>
                        </div>

						<div class="form-group">
                            <label class="col-sm-4 control-label" for="exampleFormControlInput1">Admin Access IPs</label>
                            <div class="col-sm-6">
                                <input name='admin_access_ips' value="{{ $adminAccessIPs }}" placeholder="Enter your site url IP address"> 
                                <small class="form-text text-muted"><strong>*Before enable, you must need to put your IPs</strong></small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-theme pull-right"
                            id="admin-security-settings-submit">
                            <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span
                                id="admin-security-settings-submit-text">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('extra_body_scripts')
    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('public/backend/tagify/tagify.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('public/backend/tagify/jQuery.tagify.min.js') }}" type="text/javascript"></script>
	
    <script type="text/javascript">
        $(window).on('load', function() {
            $(".admin_access_ip_setting, .admin_2fa").select2({});
        });

		var input = document.querySelector('input[name=admin_access_ips]');
		new Tagify(input)
    </script>
@endpush
