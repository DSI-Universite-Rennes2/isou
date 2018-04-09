<main role="main">
<article id="content">

<form action="{$smarty.const.URL}/index.php/dependances/service/{$service->id}/group/edit/{$dependency_group->id}" method="post">
    {if $dependency_group->id == 0}
    <h2>Ajouter un groupe</h2>
    {else}
    <h2>Mettre à jour un groupe</h2>
    {/if}

    {include file="common/messages_form.tpl"}

    <dl>
        <div class="form-information-dl-div">
            <dt class="form-topics-dt">
                <label for="service">Nom du service ISOU</label>
            </dt>
            <dd class="form-values-dd">
                {html_options name="service" id="service" disabled="1" readonly="1" options=$options_services selected=$service->id}
            </dd>
        </div>
        <div class="form-information-dl-div">
            <dt class="form-topics-dt">
                <label for="name">Nom du groupe</label>
            </dt>
            <dd class="form-values-dd">
                <input type="text" name="name" id="name" maxlength="32" value="{$dependency_group->name}" />
            </dd>
        </div>
        <div class="form-information-dl-div">
            <dt class="form-topics-dt">
                <label for="redundant">Groupe redondé</label>
            </dt>
            <dd class="form-values-dd">
                {html_radios name="redundant" id="redundant" options=$options_redundants selected=$dependency_group->redundant}
            </dd>
        </div>
        <div class="form-information-dl-div">
            <dt class="form-topics-dt">
                <label for="groupstate">État du groupe</label>
            </dt>
            <dd class="form-values-dd">
                {html_options name="groupstate" id="groupstate" options=$options_states selected=$dependency_group->groupstate}
            </dd>
        </div>
        <div class="form-information-dl-div">
            <dt class="form-topics-dt">
                <label for="message">Message automatique</label>
            </dt>
            <dd class="form-values-dd">
                <textarea name="message" id="message" cols="75" rows="10">{$dependency_group->message}</textarea>
            </dd>
        </div>
    </dl>

    <ul class="list-inline">
        <li>
            <input class="btn btn-primary" type="submit" value="enregistrer" />
        </li>
        <li>
            <a class="btn btn-default" href="{$smarty.const.URL}/index.php/dependances/service/{$service->id}">annuler</a>
        </li>
    </ul>
</form>

</article>
</main>
