{if count($categories) === 0}
    <p class="alert alert-danger">Vous n'avez pas encore défini de catégorie. Avant d'ajouter un service Isou, vous devez créer une catégorie.</p>
    <p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/categories/edit/0">Ajouter une catégorie</a></p>
{else}
    <p class="text-right"><a class="btn btn-primary" href="{$smarty.const.URL}/index.php/services/isou/edit/0">Ajouter un service ISOU</a></p>

    {include file="common/messages_session.tpl"}

    {foreach $categories as $i => $category}
        {if isset($category->services[0]) === false}
            {continue}
        {/if}

        <details>
            <summary>{$category->name}</summary>
            <table class="table table-condensed table-striped" summary="liste des services ISOU de la catégorie {$category->name}">
            {* <!-- <caption class="services-caption"></caption> --> *}
            <thead>
                <tr>
                    <th class="col-md-2" id="head-state-{$i}">État actuel</th>
                    <th class="col-md-4" id="head-name-{$i}">Nom du service</th>
                    <th class="col-md-2" id="head-action-{$i}">Actions</th>
                    <th class="col-md-4" id="head-note-{$i}">Notes</th>
                </tr>
            </thead>
            <tbody>
                {foreach $category->services as $service}
                <tr{if !empty($service->css)} class="{$service->css}"{/if}>
                    <td headers="head-state-{$i}">{$STATES[{$service->state}]}</td>
                    <td id="service-{$service->id}" headers="head-name-{$i}">
                        {$service->name}
                        {if $service->url !== NULL}
                            <br /><span>URL :</span> {$service->url}
                        {/if}
                    </td>
                    <td headers="head-action-{$i}">
                    <ul class="list-inline">
                        <!-- <li><a class="btn btn-xs btn-default" href="{$smarty.const.URL}/index.php/services/isou/inspect/{$service->id}">inspecter</a></li> -->
                        <li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/services/isou/edit/{$service->id}">modifier</a></li>
                        <li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/services/isou/delete/{$service->id}">supprimer</a></li>
                    </ul>
                    </td>
                    <td headers="head-note-{$i}">
                        {* <!-- ex: service final ISOU sans dépendance, service final dont l'état est bloqué --> *}
                        {if isset($service->notes[0])}
                        <ul class="alert alert-warning list-unstyled">
                        {foreach $service->notes as $note}
                            <li>{$note}</li>
                        {/foreach}
                        </ul>
                        {/if}
                    </td>
                </tr>
                {/foreach}
            </tbody>
            </table>
        </details>
    {/foreach}
{/if}
