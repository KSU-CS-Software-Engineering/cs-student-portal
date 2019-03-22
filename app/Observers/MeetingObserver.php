<?php

namespace App\Observers;

use App\Mail\MeetingMail;
use App\Models\Meeting;
use Illuminate\Support\Facades\Mail;

class MeetingObserver
{
    /**
     * Listen to the Meeting created event.
     *
     * @param  Meeting  $meeting
     * @return void
     */
    public function created(Meeting $meeting)
    {
        $type = "created";
        $this->sendMail($meeting, $type);
    }

    /**
     * Listen to the Meeting update event.
     *
     * @param  Meeting  $meeting
     * @return void
     */
    public function updated(Meeting $meeting)
    {
        $count = count($meeting->getDirty());
        if ($meeting->isDirty("conflict")) {
            $count = $count - 1;
        }
        if ($meeting->isDirty("updated_at")) {
            $count = $count - 1;
        }
        if ($meeting->isDirty("status")) {
            $count = $count - 1;
        }
        if ($meeting->isDirty("sequence")) {
            $count = $count - 1;
        }
        if ($count > 0) {
            $type = "updated";
            $this->sendMail($meeting, $type);
        }
    }

    /**
     * Listen to the Meeting deleting event.
     *
     * @param  Meeting  $meeting
     * @return void
     */
    public function deleting(Meeting $meeting)
    {
        $type = "removed";
        $this->sendMail($meeting, $type);
    }

    /**
     * Function to send meetin emails
     *
     * @param Meeting $meeting
     * @param String $type
     */
    private function sendMail($meeting, $type)
    {
        $to = [$meeting->advisor, $meeting->student];
        $cc = null;

        if (auth()->user()->id === $meeting->student->user->id) {
            $to = $meeting->advisor;
            $cc = $meeting->student;
        } elseif (auth()->user()->id === $meeting->advisor->user->id) {
            $to = $meeting->student;
            $cc = $meeting->advisor;
        }

        Mail::to($to)
            ->cc($cc)
            ->send(new MeetingMail($meeting, $type));
    }
}
