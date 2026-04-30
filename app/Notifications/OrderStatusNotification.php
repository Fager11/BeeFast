<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class OrderStatusNotification extends Notification
{
    use Queueable;
    protected $data;

    /**
     * Create a new notification instance.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
   
    
     
    public function toDatabase($notifiable)
    {
        $message = '';

        switch ($notifiable->role) {
            case 'admin':
                if(isset($this->data['driver_assigned']) && $this->data['driver_assigned']) {
                    $message = "تم تعيين سائق للطلب #{$this->data['order_id']}.";
                } elseif ($this->data['status'] === 'pending') {
                    $message = "وصل طلب جديد من العميل {$this->data['user_name']} برقم #{$this->data['order_id']}.";
                } elseif ($this->data['status'] === 'in_progress') {
                    $message = "تم قبول الطلب #{$this->data['order_id']}.";
                } elseif ($this->data['status'] === 'on_the_way') {
                    $message = "الطلب #{$this->data['order_id']} الآن في الطريق.";
                } elseif ($this->data['status'] === 'delivered') {
                    $message = "تم توصيل الطلب #{$this->data['order_id']}.";
                }elseif ($this->data['status'] === 'cancelled') {
                    $message = "تم إلغاء الطلب #{$this->data['order_id']} من قبل المستخدم {$this->data['user_name']}.";
                
                
                } else {
                    $message = "لديك إشعار جديد حول الطلب #{$this->data['order_id']}.";
                }
                break;
        

            case 'user':
                switch ($this->data['status']) {
                    case 'in_progress':
                        $message = "طلبك رقم #{$this->data['order_id']} قيد العمل الآن.";
                        break;
                    case 'on_the_way':
                        $message = "طلبك رقم #{$this->data['order_id']} في الطريق.";
                        break;
                    case 'delivered':
                        $message = "تم توصيل طلبك رقم #{$this->data['order_id']} بنجاح. المجموع: {$this->data['total']} .ل.س";
                        break;
                    case 'cancelled':
                        $message = "تم إلغاء طلبك رقم #{$this->data['order_id']}.";
                        break;
                    default:
                        $message = "حالة طلبك رقم #{$this->data['order_id']} تغيرت إلى {$this->data['status']}.";
                        break;
                }
                break;

            case 'driver':
                if(isset($this->data['driver_assigned']) && $this->data['driver_assigned']) {
                    $message = "تم إسناد طلب رقم #{$this->data['order_id']} لك.";
                } else {
                    $message = "حالة الطلب رقم #{$this->data['order_id']} تغيرت إلى {$this->data['status']}.";
                }
                break;

            default:
                $message = "لديك إشعار جديد حول الطلب #{$this->data['order_id']}.";
                break;
        }

        return [
            'order_id'   => $this->data['order_id'],
            'status'     => $this->data['status'],
            'user_name'  => $this->data['user_name'] ?? null,
            'total'      => $this->data['total'] ?? null,
            'message'    => $message,
        ];
    }



   
}
