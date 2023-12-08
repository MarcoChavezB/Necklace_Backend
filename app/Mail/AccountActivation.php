<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class AccountActivation extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user; //Aqui se recibe el usuario que se va a enviar en el correo
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Activacion de la cuenta.',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.activation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
    public function build()
    {
        $token = Str::random(32);  // genera un token único

        DB::table('tokens')->insert([  // guarda el token en la base de datos
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        $url = URL::signedRoute('activation', ['user' => $this->user->id, 'token' => $token]);  // crea una ruta firmada con el token

        return $this->view('emails.activation', ['url' => $url])
            ->with([
                'url' => $url,
            ]);
    }

}
