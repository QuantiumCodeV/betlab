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
                                    <th class="text-center">@lang('Title')</th>
                                    <th>@lang('League') |@lang('Category')</th>
                                    <th>@lang('Game Starts From')</th>
                                    <th>@lang('Bet Starts From')</th>
                                    <th>@lang('Bet Ends At')</th>
                                    <th>@lang('Markets')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($games as $game)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center justify-content-lg-around justify-content-end gap-1">
                                                <div class="thumb" title="{{ @$game->teamOne->name }}">
                                                    <div class="d-flex align-items-center flex-column">
                                                        <img src="{{ @$game->teamOne->teamImage() }}" alt="@lang('image')">
                                                        {{ __($game->teamOne->short_name) }}
                                                    </div>
                                                </div>
                                                <span> @lang('VS')</span>
                                                <div class="thumb" title="{{ @$game->teamTwo->name }}">
                                                    <div class="d-flex align-items-center flex-column">
                                                        <img src="{{ @$game->teamTwo->teamImage() }}" alt="@lang('image')">
                                                        {{ __(@$game->teamTwo->short_name) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ __(@$game->league->short_name) }}</span>
                                            <br>
                                            {{ __(@$game->league->category->name) }}
                                        </td>

                                        <td>
                                            <em class="fw-bold">{{ showDateTime($game->start_time, 'd M, Y h:i A') }}</em>
                                        </td>

                                        <td>
                                            {{ showDateTime($game->bet_start_time, 'd M, Y h:i A') }}
                                        </td>

                                        <td>
                                            {{ showDateTime($game->bet_end_time, 'd M, Y, h:i A') }}
                                        </td>

                                        <td>{{ $game->questions_count }}</td>

                                        <td> @php echo $game->statusBadge @endphp</td>

                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.game.edit', $game->id) }}">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </a>

                                                @if ($game->status)
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.game.status', $game->id) }}" data-question="@lang('Are you sure to disable this game')?">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.game.status', $game->id) }}" data-question="@lang('Are you sure to enable this game')?">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @endif

                                                <a class="btn btn-sm btn-outline--info" href="{{ route('admin.question.index', $game->id) }}">
                                                    <i class="la la-question-circle"></i>@lang('Markets')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($games->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($games) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirmation-modal />

    <div class="offcanvas offcanvas-end" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" tabindex="-1">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">@lang('Filter by')</h5>
            <button class="close bg--transparent" data-bs-dismiss="offcanvas" type="button" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="">
                <div class="form-group">
                    <label>@lang('Team One')</label>
                    <select class="form-control select2-basic" name="team_one_id">
                        <option value="">@lang('All')</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(request()->team_one_id == $team->id)>{{ $team->name }} - {{ @$team->short_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Team Two')</label>
                    <select class="form-control select2-basic" name="team_two_id">
                        <option value="">@lang('All')</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(request()->team_two_id == $team->id)>{{ $team->name }} - {{ @$team->short_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Leauge')</label>
                    <select class="form-control select2-basic" name="league_id">
                        <option value="">@lang('All')</option>
                        @foreach ($leagues as $league)
                            <option value="{{ $league->id }}" @selected(request()->league_id == $league->id)>{{ __($league->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Game Started From')</label>
                    <input name="start_time" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->start_time }}">
                </div>
                <div class="form-group">
                    <label>@lang('Bet Started From')</label>
                    <input name="bet_start_time" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->bet_start_time }}">
                </div>
                <div class="form-group">
                    <label>@lang('Bet Ended At')</label>
                    <input name="bet_end_time" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->bet_end_time }}">
                </div>
                <div class="form-group">
                    <button class="btn btn--primary w-100 h-45"> @lang('Filter')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .thumb img {
            width: 30px;
            height: 30px;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--info " data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" type="button" aria-controls="offcanvasRight"><i class="las la-filter"></i> @lang('Filter')</button>
    <a class="btn btn-sm btn-outline--primary " href="{{ route('admin.game.create') }}"><i class="las la-plus"></i>@lang('Add New')</a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush
@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush
