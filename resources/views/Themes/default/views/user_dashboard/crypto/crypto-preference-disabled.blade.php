@extends('user_dashboard.layouts.app')
@section('content')
    <section class="min-vh-100">
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="h3 mt10 text-danger">
                                <div class="alert alert-danger">
                                    <i class="fa fa-flag"></i> <strong>{{ __('Disabled') }}!</strong>
                                    <hr class="message-inner-separator">
                                    <p>@lang('message.dashboard.crypto.preference-disabled').</p>
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
    $(document).on('click', '.preference-link', function(e)
    {
        e.preventDefault();
        window.localStorage.setItem("crypto-disabled", true);
        window.location.href = "{{ url(\Config::get('adminPrefix').'/settings/preference') }}";
    });
</script>
@endsection