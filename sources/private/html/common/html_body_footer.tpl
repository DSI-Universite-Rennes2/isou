<div id="footer"><p>Isou {$CFG.version} - <a href="https://sourcesup.cru.fr/projects/isounagios/" title="Accéder à la page du projet libre Isou">Page officielle du projet</a></p></div>

{foreach $SCRIPTS as $SCRIPT}
	<script src="{$SCRIPT->src}" type="{$SCRIPT->type}"></script>
{/foreach}

</body>
</html>
