	<form method="post" action="{$smarty.const.URL}/index.php/services?service=nagios">
		<a name="edit"></a>
		<fieldset>
			<legend>Edition du service Nagios {$currentEdit->name}</legend>
			<p>
				<label class="label-form" for="name">Nom du service Nagios :</label>
				{html_options id=name name=name values=$arrayServices output=$arrayServices selected=$currentEdit->name}
			</p>
			<p>
				<input type="submit" name="modify" value="Enregistrer" />
				<input type="submit" name="cancel" value="Annuler" />
				<input class="hidden" type="hidden" name="idService" value="{$currentEdit->idService}">
			</p>
		</fieldset>
	</form>
