<?php

use Isou\Helpers\Script;
use Isou\Helpers\Style;

$theme_version = '1';

$STYLES[] = new Style('//static.univ-rennes2.fr/bootstrap/3.3/css/bootstrap.min.css');
$STYLES[] = new Style('//static.univ-rennes2.fr/bootstrap/3.3/rennes2/bootstrap.css');
$STYLES[] = new Style(URL.'/themes/bootstrap/css/common.css?v='.$theme_version);
$STYLES[] = new Style(URL.'/themes/rennes2/css/common.css?v='.$theme_version);

if (isset($current_page->url) === true) {
    switch ($current_page->url) {
        case 'actualite':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/news.css?v='.$theme_version);
            break;
        case 'calendrier':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/calendar.css?v='.$theme_version);
            break;
        case 'dependances':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/dependencies.css?v='.$theme_version);
            break;
        case 'rss':
            $STYLES[] = new Style(URL.'/themes/bootstrap/css/rss.css?v='.$theme_version);
    }
}

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
