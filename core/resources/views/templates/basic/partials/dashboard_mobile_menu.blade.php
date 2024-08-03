<div class="app-nav">
    <div class="container-fluid">
        <div class="row g-0">
            <div class="col-12">
                <ul class="app-nav__menu list list--row justify-content-between align-items-center">
                    <li>
                        <a class="app-nav__menu-link active" href="{{ route('home') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/bet-now.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text"> @lang('Bet Now') </span>
                        </a>
                    </li>
                    <li>
                        <a class="app-nav__menu-link" href="{{ route('user.deposit.index') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/deposit.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text"> @lang('Deposit') </span>
                        </a>
                    </li>

                    <li class="app-nav__menu-link-important-container">
                        <a class="app-nav__menu-link-important" href="javascript:void(0)">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>

                    <li>
                        <a class="app-nav__menu-link" href="{{ route('user.withdraw') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/withdraw.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text"> @lang('Withdraw') </span>
                        </a>
                    </li>

                    <li>
                        <a class="app-nav__menu-link" href="{{ route('user.bets') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/my_bets.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text">@lang('My Bets')</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>

    <div class="app-nav__drawer dashboard-menu__body" data-simplebar>
        <ul class="list app-nav__drawer-list">
            <li>
                <a class="dashboard-menu__link {{ menuActive('user.home') }}" href="{{ route('user.home') }}">
                    <span class="dashboard-menu__icon">
                        <i class="las la-home"></i>
                    </span>
                    <span class="dashboard-menu__text"> @lang('Dashboard') </span>
                </a>
            </li>
            <li>
                <a class="dashboard-menu__link {{ route('user.bets') }}" href="{{ route('user.bets') }}">
                    <span class="dashboard-menu__icon">
                        <i class="las la-list"></i>
                    </span>
                    <span class="dashboard-menu__text"> @lang('My Bets') </span>
                </a>
            </li>

            <li class="has-submenu {{ menuActive('user.deposit*', 4) }}">
                <button class="accordion-button {{ menuActive('user.deposit*', 5) }}" data-bs-toggle="collapse" type="button" aria-expanded="false">
                    <span class="accordion-button__icon">
                        <i class="las la-wallet"></i>
                    </span>
                    <span class="accordion-button__text">@lang('Deposit')</span>
                </button>

                <ul class="list dashboard-menu__inner">
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.deposit.index', 4) }}" href="{{ route('user.deposit.index') }}">
                            @lang('Deposit Now')
                        </a>
                    </li>
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.deposit.history', 4) }}" href="{{ route('user.deposit.history') }}">
                            @lang('Deposit History')
                        </a>
                    </li>
                </ul>
            </li>

            <li class="has-submenu {{ menuActive('user.withdraw*', 4) }}">
                <button class="accordion-button {{ menuActive('user.withdraw*', 5) }}" data-bs-toggle="collapse" type="button" aria-expanded="false">
                    <span class="accordion-button__icon">
                        <i class="las la-money-bill-wave-alt"></i>
                    </span>
                    <span class="accordion-button__text">@lang('Withdraw')</span>
                </button>

                <ul class="list dashboard-menu__inner">
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive(['user.withdraw', 'user.withdraw.preview'], 4) }}" href="{{ route('user.withdraw') }}">
                            @lang('Withdraw Now')
                        </a>
                    </li>
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.withdraw.history', 4) }}" href="{{ route('user.withdraw.history') }}">
                            @lang('Withdraw History')
                        </a>
                    </li>
                </ul>
            </li>

            <li class="has-submenu {{ menuActive('user.referral*', 4) }}">
                <button class="accordion-button {{ menuActive('user.referral*', 5) }}" data-bs-toggle="collapse" type="button" aria-expanded="false">
                    <span class="accordion-button__icon">
                        <i class="las la-sitemap"></i>
                    </span>
                    <span class="accordion-button__text">@lang('Referral')</span>
                </button>

                <ul class="list dashboard-menu__inner">
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.referral.users') }}" href="{{ route('user.referral.users') }}">
                            @lang('Referred Users')
                        </a>
                    </li>
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.referral.commissions', 4) }}" href="{{ route('user.referral.commissions') }}">
                            @lang('Referral Commissions')
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a class="dashboard-menu__link {{ menuActive('user.transactions', 4) }}" href="{{ route('user.transactions') }}">
                    <span class="dashboard-menu__icon">
                        <i class="las la-exchange-alt"></i>
                    </span>
                    <span class="dashboard-menu__text"> @lang('Transactions') </span>
                </a>
            </li>

            <li class="has-submenu {{ menuActive('ticket*', 4) }}">
                <button class="accordion-button {{ menuActive('user.ticket*', 5) }}" data-bs-toggle="collapse" type="button" aria-expanded="false">
                    <span class="accordion-button__icon">
                        <i class="las la-question-circle"></i>
                    </span>
                    <span class="accordion-button__text">@lang('Support Ticket')</span>
                </button>

                <ul class="list dashboard-menu__inner">
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('ticket.open', 4) }}" href="{{ route('ticket.open') }}">
                            @lang('Open New Ticket')
                        </a>
                    </li>
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive(['ticket.index', 'ticket.view'], 4) }}" href="{{ route('ticket.index') }}">
                            @lang('My Ticket')
                        </a>
                    </li>
                </ul>
            </li>

            <li class="has-submenu {{ menuActive(['user.profile.setting', 'user.change.password', 'user.twofactor'], 4) }}">
                <button class="accordion-button {{ menuActive(['user.profile.setting', 'user.change.password', 'user.twofactor'], 5) }}" data-bs-toggle="collapse" type="button" aria-expanded="false">
                    <span class="accordion-button__icon">
                        <i class="las la-user-circle"></i>
                    </span>
                    <span class="accordion-button__text">@lang('Account Setting')</span>
                </button>

                <ul class="list dashboard-menu__inner">
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.profile.setting', 4) }}" href="{{ route('user.profile.setting') }}">
                            @lang('Profile Setting')
                        </a>
                    </li>
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.change.password', 4) }}" href="{{ route('user.change.password') }}">
                            @lang('Change Password')
                        </a>
                    </li>
                    <li>
                        <a class="dashboard-menu__inner-link {{ menuActive('user.twofactor', 4) }}" href="{{ route('user.twofactor') }}">
                            @lang('2FA Security')
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a class="dashboard-menu__link" href="{{ route('user.logout') }}">
                    <span class="dashboard-menu__icon">
                        <i class="las la-sign-out-alt"></i>
                    </span>
                    <span class="dashboard-menu__text"> @lang('Logout') </span>
                </a>
            </li>
        </ul>
    </div>
</div>
