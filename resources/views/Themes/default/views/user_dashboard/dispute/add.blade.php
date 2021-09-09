@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xs-12">
                @include('user_dashboard.layouts.common.alert')
                <form method="POST" action="{{url('dispute/open')}}" id="dispute-add-form" accept-charset='UTF-8'>
                    <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
                    <input type="hidden" name="transaction_id" value="{{$transaction->id}}">
                    <input type="hidden" name="claimant_id" value="{{$transaction->user_id}}">
                    <input type="hidden" name="defendant_id" value="{{$transaction->end_user_id}}">

                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="exampleInputEmail1">@lang('message.dashboard.dispute.title')</label>
                                <input type="text" class="form-control" value="{{old('title')}}" name="title" id="title" placeholder="Enter title">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1">@lang('message.dashboard.dispute.discussion.sidebar.reason')</label>
                                <select class="form-control" name="reason_id" id="reason_id">
                                    @foreach ($reasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">@lang('message.dashboard.dispute.description')</label>
                                    <textarea class="form-control" rows="5" name="description" id="description" placeholder="Enter description">{{old('description')}}</textarea>
                                </textarea>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-grad col-12" id="dispute-add-submit-btn">
                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="dispute-add-submit-btn-txt">@lang('message.dashboard.button.submit')</span>
                                </button>
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
<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>
<script>

jQuery.extend(jQuery.validator.messages, {
    required: "{{ __('This field is required.') }}",
})

$('#dispute-add-form').validate({
    rules: {
        title: {
            required: true,
        },
        description: {
            required: true,
        },
    },
    submitHandler: function(form)
    {
        $("#dispute-add-submit-btn").attr("disabled", true);
        $(".fa-spin").show();
        $("#dispute-add-submit-btn-txt").text("{{ __('Submitting...') }}");
        form.submit();
    }
});
</script>
@endsection
