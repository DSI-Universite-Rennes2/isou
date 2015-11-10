		<dd>
			{if (isset($smarty.get.modify) && $smarty.get.modify == $dependency->idDependence) ||
				(isset($smarty.post.idDependence) && $smarty.post.idDependence == $dependency->idDependence)}
			<form method="post" action="{$smarty.const.URL}/index.php/dependances#{$smarty.get.S}">
			<p>
				<img src="{$smarty.const.URL}/images/arrow_tree_up.png" alt="lorsque" />
				<img src="{$smarty.const.URL}/images/{$STATES.{$dependency->stateOfParent}->src}" alt="{$STATES.{$dependency->stateOfParent}->alt}" />
				<span>{$dependency->name}</span>
				<a class="hidden" href="{$smarty.const.URL}/index.php/dependances?modify={$dependency->idDependence}#S{$i}" title="modifier">
					<img src="{$smarty.const.URL}/images/edit.png" alt="modifier">
				</a>
				<a class="hidden" href="{$smarty.const.URL}/index.php/dependances?delete={$dependency->idDependence}#S{$i}" title="supprimer">
					<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer">
				</a>
			</p>
			<p>
				Nouvel état :
					{html_options id=stateOfParent2 name=stateOfParent options=$optionState selected=$dependency->stateOfParent}
				<input type="submit" name="modify" value="Enregistrer">
				<input type="submit" name="cancel" value="Annuler">
			</p>
			<p>
				<label for="description">Description : </label>
				<textarea id="description" name="description" cols="50" rows="3">{$dependency->message|default:''}</textarea>
				<input class="hidden" type="hidden" name="idDependence" value="{$dependency->idDependence}">
				<input class="hidden" type="hidden" name="childService" value="{$dependency->idService}">
				<input class="hidden" type="hidden" name="parentService" value="{$dependency->idServiceParent}">
				<input class="hidden" type="hidden" name="newStateForChild" value="{$dependency->newStateForChild}">
			</p>
			</form>

			{else}

			<p>
				<img src="{$smarty.const.URL}/images/arrow_tree_up.png" alt="lorsque" />
				<img src="{$smarty.const.URL}/images/{$STATES.{$dependency->stateOfParent}->src}" alt="{$STATES.{$dependency->stateOfParent}->alt}" />
				<span>{$dependency->name}</span>
				<a href="{$smarty.const.URL}/index.php/dependances?modify={$dependency->idDependence}#S{$i}" title="modifier">
					<img src="{$smarty.const.URL}/images/edit.png" alt="modifier">
				</a>
				<a href="{$smarty.const.URL}/index.php/dependances?delete={$dependency->idDependence}#S{$i}" title="supprimer">
					<img src="{$smarty.const.URL}/images/drop.png" alt="supprimer">
				</a>
			</p>

			{if !empty($dependency->message)}
				<p>-> {$dependency->message}</p>
			{/if}

			{/if}
		</dd>
