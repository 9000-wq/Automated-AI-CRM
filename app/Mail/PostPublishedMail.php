<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PostPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function build()
    {
        return $this->subject('Your Marketing Post is Published')
                    ->view('emails.post_published')
                    ->with(['link' => $this->link]);
    }
}
