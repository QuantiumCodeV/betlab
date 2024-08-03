@extends($activeTemplate . 'layouts.' . $layout)
@section($layout)
    @if ($layout == 'frontend')
        <div class="container">
            <div class="section">
            @else
                <div class="col-12">
    @endif
    <div class="card custom--card">
        <div class="card-header d-flex flex-warp justify-content-between align-items-center">
            <h5 class="m-0">
                @php echo $myTicket->statusBadge; @endphp
                [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
            </h5>

            @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                <button class="btn btn--danger btn-sm" data-bs-toggle="modal" data-bs-target="#ticketCloseModal" type="button">
                    <i class="las la-times"></i>
                </button>
            @endif
        </div>
        <div class="card-body">
            <form method="post" class="disableSubmission" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-between">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea name="message" class="form-control form--control" rows="4" required>{{ old('message') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <button type="button" class="btn btn-dark btn-sm addAttachment my-2"> <i class="fas fa-plus"></i> @lang('Add Attachment') </button>
                        <p class="mb-2"><span class="text--info">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                        <div class="row fileUploadsContainer">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn--base w-100 my-2" type="submit"><i class="la la-fw la-lg la-reply"></i> @lang('Reply')
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    @if (!blank($messages))
        <h5 class="mb-1 mt-4">@lang('Previous Replies')</h5>
        <div class="list support-list">
            @foreach ($messages as $message)
                @if ($message->admin_id == 0)
                    <div class="support-card">
                        <div class="support-card__head">
                            <h6 class="support-card__title">
                                {{ $message->ticket->name }}
                            </h6>
                            <span class="support-card__date">
                                <code class="xsm-text text-muted"><i class="far fa-clock"></i>
                                    {{ $message->created_at->format('dS F Y @ H:i') }}</code>
                            </span>
                        </div>
                        <div class="support-card__body">
                            <p class="support-card__body-text">
                                {{ $message->message }}
                            </p>

                            @if ($message->attachments->count() > 0)
                                <ul class="list list--row support-card__list">
                                    @foreach ($message->attachments as $k => $image)
                                        <li>
                                            <a class="support-card__file" href="{{ route('ticket.download', encrypt($image->id)) }}">
                                                <span class="support-card__file-icon">
                                                    <i class="far fa-file-alt"></i>
                                                </span>
                                                <span class="support-card__file-text">
                                                    @lang('Attachment') {{ ++$k }}
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="support-card">
                        <div class="support-card__head">
                            <h6 class="support-card__title">
                                {{ $message->admin->name }}
                            </h6>
                            <span class="support-card__date">
                                <code class="xsm-text text-muted"><i class="far fa-clock"></i>
                                    {{ $message->created_at->format('dS F Y @ H:i') }}</code>
                            </span>

                        </div>
                        <div class="support-card__body">
                            <p class="support-card__body-text text-md-start mb-0 text-center">
                                {{ $message->message }}
                            </p>

                            @if ($message->attachments->count() > 0)
                                <ul class="list list--row support-card__list justify-content-center justify-content-md-start flex-wrap">
                                    @foreach ($message->attachments as $k => $image)
                                        <li>
                                            <a class="support-card__file" href="{{ route('ticket.download', encrypt($image->id)) }}">
                                                <span class="support-card__file-icon">
                                                    <i class="far fa-file-alt"></i>
                                                </span>
                                                <span class="support-card__file-text">
                                                    @lang('Attachment') {{ ++$k }}
                                                </span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    @if ($layout == 'frontend')
        </div>
        </div>
    @else
        </div>
    @endif

    <div class="modal fade custom--modal" id="ticketCloseModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation')!</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    @lang('Are you sure to close this ticket?')
                </div>
                <div class="modal-footer">
                    <button class="btn btn--dark btn--sm sm-text" data-bs-dismiss="modal" type="button">@lang('No')</button>
                    <form action="{{ route('ticket.close', $myTicket->id) }}" method="POST">
                        @csrf
                        <button class="btn btn--base btn--sm text--light sm-text" type="submit">@lang('Yes')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }

        .reply-bg {
            background-color: #ffd96729
        }

        .empty-message img {
            width: 120px;
            margin-bottom: 15px;
        }
    </style>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-4 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger text-white border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
