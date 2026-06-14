<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingSubmitted extends Notification
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
            ->subject('แจ้งคำขอจองห้องปฏิบัติการใหม่: '.$b->room->name)
            ->greeting('เรียน '.($notifiable->name ?? 'ผู้รับผิดชอบห้องปฏิบัติการ'))
            ->line('มีคำขอจองห้องปฏิบัติการใหม่รอการพิจารณา')
            ->line('ห้อง: '.$b->room->name.' ('.$b->room->code.')')
            ->line('ผู้ขอใช้: '.$b->requester_name.' — '.$b->department)
            ->line('ช่วงเวลา: '.$b->start_at->format('d/m/Y H:i').' — '.$b->end_at->format('H:i'))
            ->line('วัตถุประสงค์: '.$b->purpose)
            ->action('ตรวจสอบคำขอ', $url)
            ->salutation('ระบบจองห้องปฏิบัติการวิทยาศาสตร์ — มหาวิทยาลัยราชภัฏสงขลา');
    }
}
