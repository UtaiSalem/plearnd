<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledByAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('การจองห้องปฏิบัติการของท่านถูกยกเลิก')
            ->greeting('เรียนคุณ ' . $this->booking->requester_name)
            ->line('การจองห้องปฏิบัติการของท่านถูกยกเลิกโดยผู้ดูแลระบบ ด้วยเหตุผลดังต่อไปนี้:')
            ->line($this->booking->admin_note)
            ->line('รายละเอียดการจองที่ถูกยกเลิก:')
            ->line('ห้อง: ' . $this->booking->room->name)
            ->line('วันที่: ' . $this->booking->start_at->format('d/m/Y H:i') . ' - ' . $this->booking->end_at->format('H:i'))
            ->action('ดูรายละเอียด', route('bookings.show', $this->booking->id));
    }
}
