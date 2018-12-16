{if $count_plugins === 0}
	<p class="alert alert-danger">Aucune méthode d'authentification n'est activée.</p>
{else}
	{if empty($form) === false}
		{* Affiche un formulaire d'authentification. *}
		<div>{$form}</div>
	{else}
		{* Affiche la liste des backends d'authentification. *}
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class>Choisissez une méthode d'authentification :</p>
			</div>
			<div class="panel-body">
				<div class="row">
					<ul class="list-inline col-md-6 col-md-offset-3">
					{foreach $plugins as $plugin}
						<li class="col-md-6 text-center"><a class="btn btn-primary btn-lg" href="{$smarty.const.URL}/index.php/connexion/{$plugin->codename}">{$plugin->name}</a></li>
					{/foreach}
					</ul>
				</div>
			</div>
		</div>
	{/if}
{/if}
