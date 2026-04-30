<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Order;
class NewOrderNotification extends Notification
{
    use Queueable;
    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'user_name' => $this->order->user->name,
            'subtotal' => $this->order->subtotal,
            'discount' => $this->order->discount,
            'delivery_price' => $this->order->delivery_price,
            'total' => $this->order->total,
            'status'  => $this->order->status,
            'message'    => "لديك طلب جديد من المستخدم {$this->order->user->name} برقم #{$this->order->id}",
        ];
    }
}
