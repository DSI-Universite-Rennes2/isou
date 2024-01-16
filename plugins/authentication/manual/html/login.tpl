<h2>Authentification locale</h2>

<form action="{$smarty.const.URL}/index.php/connexion/manual" class="form" method="post">

	{include file="common/messages_form.tpl"}

	<dl class="dl-horizontal">
		<div class="form-group">
			<dt>
				<label for="username">Nom utilisateur</label>
			</dt>
			<dd>
				<input class="form-control" id="username" name="username" required="1" type="text" value="{$smarty.post.username|default:''}" />
			</dd>
		</div>
		<div class="form-group">
			<dt>
				<label for="password">Mot de passe</label>
			</dt>
			<dd>
				<input class="form-control" id="password" name="password" required="1" type="password" value="{$smarty.post.password|default:''}" />
			</dd>
		</div>
	</dl>

	<ul class="list-inline">
		<li class="list-inline-item">
			<input class="btn btn-primary" type="submit" value="Se connecter" />
		</li>
	</ul>

</form>
