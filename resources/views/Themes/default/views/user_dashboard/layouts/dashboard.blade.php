@extends('user_dashboard.layouts.app')

@section('content')
<section class="padding-30 min-vh-100">
    <div class="container">
        <!-- for express api merchant payment success/error message-->
        @include('user_dashboard.layouts.common.alert')

        <div class="row mt-4">
            <div class="col-lg-8 col-xl-9 col-xs-12 col-sm-12 mb20 marginTopPlus">
                <div class="flash-container">
                </div>
                <div class="card shadow">
                    <div class="card-header">
                        <h3>@lang('message.dashboard.left-table.title')</h3>
                    </div>

                    <div>
                        <div class="table-responsive">
                            <table class="table table-striped  table-hover recent_activity">
                                <thead>
                                    <tr>
                                        <td width="20%" class="text-left pl-5">
                                            <strong>@lang('message.dashboard.left-table.date')</strong>
                                        </td>

                                        <td class="text-left">
                                            <strong>@lang('message.dashboard.left-table.description')</strong>
                                        </td>

                                        <td class="text-left">
                                            <strong>@lang('message.dashboard.left-table.status')</strong>
                                        </td>

                                        <td class="text-right pr-5">
                                            <strong>@lang('message.dashboard.left-table.amount')</strong>
                                        </td>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if($transactions->count()>0)
                                        @foreach($transactions as $key=>$transaction)
                                            <tr click="0" data-toggle="modal" data-target="#collapseRow{{$key}}" aria-expanded="false" aria-controls="collapseRow{{$key}}"
                                                class="show_area" trans-id="{{$transaction->id}}" id="{{$key}}">

                                                <!-- Created At -->
                                                <td class="text-left pl-5 date_td">
                                                    <h4 class="text-left">{{ $transaction->created_at->format('jS F') }}</h4>
                                                    <p class="text-left">{{ $transaction->created_at->format('Y') }}</p>
                                                </td>

                                                <!-- Transaction Type -->
                                                @if(empty($transaction->merchant_id))
                                                    @if(!empty($transaction->end_user_id))
                                                        <td class="text-left">
                                                            @if($transaction->transaction_type_id)
                                                                @if($transaction->transaction_type_id==Request_From)
                                                                    <h4>
                                                                        {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                    </h4>
                                                                    <p>@lang('Request Sent')</p>
                                                                @elseif($transaction->transaction_type_id==Request_To)
                                                                    <h4>
                                                                        {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                    </h4>
                                                                    <p>@lang('Request Received')</p>

                                                                @elseif($transaction->transaction_type_id == Transferred)
                                                                    <h4>
                                                                        {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                    </h4>
                                                                    <p>@lang('Transferred')</p>

                                                                @elseif($transaction->transaction_type_id == Received)
                                                                    <h4>
                                                                        {{ $transaction->end_user->first_name.' '.$transaction->end_user->last_name }}
                                                                    </h4>
                                                                    <p>@lang('Received')</p>
                                                                @else
                                                                    <p>{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @else
                                                        <?php
                                                            if (isset($transaction->payment_method->name))
                                                            {
                                                                if ($transaction->payment_method->name == 'Mts')
                                                                {
                                                                    $payment_method = getCompanyName();
                                                                }
                                                                else
                                                                {
                                                                    $payment_method = $transaction->payment_method->name;
                                                                }
                                                            }
                                                        ?>
                                                        <td class="text-left">
                                                            <h4 class="mt-2">
                                                                @if($transaction->transaction_type->name == 'Deposit')
                                                                    @if ($transaction->payment_method->name == 'Bank')
                                                                      {{ $transaction->transaction_type->name . ' ' . 'via' . ' ' . $payment_method . ' ' .  $transaction->bank->bank_name }}
                                                                    @else
                                                                        @if(!empty($payment_method))
                                                                        {{ $transaction->transaction_type->name . ' ' . 'via' . ' ' . $payment_method }}
                                                                        @endif
                                                                    @endif

                                                                @elseif($transaction->transaction_type->name == 'Exchange_To' || $transaction->transaction_type->name == 'Exchange_From')
                                                                    {{ __(str_replace('_',' ',$transaction->transaction_type->name)) .' ' . $transaction->currency->code }}

                                                                @elseif($transaction->transaction_type->name == 'Withdrawal')
                                                                    @if(!empty($payment_method))
                                                                       {{ __('Payout via') }} {{ $payment_method }}
                                                                    @endif


                                                                @elseif($transaction->transaction_type->name == 'Transferred' && $transaction->user_type = 'unregistered')
                                                                    {{ ($transaction->email) ? $transaction->email : $transaction->phone }} <!--for send money by phone - mobile app-->
                                                                @elseif($transaction->transaction_type->name == 'Request_From' && $transaction->user_type = 'unregistered')
                                                                    {{ ($transaction->email) ? $transaction->email : $transaction->phone }} <!--for send money by phone - mobile app-->
                                                                @endif
                                                            </h4>

                                                            @if($transaction->transaction_type_id)
                                                                <p>
                                                                    @if($transaction->transaction_type_id==Request_From)
                                                                        @lang('Request Sent')
                                                                    @elseif($transaction->transaction_type_id==Request_To)
                                                                        @lang('Request Received')

                                                                    @elseif($transaction->transaction_type_id == Withdrawal)
                                                                        @lang('Payout')
                                                                    @else
                                                                        <p>{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                                                    @endif
                                                                </p>
                                                            @endif
                                                        </td>
                                                    @endif
                                                @else
                                                    <td class="text-left">
                                                        <h4>{{ $transaction->merchant->business_name }}</h4>
                                                        @if($transaction->transaction_type_id)
                                                            <p>{{ __(str_replace('_',' ',$transaction->transaction_type->name)) }}</p>
                                                        @endif
                                                    </td>
                                                @endif

                                                <!-- Status -->
                                                <td class="text-left">
                                                    <span id="status_{{$transaction->id}}" class="{{ $transaction->status }}">
                                                        {{
                                                            (
                                                                ($transaction->status == 'Blocked') ? __("Cancelled") :
                                                                (
                                                                    ($transaction->status == 'Refund') ? __("Refunded") : __($transaction->status)
                                                                )
                                                            )
                                                        }}
                                                    </span>
                                                </td>

                                                <!-- Amount -->
                                                @if($transaction->transaction_type_id == Deposit)
                                                    @if($transaction->subtotal > 0)
                                                        <td class="pr-5">
                                                            <h3 class="text-right text-success">+{{ formatNumber($transaction->subtotal) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                        </td>
                                                    @endif
                                                @elseif($transaction->transaction_type_id == Withdrawal)
                                                    <td class="pr-5">
                                                        <h3 class="text-right text-danger">-{{ formatNumber($transaction->subtotal) }} <span class="c-code"> ({{ $transaction->currency->code }}) </span></h3>
                                                    </td>
                                                @elseif($transaction->transaction_type_id == Payment_Received)
                                                    @if($transaction->subtotal > 0)
                                                        @if($transaction->status == 'Refund')
                                                            <td class="pr-5">
                                                                <h3 class="text-right text-danger">-{{ formatNumber($transaction->subtotal) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                                <p class="text-right mt-2">{{ $transaction->currency->code }}</p>
                                                            </td>
                                                        @else
                                                            <td class="pr-5">
                                                                <h3 class="text-right text-success">+{{ formatNumber($transaction->subtotal) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                            </td>
                                                        @endif
                                                    @elseif($transaction->subtotal == 0)
                                                        <td class="pr-5">
                                                            <h3 class="text-right">{{ formatNumber($transaction->subtotal) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                        </td>
                                                    @elseif($transaction->subtotal < 0)
                                                        <td class="pr-5">
                                                            <h3 class="text-right text-danger">{{ formatNumber($transaction->subtotal) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                        </td>
                                                    @endif
                                                @else
                                                    @if($transaction->total > 0)
                                                        <td class="pr-5">
                                                            <h3 class="text-right text-success">{{ $transaction->currency->type != 'fiat' ? "+".$transaction->total : "+".formatNumber($transaction->total) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                        </td>
                                                    @elseif($transaction->total == 0)
                                                        <td class="pr-5">
                                                            <h3 class="text-right">{{ formatNumber($transaction->total) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                        </td>
                                                    @elseif($transaction->total < 0)
                                                        <td class="pr-5">
                                                            <h3 class="text-right text-danger">{{ $transaction->currency->type != 'fiat' ? $transaction->total : formatNumber($transaction->total) }} <span class="c-code">({{ $transaction->currency->code }})</span></h3>
                                                        </td>
                                                    @endif
                                                @endif
                                            </tr>


                                            <!-- Modal -->
                                            <div class="modal fade-scale" id="collapseRow{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-body p-0">
                                                            <button type="button" class="close text-28" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>

                                                            <div class="row activity-details" id="loader_{{$transaction->id}}">
                                                                <div class="col-md-5 bg-primary">
                                                                    <div id="total_{{$key}}" class="p-center">

                                                                    </div>
                                                                </div>

                                                                <div class="col-md-7 col-sm-12 text-left p-0">
                                                                    <div class="preloader transaction-loader" style="display: none;">
                                                                        <div class="loader"></div>
                                                                    </div>
                                                                    
                                                                    <div class="modal-header">
                                                                        <h3 class="modal-title" id="exampleModalLabel">{{ __('Transaction details') }}</h3>
                                                                    </div>

                                                                    <div id="html_{{$key}}" class="pl-4 pr-4 mt-4">
                                                                    </div>

                                                                    <div class="text-center mb-4">
                                                                        @if( $transaction->transaction_type_id == Payment_Sent && $transaction->status == 'Success' && !isset($transaction->dispute->id))
                                                                            <a id="dispute_{{$transaction->id}}" href="{{url('/dispute/add/').'/'.$transaction->id}}" class="btn btn-grad btn-sm">@lang('message.dashboard.transaction.open-dispute')</a>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center p-5">
                                                @lang('message.dashboard.left-table.no-transaction')
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="text-center ash-color"><a class="font-weight-bold btn-circular btn-grad" href="{{url('transactions')}}">@lang('message.dashboard.left-table.view-all')</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-xl-3 col-xs-12 col-sm-12 mb20 marginTopPlus">
                <div class="flash-container">
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="float-left trans-inline">@lang('message.dashboard.right-table.title')</h4>
                        <div class="chart-list trans-inline float-right ">
                        </div>
                    </div>

                    <div class="wap-wed w-100">
                        @if($wallets->count()>0)
                            @foreach($wallets as $wallet)
                                @php
                                    $walletCurrencyCode = encrypt(strtolower($wallet->currency->code));
                                    $walletId = encrypt($wallet->id);
                                @endphp

                                    <div class="d-flex m-0 border-bottom pb-3">
                                        <!--BALANCE-->
                                        <div class="w-100">
                                            <div class="d-flex pt-3  pl-4 pr-4 w-100 justify-content-between">
                                                <div>
                                                    <!--Currency Code-->
                                                    @if ($wallet->currency->type == 'fiat' && $wallet->is_default == 'Yes')
                                                        <span>{{ $wallet->currency->code }}&nbsp;<span class="badge badge-secondary">@lang('message.dashboard.right-table.default-wallet-label')</span></span>
                                                    @else
                                                        <span>{{ $wallet->currency->code }}</span>
                                                    @endif
                                                </div>

                                                <div>
                                                    @if($wallet->balance > 0)
                                                        @if ($wallet->currency->type != 'fiat')
                                                            <h3>{{ '+'.$wallet->balance }}</h3>
                                                        @else
                                                            <h3>{{ '+'.formatNumber($wallet->balance) }}</h3>
                                                        @endif
                                                    @elseif($wallet->balance == 0)
                                                        @if ($wallet->currency->type != 'fiat')
                                                            <h3>{{ $wallet->balance }}</h3>
                                                        @else
                                                            <h3>{{ '+'.formatNumber($wallet->balance) }}</h3>
                                                        @endif
                                                    @elseif($wallet->balance < 0)
                                                        @if ($wallet->currency->type != 'fiat')
                                                            <h3 class="text-danger">{{ $wallet->balance }}</h3>
                                                        @else
                                                            <h3 class="text-danger">{{ '+'.formatNumber($wallet->balance) }}</h3>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>


                                            <!--Crypto Send & Receiv Buttons-->
                                            @if ($wallet->currency->type != 'fiat' && $wallet->currency->status == 'Active')
                                                <div class="d-flex pt-3  pl-4 pr-4 w-100 justify-content-between">
                                                    <div>
                                                        <a href="{{ url("/crpto/send/".$walletCurrencyCode."/".$walletId) }}" class="text-primary crypt-btn float-left">@lang('message.dashboard.right-table.crypto-send')</a>
                                                    </div>

                                                    <div>
                                                        <a href="{{ url("/crpto/receive/".$walletCurrencyCode."/".$walletId) }}" class="text-primary crypt-btn float-right">@lang('message.dashboard.right-table.crypto-receive')</a>
                                                    </div>
                                                </div>
                                            @endif

                                        </div>
                                    </div>

                            @endforeach
                        @else
                            @lang('message.dashboard.right-table.no-wallet')
                        @endif

                        <div class="clearfix"></div>
                    </div>
                </div>


                <div class="card mt-4">

                    <div class="row justify-content-center flex-column m-0 pl-4 pr-4 pb-4">
                        @if(Common::has_permission(auth()->id(),'manage_deposit'))
                            <div class="mt-4">
                                <a href="{{url('deposit')}}" class="btn btn-grad font-weight-bold col-md-12">
                                    &nbsp;@lang('message.dashboard.button.deposit')
                                </a>
                            </div>
                        @endif
                        @if(Common::has_permission(auth()->id(),'manage_withdrawal'))
                            <div class="mt-4">
                                <a href="{{url('payouts')}}" class="btn btn-grad font-weight-bold col-md-12 ">
                                    &nbsp;{{ __('Withdraw') }}
                                </a>
                            </div>
                        @endif


                        @if(Common::has_permission(auth()->id(),'manage_exchange'))
                            <div class="mt-4">
                                <a href="{{url('exchange')}}" class="btn btn-grad font-weight-bold col-md-12">
                                    @lang('message.dashboard.button.exchange')
                                </a>
                            </div>
                        @endif

                        @if(config('crypto_exchange.is_active') && \App\Utility\CryptoExchange::userCryptoWalletcheck())
                            <div class="mt-4">
                                <a href="{{ url('crypto-exchange/crypto-to-fiat') }}" class="btn btn-grad font-weight-bold col-md-12">
                                    {{ __('Crypto Exchange') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')

<!-- sweetalert -->
<script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>

@include('user_dashboard.layouts.common.check-user-status')

@include('common.user-transactions-scripts')

@endsection
