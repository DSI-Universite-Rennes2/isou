<div class="row">
	<div class="col-md-2">
		<ul class="flex-column nav nav-pills">
		{foreach $changelogs as $build => $changelog}
			<li class="nav-item"><a class="nav-link" href="#version-{$build}">{$build}</a></li>
		{/foreach}
		</ul>
	</div>

	<div class="col-md-10">
	{foreach $changelogs as $build => $changelog}
		<div id="version-{$build}">
			{$changelog}
		</div>
	{/foreach}
	</div>
</div>
