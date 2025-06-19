<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function notifyStatusChange(Aluno $aluno, string $previousStatus): void
    {
        $gestores = User::where('role', 'gestor')->get();

        foreach ($gestores as $gestor) {
            $this->sendNotification($gestor, $aluno, $previousStatus);
        }

        Log::channel('notifications')->info('Status do aluno alterado', [
            'aluno_id' => $aluno->id,
            'aluno_nome' => $aluno->nome,
            'status_anterior' => $previousStatus,
            'status_atual' => $aluno->status,
            'gestores_notificados' => $gestores->count(),
        ]);
    }

    private function sendNotification(User $gestor, Aluno $aluno, string $previousStatus): void
    {
        Log::channel('notifications')->info('Notificação enviada', [
            'tipo' => 'status_change',
            'gestor_id' => $gestor->id,
            'gestor_email' => $gestor->email,
            'aluno_id' => $aluno->id,
            'aluno_nome' => $aluno->nome,
            'status_anterior' => $previousStatus,
            'status_atual' => $aluno->status,
            'timestamp' => now(),
        ]);

        /*
        try {
            Mail::to($gestor->email)->send(new StatusChangeNotification($aluno, $previousStatus));
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de notificação', [
                'gestor_email' => $gestor->email,
                'aluno_id' => $aluno->id,
                'error' => $e->getMessage(),
            ]);
        }
        */
    }

    public function sendWebhookNotification(Aluno $aluno, string $previousStatus): void
    {
        $webhookUrl = config('app.webhook_url');
        
        if (!$webhookUrl) {
            return;
        }

        $payload = [
            'event' => 'student_status_changed',
            'data' => [
                'student_id' => $aluno->id,
                'student_name' => $aluno->nome,
                'previous_status' => $previousStatus,
                'current_status' => $aluno->status,
                'timestamp' => now()->toISOString(),
            ]
        ];

        Log::channel('webhooks')->info('Webhook enviado', [
            'url' => $webhookUrl,
            'payload' => $payload,
        ]);

        // Uncomment para envio real de webhook
        /*
        try {
            Http::post($webhookUrl, $payload);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar webhook', [
                'url' => $webhookUrl,
                'error' => $e->getMessage(),
            ]);
        }
        */
    }
}