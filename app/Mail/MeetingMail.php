<?php

namespace App\Mail;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Swift_Message;

class MeetingMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $meeting;
    private $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Meeting $meeting, string $type)
    {
        $this->meeting = $meeting;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $format = 'Ymd\THis\Z';
        $tz = 'UTC';
        $cancelled = $this->type === 'removed';
        $method = $cancelled ? 'CANCEL' : 'REQUEST';
        $start = $this->meeting->start->setTimezone($tz)->format($format);
        $end = $this->meeting->end->setTimezone($tz)->format($format);
        $calUid = "{$this->meeting->id}-123123123123123-@ksu.edu";
        $todayStamp = Carbon::create()->setTimezone($tz)->format($format);

        Carbon::setToStringFormat('l, j F Y, g:ia');

        return $this->from($this->meeting->advisor->email, 'Engineering Advising')
            ->subject("Advising – {$this->meeting->title}")
            ->view('email.meeting')
            ->text('email.meeting-text')
            ->with([
                'meeting' => $this->meeting,
                'type' => $this->type,
            ])
            ->attachData(
                view('email.meeting-ical')
                    ->with([
                        'meeting' => $this->meeting,
                        'cancelled' => $cancelled,
                        'method' => $method,
                        'start' => $start,
                        'end' => $end,
                        'calUid' => $calUid,
                        'todayStamp' => $todayStamp,
                    ])
                    ->render(),
                'meeting.ics',
                [
                    'mime' => "text/calendar; charset=UTF-8; method={$method}"
                ]
            )
            ->withSwiftMessage(function (Swift_Message $message) {
                $message->getHeaders()
                    ->addTextHeader('Content-Class', 'urn:content-classes:calendarmessage');
            });
    }
}
