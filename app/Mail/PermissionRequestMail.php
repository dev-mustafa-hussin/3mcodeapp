<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PermissionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $requestedPermission;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $requestedPermission, $url)
    {
        $this->user = $user;
        $this->requestedPermission = $requestedPermission;
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Access Request: ' . $this->user->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.permission-request',
            with: [
                'user' => $this->user,
                'requestedPermission' => $this->requestedPermission,
                'url' => $this->url,
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
