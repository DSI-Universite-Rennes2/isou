BEGIN:VCALENDAR
CALSCALE:GREGORIAN
PRODID:-//isou/ical//2.0/EN
VERSION:2.0
X-WR-TIMEZONE:{$timezone}
{foreach $events as $event}
BEGIN:VEVENT
UID:{$event->uid}
SUMMARY:{$event->summary|unescape}
DTSTART;TZID={$timezone}:{$event->dtstart}
{if empty($event->dtend) === false}
DTEND;TZID={$timezone}:{$event->dtend}
{/if}
{if empty($event->description) === false}
DESCRIPTION:{$event->description}
{/if}
STATUS:{$event->status}
END:VEVENT
{/foreach}
END:VCALENDAR
