@extends($activeTemplate . 'layouts.master')
@section('master')

    <div class="card custom--card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="las la-user-check"></i> @lang('KYC Data')
            </h5>
        </div>
        <div class="card-body">
            <div class="preview-details">
                @if ($user->kyc_data)
                    <ul class="list-group list-group-flush">
                        @foreach ($user->kyc_data as $val)
                            @continue(!$val->value)
                            <li class="list-group-item d-flex justify-content-between flex-wrap bg-transparent px-0">
                                <span>{{ __($val->name) }}</span>
                                <span class="fw-bold">
                                    @if ($val->type == 'checkbox')
                                        {{ implode(',', $val->value) }}
                                    @elseif($val->type == 'file')
                                        <a href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}" class="me-3"><i class="fa-regular fa-file"></i> @lang('Attachment') </a>
                                    @else
                                        <p>{{ __($val->value) }}</p>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <h5 class="text-center">@lang('KYC data not found')</h5>
                @endif
            </div>
        </div>
    </div>

@endsection
