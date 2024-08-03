@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Market')</th>
                                    <th>@lang('Match')</th>
                                    <th>@lang('Bet End Time')</th>
                                    <th>@lang('Bet Placed')</th>
                                    @if (request()->routeIs('admin.outcomes.declare.declared'))
                                        <th>@lang('Win Option')</th>
                                    @endif
                                    @if (request()->routeIs('admin.outcomes.declare.pending'))
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($questions as $question)
                                    <tr>
                                        <td class="text-start">{{ $questions->firstItem() + $loop->index }}. {{ __(@$question->title) }}</td>

                                        <td>
                                            <div class="d-flex align-items-center justify-content-end justify-content-lg-center gap-3">
                                                <div class="thumb">
                                                    <div class="d-flex align-items-center flex-column">
                                                        <img src="{{ getImage(getFilePath('team') . '/' . @$question->game->teamOne->image, getFileSize('team')) }}" alt="@lang('image')">
                                                        <span title="{{ @$question->game->teamOne->name }}">{{ __(@$question->game->teamOne->short_name) }}</span>
                                                    </div>
                                                </div>

                                                <span>@lang('VS')</span>

                                                <div class="thumb">
                                                    <div class="d-flex align-items-center flex-column">
                                                        <img src="{{ getImage(getFilePath('team') . '/' . @$question->game->teamTwo->image, getFileSize('team')) }}" alt="@lang('image')">
                                                        <span title="{{ @$question->game->teamTwo->name }}">{{ __(@$question->game->teamTwo->short_name) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            {{ showDateTime(@$question->game->bet_end_time) }}
                                            <br>
                                            {{ diffForHumans(@$question->game->bet_end_time) }}
                                        </td>

                                        <td>
                                            <span>{{ getAmount(@$question->bet_details_count) }} </span>
                                        </td>

                                        @if (request()->routeIs('admin.outcomes.declare.declared'))
                                            <td>
                                                @if (@$question->winOption)
                                                    <span class="text--success">{{ __(@$question->winOption->name) }}</span>
                                                @else
                                                    <span class="text--info">@lang('Refunded')</span>
                                                @endif
                                            </td>
                                        @endif

                                        @if (request()->routeIs('admin.outcomes.declare.pending'))
                                            <td>
                                                <div class="button--group">
                                                    <button class="btn btn-sm btn-outline--primary option-btn" data-question="{{ $question->title }}" data-options='{{ $question->options }}' type="button">
                                                        <i class="la la-info-circle"></i>@lang('Select Outcome')
                                                    </button>
                                                    <button class="btn btn-sm btn-outline--info confirmationBtn" data-action="{{ route('admin.outcomes.declare.refund', $question->id) }}" data-question="@lang('Are you sure refund this question')?" type="button">
                                                        <i class="las la-undo-alt"></i> @lang('Refund Bet')
                                                    </button>
                                                    <a class="btn btn-sm btn-outline--dark" href="{{ route('admin.bet.question', $question->id) }}">
                                                        <i class="las la-clipboard-list"></i> @lang('Bets')
                                                    </a>
                                                </div>
                                            </td>
                                        @endif

                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($questions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($questions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal" id="optionModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="result-area"></div>
                        <div class="action-area"></div>
                    </div>
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Bet Count')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
@endpush

@push('style')
    <style>
        .thumb img {
            width: 30px;
            height: 30px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let modal = $("#optionModal");
            $('.option-btn').on('click', function(e) {
                modal.find('tbody').html('')
                var question = $(this).data('question');
                var options = $(this).data('options');

                var modalTitle = `Options for - ${question}`;
                modal.find('.modal-title').text(modalTitle);
                var tableRow = ``;
                $.each(options, function(index, option) {
                    tableRow += `<tr>
                                    <td data-label="@lang('Name')">${option.name}</td>
                                    <td data-label="@lang('Odds')">${Math.abs(option.odds)}</td>
                                    <td data-label="@lang('Bet Count')">${option.bets_count}</td>
                                    <td data-label="@lang('Action')">
                                        <button class="btn btn-sm btn-outline--primary confirmationBtn" data-action="{{ route('admin.outcomes.declare.winner', '') }}/${option.id}" data-question="@lang('Are you sure to select') ${option.name}?">
                                            <i class="las la-trophy"></i>@lang('Select')
                                        </button>
                                    </td>
                                </tr>`;
                });
                modal.find('tbody').append(tableRow)
                modal.modal('show')
            });

            let confirmationModal = $("#confirmationModal");

            $(document).on('click', '.confirmationBtn', function(e) {
                modal.modal('hide');
                confirmationModal.modal('show');
            });

            $(document).on('click', '#confirmationModal [data-bs-dismiss=modal]', function(e) {
                modal.modal('show');
                confirmationModal.modal('hide')
            });


        })(jQuery);
    </script>
@endpush
