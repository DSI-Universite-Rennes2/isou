<h1 class="sr-only">Flux RSS</h1>

{if count($categories) === 0}
	<p class="alert alert-info">Aucun service disponible pour le moment.</p>
{else}
	<div class="row">
		<div class="col-md-6">
			<h2>Flux RSS sélectif</h2>
			<p class="alert alert-info">Sélectionnez les services qui vous intéressent afin de ne recevoir, par flux RSS, que les informations à propos de ces services.</p>

			<form method="post" action="{$smarty.const.URL}/index.php/rss/config#rss-url">
				<ul class="list-unstyled">
				{foreach $categories as $category}
					{if count($category->services) === 0}
						{continue}
					{/if}
					<li>
					<details>
						<summary>{$category->name}</summary>
						<ul class="list-unstyled">
						{foreach $category->services as $service}
							<li>
								<input type="checkbox" name="keys[{$service->id}]" id="key-{$service->id}" value="{$service->id}"{if isset($smarty.post.keys[$service->id])} checked="1"{/if} />
								<label for="key-{$service->id}">{$service->name}</label>
							</li>
						{/foreach}
						</ul>
					</details>
					</li>
				{/foreach}
				</ul>

				<p><input class="btn btn-primary" type="submit" name="generer" value="Générer le flux RSS" id="generer" /></p>
			</form>

			{if $rss_url !== null}
				<p id="rss-url">Vous pouvez consulter les actualités des services sélectionnés précédemment en utilisant ce lien RSS : <a href="{$rss_url}" title="lien vers le flux RSS">{$rss_url|rawurldecode}</a>.</p>
			{/if}
		</div>

		<div class="col-md-6">
			<h2>Flux RSS complet</h2>
			<p><a class="btn btn-primary" href="{$smarty.const.URL}/rss.php">Accéder au flux complet</a></p>
		</div>
	</div>
{/if}
