{{ str_starts_with(app()->getLocale() ?? 'es', 'es') ? 'Nuevo mensaje de contacto' : 'New contact message' }}

{{ (str_starts_with(app()->getLocale() ?? 'es', 'es') ? 'Nombre' : 'Name') }}: {{ $name }}
Email: {{ $email }}
{{ (str_starts_with(app()->getLocale() ?? 'es', 'es') ? 'Asunto' : 'Subject') }}: {{ $subjectLine }}

---
{{ (str_starts_with(app()->getLocale() ?? 'es', 'es') ? 'Mensaje' : 'Message') }}:

{{ $messageText }}
