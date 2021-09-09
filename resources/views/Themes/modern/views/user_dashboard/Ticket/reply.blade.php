@extends('user_dashboard.layouts.app')

@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="page-title">{{ __('Tickets') }}</h3>
                </div>

                <div>
                    <a href="{{ url('/ticket/add') }}" class="btn btn-primary px-4 py-2">
                       <i class="fa fa-plus"></i> {{ __('New ticket') }}
                    </a>
                </div>
            </div>

            <!-- Page title end-->

            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/tickets') }}">
                        <div class="mr-4 pb-3">
                            <p class="text-16  text-gray-500">{{ __('Tickets list') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-4">
                <h3 class="sub-title">@lang('message.dashboard.ticket.details.form.title')</h3>
                <p class="text-gray-500 text-16">{{ __('Open a new ticket.') }}</p>
            </div>

            <div class="row mt-2">
                <div class="col-md-4 col-xl-3">
                    <!-- Sub title start -->
                    <div class="mt-4 p-4 bg-secondary rounded shadow">
                        <div class="mb-4">
                            <div class="ticket-line">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.ticket-id')</div>
                                <div class="generalcolor-txt">{{ $ticket->code }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.subject')</div>
                                <div class="generalcolor-txt">{{ $ticket->subject }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.date')</div>
                                <div class="generalcolor-txt">{{ dateFormat($ticket->created_at) }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.priority')</div>
                                <div class="generalcolor-txt">{{ $ticket->priority }}</div>
                            </div>

                            <div class="ticket-line mt-3">
                                <div class="titlecolor-txt">@lang('message.dashboard.ticket.details.sidebar.status')</div>
                                <div class="generalcolor-txt">
                                    @if($ticket->ticket_status->name =='Closed')
                                        <span class="badge badge-danger">{{ $ticket->ticket_status->name }}</span>
                                    @elseif($ticket->ticket_status->name =='Hold')
                                        <span class="badge badge-warning">{{ $ticket->ticket_status->name }}</span>
                                    @elseif($ticket->ticket_status->name =='In Progress')
                                        <span class="badge badge-primary">{{ $ticket->ticket_status->name }}</span>
                                    @elseif($ticket->ticket_status->name =='Open')
                                        <span class="badge badge-success">{{ $ticket->ticket_status->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="ticket-btn ticket-line mt-3 d-none">
                                <select class="form-control" name="status" id="status">
                                    @foreach($ticket_status as $val)
                                        <option value="{{$val->id}}" <?= ($ticket->ticket_status->id == $val->id) ? 'selected' : ''  ?> >{{$val->name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="ticket_id" value="{{ $ticket->id }}">
                            </div>
                        </div>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-md-8 col-xl-9">
                    <div class="bg-secondary rounded m-0 mt-4 shadow">
                        <div>
                            @include('user_dashboard.layouts.common.alert')
                            <span id="alertDiv">
                            </span>

                            <div class="p-35">
                                @if($ticket->ticket_status->name != 'Closed')
                                    <form action="{{url('ticket/reply_store')}}" id="reply" method="post"
                                        enctype="multipart/form-data">
                                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label>@lang('message.dashboard.ticket.details.form.message')
                                                <span class="text-danger">*</span>
                                            </label>

                                            <textarea name="description" id="description" class="form-control"></textarea>
                                            @if($errors->has('description'))
                                                <span class="error">
                                                {{ $errors->first('description') }}
                                            </span>
                                            @endif
                                            <p id="description-error" class="text-danger"></p>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="file" name="file" id="file" hidden/>
                                                    <label class="upload-file" for="file">
                                                        <div class="upload-icon">
                                                            <i class="fa fa-cloud-upload text-28"></i>
                                                        </div>
                                                        <div class="upload-text">
                                                            {{-- <span>{{ __('Choose a file') }}</span> --}}
                                                            <input type="file" name="file">
                                                        </div>
                                                    </label>
                                                    <p id="file-chosen"></p>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group d-none">
                                                    <label class="control-label" for="exampleInputFile">@lang('message.dashboard.ticket.details.sidebar.status')</label>
                                                    <select class="form-control" name="status_id" id="status_id">
                                                        @foreach($ticket_status as $val)
                                                            <option value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="text-right">
                                                    <button class="btn btn-primary px-4 py-2" id="ticket-reply">
                                                        <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i>
                                                        <span id="ticket-reply-text" style="font-weight: bolder;">
                                                            @lang('message.dashboard.button.submit')
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                            <hr class="mt-0">

                        <div class="px-4">
                            <div class="d-flex">
                                <div class="pr-2">
                                    @if(!empty($ticket->user->picture))
                                        <?php
                                        $userTicketAvatar = $ticket->user->picture;
                                        ?>
                                        <img src='{{url("public/user_dashboard/profile/$userTicketAvatar")}}'
                                                class="rounded-circle" style="width:50px;">
                                    @else
                                        <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" alt=""
                                                class="rounded-circle" style="width:50px;">
                                    @endif
                                </div>

                                <div>
                                    <p class="font-weight-600">{{$ticket->user->first_name.' '.$ticket->user->last_name}}</p>
                                    <p class="text-12 text-gray-500">{{ dateFormat($ticket->created_at) }}</p>
                                </div>
                            </div>

                            <div class="pl-5 mt-3 ml-2">
                                <p>{{ $ticket->message }}</p>
                            </div>
                        </div>
                        <br>
                            @if( $ticket_replies->count() > 0 )
                                @foreach($ticket_replies as $result)
                                    @if($result->user_type == 'user' )
                                        <div class="px-4">
                                            <div class="d-flex">
                                                <div class="pr-2">
                                                    @if(!empty($result->user->picture))
                                                        <?php
                                                        $userAvatar = $result->user->picture;
                                                        ?>
                                                        <img src='{{url("public/user_dashboard/profile/$userAvatar")}}'
                                                                class="rounded-circle" style="width:50px;">
                                                    @else
                                                        <img src="{{url('public/user_dashboard/images/avatar.jpg')}}"
                                                                alt="" class="rounded-circle" style="width:50px;">
                                                    @endif
                                                </div>

                                                <div>
                                                    <p class="font-weight-600">{{ $result->user->first_name.' '.$result->user->last_name }}</p>
                                                    <p class="text-12 text-gray-500">{{ dateFormat($result->created_at) }}</p>
                                                </div>
                                            </div>

                                            <div class="pl-5 mt-3 ml-2">
                                                <p>{!! $result->message !!}</p>
                                                    @if($result->file)
                                                        ----------------<br>
                                                        <h5>
                                                            <a class="text-info"
                                                                href="{{url('public/uploads/ticketFile').'/'.$result->file->filename}}">
                                                                <i class="fa fa-download"></i> {{$result->file->originalname}}
                                                            </a>
                                                        </h5>
                                                    @endif
                                            </div>
                                        </div>
                                        <br>
                                    @else

                                        <div class="px-4">
                                            <div class="d-flex">
                                                <div class="pr-2">
                                                    @if(!empty($result->admin->picture))
                                                        <?php
                                                        $adminAvatar = $result->admin->picture;
                                                        ?>
                                                        <img src='{{url("public/uploads/userPic/$adminAvatar")}}' style="width:50px;">
                                                    @else
                                                        <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" alt="" style="width:50px;">
                                                    @endif
                                                </div>

                                                <div>
                                                    <p class="font-weight-600">{{ $result->admin->first_name.' '.$result->admin->last_name }}</p>
                                                    <p class="text-12 text-gray-500">{{ dateFormat($result->created_at) }}</p>
                                                </div>
                                            </div>

                                            <div class="pl-5 mt-3 ml-2">
                                                <p>{!! $result->message !!}</p>
                                                @if($result->file)
                                                    ----------------<br>
                                                    <h5>
                                                        <a class="text-info"
                                                            href="{{url('public/uploads/ticketFile').'/'.$result->file->filename}}">
                                                            <i class="fa fa-download"></i> {{$result->file->originalname}}
                                                        </a>
                                                    </h5>
                                                @endif
                                            </div>
                                        </div>
                                        <br>
                                    @endif
                                @endforeach
                            @endif
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
<script src="{{theme_asset('public/js/additional-methods.min.js')}}" type="text/javascript"></script>

<script>
const actualBtn = document.getElementById('file');

const fileChosen = document.getElementById('file-chosen');

actualBtn.addEventListener('change', function(){
  fileChosen.textContent = this.files[0].name
})



jQuery.extend(jQuery.validator.messages, {
    required: "{{ __('This field is required.') }}",
})

$('#reply').validate({
    rules: {
        description: {
            required: true,
        },
        file: {
            extension: "docx|rtf|doc|pdf|png|jpg|jpeg|gif|bmp",
        },
    },
    messages: {
      file: {
        extension: "{{ __("Please select (docx, rtf, doc, pdf, png, jpg, jpeg, gif or bmp) file!") }}"
      },
    },
    submitHandler: function(form)
    {
        $("#ticket-reply").attr("disabled", true).click(function (e)
        {
            e.preventDefault();
        });
        $(".spinner").show();
        $("#ticket-reply-text").text("{{ __('Submitting...') }}");
        form.submit();
    }
});

$("#status").on('change', function () {
    var status_id = $(this).val();
    var ticket_id = $("#ticket_id").val();

    $.ajax({
        method: "POST",
        url: SITE_URL + "/ticket/change_reply_status",
        data: {status_id: status_id, ticket_id: ticket_id}
    })
    .done(function (reply) {
        message = 'Ticket reply status ' + reply.status + ' successfully done.';
        var messageBox = '<div class="alert alert-success" role="alert">' + message + '</div><br>';
        $("#alertDiv").html(messageBox);
        setTimeout(function () {
            location.reload()
        }, 2000);
    });
});

</script>

@endsection
