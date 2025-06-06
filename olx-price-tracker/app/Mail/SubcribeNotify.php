<?php

namespace App\Mail;

use App\Enums\NotificationType;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubcribeNotify extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Subscription $subscription,
        public NotificationType $type
    ) {}

    public function build()
    {
        return $this->subject($this->type->title())
            ->view('emails.price_alert')
            ->with([
                'subscription' => $this->subscription,
                'type' => $this->type
            ]);
    }
}
