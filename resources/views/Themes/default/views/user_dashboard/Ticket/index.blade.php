@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center ">
            <div class="col-md-12 col-xs-12">
                @include('user_dashboard.layouts.common.alert')
                <div class="right mb10">
                    <a href="{{url('/ticket/add')}}" class="btn btn-grad"><i class="fa fa-ticket"></i>&nbsp; @lang('message.dashboard.button.new-ticket')</a>
                </div>

                <div class="clearfix"></div>
                <div class="card mt-2">
                    <div class="card-header">
                        <h3>@lang('message.dashboard.ticket.title')</h3>
                    </div>

                    <div>
                        <div class="table-responsive">
                            @if($tickets->count() > 0)

                            <table class="table recent_activity">
                                <thead>
                                    <tr>
                                        <td class="text-left pl-5" width="16%"><strong>@lang('message.dashboard.ticket.ticket-no')</strong></td>
                                        <td class="text-left"><strong>@lang('message.dashboard.ticket.subject')</strong></td>
                                        <td width="15%"><strong>@lang('message.dashboard.ticket.status')</strong></td>
                                        <td width="6%"><strong>@lang('message.dashboard.ticket.priority')</strong></td>
                                        <td width="15%"><strong>@lang('message.dashboard.ticket.date')</strong></td>
                                        <td class="pr-5 text-right" width="6%"><strong>@lang('message.dashboard.ticket.action')</strong></td>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($tickets as $result)
                                        <tr>
                                            <td class="text-left pl-5">{{ $result->code}} </td>
                                            <td class="text-left">
                                                <h4>
                                                    <a class="text-dark" href="{{ url('ticket/reply').'/'.$result->id }}">{{ $result->subject}}</a>
                                                </h4>
                                            </td>

                                            @if($result->ticket_status->name =='Closed')
                                                <td><span class="badge badge-danger">{{ $result->ticket_status->name }}</span></td>
                                            @elseif($result->ticket_status->name =='Hold')
                                                <td><span class="badge badge-warning">{{ $result->ticket_status->name }}</span></td>
                                            @elseif($result->ticket_status->name =='In Progress')
                                                <td><span class="badge badge-primary">{{ $result->ticket_status->name }}</span></td>
                                            @elseif($result->ticket_status->name =='Open')
                                                <td><span class="badge badge-success">{{ $result->ticket_status->name }}</span></td>
                                            @endif

                                            <td>{{ $result->priority }} </td>
                                            <td>{{ dateFormat($result->created_at) }} </td>
                                            <td class="pr-5 text-right">
                                                <a href="{{ url('ticket/reply').'/'.$result->id }}" class="btn btn-action btn-sm"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                                <h5 class="text-center p-5">@lang('message.dashboard.ticket.no-ticket')</h5>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer">
                        {{ $tickets->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection