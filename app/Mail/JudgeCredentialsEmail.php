<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JudgeCredentialsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$email, $password)
    {
        $this->name=$name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.judge-credentials')
                    ->subject('Your Dashboard Credentials');
    }
}
