Hola {{ $recipientName }}

{{ $messageText }}

@if($documentUrl)
Abrir documento: {{ $documentUrl }}
@endif

Gracias,
{{ config('app.name') }}