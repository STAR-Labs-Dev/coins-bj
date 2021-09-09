@extends('user_dashboard.layouts.app')

@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                <div class="clearfix"></div>
                @include('user_dashboard.layouts.common.alert')
                <form action="{{url('ticket/store')}}" method="post" enctype="multipart/form-data" accept-charset="utf-8" id="ticket">
                    <div class="card">
                        <div class="card-header">
                            <h3>@lang('message.dashboard.ticket.add.title')</h3>
                        </div>

                        <div class="card-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

                            <div class="form-group">
                                <label for="subject">@lang('message.dashboard.ticket.add.name')<span class="text-danger">*</span></label>
                                <input class="form-control" name="subject" id="subject" type="text"
                                        value="{{old('subject')}}">
                                @if($errors->has('subject'))
                                    <span class="help-block">
                                <strong class="text-danger">{{ $errors->first('subject') }}</strong>
                            </span>
                                @endif
                            </div>


                            <div class="form-group">
                                <label for="description">@lang('message.dashboard.ticket.add.message')<span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control"
                                            id="description">{{old('description')}}</textarea>
                                @if($errors->has('description'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                                <p id="description-error" class="text-danger"></p>
                            </div>

                            <div class="form-group">
                                <label>@lang('message.dashboard.ticket.add.priority')</label>
                                <select class="form-control" name="priority" id="priority">
                                    <option value="Low">{{ __('Low') }}</option>
                                    <option value="Normal">{{ __('Normal') }}</option>
                                    <option value="High">{{ __('High') }}</option>
                                </select>
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-grad col-12" id="ticket_create">
                                    <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="ticket_create_text">@lang('message.dashboard.button.submit')</span>
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

<script>

jQuery.extend(jQuery.validator.messages, {
    required: "{{ __('This field is required.') }}",
})

$('#ticket').validate({
    rules: {
        subject: {
            required: true
        },
        description: {
            required: true
        }
    },
    submitHandler: function(form)
    {
        $("#ticket_create").attr("disabled", true);
        $(".spinner").show();
        $("#ticket_create_text").text("{{ __('Submitting...') }}");
        form.submit();
    }
});

</script>

@endsection
