@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-xl-6">
                @include('user_dashboard.layouts.common.alert')
                <div class="card">
                    <div class="card-header">
                        <h3>{{ __("Deposit with Paypal") }}</h3>
                    </div>
                    <div class="card-body">
                        <div id="paypal-button-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('user_dashboard.layouts.common.help')
@endsection
@section('js')
<script src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}&disable-funding=paylater&currency={{ $currencyCode }}"></script> 
<script>
    paypal.Buttons({
        createOrder: function (data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: "{!! $amount !!}"
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {
                // This function shows a transaction success message to your buyer.
                // alert('Transaction completed by ' + details.payer.name.given_name);
                window.location.replace(SITE_URL + "/deposit/paypal-payment/success/" + btoa(details.purchase_units[0].amount.value));
            });
        }
    }).render('#paypal-button-container');
</script>
@endsection