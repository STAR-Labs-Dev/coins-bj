@extends('user_dashboard.layouts.app')

@section('content')
    <!--Start Section-->
    <section class="min-vh-100">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12">
                    @include('user_dashboard.layouts.common.alert')

                    <div class="right mb10">
                        <a href="{{url('/merchant/add')}}" class="btn btn-grad"><i class="fa fa-user"></i>&nbsp;
                            @lang('message.dashboard.button.new-merchant')</a>
                    </div>
                    <div class="clearfix"></div>

                    <div class="card mt-2">
                        <div class="card-header">
                            <div class="chart-list float-left">
                                <ul>
                                    <li>
                                        <a href="{{url('/merchants')}}">@lang('message.dashboard.merchant.menu.merchant')</a>
                                    </li>

                                    <li class="active">
                                        <a href="#">@lang('message.dashboard.merchant.menu.payment')</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <div class="table-responsive">
                                @if($merchant_payments->count() > 0)
                                    <table class="table recent_activity" id="merchant">
                                        <thead>
                                            <tr>
                                                <td class="pl-5">
                                                    <strong>@lang('message.dashboard.merchant.payment.created-at')</strong>
                                                </td>

                                                <td>
                                                    <strong>@lang('message.dashboard.merchant.payment.merchant')</strong>
                                                </td>

                                                <td>
                                                    <strong>@lang('message.dashboard.merchant.payment.method')</strong>
                                                </td>

                                                <td class="text-center">
                                                    <strong>@lang('message.dashboard.merchant.payment.order-no')</strong>
                                                </td>

                                                <td class="text-center">
                                                    <strong>@lang('message.dashboard.merchant.payment.amount')</strong>
                                                </td>

                                                <td class="text-center">
                                                    <strong>@lang('message.dashboard.merchant.payment.fee')</strong>
                                                </td>

                                                <td class="text-center">
                                                    <strong>@lang('message.dashboard.merchant.payment.total')</strong>
                                                </td>

                                                <td class="text-center">
                                                    <strong>@lang('message.dashboard.merchant.payment.currency')</strong>
                                                </td>

                                                <td class="text-right pr-5">
                                                    <strong>@lang('message.dashboard.merchant.payment.status')</strong>
                                                </td>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            @foreach($merchant_payments as $result)
                                                <tr>
                                                    <td class="pl-5">
                                                        <h3 class="text-left">{{ $result->created_at->format('jS F') }}</h3>
                                                        <p class="text-left">{{ $result->created_at->format('Y') }}</p>
                                                    </td>



                                                    <td>
                                                        <h4>{{ $result->merchant->business_name }}</h4>
                                                    </td>

                                                    <td>{{ ($result->payment_method->name == "Mts") ? getCompanyName() : $result->payment_method->name }}</td>

                                                    <td class="text-center">{{ !empty($result->order_no) ? $result->order_no : "-" }}</td>

                                                    <td class="text-center">{{ formatNumber($result->amount)}}</td>

                                                    <td class="text-center">{{ (($result->charge_percentage + $result->charge_fixed) == 0) ? '-' : formatNumber($result->charge_percentage + $result->charge_fixed) }}</td>

                                                    <td class="text-center">
                                                       <h4>{{ formatNumber($result->total) }}</h4>
                                                    </td>

                                                    <td class="text-center">{{ $result->currency->code}}</td>

                                                    @if($result->status == 'Pending')
                                                        <td class="pr-5 text-right">
                                                            <span class="badge badge-primary">@lang('message.dashboard.merchant.payment.pending')</span>
                                                        </td>
                                                    @elseif($result->status == 'Success')
                                                        <td class="pr-5 text-right">
                                                            <span class="badge badge-success">@lang('message.dashboard.merchant.payment.success')</span>
                                                        </td>
                                                    @elseif($result->status == 'Blocked')
                                                        <td class="pr-5 text-right">
                                                            <span class="badge badge-danger">@lang('message.dashboard.merchant.payment.block')</span>
                                                        </td>
                                                    @elseif($result->status == 'Refund')
                                                        <td class="pr-5 text-right">
                                                            <span class="badge badge-warning">@lang('message.dashboard.transaction.refund')</span>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h5 class="text-center p-5">
                                        @lang('message.dashboard.merchant.table.not-found')
                                    </h5>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer">
                            {{ $merchant_payments->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End Section-->
@endsection
