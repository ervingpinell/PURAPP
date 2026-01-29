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
    <div class="col-lg-9">
        <form action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data" id="brandingForm">
            @csrf

            <div class="card card-primary card-outline card-outline-tabs">
                <div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="brandingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="colors-tab" data-toggle="pill" href="#colors" role="tab">
                                <i class="fas fa-palette"></i> Colors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="logos-tab" data-toggle="pill" href="#logos" role="tab">
                                <i class="fas fa-image"></i> Logos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="hero-tab" data-toggle="pill" href="#hero" role="tab">
                                <i class="fas fa-star"></i> Hero
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="seo-tab" data-toggle="pill" href="#seo" role="tab">
                                <i class="fas fa-search"></i> SEO
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reviews-embed-tab" data-toggle="pill" href="#reviews-embed" role="tab">
                                <i class="fas fa-star"></i> Reviews Embed
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="layout-tab" data-toggle="pill" href="#layout" role="tab">
                                <i class="fas fa-cog"></i> Layout & Effects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="text-shadow-tab" data-toggle="pill" href="#text-shadow" role="tab">
                                <i class="fas fa-font"></i> Text Shadow
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="preview-tab" data-toggle="pill" href="#preview" role="tab">
                                <i class="fas fa-eye"></i> Preview
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="brandingTabContent">
                        
                        <!-- COLORS TAB -->
                        <div class="tab-pane fade show active" id="colors" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-palette text-primary"></i> Color Variables</h4>
                            <p class="text-muted">Customize all color variables used throughout the site.</p>
                            <div class="row">
                                @foreach($settings->get('colors', []) as $setting)
                                    <div class="col-md-6 mb-3">
                                        <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                        <div class="input-group">
                                            <input type="color" 
                                                   class="form-control form-control-color color-picker-input" 
                                                   data-target="{{ $setting->key }}" 
                                                   value="{{ $setting->value }}"
                                                   style="max-width: 80px;">
                                            <input type="text" 
                                                   class="form-control color-text-input" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ $setting->value }}" 
                                                   placeholder="#000000"
                                                   autocomplete="off"
                                                   pattern="^#[0-9A-Fa-f]{6}$">
                                        </div>
                                        @if($setting->description)
                                            <small class="text-muted">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- LOGOS TAB -->
                        <div class="tab-pane fade" id="logos" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-image text-info"></i> Logo Settings</h4>
                            <p class="text-muted">Upload and manage your brand logos.</p>
                            @foreach($settings->get('logos', []) as $setting)
                                <div class="mb-4">
                                    <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    
                                    @if($setting->value)
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

                        <!-- HERO TAB -->
                        <div class="tab-pane fade" id="hero" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-star text-purple"></i> Hero Section</h4>
                            <p class="text-muted">Customize the hero section on your home page.</p>
                            
                            @foreach($settings->get('hero', []) as $setting)
                                @if($setting->type === 'text' && !str_contains($setting->key, 'button'))
                                    <div class="mb-3">
                                        <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                        @if(str_contains($setting->key, 'subtitle'))
                                            <textarea class="form-control" 
                                                      id="{{ $setting->key }}" 
                                                      name="settings[{{ $setting->key }}]" 
                                                      rows="2">{{ $setting->value }}</textarea>
                                        @else
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ $setting->value }}">
                                        @endif
                                        @if($setting->description)
                                            <small class="text-muted">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endif
                            @endforeach

                            <div class="row">
                                @foreach($settings->get('hero', []) as $setting)
                                    @if($setting->type === 'text' && str_contains($setting->key, 'button'))
                                        <div class="col-md-6 mb-3">
                                            <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ $setting->value }}">
                                            @if($setting->description)
                                                <small class="text-muted">{{ $setting->description }}</small>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @foreach($settings->get('hero', []) as $setting)
                                @if($setting->type === 'file')
                                    <div class="mb-4">
                                        <label for="{{ $setting->key }}">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                        
                                        @if($setting->value && file_exists(public_path($setting->value)))
                                            <div class="mb-2">
                                                <img src="{{ asset($setting->value) }}" alt="{{ $setting->key }}" style="max-height: 200px; max-width: 100%;" class="img-thumbnail">
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

                        <!-- SEO TAB -->
                        <div class="tab-pane fade" id="seo" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-search text-success"></i> SEO Settings</h4>
                            <p class="text-muted">Edit SEO content in Spanish. Translations to other languages will be generated automatically using DeepL.</p>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-language"></i> 
                                <strong>Auto-translation:</strong> When you save, your Spanish content will be automatically translated to English, French, German, and Portuguese.
                            </div>

                            <!-- Home Page SEO -->
                            <h5 class="mt-4"><i class="fas fa-home"></i> Home Page</h5>
                            <div class="mb-3">
                                <label for="seo_home_title_es">Title (Spanish)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_home_title_es"
                                       name="settings[seo_home_title_es]" 
                                       value="{{ branding('seo_home_title_es', '') }}"
                                       maxlength="60"
                                       oninput="updateCharCount(this, 'seo_home_title_count', 60)">
                                <small class="text-muted">
                                    <span id="seo_home_title_count">{{ strlen(branding('seo_home_title_es', '')) }}</span>/60 characters (recommended: 50-60)
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_home_description_es">Description (Spanish)</label>
                                <textarea class="form-control" 
                                          id="seo_home_description_es"
                                          name="settings[seo_home_description_es]" 
                                          rows="2"
                                          maxlength="160"
                                          oninput="updateCharCount(this, 'seo_home_description_count', 160)">{{ branding('seo_home_description_es', '') }}</textarea>
                                <small class="text-muted">
                                    <span id="seo_home_description_count">{{ strlen(branding('seo_home_description_es', '')) }}</span>/160 characters (recommended: 150-160)
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_home_keywords_es">Keywords (Spanish)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_home_keywords_es"
                                       name="settings[seo_home_keywords_es]" 
                                       value="{{ branding('seo_home_keywords_es', '') }}"
                                       maxlength="200"
                                       placeholder="e.g., transporte privado, La Fortuna, Costa Rica, traslados"
                                       oninput="updateCharCount(this, 'seo_home_keywords_count', 200)">
                                <small class="text-muted">
                                    <span id="seo_home_keywords_count">{{ strlen(branding('seo_home_keywords_es', '')) }}</span>/200 characters (recommended: 150-200)
                                </small>
                            </div>

                            <!-- Products Page SEO -->
                            <h5 class="mt-4"><i class="fas fa-map-marked-alt"></i> Products Page</h5>
                            <div class="mb-3">
                                <label for="seo_tours_title_es">Title (Spanish)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_tours_title_es"
                                       name="settings[seo_tours_title_es]" 
                                       value="{{ branding('seo_tours_title_es', '') }}"
                                       maxlength="60"
                                       oninput="updateCharCount(this, 'seo_tours_title_count', 60)">
                                <small class="text-muted">
                                    <span id="seo_tours_title_count">{{ strlen(branding('seo_tours_title_es', '')) }}</span>/60 characters
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_tours_description_es">Description (Spanish)</label>
                                <textarea class="form-control" 
                                          id="seo_tours_description_es"
                                          name="settings[seo_tours_description_es]" 
                                          rows="2"
                                          maxlength="160"
                                          oninput="updateCharCount(this, 'seo_tours_description_count', 160)">{{ branding('seo_tours_description_es', '') }}</textarea>
                                <small class="text-muted">
                                    <span id="seo_tours_description_count">{{ strlen(branding('seo_tours_description_es', '')) }}</span>/160 characters
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_tours_keywords_es">Keywords (Spanish)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_tours_keywords_es"
                                       name="settings[seo_tours_keywords_es]" 
                                       value="{{ branding('seo_tours_keywords_es', '') }}"
                                       maxlength="200"
                                       placeholder="e.g., products La Fortuna, actividades, volcán Arenal, aventura"
                                       oninput="updateCharCount(this, 'seo_tours_keywords_count', 200)">
                                <small class="text-muted">
                                    <span id="seo_tours_keywords_count">{{ strlen(branding('seo_tours_keywords_es', '')) }}</span>/200 characters
                                </small>
                            </div>

                            <!-- Contact Page SEO -->
                            <h5 class="mt-4"><i class="fas fa-envelope"></i> Contact Page</h5>
                            <div class="mb-3">
                                <label for="seo_contact_title_es">Title (Spanish)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_contact_title_es"
                                       name="settings[seo_contact_title_es]" 
                                       value="{{ branding('seo_contact_title_es', '') }}"
                                       maxlength="60"
                                       oninput="updateCharCount(this, 'seo_contact_title_count', 60)">
                                <small class="text-muted">
                                    <span id="seo_contact_title_count">{{ strlen(branding('seo_contact_title_es', '')) }}</span>/60 characters
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_contact_description_es">Description (Spanish)</label>
                                <textarea class="form-control" 
                                          id="seo_contact_description_es"
                                          name="settings[seo_contact_description_es]" 
                                          rows="2"
                                          maxlength="160"
                                          oninput="updateCharCount(this, 'seo_contact_description_count', 160)">{{ branding('seo_contact_description_es', '') }}</textarea>
                                <small class="text-muted">
                                    <span id="seo_contact_description_count">{{ strlen(branding('seo_contact_description_es', '')) }}</span>/160 characters
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="seo_contact_keywords_es">Keywords (Spanish)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="seo_contact_keywords_es"
                                       name="settings[seo_contact_keywords_es]" 
                                       value="{{ branding('seo_contact_keywords_es', '') }}"
                                       maxlength="200"
                                       placeholder="e.g., contacto, reservas, cotización, transporte privado"
                                       oninput="updateCharCount(this, 'seo_contact_keywords_count', 200)">
                                <small class="text-muted">
                                    <span id="seo_contact_keywords_count">{{ strlen(branding('seo_contact_keywords_es', '')) }}</span>/200 characters
                                </small>
                            </div>
                        </div>

                        <!-- REVIEWS EMBED TAB -->
                        <div class="tab-pane fade" id="reviews-embed" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-star text-warning"></i> Reviews Embed (iFrame)</h4>
                            <p class="text-muted">Customize colors for the embedded reviews iframe displayed on external sites.</p>
                            <div class="row">
                                @foreach($settings->get('reviews_embed', []) as $setting)
                                    <div class="col-md-6 mb-3">
                                        <label for="{{ $setting->key }}">{{ ucwords(str_replace(['reviews_embed_', '_'], ['', ' '], $setting->key)) }}</label>
                                        <div class="input-group">
                                            <input type="color" 
                                                   class="form-control form-control-color color-picker-input" 
                                                   data-target="{{ $setting->key }}" 
                                                   value="{{ $setting->value }}"
                                                   style="max-width: 80px;">
                                            <input type="text" 
                                                   class="form-control color-text-input" 
                                                   id="{{ $setting->key }}" 
                                                   name="settings[{{ $setting->key }}]" 
                                                   value="{{ $setting->value }}" 
                                                   placeholder="#000000"
                                                   autocomplete="off"
                                                   pattern="^#[0-9A-Fa-f]{6}$">
                                        </div>
                                        @if($setting->description)
                                            <small class="text-muted">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- LAYOUT & EFFECTS TAB -->
                        <div class="tab-pane fade" id="layout" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-cog text-success"></i> Layout & Effects</h4>
                            <p class="text-muted">Configure layout options and visual effects.</p>
                            
                            <h5 class="mt-4 mb-3">Layout Options</h5>
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

                            <h5 class="mt-4 mb-3">Visual Effects</h5>
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

                        <!-- TEXT SHADOW TAB -->
                        <div class="tab-pane fade" id="text-shadow" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-font text-dark"></i> Text Shadow Configuration</h4>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Configure text shadow for headings and special text elements across your site.
                            </div>

                            @php
                                $textShadowSettings = $settings->get('text_shadow', collect());
                                $shadowProps = ['text_shadow_color', 'text_shadow_x', 'text_shadow_y', 'text_shadow_blur', 'text_shadow_opacity'];
                                $shadowToggles = ['text_shadow_enabled', 'text_shadow_headings', 'text_shadow_big_title', 'text_shadow_lead', 'text_shadow_text_muted', 'text_shadow_breadcrumbs'];
                            @endphp

                            <!-- Shadow Properties -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Shadow Properties</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($shadowProps as $propKey)
                                        @php $setting = $textShadowSettings->where('key', $propKey)->first(); @endphp
                                        @if($setting)
                                            @if($setting->type === 'color')
                                                <div class="form-group">
                                                    <label for="{{ $setting->key }}">Shadow Color</label>
                                                    <div class="input-group">
                                                        <input type="color" 
                                                            class="form-control color-picker" 
                                                            data-target="{{ $setting->key }}_text"
                                                            value="{{ $setting->value }}">
                                                        <input type="text" 
                                                            class="form-control color-text-input" 
                                                            id="{{ $setting->key }}_text"
                                                            name="settings[{{ $setting->key }}]"
                                                            value="{{ $setting->value }}"
                                                            placeholder="#000000"
                                                            autocomplete="off">
                                                    </div>
                                                    @if($setting->description)
                                                        <small class="text-muted">{{ $setting->description }}</small>
                                                    @endif
                                                </div>
                                            @elseif($setting->type === 'number')
                                                <div class="form-group">
                                                    <label for="{{ $setting->key }}">
                                                        @if($setting->key === 'text_shadow_x') Horizontal Offset (X)
                                                        @elseif($setting->key === 'text_shadow_y') Vertical Offset (Y)
                                                        @elseif($setting->key === 'text_shadow_blur') Blur Radius
                                                        @elseif($setting->key === 'text_shadow_opacity') Opacity
                                                        @endif
                                                        <span class="badge badge-secondary" id="{{ $setting->key }}_value">{{ $setting->value }}</span>
                                                    </label>
                                                    <input type="range" 
                                                        class="form-control-range" 
                                                        id="{{ $setting->key }}" 
                                                        name="settings[{{ $setting->key }}]"
                                                        value="{{ $setting->value }}"
                                                        min="{{ $setting->key === 'text_shadow_opacity' ? '0' : '-10' }}"
                                                        max="{{ $setting->key === 'text_shadow_opacity' ? '1' : '20' }}"
                                                        step="{{ $setting->key === 'text_shadow_opacity' ? '0.05' : '1' }}"
                                                        oninput="document.getElementById('{{ $setting->key }}_value').textContent = this.value">
                                                    @if($setting->description)
                                                        <small class="text-muted">{{ $setting->description }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Apply To Elements -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Apply Shadow To</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($shadowToggles as $toggleKey)
                                        @php $toggle = $textShadowSettings->where('key', $toggleKey)->first(); @endphp
                                        @if($toggle)
                                            <div class="form-group">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                        class="custom-control-input" 
                                                        id="{{ $toggle->key }}"
                                                        name="settings[{{ $toggle->key }}]"
                                                        value="1"
                                                        {{ $toggle->value == '1' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="{{ $toggle->key }}" style="pointer-events: none;">
                                                        <strong>
                                                            @if($toggle->key === 'text_shadow_enabled') Enable Text Shadow
                                                            @elseif($toggle->key === 'text_shadow_headings') Headings (h1-h6)
                                                            @elseif($toggle->key === 'text_shadow_big_title') Big Title
                                                            @elseif($toggle->key === 'text_shadow_lead') Lead Text
                                                            @elseif($toggle->key === 'text_shadow_text_muted') Text Muted
                                                            @elseif($toggle->key === 'text_shadow_breadcrumbs') Breadcrumbs
                                                            @endif
                                                        </strong>
                                                    </label>
                                                </div>
                                                @if($toggle->description)
                                                    <small class="text-muted d-block ml-4">{{ $toggle->description }}</small>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="alert alert-success">
                                <strong>Preview:</strong>
                                <div style="margin-top: 1rem; padding: 2rem; background: #f8f9fa; border-radius: 8px;">
                                    @php
                                        $previewShadow = branding('text_shadow_x', '2') . 'px ' . 
                                                        branding('text_shadow_y', '2') . 'px ' . 
                                                        branding('text_shadow_blur', '4') . 'px rgba(0,0,0,' . 
                                                        branding('text_shadow_opacity', '0.5') . ')';
                                    @endphp
                                    <h1 style="text-shadow: {{ $previewShadow }};">Heading Example</h1>
                                    <p class="lead" style="text-shadow: {{ $previewShadow }};">Lead text example with shadow</p>
                                    <p class="text-muted" style="text-shadow: {{ $previewShadow }};">Muted text example</p>
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb" style="background: transparent; padding: 0;">
                                            <li class="breadcrumb-item" style="text-shadow: {{ $previewShadow }};"><a href="#" style="text-shadow: {{ $previewShadow }};">Home</a></li>
                                            <li class="breadcrumb-item active" style="text-shadow: {{ $previewShadow }};">Current</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <!-- PREVIEW TAB -->
                        <div class="tab-pane fade" id="preview" role="tabpanel">
                            <h4 class="mb-3"><i class="fas fa-eye text-dark"></i> Live Preview</h4>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Note:</strong> Changes will be applied site-wide after saving. Clear your browser cache if changes don't appear immediately.
                            </div>

                            <h5>Current Colors</h5>
                            <div class="row mb-4">
                                <div class="col-6 col-md-3 mb-2">
                                    <div class="p-2 text-center" style="background-color: {{ branding('color_page_title', '#2c6e49') }}; color: white; border-radius: 4px;">
                                        Page Title
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-2">
                                    <div class="p-2 text-center" style="background-color: {{ branding('color_button_primary', '#256d1b') }}; color: white; border-radius: 4px;">
                                        Primary Button
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-2">
                                    <div class="p-2 text-center" style="background-color: {{ branding('color_card_accent', '#e74c3c') }}; color: white; border-radius: 4px;">
                                        Card Accent
                                    </div>
                                </div>
                                <div class="col-6 col-md-3 mb-2">
                                    <div class="p-2 text-center" style="background-color: {{ branding('color_surface_dark', '#0f2419') }}; color: white; border-radius: 4px;">
                                        Surface Dark
                                    </div>
                                </div>
                            </div>

                            <h5>Quick Stats</h5>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-palette text-primary"></i> <strong>{{ $settings->get('colors', collect())->count() }}</strong> Color variables</li>
                                <li><i class="fas fa-image text-info"></i> <strong>{{ $settings->get('logos', collect())->count() }}</strong> Logo settings</li>
                                <li><i class="fas fa-star text-purple"></i> <strong>{{ $settings->get('hero', collect())->count() }}</strong> Hero settings</li>
                                <li><i class="fas fa-toggle-on text-success"></i> <strong>{{ $settings->get('layout', collect())->count() }}</strong> Layout options</li>
                                <li><i class="fas fa-magic text-warning"></i> <strong>{{ $settings->get('effects', collect())->count() }}</strong> Visual effects</li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Branding Settings
                    </button>
                    <a href="{{ route('admin.branding.export.current') }}" class="btn btn-warning btn-lg" title="Download current configuration as JSON">
                        <i class="fas fa-download"></i> Export Current Config
                    </a>
                    <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#importTemplateModal">
                        <i class="fas fa-upload"></i> Import Template
                    </button>
                    <a href="{{ route('admin.home') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Quick Access Sidebar -->
    <div class="col-lg-3">
        <div class="card card-dark sticky-top" style="top: 20px;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-rocket"></i> Quick Access</h3>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#colors" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-palette text-primary"></i> Colors
                    </a>
                    <a href="#logos" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-image text-info"></i> Logos
                    </a>
                    <a href="#hero" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-star text-purple"></i> Hero
                    </a>
                    <a href="#seo" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-search text-success"></i> SEO
                    </a>
                    <a href="#layout" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-cog text-success"></i> Layout & Effects
                    </a>
                    <a href="#preview" class="list-group-item list-group-item-action" data-toggle="pill">
                        <i class="fas fa-eye text-dark"></i> Preview
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Import Template Modal -->
<div class="modal fade" id="importTemplateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.branding.templates.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-upload"></i> Import Template</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="template_file">Configuration File (JSON) *</label>
                        <div class="custom-file">
                            <input type="file" 
                                   class="custom-file-input" 
                                   id="template_file" 
                                   name="template_file" 
                                   accept=".json"
                                   required>
                            <label class="custom-file-label" for="template_file">Choose JSON file or drag here...</label>
                        </div>
                        <small class="text-muted">Upload a branding configuration JSON file (exported previously)</small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Warning:</strong> Importing will immediately apply this configuration to your branding system, overwriting current settings.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload"></i> Import Template
                    </button>
                </div>
            </form>
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
    .nav-tabs .nav-link {
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        font-weight: 600;
    }
</style>
@stop

@section('js')
<script>
    // Bidirectional sync between color picker and text input
    
    // Update text input when color picker changes
    document.querySelectorAll('.color-picker-input').forEach(function(colorPicker) {
        colorPicker.addEventListener('input', function() {
            var targetId = this.getAttribute('data-target');
            var textInput = document.getElementById(targetId);
            if (textInput) {
                textInput.value = this.value.toUpperCase();
            }
        });
        
        colorPicker.addEventListener('change', function() {
            var targetId = this.getAttribute('data-target');
            var textInput = document.getElementById(targetId);
            if (textInput) {
                textInput.value = this.value.toUpperCase();
            }
        });
    });
    
    // Update color picker when text input changes
    document.querySelectorAll('.color-text-input').forEach(function(textInput) {
        textInput.addEventListener('input', function() {
            var value = this.value.trim().toUpperCase();
            var colorPicker = this.previousElementSibling;
            
            // Validate hex color format
            if (/^#[0-9A-F]{6}$/.test(value) && colorPicker) {
                colorPicker.value = value;
            }
        });
        
        // Also update on blur (when user finishes typing)
        textInput.addEventListener('blur', function() {
            var value = this.value.trim();
            var colorPicker = this.previousElementSibling;
            
            // If it's a valid hex without #, add it
            if (/^[0-9A-Fa-f]{6}$/.test(value)) {
                value = '#' + value.toUpperCase();
                this.value = value;
                if (colorPicker) {
                    colorPicker.value = value;
                }
            } else if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                // Just normalize to uppercase
                this.value = value.toUpperCase();
                if (colorPicker) {
                    colorPicker.value = value.toUpperCase();
                }
            }
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

    // Remember last active tab and setup toggle debugging
    $(document).ready(function() {
        console.log('🔵 DOM Ready - Setting up toggle listeners...');
        
        // Debug: Test if toggles are clickable
        const checkboxes = document.querySelectorAll('.custom-control-input[type="checkbox"]');
        console.log('🔵 Found ' + checkboxes.length + ' checkboxes');
        
        checkboxes.forEach(function(checkbox) {
            console.log('🔵 Checkbox:', checkbox.id, 'Initial checked:', checkbox.checked);
            
            checkbox.addEventListener('click', function(e) {
                console.log('🟢 CLICK on', this.id, '- Before:', !this.checked, '→ After:', this.checked);
            });
            
            checkbox.addEventListener('change', function(e) {
                console.log('🟡 CHANGE on', this.id, '- New value:', this.checked);
            });
        });
        
        // Restore last active tab
        var lastTab = localStorage.getItem('brandingActiveTab');
        if (lastTab) {
            $('#brandingTabs a[href="' + lastTab + '"]').tab('show');
        }

        // Save active tab on change
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            localStorage.setItem('brandingActiveTab', $(e.target).attr('href'));
        });
    });

    // Character counter function for SEO fields
    function updateCharCount(input, counterId, maxLength) {
        const count = input.value.length;
        document.getElementById(counterId).textContent = count;
        
        // Visual feedback
        const counter = document.getElementById(counterId);
        if (count > maxLength * 0.9) {
            counter.style.color = '#e74c3c'; // Red when near limit
        } else if (count > maxLength * 0.7) {
            counter.style.color = '#f39c12'; // Orange when getting close
        } else {
            counter.style.color = '#27ae60'; // Green when good
        }
    }

    // Show success message if exists
    @if(session('success'))
        $(document).ready(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ session('success') }}');
            } else {
                alert('{{ session('success') }}');
            }
        });
    @endif
    
    // Show error message if exists
    @if(session('error'))
        $(document).ready(function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ session('error') }}');
            } else {
                alert('{{ session('error') }}');
            }
        });
    @endif
</script>
@stop
