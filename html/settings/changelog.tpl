<div class="row">
	<div class="col-md-2">
		<ul class="nav nav-pills nav-stacked">
		{foreach $changelogs as $build => $changelog}
			<li><a href="#version-{$build}">{$build}</a></li>
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
