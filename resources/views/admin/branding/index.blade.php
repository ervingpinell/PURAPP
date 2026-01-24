@extends('adminlte::page')

@section('title', 'Branding Management')

@section('content_header')
    <h1>
        <i class="fas fa-palette"></i> Branding Management
    </h1>
@stop

@section('content')
<div class="row">
    <!-- Main Form Column -->
    <div class="col-lg-8">
        <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Colors Section -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-palette"></i> Color Settings</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($settings->get('colors', []) as $setting)
                            <div class="col-md-6 mb-3">
                                <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="{{ $setting->key }}" 
                                           name="settings[{{ $setting->key }}]" 
                                           value="{{ $setting->value }}"
                                           style="max-width: 80px;">
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $setting->value }}" 
                                           readonly>
                                </div>
                                @if($setting->description)
                                    <small class="text-muted">{{ $setting->description }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Logos Section -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-image"></i> Logo Settings</h3>
                </div>
                <div class="card-body">
                    @foreach($settings->get('logos', []) as $setting)
                        <div class="mb-4">
                            <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            
                            @if($setting->value && file_exists(public_path($setting->value)))
                                <div class="mb-2">
                                    <img src="{{ asset($setting->value) }}" alt="{{ $setting->key }}" style="max-height: 80px;" class="img-thumbnail">
                                </div>
                            @endif
                            
                            <div class="custom-file">
                                <input type="file" 
                                       class="custom-file-input" 
                                       id="{{ $setting->key }}" 
                                       name="settings[{{ $setting->key }}]"
                                       accept="image/*">
                                <label class="custom-file-label" for="{{ $setting->key }}">Choose file...</label>
                            </div>
                            
                            @if($setting->description)
                                <small class="text-muted d-block mt-1">{{ $setting->description }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Layout Section -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layout"></i> Layout Settings</h3>
                </div>
                <div class="card-body">
                    @foreach($settings->get('layout', []) as $setting)
                        @if($setting->type === 'boolean')
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="{{ $setting->key }}" 
                                       name="settings[{{ $setting->key }}]"
                                       value="1"
                                       {{ $setting->value == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="{{ $setting->key }}">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                                @if($setting->description)
                                    <br><small class="text-muted">{{ $setting->description }}</small>
                                @endif
                            </div>
                        @elseif($setting->type === 'file')
                            <div class="mb-4">
                                <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                
                                @if($setting->value && file_exists(public_path($setting->value)))
                                    <div class="mb-2">
                                        <img src="{{ asset($setting->value) }}" alt="{{ $setting->key }}" style="max-height: 150px;" class="img-thumbnail">
                                    </div>
                                @endif
                                
                                <div class="custom-file">
                                    <input type="file" 
                                           class="custom-file-input" 
                                           id="{{ $setting->key }}" 
                                           name="settings[{{ $setting->key }}]"
                                           accept="image/*">
                                    <label class="custom-file-label" for="{{ $setting->key }}">Choose file...</label>
                                </div>
                                
                                @if($setting->description)
                                    <small class="text-muted d-block mt-1">{{ $setting->description }}</small>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Effects Section -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-magic"></i> Visual Effects</h3>
                </div>
                <div class="card-body">
                    @foreach($settings->get('effects', []) as $setting)
                        @if($setting->type === 'boolean')
                            <div class="custom-control custom-switch mb-3">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="{{ $setting->key }}" 
                                       name="settings[{{ $setting->key }}]"
                                       value="1"
                                       {{ $setting->value == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="{{ $setting->key }}">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                </label>
                                @if($setting->description)
                                    <br><small class="text-muted">{{ $setting->description }}</small>
                                @endif
                            </div>
                        @elseif($setting->type === 'number')
                            <div class="mb-3">
                                <label for="{{ $setting->key }}">
                                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    <span class="badge badge-secondary" id="{{ $setting->key }}_value">{{ $setting->value }}</span>
                                </label>
                                <input type="range" 
                                       class="custom-range" 
                                       id="{{ $setting->key }}" 
                                       name="settings[{{ $setting->key }}]"
                                       value="{{ $setting->value }}"
                                       min="{{ str_contains($setting->key, 'opacity') ? '0' : '0' }}"
                                       max="{{ str_contains($setting->key, 'opacity') ? '1' : '30' }}"
                                       step="{{ str_contains($setting->key, 'opacity') ? '0.05' : '1' }}"
                                       oninput="document.getElementById('{{ $setting->key }}_value').textContent = this.value">
                                @if($setting->description)
                                    <small class="text-muted d-block">{{ $setting->description }}</small>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Branding Settings
                    </button>
                    <a href="{{ route('admin.home') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Live Preview Column -->
    <div class="col-lg-4">
        <div class="card card-dark sticky-top" style="top: 20px;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye"></i> Live Preview</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> Changes will be applied site-wide after saving. Clear your browser cache if changes don't appear immediately.
                </div>

                <h5>Current Colors</h5>
                <div class="row">
                    <div class="col-6 mb-2">
                        <div class="p-2 text-center" style="background-color: {{ branding('color_page_title', '#2c6e49') }}; color: white; border-radius: 4px;">
                            Page Title
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="p-2 text-center" style="background-color: {{ branding('color_button_primary', '#256d1b') }}; color: white; border-radius: 4px;">
                            Primary Button
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="p-2 text-center" style="background-color: {{ branding('color_card_accent', '#e74c3c') }}; color: white; border-radius: 4px;">
                            Card Accent
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <div class="p-2 text-center" style="background-color: {{ branding('color_surface_dark', '#0f2419') }}; color: white; border-radius: 4px;">
                            Surface Dark
                        </div>
                    </div>
                </div>

                <h5 class="mt-3">Quick Stats</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-palette text-primary"></i> <strong>{{ $settings->get('colors', collect())->count() }}</strong> Color variables</li>
                    <li><i class="fas fa-image text-info"></i> <strong>{{ $settings->get('logos', collect())->count() }}</strong> Logo settings</li>
                    <li><i class="fas fa-toggle-on text-success"></i> <strong>{{ $settings->get('layout', collect())->count() }}</strong> Layout options</li>
                    <li><i class="fas fa-magic text-warning"></i> <strong>{{ $settings->get('effects', collect())->count() }}</strong> Visual effects</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .sticky-top {
        position: sticky;
        top: 20px;
        z-index: 1020;
    }
    .form-control-color {
        height: 38px;
        padding: 2px;
    }
</style>
@stop

@section('js')
<script>
    // Update color input text when color picker changes
    document.querySelectorAll('input[type="color"]').forEach(function(colorInput) {
        colorInput.addEventListener('input', function() {
            this.nextElementSibling.value = this.value;
        });
    });

    // Update file input label when file is selected
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file...';
            var label = e.target.nextElementSibling;
            label.textContent = fileName;
        });
    });

    // Show success message if exists
    @if(session('success'))
        $(document).ready(function() {
            toastr.success('{{ session('success') }}');
        });
    @endif
</script>
@stop
