<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    use Queueable;

    public $type;
    public $title;
    public $desc;
    public $link;

    public function __construct(string $type, string $title, string $desc, ?string $link = null)
    {
        $this->type = $type;
        $this->title = $title;
        $this->desc = $desc;
        $this->link = $link;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'  => $this->type,
            'title' => $this->title,
            'desc'  => $this->desc,
            'link'  => $this->link,
        ];
    }
}
