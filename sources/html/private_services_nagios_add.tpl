	{if !empty($error)}
		<p id="update">{$error}</p>
	{/if}

	<form id="addform1" method="post" action="{$smarty.const.URL}/index.php/services?service=nagios">
		<fieldset>
			<legend>Ajouter un service</legend>
			<p class="header">
				<label class="longbox" for="servicename">Nom informatique du service</label>
			</p>
			<p>
				{* {html_options id=servicename name=name options=$arrayServices} *}
				{html_options id=servicename name=name output=$arrayServices values=$arrayServices}
				<input type="submit" name="insert" value="Enregistrer" />
			</p>
		</fieldset>
	</form>

	<form id="addform2" method="post" action="{$smarty.const.URL}/index.php/services?service=nagios">
		<fieldset>
			<legend>Ajouter un hôte</legend>
			<p class="header">
				<label class="longbox" for="hostname">Nom informatique de l'hôte</label>
			</p>
			<p>
				{* {html_options id=hostname name=name options=$arrayHosts} *}
				{html_options id=servicename name=name output=$arrayHosts values=$arrayHosts}
				<input type="submit" name="insert" value="Enregistrer" />
			</p>
		</fieldset>
	</form>
