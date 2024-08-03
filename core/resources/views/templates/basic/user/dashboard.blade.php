@extends($activeTemplate . 'layouts.master')
@section('master')
    @php
        $kycContent = getContent('kyc_instructions.content', true);
    @endphp
    <div class="row gy-4">
        <div class="col-12">
            <h5 class="mb-3 mt-0">
                @lang('Referral Link')
            </h5>
            <div class="qr-code text--base mb-1">
                <div class="qr-code-copy-form" data-copy=true>
                    <input id="qr-code-text" type="text" value="{{ route('home') }}?reference={{ $user->referral_code }}" readonly>
                    <button class="text-copy-btn copy-btn lh-1 text-white" data-bs-toggle="tooltip" data-bs-original-title="@lang('Copy to clipboard')" type="button">@lang('Copy</')</button>
                </div>
            </div>
        </div>
        <div class="col-12">
            @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
                <div class="alert alert-danger mt-0" role="alert">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="m-0">@lang('KYC Documents Rejected')</h5>
                        <button class="btn btn--danger btn--sm" data-bs-toggle="modal" data-bs-target="#kycRejectionReason">@lang('Show Reason')</button>
                    </div>
                    <hr>
                    <p class="mb-0">
                        {{ __(@$kyc->data_values->reject) }}
                        <a href="{{ route('user.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.
                    </p>
                    <p class="mb-0">
                        <a href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                    </p>
                </div>
            @elseif ($user->kv == Status::KYC_UNVERIFIED)
                <div class="alert alert-warning mt-0" role="alert">
                    <h5 class="m-0">@lang('KYC Verification Required')</h5>
                    <hr>
                    <p class="mb-0">
                        {{ __(@$kycContent->data_values->for_verification) }}
                        <a class="text--base" href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a>
                    </p>
                </div>
            @elseif($user->kv == Status::KYC_PENDING)
                <div class="alert alert-info" role="alert">
                    <h5 class="m-0">@lang('KYC Verification Pending')</h5>
                    <hr>
                    <p class="mb-0">
                        {{ __(@$kycContent->data_values->for_pending) }}
                        <a class="text--base" href="{{ route('user.kyc.data') }}">@lang('See KYC data')</a>
                    </p>
                </div>
            @endif
        </div>

        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Total Deposited" link="{{ route('user.deposit.history') }}" icon="las la-wallet" amount="{{ showAmount($widget['totalDeposit']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Total Withdrawan" link="{{ route('user.withdraw.history') }}" icon="las la-money-bill-wave" amount="{{ showAmount($widget['totalWithdraw']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Total Bet" link="{{ route('user.bets') }}" icon="las la-gamepad" amount="{{ getAmount($widget['totalBet']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Pending Bet" link="{{ route('user.bets') }}" icon="las la-spinner" amount="{{ getAmount($widget['pendingBet']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Won Bet" link="{{ route('user.bets', 'win') }}" icon="las la-trophy" amount="{{ getAmount($widget['wonBet']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Lose Bet" link="{{ route('user.bets', 'lose') }}" icon="las la-frown" amount="{{ getAmount($widget['loseBet']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Refunded Bet" link="{{ route('user.bets', 'refunded') }}" icon="las la-undo-alt" amount="{{ getAmount($widget['refundedBet']) }}" />
        </div>
        <div class="col-sm-6 col-lg-6 col-xl-4">
            <x-user-dashboard-widget title="Transaction" link="{{ route('user.transactions') }}" icon="las la-exchange-alt" amount="{{ getAmount($widget['totalTransaction']) }}" />
        </div>
        <div class="col-12 col-xl-4">
            <x-user-dashboard-widget title="Support Tickets" link="{{ route('ticket.index') }}" icon="las la-ticket-alt" amount="{{ getAmount($widget['totalTicket']) }}" />
        </div>
        <div class="col-12">
            <div class="bet-chart-heading-area d-flex justify-content-between align-items-center">
                <h5>@lang('Bet Chart')</h5>
                <input class="form-control w-auto bg-white" name="date" type="text" value="{{ request()->date }}" autocomplete="off" placeholder="@lang('Start Date - End Date')">
            </div>
            <div class="card custom--card">
                <div class="card-body">
                    <div id="betChart"></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <h5 class="mb-2 mt-2">
                @lang('Latest Transaction History')
            </h5>
            @include($activeTemplate . 'partials.transaction_table')
        </div>
    </div>

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $user->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('style-lib')
    <link href="{{ asset('assets/global/css/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.copyBtn').on('click', function() {
                var copyText = document.getElementById("textToCopy");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                iziToast.success({
                    message: "Copied: " + copyText.value,
                    position: "topRight"
                });
            });

            var startsOne;
            var endOne;
            let startDate;
            let endDate;

            @if (@$request->starts_from_start)
                startsOne = moment(`{{ @$request->startDate }}`);
            @endif

            @if (@$request->starts_from_end)
                endOne = moment(`{{ @$request->endDate }}`);
            @endif


            function intDateRangePicker(element, start, end) {
                $(element).daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Clear': ['', ''],
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    }
                });

                $(element).on('apply.daterangepicker', function(ev, picker) {
                    if (!(picker.startDate.isValid() && picker.endDate.isValid())) {
                        $(element).val('');
                    }
                    window.location = appendQueryParameter('date', this.value);
                });
            }

            intDateRangePicker('[name=date]', startsOne, endOne);

            var betOptions = {
                series: [{
                    name: 'Total Stake',
                    data: [
                        @foreach ($report['bet_stake_amount'] as $stakeAmount)
                            "{{ $stakeAmount }}",
                        @endforeach
                    ]
                }, {
                    name: 'Total Return',
                    data: [
                        @foreach ($report['bet_return_amount'] as $returnAmount)
                            "{{ $returnAmount }}",
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: false
                        }
                    }
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    },
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [
                        @foreach ($report['bet_dates'] as $date)
                            "{{ $date }}",
                        @endforeach
                    ],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return `${val} {{ gs('cur_text') }}`
                        }
                    }
                },
            };
            var chart = new ApexCharts(document.querySelector("#betChart"), betOptions);
            chart.render();
        })(jQuery);
    </script>
@endpush
