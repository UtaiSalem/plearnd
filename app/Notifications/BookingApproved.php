<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingApproved extends Notification
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
        $b = $this->booking;
        $url = url(route('bookings.show', $b, absolute: false));

        return (new MailMessage)
            ->subject('คำขอจองห้องของท่านได้รับการอนุมัติ: '.$b->room->name)
            ->greeting('เรียน '.$b->requester_name)
            ->line('คำขอจองห้องปฏิบัติการของท่านได้รับการอนุมัติแล้ว')
            ->line('ห้อง: '.$b->room->name.' ('.$b->room->code.')')
            ->line('ช่วงเวลา: '.$b->start_at->format('d/m/Y H:i').' — '.$b->end_at->format('H:i'))
            ->action('ดูรายละเอียดการจอง', $url)
            ->line('กรุณามาก่อนเวลาที่จองอย่างน้อย 10 นาที และนำบัตรแสดงตนมาด้วย')
            ->salutation('ระบบจองห้องปฏิบัติการวิทยาศาสตร์ — มหาวิทยาลัยราชภัฏสงขลา');
    }
}
