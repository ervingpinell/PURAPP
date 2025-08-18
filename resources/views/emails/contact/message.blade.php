@component('mail::message')
# Nuevo mensaje de contacto

**Nombre:** {{ $name }}
**Email:** {{ $email }}
**Asunto:** {{ $subjectLine }}

---

{{ $messageText }}

@endcomponent
