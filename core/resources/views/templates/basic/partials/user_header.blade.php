<header class="header-primary user-header-primary">
    <div class="container">
        <div class="row g-0 align-items-center">
            <div class="header-fluid-custom-parent">
                <a class="logo" href="{{ route('home') }}"><img class="img-fluid logo__is" src="{{ siteLogo() }}" alt="@lang('logo')"></a>
                <nav class="primary-menu-container">
                    <ul class="list list--row primary-menu-lg justify-content-end justify-content-lg-start">
                        <li class="text-white d-lg-none d-block"><i class="la la-user-circle"></i> {{ auth()->user()->username }}</li>
                    </ul>
                    <ul class="list list--row primary-menu justify-content-end align-items-center right-side-nav gap-4">

                        @if (gs('multi_language'))
                            @php
                                $languages = App\Models\Language::all();
                                $language = $languages->where('code', '!=', session('lang'));
                                $activeLanguage = $languages->where('code', session('lang'))->first();
                            @endphp
                            <li>
                                <div class="dropdown-lang dropdown mt-0">
                                    <a href="#" class="language-btn dropdown-toggle" data-bs-toggle="dropdown"
                                       aria-expanded="false">
                                        <img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$activeLanguage->image, getFileSize('language')) }}" alt="us">
                                        <span class="language-text text-white">{{ __(@$activeLanguage->name) }}</span>
                                    </a>
                                    <ul class="dropdown-menu" style="">
                                        @foreach ($language as $item)
                                            <li>
                                                <a href="javascript:void(0)" class="langSel" data-code="{{ $item->code }}">
                                                    <img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$item->image, getFileSize('language')) }}" alt="image">
                                                    {{ __(@$item->name) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                        <li><a class="btn btn--signup" href="{{ route('home') }}"> @lang('Bet Now') </a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
