<?php

namespace App\Notifications\Tickets;

use App\Models\Tickets;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Str;

class NewTicketsReplyFromUserToUser extends Notification
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
            ->subject(__('New reply').': '.Str::limit($this->Tickets->subject, 35))
            ->greeting(__('Hi').' '.$this->Tickets->user->name.',')
            ->line(__('We have received your response to the Tickets, we will try to respond as soon as possible, you can see the details in this link').':')
            ->action(__('See Tickets'), url('/Tickets/'.$this->Tickets->uuid))
            ->line(__('In order to view the Tickets you have to log in with your email and password, if you do not remember the password, you can reset it using the email account that this message has reached').'.');
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
