<li data-option_id="{{ @$option->id }}" data-option_odds="{{ $bet->odds }}">
    <button class="betslip__list-close text--danger removeFromSlip" data-option_id="{{ @$option->id }}" type="button">
        <i class="las la-trash-alt"></i>
    </button>
    <div class="betslip__list-content">
        <span class="betslip__list-team">{{ __(@$option->question->game->teamOne->short_name) }} @lang('vs') {{ __(@$option->question->game->teamTwo->short_name) }}</span>
        <span class="betslip__list-question">{{ __(@$option->question->title) }}</span>
        <span class="betslip__list-match">{{ __($option->name) }}</span>
        @if (isSuspendBet($bet))
            <div class="betslip__list-text text--danger fw-bold">@lang('Suspended')</div>
        @else
            <div class="betslip__list-text">{{ rateData($bet->odds) }}</div>
        @endif
    </div>

    <div class="betslip-right">
        <div class="betslip__list-ratio">
            <input class="investAmount" name="invest_amount" type="number" @if (@$bet->stake_amount) value="{{ @$bet->stake_amount }}" @endif autocomplete="off" step="any" placeholder="0.0">
            <span>@lang('STAKE')</span>
        </div>
        <small class="text--danger validation-msg"></small>
        <span class="betslip-return">@lang('Returns'):
            {{ gs('cur_sym') }}<span class="bet-return-amount">{{ getAmount($bet->return_amount) }}</span>
        </span>
    </div>
</li>
