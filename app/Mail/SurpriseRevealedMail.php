<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\BirthdaySurprise;

class SurpriseRevealedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $surprise;
    public $receiver;
    public $sender;

    /**
     * Create a new message instance.
     */
    public function __construct(BirthdaySurprise $surprise)
    {
        $this->surprise = $surprise;
        $this->receiver = $surprise->receiver;
        $this->sender = $surprise->sender;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎁 Your Birthday Surprise is Ready! - Khairun',
            from: config('mail.from.address', 'noreply@khairun.app'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.surprise-revealed',
            with: [
                'surprise' => $this->surprise,
                'receiver' => $this->receiver,
                'sender' => $this->sender,
                'surpriseUrl' => route('birthday-surprises.show', $this->surprise),
                'contentIcon' => $this->surprise->getContentIcon(),
                'revealDate' => $this->surprise->reveal_at->format('d F Y, H:i'),
            ]
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
