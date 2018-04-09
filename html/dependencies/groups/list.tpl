<main role="main">
<article id="content">

<ul class="breadcrumb">
    <li><a href="{$smarty.const.URL}/index.php/dependances">dépendances</a></li>
    <li class="active">{$service->name}</li>
</ul>

<ul class="list-inline text-right">
    <li><a class="btn btn-success" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/edit/0">Créer un groupe de dépendances</a></li>
</ul>

{include file="common/messages_session.tpl"}

{if count($groups) === 0}
    <p class="alert alert-info text-center">Aucun groupe de dépendances défini.</p>
{else}
    {foreach $groups as $state => $grps}
    <div class="col-md-6">
        <h2 class="isou-dependencies-group-h1 text-center">Groupes {$STATES[$state]::$STATES[$state]|lower}s</h2>
        <ul class="list-unstyled">
        {foreach $grps as $group}
        <li class="well">
            <h3 class="isou-dependencies-group-h3">{$STATES[$state]} {$group->name}</h2>
            {if $group->redundant === "0"}
            <p class="small isou-non-redundant-groups">groupe de services non-redondés</p>
            {else}
            <p class="small isou-redundant-groups">groupe de services redondés</p>
            {/if}

            <h4 class="isou-dependencies-group-h4">Contenu du groupe</h4>
            <ul class="list-inline text-right">
                <li><a class="btn btn-xs btn-success" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$group->id}/content/edit/0">ajouter du contenu</a></li>
            </ul>

            {if count($group->contents) === 0}
            <p class="alert alert-danger">Groupe vide.</p>
            {else}
            <ul class="alert list-unstyled isou-dependencies-content-ul">
            {foreach $group->contents as $content}
                <li class="clearfix">
                <ul class="pull-left list-inline">
                    <li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$group->id}/content/edit/{$content->id}">modifier</a></li>
                    <li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/{$group->id}/content/delete/{$content->id}">supprimer</a></li>
                </ul>
                {$STATES[$content->servicestate]} {$content->name}
                </li>
            {/foreach}
            </ul>
            {/if}

            <h4 class="isou-dependencies-group-h4">Message affiché en cas d'erreur</h4>
            {if empty($group->message) === true}
                <p class="alert alert-warning">Aucun message défini.</p>
            {else}
                <div class="alert alert-info isou-dependencies-group-message">{$group->message}</div>
            {/if}

            <h4 class="isou-dependencies-group-h4">Actions sur le groupe</h4>
            <ul class="list-inline">
                <li><a class="btn btn-xs btn-primary" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/edit/{$group->id}">modifier</a></li>
                <li><a class="btn btn-xs btn-danger" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/delete/{$group->id}">supprimer</a></li>
                <li class="pull-right"><a class="btn btn-xs btn-success" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/duplicate/{$group->id}">dupliquer le groupe</a></li>
            </ul>
        </li>
        {/foreach}
        </ul>
    </div>
    {/foreach}
{/if}

</article>
</main>
