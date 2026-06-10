<?php

namespace App\Actions;

use App\Mail\DocumentStatusNotificationMail;
use App\Models\ChatMessage;
use App\Models\Document;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

// desde aqui salen los avisos visibles y varios correos, osea que es una pieza bastante central.
class NotificacionAction
{

    public function listForUser(User $user): Collection
    {
        return $user->notificaciones()
            ->orderByRaw('fecha_lectura is null desc')
            ->orderByDesc('fecha_envio')
            ->orderByDesc('created_at')
            ->get();
    }

    public function notifyDocumentStatus(Document $document, string $statusName): void
    {
        $document->loadMissing(['pr.teachers', 'reviewers']);

        $reviewers = $document->reviewers;

        $statusRecipients = match ($statusName) {
            '01_desarrollo' => $document->pr->teachers,
            '02_candidato' => collect(),
            '03_produccion' => User::query()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'gestor');
                })
                ->get(),
            default => collect(),
        };

        $recipients = $reviewers
            ->concat($statusRecipients)
            ->unique('id')
            ->values();

        if ($recipients->isEmpty()) {
            return;
        }

        $label = $this->statusLabel($statusName);

        $displayName = app(CreateDocumentAction::class)->buildDisplayName($document, $document->pr);
        $message = sprintf('El documento %s ha pasado a estado %s.', $displayName, $label);
        
        $link = route('pr.documentos.index', ['pr' => $document->pr_id]) . '#document-' . $document->id;
        $sentAt = Carbon::now();

        foreach ($recipients as $recipient) {
            Notificacion::create([
                'tema' => 'Documento en ' . $label,
                'user_id' => $recipient->id,
                'mensaje' => $message,
                'link' => $link,
                'fecha_envio' => $sentAt,
            ]);

            DB::afterCommit(function () use ($recipient, $label, $message, $link): void {
                try {
                    $this->sendNotificationMail($recipient, $label, $message, $link);
                } catch (Throwable $exception) {
                    Log::warning('No se pudo enviar el correo de notificacion del documento.', [
                        'recipient_id' => $recipient->id,
                        'recipient_email' => $recipient->email,
                        'label' => $label,
                        'error' => $exception->getMessage(),
                    ]);
                }
            });
        }
    }

    public function notifyChatMessage(User $sender, User $recipient): void
    {
        Notificacion::create([
            'tema' => 'Nuevo mensaje de chat',
            'user_id' => $recipient->id,
            'mensaje' => sprintf('%s te ha enviado un mensaje nuevo por el chat.', $sender->name),
            'link' => route('chat.show', ['contact' => $sender->id]),
            'fecha_envio' => Carbon::now(),
        ]);
    }

    public function markAsReadForUser(Notificacion $notificacion, User $user): Notificacion
    {
        abort_unless((int) $notificacion->user_id === (int) $user->id, 403);

        if ($notificacion->fecha_lectura === null) {
            $notificacion->forceFill([
                'fecha_lectura' => Carbon::now(),
            ])->save();
        }

        return $notificacion->fresh();
    }

    private function statusLabel(string $statusName): string
    {
        return match ($statusName) {
            '01_desarrollo' => 'Desarrollo',
            '02_candidato' => 'Candidato',
            '03_produccion' => 'Produccion',
            default => $statusName,
        };
    }

    private function sendNotificationMail(User $recipient, string $label, string $message, string $link): void
    {
        if (! $recipient->receive_notification_emails) {
            return;
        }

        $deliveryMailer = (string) config('mail.delivery_mailer', config('mail.default'));
        $logMailer = (string) config('mail.log_mailer', 'log');
        $shouldLogCopy = (bool) config('mail.log_copy', true);

        $deliveryMessage = $this->buildMailMessage($recipient, $label, $message, $link);

        Mail::mailer($deliveryMailer)
            ->to($recipient)
            ->send($deliveryMessage);

        if (! $shouldLogCopy || $deliveryMailer === $logMailer) {
            return;
        }

        Mail::mailer($logMailer)
            ->to($recipient)
            ->send($this->buildMailMessage($recipient, $label, $message, $link));
    }

    private function buildMailMessage(User $recipient, string $label, string $message, string $link): DocumentStatusNotificationMail
    {
        return new DocumentStatusNotificationMail(
            recipient: $recipient,
            subjectLine: 'Documento en ' . $label,
            messageText: $message,
            documentUrl: $link,
        );
    }
}