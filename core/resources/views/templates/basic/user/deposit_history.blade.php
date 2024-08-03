@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row justify-content-end mb-3">
        <div class="col-xl-5 col-md-8">
            <form>
                <div class="input-group">
                    <input class="form-control form--control" name="search" type="text" value="{{ request()->search }}" placeholder="@lang('Search by Transaction')">
                    <button class="input-group-text bg--base text-white"><i class="las la-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="bet-table">
        <table class="table-responsive--md custom--table custom--table-separate table">
            <thead>
                <tr>
                    <th>@lang('Gateway') | @lang('TRX. No.')</th>
                    <th>@lang('Initiated')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Conversion')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Details')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deposits as $deposit)
                    <tr>
                        <td>
                            <div class="">
                                <span class="fw-bold">
                                    <span class="text-primary">
                                        @if ($deposit->method_code < 5000)
                                            {{ __(@$deposit->gateway->name) }}
                                        @else
                                            @lang('Google Pay')
                                        @endif
                                    </span>
                                </span>
                                <br>
                                <small> {{ $deposit->trx }} </small>
                            </div>
                        </td>

                        <td>
                            <span>{{ showDateTime($deposit->created_at) }}<br>{{ diffForHumans($deposit->created_at) }}</span>
                        </td>
                        <td>
                            <div>
                                {{ showAmount($deposit->amount) }} + <span class="text--danger" data-bs-toggle="tooltip" title="@lang('Processing Charge')">{{ showAmount($deposit->charge) }} </span>
                                <br>
                                <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                </strong>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ showAmount(1) }} = {{ showAmount($deposit->rate, currencyFormat: false) }} {{ __($deposit->method_currency) }}
                                <br>
                                <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</strong>
                            </div>
                        </td>
                        <td>
                            @php echo $deposit->statusBadge @endphp
                        </td>
                        @php
                            $details = [];
                            if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                foreach (@$deposit->detail ?? [] as $key => $info) {
                                    $details[] = $info;
                                    if ($info->type == 'file') {
                                        $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                    }
                                }
                            }
                        @endphp

                        <td>
                            @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                <a href="javascript:void(0)" class="btn btn--base btn--sm detailBtn" data-info="{{ json_encode($details) }}"
                                   @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                    <i class="fas fa-desktop"></i>
                                </a>
                            @else
                                <button class="btn btn--success btn--sm automatic-payment" type="button" data-bs-toggle="tooltip" title="@lang('Automatically processed')">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 align-items-center pagination-wrapper">
        {{ $deposits->links() }}
    </div>

    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .input-group-text {
            border-radius: 0 5px 5px 0 !important;
        }

        .automatic-payment {
            color: hsl(var(--white));
            background: hsl(var(--success));
            cursor: context-menu !important;
        }
    </style>
@endpush



@push('script')
    <script>
        (function($) {
            "use strict";

            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `<li class="d-flex flex-wrap aligh-items-center justify-content-between">
                                        <span class="deposit-card__title fw-bold">
                                            ${element.name}
                                        </span>
                                        <span class="deposit-card__amount">
                                            ${element.value}
                                        </span>
                                    </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                if (adminFeedback) {
                    modal.find('.feedback').html(adminFeedback).addClass('deposit-card');
                } else {
                    modal.find('.feedback').removeClass('deposit-card').empty();
                }

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
