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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Short Name')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leagues as $league)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb"><img src="{{ getImage(getFilePath('league') . '/' . $league->image, getFileSize('league')) }}" alt="@lang('image')" class="plugin_bg"></div>
                                                <span class="name">{{ __($league->name) }}</span>
                                            </div>
                                        </td>

                                        <td>{{ __($league->short_name) }}</td>
                                        <td>{{ $league->slug }}</td>
                                        <td>{{ __(@$league->category->name) }}</td>
                                        <td>
                                            @php echo $league->statusBadge @endphp
                                        </td>
                                        <td>
                                            @php
                                                $league->image_with_path = getImage(getFilePath('league') . '/' . $league->image, getFileSize('league'));
                                            @endphp
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn editBtn" data-category_id="{{ $league->category_id }}" data-image="{{ $league->image_with_path }}" data-resource="{{ $league }}" data-modal_title="@lang('Edit League')">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($league->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this league?')" data-action="{{ route('admin.league.status', $league->id) }}">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this league?')" data-action="{{ route('admin.league.status', $league->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
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

                @if ($leagues->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($leagues) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create or Update Modal --}}
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.league.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ getImage(getFilePath('league'), getFileSize('league')) }}" class="w-100" type="league" :required=false />
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Category')</label>
                                    <select name="category_id" class="form-control select2" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control makeSlug" name="name" value="{{ old('name') }}" required />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Short Name')</label>
                                    <input type="text" class="form-control" name="short_name" value="{{ old('short_name') }}" required />
                                </div>
                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input type="text" class="form-control checkSlug" name="slug" value="{{ old('slug') }}" required />
                                    <code>@lang('Spaces are not allowed')</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Name / Slug / Category" />
    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New League')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            let modal = $('#cuModal');
            $('.editBtn').on('click', function() {
                modal.find('select[name=category_id]').val($(this).data('category_id')).change();
                modal.find('[name=image]').removeAttr('required');
                modal.find('[name=image]').closest('.form-group').find('label').first().removeClass('required');
                modal.find('.image-upload-preview').attr('style', `background-image: url(${$(this).data('image')})`);
            });

            var placeHolderImage = "{{ getImage(getFilePath('league'), getFileSize('league')) }}";
            $('#cuModal').on('hidden.bs.modal', function() {
                $('#cuModal form')[0].reset();
                modal.find('select[name=category_id]').val('').change();
                modal.find('.image-upload-preview').attr('style', `background-image: url(${placeHolderImage})`);
                modal.find('[name=image]').attr('required', 'required');
                modal.find('[name=image]').closest('.form-group').find('label').first().addClass('required');
            });
        })(jQuery);
    </script>
@endpush
