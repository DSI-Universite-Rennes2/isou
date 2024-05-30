Résumé quotidien
==================

État des services depuis le {$last_daily_report|date_format:"%a %d %B %Y %H:%M"}.

Nombre de services uniques perturbés : {$count_warning_events}
Nombre de services uniques indisponibles : {$count_critical_events}

Nombre d'évènements prévus : {$count_scheduled_events}
Nombre d'évènements non prévus : {$count_unscheduled_events}
{* Liste des services actuellement forcés *}
{if $locked_services|count > 0}


Liste des services actuellement forcés
=======================================
{foreach $locked_services as $locked_service}
- {$locked_service->name|unescape:"htmlall"} : {$STATES[{$locked_service->state}]->alternate_text}
{/foreach}
{/if}
{* Liste des services actuellement fermés *}
{if $closed_services|count > 0}


Liste des services actuellement fermés
=======================================
{foreach $closed_services as $closed_service}
- {$closed_service->name|unescape:"htmlall"} : {$STATES[{$closed_service->state}]->alternate_text}
{/foreach}
{/if}
{* Liste des interruptions régulières *}
{if $regular_events|count > 0}


Liste des interruptions régulières
==================================
{foreach $regular_events as $record}
- {$record->service_name|unescape:"htmlall"} ; {$record}
{/foreach}
{/if}
{* Liste des services indisponibles de la journée *}
{if $events|count > 0}


Liste des services indisponibles de la journée
===============================================
{foreach $events as $plugin => $records}

{$plugin}
---------
{foreach $records as $record}
- {$record->service_name|unescape:"htmlall"} ; {$record}
{/foreach}
{/foreach}
{/if}
