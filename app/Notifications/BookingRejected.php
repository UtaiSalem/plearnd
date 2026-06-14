<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingRejected extends Notification
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
            ->subject('คำขอจองห้องของท่านไม่ได้รับการอนุมัติ: '.$b->room->name)
            ->greeting('เรียน '.$b->requester_name)
            ->line('ขออภัย คำขอจองห้องปฏิบัติการของท่านไม่ได้รับการอนุมัติ')
            ->line('ห้อง: '.$b->room->name.' ('.$b->room->code.')')
            ->line('ช่วงเวลา: '.$b->start_at->format('d/m/Y H:i').' — '.$b->end_at->format('H:i'))
            ->line('เหตุผล: '.($b->rejection_reason ?: 'ไม่ระบุ'))
            ->action('ดูรายละเอียด', $url)
            ->line('ท่านสามารถยื่นคำขอใหม่โดยปรับเปลี่ยนตามคำแนะนำได้ในระบบ')
            ->salutation('ระบบจองห้องปฏิบัติการวิทยาศาสตร์ — มหาวิทยาลัยราชภัฏสงขลา');
    }
}
