{if count($categories) > 0}
Liste des services indisponibles de la journée
-----------------------------------------------
{section name=i loop=$categories}
 **{$categories[i]->name} :**
{section name=j loop=$categories[i]->services}
   - {$categories[i]->services[j]->getNameForUsers()} : {$categories[i]->services[j]->total}
{foreach $categories[i]->services[j]->getEvents() as $event}
     . {$categories[i]->services[j]->getNameForUsers()}, {if $event->getEndDate() === NULL}depuis {$event->getBeginDate()|date_format:'%c'}{else}de {$event->getBeginDate()|date_format:'%c'} à {$event->getEndDate()|date_format:'%c'}{/if}
{if $event->getDescription() != ""}

       x {$event->getDescription()}
{/if}

{/foreach}
{/section}

{/section}
{/if}

Statistique des visites
-----------------------
 ** Visites (hors bots et autres) **
   - Visites externes : {$visits->externe}
   - Visites UHB : {$visits->interne}
   - Visites du CRI : {$visits->cri}
   - Total des visites : {$visits->count}

 ** Navigateurs **
{foreach from=$browsers item=browser}
   - {$browser->browser} : {$browser->total} visites
{/foreach}

 ** Système d'exploitation **
{foreach from=$os item=o}
   - {$o->os} : {$o->total} visites
{/foreach}

 ** Bots et Autres **
Total des visites : {$visits->bots}
{if isset($googlebot)}
   - googlebot : {$googlebot->total} visites
{/if}
{foreach from=$bots item=bot}
   - {$bot->userAgent|escape:'htmlall'} : {$bot->total} visites
{/foreach}

{if count($nagiosServices) > 0}
Liste des services Nagios supprimés
-----------------------------------
{foreach $nagiosServices as $service}
   - {$service}
{/foreach}
{/if}


