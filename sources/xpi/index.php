<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Fisou - Extension d'Isou pour Firefox</title>
	<!-- <link rel="stylesheet" type="text/css" href="style-www.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="style-print.css" media="print" /> -->
</head>
<body>
	<header>
		<p>Fisou est une extension pour Firefox 3 et versions supérieures.<br />
		Description : Fisou interroge Isou et renseigne l'état des services dans la barre de statut de Firefox<br />
		Télécharger : <a href="fisou.xpi" hash="sha1:<?php echo sha1_file("fisou.xpi");?>" onclick="return install(event);">Fisou.xpi</a></p>
	</header>
	<nav>
		<ul>
		<li><a href="#fisou050">Fisou 0.5.0</a></li>
		<li><a href="#fisou042">Fisou 0.4.2</a></li>
		<li><a href="#fisou041">Fisou 0.4.1</a></li>
		<li><a href="#fisou04">Fisou 0.4</a></li>
		<li><a href="#fisou03">Fisou 0.3</a></li>
		<li><a href="#fisou02">Fisou 0.2</a></li>
		<li><a href="#fisou01">Fisou 0.1beta</a></li>
		</ul>
	</nav>
	<section>
		<a name="fisou050"></a>
		<h1>Fisou 0.5.0</h1>
		<p>Date de sortie : 20 juin 2011</p>
		<p>Télécharger : <a href="fisou0.5.0.xpi" hash="sha1:<?php echo sha1_file("fisou0.5.0.xpi");?>" onclick="return install(event);">Fisou0.5.0.xpi</a></p>
		<p>Modifications :</p>
		<ul>
		<li>ajout du support pour Firefox 5</li>
		</ul>
	</section>
	<section>
		<a name="fisou042"></a>
		<h1>Fisou 0.4.2</h1>
		<p>Date de sortie : 3 mars 2011</p>
		<p>Télécharger : <a href="fisou0.4.2.xpi" hash="sha1:<?php echo sha1_file("fisou0.4.2.xpi");?>" onclick="return install(event);">Fisou0.4.2.xpi</a></p>
		<p>Modifications :</p>
		<ul>
		<li>correction d'un bug lorsque le service interrompu était dans un état "vert" (exemple : mode maintenance sur certains services web)</li>
		<li>prise en compte des évènements planifiés</li>
		<li>correction d'une erreur de syntaxe dans le fichier fisou.js</li>
		<li>correction d'un bug pouvant masquer fisou lorsqu'on l'arrête alors qu'un ou plusieurs services étaient signalés perturbés</li>
		</ul>
	</section>
	<section>
		<a name="fisou041"></a>
		<h1>Fisou 0.4.1</h1>
		<p>Date de sortie : 1 mars 2011</p>
		<p>Télécharger : <a href="fisou0.4.1.xpi" hash="sha1:<?php echo sha1_file("fisou0.4.1.xpi");?>" onclick="return install(event);">Fisou0.4.1.xpi</a></p>
		<p>Modifications :</p>
		<ul>
		<li>limitation à 75 caractères lors de l'affichage du popup listant les services perturbés</li>
		<li>correction d'un bug sur la définition du numéro de version de fisou</li>
		</ul>
		<p>Todo : prise en compte des évènements planifiés.</p>
	</section>
	<section>
		<a name="fisou04"></a>
		<h1>Fisou 0.4</h1>
		<p>Date de sortie : 17 février 2011</p>
		<p>Télécharger : <a href="fisou0.4.xpi" hash="sha1:<?php echo sha1_file("fisou0.4.xpi");?>" onclick="return install(event);">Fisou0.4.xpi</a></p>
		<p>Modifications :</p>
		<ul>
		<li>compatibilité avec Firefox 4</li>
		<li>ajout d'une description de la panne si elle existe</li>
		<li>correction de bugs</li>
		<li>mise en place d'une notification (popup) par défaut</li>
		<li>changement de la valeur de rafraichissement par défaut (1min au lieu de 5min)</li>
		</ul>
		<p>Todo : prise en compte des évènements planifiés.</p>
	</section>
	<section>
		<a name="fisou03"></a>
		<h1>Fisou 0.3</h1>
		<p>Date de sortie : 26 février 2010</p>
		<p>Télécharger : <a href="fisou0.3.xpi" hash="sha1:<?php echo sha1_file("fisou0.3.xpi");?>" onclick="return install(event);">Fisou0.3.xpi</a></p>
		<p>Modifications :</p>
		<ul>
		<li>réintégration du bouton "mise à jour"</li>
		<li>prise en compte lorsqu'Isou est indisponible</li>
		<li>utilisation de la fonction native de Firefox pour parser Json() au lieu d'un Eval()</li>
		<li>notification lorsqu'un service tombe en panne (option expérimentale)</li>
		</ul>
	</section>
	<section>
		<a name="fisou02"></a>
		<h1>Fisou 0.2</h1>
		<p>Date de sortie : 17 février 2010</p>
		<p>Télécharger : <a href="fisou0.2.xpi" hash="sha1:<?php echo sha1_file("fisou0.2.xpi");?>" onclick="return install(event);">Fisou0.2.xpi</a></p>
		<p>Modifications :</p>
		<ul>
		<li>ajout d'un logo R2 dans la barre de statut, à la place de la chaine "Aucun service perturbé"</li>
		<li>ajout d'une police rouge dans la barre de statut</li>
		<li>ajout de la date du dernier scan lorsqu'on clique sur Fisou dans la barre de statut</li>
		<li>le double-clic sur Fisou permet de lancer un onglet sur le site https://services.uhb.fr/isou</li>
		<li>remplacement du bouton "mettre à jour" par "démarrer/arrêter" dans le menu contextuel</li>
		<li>ajout des options delay_sync et auto_sync</li>
		</ul>
	</section>
	<section>
		<a name="fisou01"></a>
		<h1>Fisou 0.1 beta</h1>
		<p>Date de sortie : 15 février 2010</p>
		<p>Télécharger : <a href="fisou0.1b.xpi" hash="sha1:<?php echo sha1_file("fisou0.1b.xpi");?>" onclick="return install(event);">Fisou0.1b.xpi</a></p>
		<p>Fonctionalités :</p>
		<ul>
		<li>scan toutes les 5 minutes Isou</li>
		<li>permet di'ouvrir un onglet sur le site https://services.uhb.fr/isou via le menu contextuel</li>
		</ul>
	</section>
	<footer>CRI Rennes 2 - 2010</footer>
	<script type="application/javascript">
	<!--
	function install (aEvent)
	{
		var params = {
		"Fisou": { URL: aEvent.target.href,
			// IconURL: "https://services.univ-rennes2.fr/isou/xpi/icon.png",
			Hash: aEvent.target.getAttribute("hash"),
			toString: function () { return this.URL; }
			}
		};

		InstallTrigger.install(params);
		return false;
	}
	-->
	</script>

</body>
</html>
