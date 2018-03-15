<form method="post" action="{$smarty.const.URL}/index.php/configuration/plugins/{$plugin->codename}">

    {include file="common/messages_form.tpl"}

    <fieldset>
        <legend>{$plugin->name}</legend>

        <dl>
            <div class="form-group">
                <dt class="form-topics-dt">
                    <label for="plugin-isou-enable">Activer</label>
                </dt>
                <dd class="form-values-dd">
                    {html_radios id="plugin-isou-enable" name="plugin_isou_enable" options=$options_yes_no selected=$plugin->active disabled="1"}
                </dd>
            </div>
            <div class="form-group">
                <dt class="form-topics-dt">
                    <label for="tolerance" aria-describedby="tolerance-aria-describedby">Tolérance d'interruption (en secondes)</label>
                </dt>
                <dd class="form-values-dd">
                    <input type="number" step="60" min="0" max="600" name="tolerance" id="tolerance" value="{$plugin->settings->tolerance}" /><br />
                    <span id="tolerance-aria-describedby">exemple : ne pas afficher sur les pages publiques les interruptions inférieures à 300 secondes (5 minutes)</span>
                </dd>
            </div>
        </dl>
    </fieldset>

    <ul class="list-inline">
        <li>
            <input class="btn btn-primary" type="submit" value="enregistrer" />
        </li>
    </ul>
</form>
