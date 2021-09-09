@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h3>@lang('message.dashboard.send-request.request.success.title')</h3>
                    </div>

                    <div class="card-body">
                        <div class="text-center">
                            <div class="confirm-btns"><i class="fa fa-check"></i></div>
                        </div>

                        <div class="text-center">
                            <div class="h3 mt6 text-success">@lang('message.dashboard.send-request.request.confirmation.success')!</div>
                        </div>

                        <!-- check mail error message-->
                        @if (!empty($errorMessage))
                            <div class="text-center"><p><strong>@lang('message.dashboard.send-request.request.success.request-complete') @lang('message.dashboard.mail-not-sent').</strong></p></div>
                        @else
                            <div class="text-center"><p><strong>@lang('message.dashboard.send-request.request.success.request-complete').</strong></p></div>
                        @endif

                        <div class="text-center mt10 img-success">
                            @if(!empty(@$requestCreator['picture']))
                                <img style="" src="{{url('public/user_dashboard/profile')}}/{{$requestCreator['picture']}}">
                            @else
                                <img style="" src="{{url('public/images')}}/avatar.png">
                            @endif
                        </div>

                        <h4 class="text-center mt10"><strong>{{ $requestCreator['first_name'].' '.$requestCreator['last_name'] }}</strong></h4>
                        <h5 class="text-center mt10">@lang('message.dashboard.send-request.request.success.accept-amount') : {{  moneyFormat($transInfo['currSymbol'], formatNumber($transInfo['amount'])) }}</h5>
                    
                        <div class="mt-5">
                            <div class="text-center">
                                <a href="{{url('request-payment/print')}}/{{$transInfo['trans_id']}}" target="_blank" class="btn btn-grad">
                                    <strong>@lang('message.dashboard.vouchers.success.print')</strong>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script type="text/javascript">
    function printFunc(){
        window.print();
    }
    $(document).ready(function() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    });
</script>
@endsection