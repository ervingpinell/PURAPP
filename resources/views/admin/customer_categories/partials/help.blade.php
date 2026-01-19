<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-question-circle"></i> {{ __('customer_categories.help.title') }}
        </h3>
    </div>
    <div class="card-body">
        <h5>{{ __('Ayuda') }}</h5>
        <div class="mb-3">
            <strong>Full Slug (Identificador)</strong>
            <p class="small text-muted mb-0">
                El slug es el identificador único que se utiliza en la URL. Debe ser todo minúsculas, sin espacios (usar guiones <code>-</code>) y único.
                <br>
                <em>Ejemplo: <code>adulto-mayor</code>, <code>nino-3-12</code></em>
            </p>
        </div>

        <hr>

        <h5>{{ __('customer_categories.help.examples_title') }}</h5>

        <div class="mb-3">
            <strong>{{ __('customer_categories.help.infant') }}</strong>
            <ul class="mb-2">
                <li>{{ __('customer_categories.help.age_from_tip') }} <code>0</code></li>
                <li>{{ __('customer_categories.help.age_to_tip') }} <code>2</code></li>
                <li>{{ __('customer_categories.help.range_tip') }} 0-2</li>
            </ul>
        </div>

        <div class="mb-3">
            <strong>{{ __('customer_categories.help.child') }}</strong>
            <ul class="mb-2">
                <li>{{ __('customer_categories.help.age_from_tip') }} <code>3</code></li>
                <li>{{ __('customer_categories.help.age_to_tip') }} <code>12</code></li>
                <li>{{ __('customer_categories.help.range_tip') }} 3-12</li>
            </ul>
        </div>

        <div class="mb-3">
            <strong>{{ __('customer_categories.help.adult') }}</strong>
            <ul class="mb-2">
                <li>{{ __('customer_categories.help.age_from_tip') }} <code>13</code></li>
                <li>{{ __('customer_categories.help.age_to_tip') }} <code>64</code></li>
                <li>{{ __('customer_categories.help.range_tip') }} 13-64</li>
            </ul>
        </div>

        <div class="mb-3">
            <strong>{{ __('customer_categories.help.senior') }}</strong>
            <ul class="mb-2">
                <li>{{ __('customer_categories.help.age_from_tip') }} <code>65</code></li>
                <li>{{ __('customer_categories.help.age_to_tip') }} <code>NULL</code></li>
                <li>{{ __('customer_categories.help.range_tip') }} 65+</li>
            </ul>
        </div>

        <hr>

        <h5>{{ __('customer_categories.rules.title') }}</h5>
        <ul class="small">
            <li>{{ __('customer_categories.rules.no_overlap') }}</li>
            <li>{{ __('customer_categories.rules.no_upper_limit_hint') }}</li>
            <li>{{ __('customer_categories.rules.slug_unique') }}</li>
            <li>{{ __('customer_categories.rules.order_affects_display') }}</li>
        </ul>
    </div>
</div>

<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exclamation-triangle"></i> {{ __('customer_categories.alerts.warning_title') }}
        </h3>
    </div>
    <div class="card-body">
        <p class="small mb-0">
            {{ __('customer_categories.alerts.warning_text') }}
        </p>
    </div>
</div>
