@extends($activeTemplate . 'layouts.app')
@section('content')
    <header class="header-primary">
        <div class="container-fluid">
            @include($activeTemplate . 'partials.header')
        </div>
    </header>
    <main class="home-page">
        @include($activeTemplate . 'partials.category')
        <div class="sports-body">
            <div class="container-fluid">
                <div class="row g-3">
                    @yield('bet')

                    <div class="col-12">
                        <div class="footer footer--light">
                            @include($activeTemplate . 'partials.footer_top')
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="footer-bottom">
                            <div class="container-fluid">
                                @include($activeTemplate . 'partials.footer_bottom')
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        @include($activeTemplate . 'partials.bet_slip')
        @include($activeTemplate . 'partials.mobile_menu')
    </main>
@endsection
