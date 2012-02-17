<div id="content">

<h2>Plages IP</h2>
<form method="post" action="{$smarty.const.URL}/index.php/configuration?type=advanced#form-iplocal" id="form-iplocal">
{if isset($smarty.post.error.iplocal)}
{if count($smarty.post.error.iplocal) == 0}
	<p id="update">La plage a été ajoutée.</p>
{else}
	{foreach $smarty.post.error.iplocal as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}
{if isset($smarty.post.error.ip_local)}
{if count($smarty.post.error.ip_local) == 0}
	<p id="update">La plage a été supprimée.</p>
{else}
	{foreach $smarty.post.error.ip_local as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<label class="label" for="iplocal">Plage IP des campus de l'université.</label>
	<span class="input">
	{foreach $CFG.ip_local as $ip}
	{if isset($ip.1)}de {$ip.0} à {$ip.1}{else}{$ip.0}{/if}
		<a href="{$smarty.const.URL}/index.php/configuration?type=advanced&amp;action=drop&amp;key=ip_local&amp;index={$ip@iteration}#form-iplocal" title="supprimer la plage ip {if isset($ip.1)}de {$ip.0} à {$ip.1}{else}{$ip.0}{/if}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	de <input type="text" name="iplocal1" id="iplocal1" value="{$smarty.post.iplocal1|default:''}" />
	à <input type="text" name="iplocal2" id="iplocal2" value="{$smarty.post.iplocal2|default:''}" /><input type="submit" name="iplocalsubmit" value="ajouter" />
	</span><br />
	<span class="key">ip_local</span><br />
</p>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration?type=advanced#form-ipservice" id="form-ipservice">
{if isset($smarty.post.error.ipservice)}
{if count($smarty.post.error.ipservice) == 0}
	<p id="update">La plage a été ajoutée.</p>
{else}
	{foreach $smarty.post.error.ipservice as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}
{if isset($smarty.post.error.ip_service)}
{if count($smarty.post.error.ip_service) == 0}
	<p id="update">La plage a été supprimée.</p>
{else}
	{foreach $smarty.post.error.ip_service as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<label class="label" for="ipservice">Plage IP du service administrant Isou.</label>
	<span class="input">
	{foreach $CFG.ip_service as $ip}
	{if isset($ip.1)}{$ip.0} - {$ip.1}{else}{$ip.0}{/if}
		<a href="{$smarty.const.URL}/index.php/configuration?type=advanced&amp;action=drop&amp;key=ip_service&amp;index={$ip@iteration}#form-ipservice" title="supprimer la plage ip {if isset($ip.1)}de {$ip.0} à {$ip.1}{else}{$ip.0}{/if}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	de <input type="text" name="ipservice1" id="ipservice1" value="{$smarty.post.ipservice1|default:''}" />
	à <input type="text" name="ipservice2" id="ipservice2" value="{$smarty.post.ipservice2|default:''}" /><input type="submit" name="ipservicesubmit" value="ajouter" />
	</span><br />

	<span class="key">ip_service</span>
</p>
</form>

<h2>Administrateurs</h2>
<form method="post" action="{$smarty.const.URL}/index.php/configuration?type=advanced#form-adminusers" id="form-adminusers">

{if isset($smarty.post.error.adminusers)}
{if count($smarty.post.error.adminusers) == 0}
	<p id="update">L'administrateur a été ajouté.</p>
{else}
	{foreach $smarty.post.error.adminusers as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

{if isset($smarty.post.error.admin_users)}
{if count($smarty.post.error.admin_users) == 0}
	<p id="update">L'administrateur a été supprimée.</p>
{else}
	{foreach $smarty.post.error.admin_users as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<label class="label" for="adminusers">Liste des utilisateurs administrateurs.</label>
	<span class="input">
	{foreach $CFG.admin_users as $user}
	{$user}
		<a href="{$smarty.const.URL}/index.php/configuration?type=advanced&amp;action=drop&amp;key=admin_users&amp;index={$user@iteration}#form-adminusers" title="supprimer l'utilisateur {$user}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	<input type="text" name="adminusers" id="adminusers" value="{$smarty.post.adminusers|default:''}" /><input type="submit" name="adminuserssubmit" value="ajouter" />
	</span><br />
	<span class="key">admin_users</span><br />
</p>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration?type=advanced#form-adminmails" id="form-adminmails">
{if isset($smarty.post.error.adminmails)}
{if count($smarty.post.error.adminmails) == 0}
	<p id="update">Le mail a été ajouté.</p>
{else}
	{foreach $smarty.post.error.adminmails as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

{if isset($smarty.post.error.admin_mails)}
{if count($smarty.post.error.admin_mails) == 0}
	<p id="update">Le mail a été supprimée.</p>
{else}
	{foreach $smarty.post.error.admin_mails as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<span class="label"><label for="adminmails">Liste des mails administrateurs.</label><br />
	<span class="example">note : sert aux diverses notifications (alerte, rapport, etc...)</span></span>
	<span class="input">
	{foreach $CFG.admin_mails as $mail}
	{$mail}
		<a href="{$smarty.const.URL}/index.php/configuration?type=advanced&amp;action=drop&amp;key=admin_mails&amp;index={$mail@iteration}#form-adminmails" title="supprimer l'utilisateur {$mail}">
			<img alt="supprimer" src="{$smarty.const.URL}/images/drop.png" />
		</a><br />
	{/foreach}
	<input type="text" name="adminmails" id="adminmails" value="{$smarty.post.adminmails|default:''}" /><input type="submit" name="adminmailssubmit" value="ajouter" />
	</span><br />
	<span class="key">admin_mails</span><br />
</p>
</form>

<form method="post" action="{$smarty.const.URL}/index.php/configuration?type=advanced#form-localmail" id="form-localmail">
{if isset($smarty.post.error.localmail)}
{if count($smarty.post.error.localmail) == 0}
	<p id="update">Le mail a été ajouté.</p>
{else}
	{foreach $smarty.post.error.localmail as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

{if isset($smarty.post.error.local_mail)}
{if count($smarty.post.error.local_mail) == 0}
	<p id="update">Le mail a été supprimée.</p>
{else}
	{foreach $smarty.post.error.local_mail as $error}
	<p id="update">{$error}</p>
	{/foreach}
{/if}
{/if}

<p>
	<span class="label"><label for="localmail">Expéditeur des mails envoyés par {$smarty.const.NAME}.</label><br />
	<span class="example">note : si l'expéditeur n'est pas renseigné, c'est l'adresse des destinataires qui sera utilisée</span></span>
	<span class="input">
	<input type="text" name="localmail" id="localmail" value="{$smarty.post.localmail|default:$CFG.local_mail}" /><input type="submit" name="localmailsubmit" value="ajouter" />
	</span><br />
	<span class="key">local_mail</span><br />
</p>
</form>

</div>
