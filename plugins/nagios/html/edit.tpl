<form action="{$smarty.const.URL}/index.php/services/nagios/edit/{$service->id}" method="post">
    {if $service->id === 0}
        <h2>Ajouter un service Nagios</h2>
    {else}
        <h2>Remplacer le service {$service->name}</h2>
    {/if}

    {include file="common/messages_form.tpl"}

    <dl>
        <dt>
            <label for="service">Service Nagios</label>
        </dt>
        <dd>
            <input list="services" id="service" name="service" value="{$service->name}" />
            <datalist id="services">
            {foreach $services as $service}
                <option value="{$service}">
            {/foreach}
            </datalist>
        </dd>
    </dl>

    <ul class="list-inline">
        <li>
            <input class="btn btn-primary" type="submit" value="enregistrer" />
        </li>
        <li>
            <a class="btn btn-default" href="{$smarty.const.URL}/index.php/services/nagios">annuler</a>
        </li>
    </ul>
</form>
