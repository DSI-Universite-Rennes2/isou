<table class="caption-top table table-striped">
	<thead>
		<tr>
			<th>Type d'authentification</th>
			<th>Nom utilisateur</th>
			<th>Nom affiché</th>
			<th>Email</th>
			<th>Admin</th>
			<th>Date de création</th>
			<th>Dernier accès</th>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}
		<tr>
			<td>{$user->authentification}</td>
			<td>{$user->username}</td>
			<td>{$user}</td>
			<td>{$user->email}</td>
			<td>{if empty($user->admin) === true}Non{else}Oui{/if}</td>
			<td>{$user->timecreated|date_format:"%a %d %b %Y à %Hh%M"}</td>
			<td>{if $user->lastaccess === null}jamais{else}{$user->lastaccess|date_format:"%a %d %b %Y à %Hh%M"}{/if}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
