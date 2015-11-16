Service;Date de début;Date de fin;Durée;Durée en minutes;Description;Type d'interruption
{foreach $events as $event}
{$event->name};{$event->begindate|date_format:'%A %e %B %Y %H:%M'};{$event->enddate|date_format:'%A %e %B %Y %H:%M'};{$event->total};{$event->total_minutes};{$event->description|default:''};{if $event->type === {"UniversiteRennes2\Isou\Event::TYPE_SCHEDULED"|constant}}Prévues{else}Non prévues{/if}

{/foreach}
