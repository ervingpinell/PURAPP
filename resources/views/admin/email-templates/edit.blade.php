@extends('adminlte::page')

@section('title', 'Edit Email Template')

@section('content_header')
<h1>
    <i class="fas fa-edit"></i> Edit Template: {{ $template->name }}
</h1>
@stop

@section('content')
<form action="{{ route('admin.email-templates.update', $template) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        @foreach($locales as $index => $locale)
                        <li class="nav-item">
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                data-toggle="tab"
                                href="#locale-{{ $locale }}"
                                role="tab">
                                {{ strtoupper($locale) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @foreach($locales as $index => $locale)
                        @php
                        $content = $template->getContentForLocale($locale);
                        @endphp
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                            id="locale-{{ $locale }}"
                            role="tabpanel">

                            <input type="hidden" name="locales[{{ $index }}][locale]" value="{{ $locale }}">

                            <div class="form-group">
                                <label>Subject Line</label>
                                <input type="text"
                                    class="form-control"
                                    name="locales[{{ $index }}][subject]"
                                    value="{{ old("locales.{$index}.subject", $content?->subject) }}"
                                    placeholder="Email subject (use {{variables}} for dynamic content)"
                                    required>
                                <small class="text-muted">Use variables like {{customer_name}}, {{booking_reference}}</small>
                            </div>

                            @if($content && $content->content)
                            @foreach($content->content as $key => $value)
                            <div class="form-group">
                                <label>{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                <textarea class="form-control"
                                    name="locales[{{ $index }}][content][{{ $key }}]"
                                    rows="3"
                                    placeholder="Content for {{ $key }}">{{ old("locales.{$index}.content.{$key}", $value) }}</textarea>
                            </div>
                            @endforeach
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No content defined for this language yet. Add content sections below:
                            </div>

                            {{-- Default content sections --}}
                            @foreach(['greeting', 'intro', 'cta_button'] as $section)
                            <div class="form-group">
                                <label>{{ ucfirst(str_replace('_', ' ', $section)) }}</label>
                                <textarea class="form-control"
                                    name="locales[{{ $index }}][content][{{ $section }}]"
                                    rows="3"
                                    placeholder="Content for {{ $section }}"></textarea>
                            </div>
                            @endforeach
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.email-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            {{-- Variable Reference --}}
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title"><i class="fas fa-code"></i> Available Variables</h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($variables as $var => $description)
                        <div class="list-group-item">
                            <code class="variable-code" style="cursor: pointer;" title="Click to copy">
                                {{'{{'}}{{ $var }}{{'}}'}}
                            </code>
                            <br>
                            <small class="text-muted">{{ $description }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Preview --}}
            <div class="card mt-3">
                <div class="card-header bg-success">
                    <h3 class="card-title"><i class="fas fa-eye"></i> Preview</h3>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-block" id="preview-btn">
                        <i class="fas fa-eye"></i> Preview Email
                    </button>
                    <small class="text-muted d-block mt-2">
                        Preview with sample data
                    </small>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Copy variable to clipboard on click
        $('.variable-code').click(function() {
            const text = $(this).text();
            navigator.clipboard.writeText(text).then(() => {
                toastr.success('Variable copied to clipboard!');
            });
        });

        // Preview functionality
        $('#preview-btn').click(function() {
            const activeTab = $('.tab-pane.active');
            const locale = activeTab.attr('id').replace('locale-', '');

            $.get(`{{ route('admin.email-templates.preview', $template) }}?locale=${locale}`)
                .done(function(response) {
                    // Show preview in modal
                    const modal = `
                    <div class="modal fade" id="preview-modal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Email Preview (${locale.toUpperCase()})</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <strong>Subject:</strong> ${response.subject}
                                    </div>
                                    <hr>
                                    <div class="preview-content">
                                        ${Object.entries(response.sections).map(([key, value]) => `
                                            <div class="mb-3">
                                                <strong>${key.replace(/_/g, ' ')}:</strong>
                                                <p>${value}</p>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    $('#preview-modal').remove();
                    $('body').append(modal);
                    $('#preview-modal').modal('show');
                })
                .fail(function() {
                    toastr.error('Failed to load preview');
                });
        });
    });
</script>
@stop