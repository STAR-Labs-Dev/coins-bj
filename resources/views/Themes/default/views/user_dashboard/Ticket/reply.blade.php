@extends('user_dashboard.layouts.app')

@section('content')

    <section class="min-vh-100">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-4 col-xs-12 col-sm-12">
                    <div class="flash-container">
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3>@lang('message.dashboard.ticket.details.sidebar.header')</h3>
                        </div>

                        <div class="mb-4">
                            <div class="ticket-line mt-3">
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
                </div>

                <div class="col-md-8 col-xs-12 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>@lang('message.dashboard.ticket.details.form.title')</h3>
                        </div>

                        <div class="card-body">
                            @include('user_dashboard.layouts.common.alert')
                            <span id="alertDiv">
                                </span>

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
                                                    <i class="fas fa-cloud-upload-alt text-28"></i>
                                                </div>
                                                <div class="upload-text">
                                                    <span>{{ __('Choose a file') }}</span>
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
                                            <button class="btn btn-grad" id="ticket-reply">
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

                        <br>
                        <div class="">
                            <div class="reply-views">
                                <div class="reply-box">
                                    <div class="left">

                                        <div class="profile-id-pic left">
                                            @if(!empty($ticket->user->picture))
                                                <?php
                                                $userTicketAvatar = $ticket->user->picture;
                                                ?>
                                                <img src='{{url("public/user_dashboard/profile/$userTicketAvatar")}}'
                                                     class="rounded-circle" style="width:60px;">
                                            @else
                                                <img src="{{url('public/user_dashboard/images/avatar.jpg')}}" alt=""
                                                     class="rounded-circle" style="width:60px;">
                                            @endif
                                        </div>

                                        <div class="left">
                                            <h5 class="">{{$ticket->user->first_name.' '.$ticket->user->last_name}}</h5>
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div class="update-time">{{ dateFormat($ticket->created_at) }}</div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="reply-details">
                                    <p>{!! $ticket->message !!}</p>
                                </div>
                            </div>
                        </div>
                        <br>

                        @if( $ticket_replies->count() > 0 )
                            @foreach($ticket_replies as $result)
                                @if($result->user_type == 'user' )
                                    <div class="">
                                        <div class="reply-views">
                                            <div class="reply-box">
                                                <div class="left">

                                                    <div class="profile-id-pic left">
                                                        @if(!empty($result->user->picture))
                                                            <?php
                                                            $userAvatar = $result->user->picture;
                                                            ?>
                                                            <img src='{{url("public/user_dashboard/profile/$userAvatar")}}'
                                                                 class="rounded-circle" style="width:60px;">
                                                        @else
                                                            <img src="{{url('public/user_dashboard/images/avatar.jpg')}}"
                                                                 alt="" class="rounded-circle" style="width:60px;">
                                                        @endif
                                                    </div>

                                                    <div class="left">
                                                        <h5 class="">{{$result->user->first_name.' '.$result->user->last_name}}</h5>
                                                    </div>

                                                </div>
                                                <div class="right">
                                                    <div class="update-time">{{ dateFormat($result->created_at) }}</div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="reply-details">

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
                                    </div>
                                    <br>
                                @else

                                    <div class="">
                                        <div class="reply-views">
                                            <div class="reply-box">
                                                <div class="left">
                                                    <div class="profile-id-pic left">

                                                        @if(!empty($result->admin->picture))
                                                            <?php
                                                            $adminAvatar = $result->admin->picture;
                                                            ?>
                                                            <img src='{{url("public/uploads/userPic/$adminAvatar")}}'
                                                                 class="rounded-circle" style="width:60px;">
                                                        @else
                                                            <img src="{{url('public/user_dashboard/images/avatar.jpg')}}"
                                                                 alt="" class="rounded-circle" style="width:60px;">
                                                        @endif

                                                    </div>
                                                    <div class="left">
                                                        <h5 class="">{{ $result->admin->first_name.' '.$result->admin->last_name }}</h5>
                                                    </div>
                                                </div>
                                                <div class="right">
                                                    <div class="update-time">{{ dateFormat($result->created_at) }}</div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="reply-details">

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
    </section>
    <!--End Section-->
@endsection

@section('js')

<script src="{{ theme_asset('public/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/additional-methods.min.js') }}" type="text/javascript"></script>

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
        extension: "{{ __('Please select (docx, rtf, doc, pdf, png, jpg, jpeg, gif or bmp) file!') }}"
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