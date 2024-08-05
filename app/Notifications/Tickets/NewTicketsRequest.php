<?php

namespace App\Notifications\Tickets;

use App\Models\Tickets;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Str;

class NewTicketsRequest extends Notification
{
    use Queueable;

    private $Tickets;

    /**
     * Create a new notification instance.
     *
     * @param  Tickets  $Tickets
     */
    public function __construct(Tickets $Tickets)
    {
        $this->Tickets = $Tickets;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('New Tickets').': '.Str::limit($this->Tickets->subject, 35))
            ->greeting(__('Hi').' '.$this->Tickets->user->name.',')
            ->line(__('We have received your Tickets and we will try to answer you as soon as possible').'.')
            ->line(__('You can view the Tickets details and add responses from this link').':')
            ->action(__('See Tickets'), url('/Tickets/'.$this->Tickets->uuid));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
