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

    <table class="table-responsive--md custom--table custom--table-separate table">
        <thead>
            <tr>
                <th>@lang('Gateway | Transaction')</th>
                <th>@lang('Initiated')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Conversion')</th>
                <th>@lang('Status')</th>
                <th>@lang('Details')</th>
            </tr>
        </thead>

        <tbody>
            @forelse($withdraws as $withdraw)
                @php
                    $details = [];
                    foreach ($withdraw->withdraw_information as $key => $info) {
                        $details[] = $info;
                        if ($info->type == 'file') {
                            $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                        }
                    }
                @endphp
                <tr>
                    <td>
                        <div>
                            <span class="fw-bold"><span class="text-primary"> {{ __(@$withdraw->method->name) }}</span></span>
                            <br>
                            <small>{{ $withdraw->trx }}</small>
                        </div>
                    </td>
                    <td>
                        <span>{{ showDateTime($withdraw->created_at) }} <br> {{ diffForHumans($withdraw->created_at) }}</span>
                    </td>
                    <td>
                        <div>
                            {{ showAmount($withdraw->amount) }} - <span class="text--danger" data-bs-toggle="tooltip" title="@lang('Processing Charge')">{{ showAmount($withdraw->charge) }} </span>
                            <br>
                            <strong data-bs-toggle="tooltip" title="@lang('Amount after charge')">
                                {{ showAmount($withdraw->amount - $withdraw->charge) }}
                            </strong>
                        </div>
                    </td>
                    <td>
                        <div>
                            {{ showAmount(1) }} = {{ showAmount($withdraw->rate, currencyFormat: false) }} {{ __($withdraw->currency) }}
                            <br>
                            <strong>{{ showAmount($withdraw->final_amount, currencyFormat: false) }} {{ __($withdraw->currency) }}</strong>
                        </div>
                    </td>
                    <td>
                        @php echo $withdraw->statusBadge @endphp
                    </td>
                    <td>
                        <button class="btn btn--sm btn--base detailBtn"
                                data-user_data="{{ json_encode($details) }}"
                                @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                            <i class="la la-desktop"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 align-items-center pagination-wrapper">
        {{ $withdraws->links() }}
    </div>

    <div class="modal fade custom--modal" id="detailModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="deposit-card">
                        <ul class="deposit-card__list list userData">
                        </ul>
                    </div>
                    <div class="feedback mt-2 pt-1 pb-1"></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                console.log(userData);
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span">${element.value}</span>
                        </li>`;
                    } else {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                        </li>`;
                    }
                });
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

                modal.find('.feedback').html(adminFeedback);

                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush
