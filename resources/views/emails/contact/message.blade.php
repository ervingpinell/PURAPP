{{-- resources/views/emails/contact/message.blade.php --}}
@extends('emails.layouts.base', [
    'mailLocale' => $mailLocale ?? app()->getLocale(),
    'title'      => (str_starts_with(($mailLocale ?? app()->getLocale()), 'es')
                        ? 'Nuevo mensaje de contacto'
                        : 'New contact message'),
    'company'    => $company ?? config('app.name', 'Green Vacations CR'),
])

@section('content')
  @php
    $loc  = strtolower($mailLocale ?? app()->getLocale());
    $isEs = str_starts_with($loc, 'es');
  @endphp

  <h1 class="title">
    {{ $isEs ? 'Nuevo mensaje de contacto' : 'New contact message' }}
  </h1>

  <div class="section-card">
    <div class="section-title">
      {{ $isEs ? 'Detalles del contacto' : 'Contact details' }}
    </div>

    <table class="data-table" role="presentation">
      <tr>
        <td style="width: 30%; font-weight: 600;">
          {{ $isEs ? 'Nombre' : 'Name' }}
        </td>
        <td>
          {{ $name }}
        </td>
      </tr>
      <tr>
        <td style="width: 30%; font-weight: 600;">
          Email
        </td>
        <td>
          {{ $email }}
        </td>
      </tr>
      @if(!empty($subjectLine))
        <tr>
          <td style="width: 30%; font-weight: 600;">
            {{ $isEs ? 'Asunto' : 'Subject' }}
          </td>
          <td>
            {{ $subjectLine }}
          </td>
        </tr>
      @endif
    </table>
  </div>

  <div class="section-card">
    <div class="section-title">
      {{ $isEs ? 'Mensaje' : 'Message' }}
    </div>
    <div style="margin-top: 6px; white-space: pre-line; font-size: 14px;">
      {{ $messageText }}
    </div>
  </div>

  @if(!empty($companyPhone) || !empty($contactEmail))
    <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">
      {{ $isEs
        ? 'Este correo fue generado desde el formulario de contacto del sitio web.'
        : 'This email was generated from the website contact form.'
      }}
      @if(!empty($companyPhone))
        <br>{{ $isEs ? 'Teléfono:' : 'Phone:' }} {{ $companyPhone }}
      @endif
      @if(!empty($contactEmail))
        <br>{{ $isEs ? 'Correo de contacto:' : 'Contact email:' }} {{ $contactEmail }}
      @endif
    </p>
  @endif
@endsection
