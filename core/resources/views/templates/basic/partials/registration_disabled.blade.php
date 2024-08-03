@php
    $registrationDisabled = getContent('register_disable.content', true);
@endphp
<div class="register-disable">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6">
                <div class="register-disable-content">
                    <h5 class="register-disable-title">{{ __(@$registrationDisabled->data_values->heading) }}</h5>
                    <p class="register-disable-desc">
                        {{ __(@$registrationDisabled->data_values->subheading) }}
                    </p>
                    <div>
                        <a href="{{ url(@$registrationDisabled->data_values->button_url) }}" class="btn btn--base">{{ __(@$registrationDisabled->data_values->button_name) }}</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="register-disable-image">
                    <img class="fit-image" src="{{ frontendImage('register_disable', @$registrationDisabled->data_values->image, '280x280') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
@endpush
