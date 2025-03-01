<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    protected $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {

        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Notificación de recuperación de cuenta')
            ->line('Está recibiendo este correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.')
            ->action('Recuperar cuenta', $url)
            ->line('Este enlace de reinicio de contraseña caducará en ' .
                                config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minutos')
            ->line('Si no solicitó un restablecimiento de contraseña, no se requiere ninguna otra acción.
');
    }
    protected function resetUrl($notifiable)
    {

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
