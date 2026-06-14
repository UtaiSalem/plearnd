<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingUpdatedByAdmin extends Notification implements ShouldQueue
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
            ->subject('รายละเอียดการจองห้องปฏิบัติการของท่านถูกแก้ไขโดยผู้ดูแลระบบ')
            ->greeting('เรียนคุณ ' . $this->booking->requester_name)
            ->line('รายละเอียดการจองห้องปฏิบัติการของท่านถูกแก้ไขโดยผู้ดูแลระบบ (Admin) โดยมีรายละเอียดใหม่ดังนี้:')
            ->line('ห้อง: ' . $this->booking->room->name)
            ->line('วันที่: ' . $this->booking->start_at->format('d/m/Y'))
            ->line('เวลา: ' . $this->booking->start_at->format('H:i') . ' - ' . $this->booking->end_at->format('H:i') . ' น.')
            ->line('จำนวนผู้เข้าร่วม: ' . $this->booking->attendees . ' คน')
            ->line('หมายเหตุจากผู้ดูแล: ' . ($this->booking->admin_note ?? '-'))
            ->action('ดูรายละเอียดการจอง', route('bookings.show', $this->booking->id));
    }
}
