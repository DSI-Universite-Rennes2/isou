</div> {* / .container *}

<footer class="footer text-center" role="contentinfo">
	<p><span id="footer-span">Isou {$CFG.version} - <a href="https://sourcesup.cru.fr/projects/isounagios/" title="Accéder à la page du projet libre Isou">Page officielle du projet</a></span></p>
</footer>

{foreach $SCRIPTS as $SCRIPT}
<script src="{$SCRIPT->src}" type="{$SCRIPT->type}"></script>
{/foreach}

</body>
</html>
