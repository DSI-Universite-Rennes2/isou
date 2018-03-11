Résumé
-------
Nombre de serivces Isou forcés : {count($forcedservices)}
Nombre de services Nagios supprimés : {count($nagiosServices)}

{if count($nagiosServices) > 0}
Liste des services Nagios supprimés
------------------------------------
{foreach $nagiosServices as $service}
   - {$service}
{/foreach}
{/if}

{if count($forcedservices) > 0}
Liste des services actuellement forcés
---------------------------------------
{foreach $forcedservices as $forcedservice}
- {$forcedservice->nameForUsers} : {$STATES.{$forcedservice->state}->alt}
{/foreach}
{/if}

{if count($categories) > 0}
Liste des services indisponibles de la journée
-----------------------------------------------
{section name=i loop=$categories}
 **{$categories[i]->name} :**
{section name=j loop=$categories[i]->services}
   - {$categories[i]->services[j]->getNameForUsers()} : {$categories[i]->services[j]->total}
{foreach $categories[i]->services[j]->getEvents() as $event}
     . {$categories[i]->services[j]->getNameForUsers()}, {if $event->getEndDate() === NULL}depuis {$event->getStartDate()|date_format:'%c'}{else}de {$event->getStartDate()|date_format:'%c'} à {$event->getEndDate()|date_format:'%c'}{/if}
{if $event->getDescription() != ""}

       x {$event->getDescription()}
{/if}

{/foreach}
{/section}

{/section}
{/if}

