BEGIN:VCALENDAR
VERSION:2.0
METHOD:{{ $method }}
BEGIN:VEVENT
ORGANIZER;CN=Engineering Advising:MAILTO:{{ $meeting->advisor->email }}
ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={{ $meeting->advisor->email }}:MAILTO:{{ $meeting->advisor->email }}
@unless($cancelled)
ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN={{ $meeting->student->email }}:MAILTO:{{ $meeting->student->email }}
@endunless
DTSTART:{{ $start }}
DTEND:{{ $end }}
LOCATION:{{ $meeting->advisor->office }}
TRANSP:OPAQUE
SEQUENCE:{{ $meeting->sequence }}
UID:{{ $calUid }}
DTSTAMP:{{ $todayStamp }}
DESCRIPTION:{{ $meeting->description }}
SUMMARY:{{ $meeting->title }}
@if($cancelled)
STATUS:CANCELLED
@endif
END:VEVENT
END:VCALENDAR
