@extends('admin.layouts.master')
@section('title', 'Edit Address Verification')

@section('page_content')
	<div class="box box-default">
		<div class="box-body">
			<div class="d-flex justify-content-between">
				<div>
					<div class="top-bar-title padding-bottom pull-left">Address Verification Details</div>
				</div>

				<div>
					@if ($documentVerification->status)
						<h4 class="text-left">Status : @if ($documentVerification->status == 'approved')<span class="text-green">Approved</span>@endif
						@if ($documentVerification->status == 'pending')<span class="text-blue">Pending</span>@endif
						@if ($documentVerification->status == 'rejected')<span class="text-red">Rejected</span>@endif</h4>
					@endif
				</div>
			</div>
		</div>
	</div>

    <section class="min-vh-100">
        <div class="my-30">
            <div class="row">
                <form action="{{ url(\Config::get('adminPrefix').'/address-proofs/update') }}" class="form-horizontal" id="deposit_form" method="POST">
                    {{ csrf_field() }}
                    <!-- Page title start -->
                    <div class="col-md-8 col-xl-9">
                        <div class="box">
                            <div class="box-body">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="mt-4 p-4 bg-secondary rounded shadow">
                                            <input type="hidden" value="{{ $documentVerification->id }}" name="id" id="id">
                                            <input type="hidden" value="{{ $documentVerification->user_id }}" name="user_id" id="user_id">
                                            <input type="hidden" value="{{ $documentVerification->verification_type }}" name="verification_type" id="verification_type">

                                            <div class="panel panel-default">
                                                <div class="panel-body">

                                                    @if ($documentVerification->user_id)
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-3" for="user">User</label>
                                                            <input type="hidden" class="form-control" name="user" value="{{ isset($documentVerification->user) ? $documentVerification->user->first_name.' '.$documentVerification->user->last_name :"-" }}">
                                                            <div class="col-sm-9">
                                                            <p class="form-control-static">{{ isset($documentVerification->user) ? $documentVerification->user->first_name.' '.$documentVerification->user->last_name :"-" }}</p>
                                                            </div>
                                                        </div>
                                                    @endif


                                                    @if ($documentVerification->created_at)
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-3" for="created_at">Date</label>
                                                            <input type="hidden" class="form-control" name="created_at" value="{{ $documentVerification->created_at }}">
                                                            <div class="col-sm-9">
                                                            <p class="form-control-static">{{ dateFormat($documentVerification->created_at) }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($documentVerification->status)
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-3" for="status">Change Status</label>
                                                            <div class="col-sm-9">
                                                                <select class="form-control select2" name="status" style="width: 60%;">
                                                                    <option value="approved" {{ $documentVerification->status ==  'approved'? 'selected':"" }}>Approved</option>
                                                                    <option value="pending"  {{ $documentVerification->status == 'pending' ? 'selected':"" }}>Pending</option>
                                                                    <option value="rejected"  {{ $documentVerification->status == 'rejected' ? 'selected':"" }}>Rejected</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-3"></div>
                                                                <div class="col-md-2"><a id="cancel_anchor" class="btn btn-theme-danger pull-left" href="{{ url(\Config::get('adminPrefix').'/address-proofs') }}">Cancel</a></div>
                                                                <div class="col-md-4">
                                                                    <button type="submit" class="btn btn-theme pull-right" id="deposits_edit">
                                                                        <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span id="deposits_edit_text">Update</span>
                                                                    </button>
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
                        </div>
                    </div>
                    <div class="col-md-4 col-xl-3">
                        <div class="box">
                            <div class="box-body">
                                <div class="panel">
                                    <div class="panel-body">
                                        <div class="mt-4 p-4 bg-secondary rounded shadow">
                                            @if ($documentVerification->file)
	                                            <div>

	                                            	<input type="hidden" class="form-control" name="address_file" value="{{ $documentVerification->file->filename }}">
	                                                <ul style="list-style-type: none;">
	                                                	<h4 style="text-decoration: underline;">Address Proof</h4>
													    <li> {{ $documentVerification->file->filename }}
															<a class="text-info pull-right" href="{{ url('public/uploads/user-documents/address-proof-files').'/'.$documentVerification->file->filename }}">
																<i class="fa fa-download"></i>
			                                                </a>
													    </li>
													</ul>
												</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('extra_body_scripts')
<script type="text/javascript">

	$(".select2").select2({});

	// disabling submit and cancel button after form submit
	$(document).ready(function()
	{
	  $('form').submit(function()
	  {
	     	$("#deposits_edit").attr("disabled", true);

	     	$('#cancel_anchor').attr("disabled","disabled");

            $(".fa-spin").show();

            $("#deposits_edit_text").text('Updating...');

            // Click False
			$('#deposits_edit').click(false);
			$('#cancel_anchor').click(false);
	  });
	});
</script>
@endpush
