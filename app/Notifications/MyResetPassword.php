<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class MyResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {;
        return  (new MailMessage)
        ->view('emails.my_reset_password', ['token' => $this->token])
        ->subject(trans('emails/my_reset_password_lang.subject'));
        
        return (new MailMessage)
        ->subject('Recuperar contraseña')
        ->greeting('Hola')
        ->line('Estás recibiendo este correo porque hiciste una solicitud de recuperación de contraseña para tu cuenta.'.route('password.reset', $this->token))
        ->action('Recuperar contraseña', route('password.reset', $this->token))
        ->line('Si no realizaste esta solicitud, no se requiere realizar ninguna otra acción.')
        ->salutation('Saludos, '. config('app.name'));
    }
}