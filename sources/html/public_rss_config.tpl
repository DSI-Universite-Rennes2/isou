<div id="content">
<a name="content"></a>
{if count($categoryItems) > 0}
<p>Sélectionnez les services qui vous intéressent afin de ne recevoir, par flux RSS, que les informations à propos de ces services ou cliquez directement sur le bouton "Générer le flux RSS" en bas de page pour surveiller tous les services.</p>

<form id="configForm" method="post" action="{$smarty.const.URL}/index.php/rss/config">
	<ul>
		{section name=i loop=$categoryItems}
			<li>
				<span>{$categoryItems[i][1]}</span>
				<ul>
					{section name=j loop=$serviceItems}
						{if $serviceItems[j][0] == $categoryItems[i][0]}
							<li>
								<input type="checkbox" name="key_{$serviceItems[j][2]}" id="key_{$serviceItems[j][2]}" value="{$serviceItems[j][2]}" />
								<label for="key_{$serviceItems[j][2]}">
									{$serviceItems[j][1]}
								</label>
							</li>
						{/if}
					{/section}
				</ul>
			</li>
		{/section}
	</ul>
	<p>
		<input type="submit" name="generer" value="Générer le flux RSS" id="generer" />
	</p>
</form>

{if isset($urlKey)}
<p>{$urlKey|default:''}</p>
{else}
<p></p>
{/if}
{else}
<p id="no-event">Aucun service disponible pour le moment.</p>
{/if}
</div>

