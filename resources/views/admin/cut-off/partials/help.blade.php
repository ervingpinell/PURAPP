<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="mb-2">{{ __('m_config.cut-off.help.title') }}</h5>
    <ol class="mb-3">
      <li><strong>{{ __('m_config.cut-off.tabs.global') }}:</strong> {{ __('m_config.cut-off.help.global') }}</li>
      <li><strong>{{ __('m_config.cut-off.tabs.tour') }}:</strong> {{ __('m_config.cut-off.help.tour') }}</li>
      <li><strong>{{ __('m_config.cut-off.tabs.schedule') }}:</strong> {{ __('m_config.cut-off.help.schedule') }}</li>
    </ol>
    <div class="alert alert-info mb-0">
      <strong>{{ __('m_config.cut-off.help.precedence') }}:</strong>
      <span class="badge badge-override me-1">{{ __('m_config.cut-off.badges.schedule') }}</span> ➜
      <span class="badge badge-override me-1">{{ __('m_config.cut-off.badges.tour') }}</span> ➜
      <span class="badge badge-global me-1">{{ __('m_config.cut-off.badges.global') }}</span> ➜
      <code>config/booking.php</code>.
    </div>
  </div>
</div>
