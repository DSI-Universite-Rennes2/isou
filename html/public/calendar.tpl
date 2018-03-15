<main role="main">
<article id="content">

    <h1 class="sr-only">Calendrier</h1>

    <p class="alert alert-info text-center">Liste des opérations de maintenance prévues.</p>

    <table id="calendrier" class="table table-bordered" summary="Calendrier répertoriant toutes les intervertions prévues">
        {* Titre du calendrier. *}
        <caption class="text-center">
            <div class="sr-only">Calendrier des interventions</div>
            <ul class="list-unstyled row">
                {if $smarty.get.page > 1}
                <li class="col-md-6 text-left">
                    <a class="btn" href="{$smarty.const.URL}/index.php/calendrier/{$smarty.get.page - 1}"><img alt="Page précédente" src="{$smarty.const.URL}/themes/{$CFG.theme}/images/arrow-left.gif" /></a>
                </li>
                {/if}
                {if $smarty.get.page < 5}
                <li class="col-md-6-offset text-right">
                    <a class="btn" href="{$smarty.const.URL}/index.php/calendrier/{$smarty.get.page + 1}"><img alt="Page suivante" src="{$smarty.const.URL}/themes/{$CFG.theme}/images/arrow-right.gif" /></a>
                </li>
                {/if}
            </ul>
        </caption>

        {* Entêtes du calendrier. *}
        <thead>
            <tr>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
                <th>Samedi</th>
                <th>Dimanche</th>
            </tr>
        </thead>

        {* Corps du calendrier. *}
        <tbody>
        {foreach $calendar as $week}
            <tr>
            {foreach $week as $day}
                <td {if $day->datetime < $now}class="active"{else if $day->datetime === $now}class="info"{/if}">
                    <span id="date-{$day->datetime|date_format:'%d-%B-%Y'}">{$day->datetime|date_format:$day->strftime}</span>
                    {if isset($day->services[0])}
                    <ul>
                        {foreach $day->services as $service}
                        <li>
                            <div>{$service->name} - de {$service->event->startdate->format('H\hi')} à {$service->event->enddate->format('H\hi')}.</div>
                            {if empty($service->event->description) === false}
                            <div class="isou-calendar-description">{$service->event->description}</div>
                            {/if}
                        </li>
                        {/foreach}
                    </ul>
                    {else}
                        {if $day->datetime === $now}
                            <p>Aucune intervention prévue</p>
                        {/if}
                    {/if}
                </td>
            {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>
</article>
</main>
