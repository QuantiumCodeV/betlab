@extends($activeTemplate . 'layouts.app')
@section('content')
    @include($activeTemplate . 'partials.user_header')
    <div class="user-dashboard">
        <div class="container">
            <div class="row">
                @include($activeTemplate . 'partials.dashboard_sidebar')
                <div class="col-lg-8 col-xl-9 ps-lg-3">
                    @yield('master')
                </div>
            </div>
        </div>
    </div>
    @include($activeTemplate . 'partials.footer')
    @include($activeTemplate . 'partials.dashboard_mobile_menu')
@endsection
@push('script')
    <script>
        (function($) {
            "use strict";
            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            function formatState(state) {
                if (!state.id) return console.log(state.text);
                state.text;
                let gatewayData = $(state.element).data();
                return $(`<div class="d-flex gap-2">${gatewayData.imageSrc ? `<div class="select2-image-wrapper"><img class="select2-image" src="${gatewayData.imageSrc}"></div>` : '' }<div class="select2-content"> <p class="select2-title">${gatewayData.title}</p><p class="select2-subtitle">${gatewayData.subtitle}</p></div></div>`);
            }

            $('.select2').each(function(index, element) {
                $(element).select2({
                    templateResult: formatState,
                    minimumResultsForSearch: "-1"
                });
            });

            $('.select2-searchable').each(function(index, element) {
                $(element).select2({
                    templateResult: formatState,
                    minimumResultsForSearch: "1"
                });
            });


            $('.select2-basic').each(function(index, element) {
                $(element).select2({
                    dropdownParent: $(element).closest('.select2-parent')
                });
            });
        })(jQuery)
    </script>
@endpush
