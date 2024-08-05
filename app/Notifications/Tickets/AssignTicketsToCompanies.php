<?php

namespace App\Notifications\Tickets;

use App\Models\Tickets;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Str;

class AssignTicketsToCompanies extends Notification
{
    use Queueable;

    private $Tickets;
    private $agent;

    /**
     * Create a new notification instance.
     *
     * @param  Tickets  $Tickets
     * @param  User  $agent
     */
    public function __construct(Tickets $Tickets, User $agent)
    {
        $this->Tickets = $Tickets;
        $this->agent = $agent;
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
            ->subject(__('New Tickets assigned to Companies').': '.Str::limit($this->Tickets->subject, 35))
            ->greeting(__('Hi').' '.$this->agent->name.',')
            ->line(__('A new Tickets has been assigned to your Companies').'.')
            ->line(__('You can view the Tickets details and add responses from this link').':')
            ->action(__('See Tickets'), url('/dashboard/Tickets/'.$this->Tickets->uuid.'/manage'));
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
