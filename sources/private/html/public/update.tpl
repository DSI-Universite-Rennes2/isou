{if isset($updatelink)}
<form id="update" action="{$smarty.const.URL}/index.php" method="get">
<p>Un changement de version a été détecté.</p>
<fieldset style="display:inline;padding: 1em;margin: 0.5em;">
<legend>Souhaitez-vous lancer la mise à jour ?</legend>
<p>
<label for="confirm-yes"><input type="radio" name="confirm" id="confirm-yes" value="1" />Oui</label>
<label for="confirm-no"><input type="radio" name="confirm" id="confirm-no" value="0" checked="checked" />Non</label></p>
</fieldset>
<p><input type="submit" value="valider" /></p>
<p style="margin:0.5em;">Note : la mise à jour peut prendre quelques minutes</p>
</form>
{else}
<p id="update">Site en cours de mise à jour...<br />
Merci de patienter quelques minutes.</p>
{/if}
