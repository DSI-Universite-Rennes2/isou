<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2 - DSI <dsi-contact@univ-rennes2.fr>
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use Isou\Helpers\Script;
use Isou\Helpers\Style;

$theme_version = '4.0';

$STYLES[] = new Style('//static.univ-rennes2.fr/bootstrap/5.3/css/bootstrap.min.css');
$STYLES[] = new Style('//static.univ-rennes2.fr/bootstrap-icons/1.11/font/bootstrap-icons.min.css');
$STYLES[] = new Style(URL.'/themes/bootstrap/css/common.css?v='.$theme_version);
$STYLES[] = new Style(URL.'/themes/rennes2/css/common.css?v='.$theme_version);

if (preg_match('#^dependances/service/[0-9]+/group/[0-9]+/content/edit/0$#', implode('/', $PAGE_NAME)) === 1) {
    $SCRIPTS[] = new Script(URL.'/scripts/dependencies.js');
} elseif (preg_match('#^evenements/[a-z]+/edit/[0-9]+$#', implode('/', $PAGE_NAME)) === 1) {
    $SCRIPTS[] = new Script(URL.'/scripts/events.js');
} elseif ($PAGE_NAME[0] === 'annonce') {
    $SCRIPTS[] = new Script(URL.'/scripts/tinymce/tinymce.min.js');
    $SCRIPTS[] = new Script(URL.'/scripts/announcement.js');
}

$SCRIPTS[] = new Script('//static.univ-rennes2.fr/bootstrap/5.3/js/bootstrap.bundle.min.js');
$SCRIPTS[] = new Script('//static.univ-rennes2.fr/barre-ent/script.min.js');

$ADDITIONAL_CONTENT = <<<EOF
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://webstat.univ-rennes2.fr/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', 15]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript';
    g.defer=true; g.async=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="http://webstat.univ-rennes2.fr/piwik.php?idsite=15" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
EOF;

$smarty->assign('ADDITIONAL_CONTENT', $ADDITIONAL_CONTENT);
