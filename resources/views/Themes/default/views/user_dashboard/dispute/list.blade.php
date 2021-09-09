@extends('user_dashboard.layouts.app')
@section('content')
<!--Start Section-->
<section class="min-vh-100">
	<div class="container mt-5">
		<div class="row justify-content-center">
			<div class="col-md-12 col-xs-12">
				<div class="card">
					<div class="card-header">
						<h3 class=""> @lang('message.dashboard.dispute.dispute')</h3>
					</div>

					<div class="card-body">
						@if($list->count() > 0)
							@foreach($list as $result)
								<div class="card-body-custom">
									<div class="row">
										<div class="col-md-10">
											<div class="mb-2">
												<h3 class="text-dark"> {{$result->title}} </h3>
											</div>
											<p>@lang('message.dashboard.dispute.dispute-id'): {{ isset($result->code) ? $result->code :"-" }}</p>
											<p>@lang('message.dashboard.dispute.transaction-id'): {{ isset($result->transaction) ? $result->transaction->uuid :"-" }}</p>

											@if(Auth::user()->id != $result->claimant_id)
												<p>
													@lang('message.dashboard.dispute.claimant') :
													{{ $result->claimant->first_name .' '.$result->claimant->last_name}}
												</p>
											@endif

											@if(Auth::user()->id != $result->defendant_id)
												<p>
													@lang('message.dashboard.dispute.defendant') :
													{{ $result->defendant->first_name .' '.$result->defendant->last_name }}
												</p>
											@endif

											<p>
												@lang('message.dashboard.dispute.created-at') : {{ dateFormat($result->created_at) }} 
											</p>
											<p>
												@lang('message.dashboard.dispute.status') :

												@if($result->status =='Open')
													<span class="badge badge-primary">@lang('message.dashboard.dispute.status-type.open')</span>
												@elseif($result->status =='Solve')
													<span class="badge badge-danger">@lang('message.dashboard.dispute.status-type.solved')</span>
												@elseif($result->status =='Close')
													<span class="badge badge-danger">@lang('message.dashboard.dispute.status-type.closed')</span>
												@endif

											</p>
										</div>

										<div class="col-md-2">
											<p class="text-right">
												<a href='{{url("dispute/discussion/$result->id") }}' class="btn btn-grad pt-1 pb-1">
												@lang('message.dashboard.button.details')
												</a>
											</p>
										</div>
									</div>
								</div>
								
							@endforeach
						@else
							<h5 class="text-center p-5">@lang('message.dashboard.dispute.no-dispute')</h5>
							
						@endif
					</div>

					<div class="card-footer">
						{{ $list->links('vendor.pagination.bootstrap-4') }}
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--End Section-->
@endsection