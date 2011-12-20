<div id="content">
<a name="content"></a>
<h2>L'annonce est un message qui sera affiché en bandeau sur toutes les pages publiques.</h2>

{if !empty($error)}
<p id="update">{$error}</p>
{/if}

<form action="{$smarty.const.URL}/index.php/annonce" method="post">

<p><label for="message">Contenu de l'annonce :</label>
<textarea id="message" name="message" cols="100" rows="10">{$message}</textarea></p>
<p><label for="afficher">Afficher l'annonce</label><input type="checkbox" name="afficher" id="afficher" value="1"{$afficher}</p>
<p><input type="submit" name="submit" /></p>

</form>

</div>

