<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;



class LeadEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $subjectLine;
    public $bodyText;
  

    public function __construct($subjectLine, $bodyText)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyText = $bodyText;
        

       

    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
       return new Envelope(
            subject: $this->subjectLine, // Use passed subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lead_email', // Your blade file
            with: [
                'bodyText' => $this->bodyText, // Pass data to view
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
         return [];
    }
}
