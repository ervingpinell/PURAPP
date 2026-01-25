{{-- Hero Section --}}
<section class="hero-section">
    <div class="hero-overlay"></div>
    
    @if(branding('hero_image'))
        <div class="hero-background" style="background-image: url('{{ asset(branding('hero_image')) }}');"></div>
    @endif
    
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">{{ branding('hero_title', 'Welcome to Your Adventure') }}</h1>
            <p class="hero-subtitle">{{ branding('hero_subtitle', 'Discover amazing experiences') }}</p>
            
            @if(branding('hero_button_text') && branding('hero_button_link'))
                <div class="hero-buttons">
                    <a href="{{ branding('hero_button_link', '/tours') }}" class="btn btn-hero-primary">
                        {{ branding('hero_button_text', 'Explore Tours') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
