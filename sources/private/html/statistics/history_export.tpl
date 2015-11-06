Service;Date de début;Date de fin;Durée;Description;Type d'interruption
{foreach from=$events item=event}
{$event->nameForUsers};{$event->beginDate|date_format:'%A %e %B %Y %H:%M'};{$event->endDate|date_format:'%A %e %B %Y %H:%M'};{$event->total} minutes;{$event->description|default:''};{if $event->isScheduled == 1}Prévues{else}Non prévues{/if}

{/foreach}
