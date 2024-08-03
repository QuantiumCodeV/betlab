<div class="bet-table">
    <table class="table-responsive--md custom--table custom--table-separate table">
        <thead>
            <tr>
                <th>@lang('Transaction ID')</th>
                <th>@lang('Transacted')</th>
                <th>@lang('Amount')</th>
                <th>@lang('Post Balance')</th>
                <th>@lang('Detail')</th>
            </tr>
        </thead>

        <tbody>
            @forelse($transactions as $trx)
                <tr>
                    <td>{{ $trx->trx }}</td>
                    <td>
                        {{ showDateTime($trx->created_at) }}
                    </td>
                    <td>
                        <span class="@if ($trx->trx_type == '+') text-success @else text-danger @endif">
                            {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                        </span>
                    </td>
                    <td>
                        {{ showAmount($trx->post_balance) }}
                    </td>
                    <td>{{ __($trx->details) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (!request()->routeIs('user.home'))
    <div class="mt-4 align-items-center pagination-wrapper">
        {{ $transactions->links() }}
    </div>
@endif
