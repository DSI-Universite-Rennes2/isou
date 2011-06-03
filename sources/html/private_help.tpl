<div id="content">
<a name="content"></a>
	<div id="sommaire">
	<h2><a href="#" name="sommaire">Sommaire</a></h2>
	<ul>
		<li>
			<h3><a href="{$smarty.const.URL}/index.php/aide#administration_generale_et_courante" title="aller directement au contenu">Administration générale et courante</a></h3>
			<ul>
				<li><a href="{$smarty.const.URL}/index.php/aide#signaler_une_operation_exceptionnelle" title="aller directement au contenu">Signaler une opération exceptionnelle (maintenance, mise à jour d'une application, etc...)</a></li>
				<ul>
					<li><a href="{$smarty.const.URL}/index.php/aide#forcer_un_service_isou" title="aller directement au contenu">Forcer un service ISOU</a></li>
				</ul>
				<li><a href="{$smarty.const.URL}/index.php/aide#signaler_une_interruption_reguliere" title="aller directement au contenu">Signaler une interruption régulière (arrêt journalier d'une base de données, verrouillage d'une application, etc...)</a></li>
				<li><a href="{$smarty.const.URL}/index.php/aide#signaler_une_fermeture_de_service" title="aller directement au contenu">Signaler une fermeture de service</a></li>
				<li><a href="{$smarty.const.URL}/index.php/aide#annoncer_un_evenement_extraordinaire" title="aller directement au contenu">Annoncer un évènement extra-ordinaire, hors ISOU (ex : interruption des serveurs)</a></li>
			</ul>
		</li>
		<li>
			<h3><a href="{$smarty.const.URL}/index.php/aide#administration_avancee" title="aller directement au contenu">Administration avancée</a></h3>
			<ul>
				<li>
					<a href="{$smarty.const.URL}/index.php/aide#ajouter_son_service_dans_isou" title="aller directement au contenu">Ajouter son service dans ISOU</a>
					<ul>
						<li><a href="{$smarty.const.URL}/index.php/aide#ajouter_un_service_dans_isou" title="aller directement au contenu">Ajouter un service dans ISOU</a></li>
						<li><a href="{$smarty.const.URL}/index.php/aide#ajouter_un_service_monitoré_par_nagios" title="aller directement au contenu">Ajouter un service monitoré par Nagios</a></li>
						<li><a href="{$smarty.const.URL}/index.php/aide#affecter_des_dependances" title="aller directement au contenu">Affecter des dépendances</a></li>
					</ul>
				</li>
				<li><a href="{$smarty.const.URL}/index.php/aide#gerer_les_categories" title="aller directement au contenu">Gérer les catégories</a></li>
			</ul>
		</li>
	</ul>
	</div>

	<div id="help">
	<h2><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="administration_generale_et_courante">Administration générale et courante</a></h2>
		<h3><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="signaler_une_operation_exceptionnelle">Signaler une opération exceptionnelle</a></h3>
		<p>
			Pour signaler une opération exceptionnelle, comme la maintenance d'un service, la mise à jour d'une application, etc... vous devez aller sur la <a href="{$smarty.const.URL}/index.php/evenements" title="aller sur la page des évènements">page des évènements</a>, accessible via l'onglet du même nom.<br />
			Cliquez sur le bouton vert, "ajouter un évènement". Un formulaire apparait alors.
		</p>
		<p>Il y a 5 champs à remplir.</p>
		<ul>
			<li>Type d'opération : Choisir dans la liste proposée "Opération exceptionnelle"</li>
			<li>Services mis en maintenance : Choisir dans la liste proposée le service désiré. Si il n'apparait pas, aller à la rubrique "<a href="{$smarty.const.URL}/index.php/aide#ajouter_son_service_dans_isou" title="aller directement au contenu">ajouter son service dans ISOU</a>".</li>
			<li>Date de la prochaine maintenance : Définir la date de début d'interventation soit à l'aide du calendrier via l'icône <img alt="icône du calendrier" src="{$smarty.const.URL}/images/calendar.png" />, soit directement en saisissant la date au format JJ/MM/AAAA HH:MM (jour/mois/année heure:minute).</li>
			<li>Date de fin de la maintenance : Définir la date de fin d'interventation (estimer de préférence une fourchette un peu plus large) soit à l'aide du calendrier via l'icône <img alt="icône du calendrier" src="{$smarty.const.URL}/images/calendar.png" />, soit directement en saisissant la date au format JJ/MM/AAAA HH:MM (jour/mois/année heure:minute).</li>
			<li>Raison de la maintenance : Ce champs est facultatif. Il permet de renseigner l'utilisateur sur les raisons de la maintenance. Ce message ne doit pas être technique et compréhensible, plus ou moins, de tous.</li>
		</ul>
		<p>Lorsque vous avez appuyez sur le bouton "enregistrer", vous pouvez vérifier que votre évènement a bien été pris en compte, en vous rendant sur <a href="{$smarty.const.URL}/index.php/actualite" title="aller sur la page des actualités">les actualités</a>, <a href="{$smarty.const.URL}/index.php/liste" title="aller sur la page de vue en liste">la liste</a> ou <a href="{$smarty.const.URL}/index.php/calendrier" title="aller sur la page du calendrier">le calendrier</a>.</p>
		<h4><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="forcer_un_service_isou">Forcer un service ISOU</a></h4>
			<p>
				Cette fonction permet à ISOU, lorsqu'un service Nagios est mal monitoré (ex: détection de problème, alors qu'il n'y en a pas, et inversement), d'ignorer ces remontées.<br />
				Pour activer cette fonction, il faut choisir un statut dans le menu déroulant "Forcer l'état du service" lors de l'enregistrement d'un évènement non prévu. Un cadena apparaîtra devant le nom du service dans la liste des évènements non prévus.<br />
				<!-- todo cron: N'oubliez pas de "déverrouiller" le service lorsque l'évènement est terminé. -->
			</p>

		<h3><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="signaler_une_interruption_reguliere">Signaler une interruption régulière</a></h3>
		<p>
			Pour signaler une opération régulière, comme l'arrêt journalier d'une base de données, le verrouillage d'une application, etc... vous devez aller sur la <a href="{$smarty.const.URL}/index.php/evenements" title="aller sur la page des évènements">page des évènements</a>, accessible via l'onglet du même nom.<br />
			Cliquez sur le bouton vert, "ajouter un évènement". Un formulaire apparait alors.
		</p>
		<p>Il y a 6 champs à remplir.</p>
		<ul>
			<li>Type d'opération : Choisir dans la liste proposée "Opération régulière"</li>
			<li>Services mis en maintenance : Choisir dans la liste proposée le service désiré. Si il n'apparait pas, aller à la rubrique "<a href="{$smarty.const.URL}/index.php/aide#ajouter_son_service_dans_isou" title="aller directement au contenu">ajouter son service dans ISOU</a>".</li>
			<li>Date de la prochaine maintenance : Définir la première date d'interruption à venir soit à l'aide du calendrier via l'icône <img alt="icône du calendrier" src="{$smarty.const.URL}/images/calendar.png" />, soit directement en saisissant la date au format JJ/MM/AAAA HH:MM (jour/mois/année heure:minute).</li>
			<li>Date de fin de la maintenance : Définir la première date de fin d'interruption à venir soit à l'aide du calendrier via l'icône <img alt="icône du calendrier" src="{$smarty.const.URL}/images/calendar.png" />, soit directement en saisissant la date au format JJ/MM/AAAA HH:MM (jour/mois/année heure:minute).</li>
			<li>Périodicité : Choisir entre un arrêt journalier ou hebdomadaire.</li>
			<li>Raison de la maintenance : Ce champs est facultatif. Il permet de renseigner l'utilisateur sur les raisons de la maintenance. Ce message ne doit pas être technique et compréhensible, plus ou moins, de tous.</li>
		</ul>
		<p>Lorsque vous avez appuyez sur le bouton "enregistrer", vous pouvez vérifier que votre évènement a bien été pris en compte, en vous rendant sur la page de <a href="{$smarty.const.URL}/index.php/liste" title="aller sur la page de vue en liste">vue en liste</a>. Les interruptions régulières ne sont pas répertoriées dans les actualités, ni dans le calendrier.</p>

		<h3><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="signaler_une_fermeture_de_service">Signaler une fermeture de service</a></h3>
		<p>
			Pour signaler une fermeture de service, vous devez aller sur la <a href="{$smarty.const.URL}/index.php/evenements" title="aller sur la page des évènements">page des évènements</a>, accessible via l'onglet du même nom.<br />
			Cliquez sur le bouton vert, "ajouter un évènement". Un formulaire apparait alors.
		</p>
		<p>Il y a 5 champs à remplir.</p>
		<ul>
			<li>Type d'opération : Choisir dans la liste proposée "Fermture de service"</li>
			<li>Services mis en maintenance : Choisir dans la liste proposée le service désiré. Si il n'apparait pas, aller à la rubrique "<a href="{$smarty.const.URL}/index.php/aide#ajouter_son_service_dans_isou" title="aller directement au contenu">ajouter son service dans ISOU</a>".</li>
			<li>Date de la prochaine maintenance : Définir la date de fermeture, soit à l'aide du calendrier via l'icône <img alt="icône du calendrier" src="{$smarty.const.URL}/images/calendar.png" />, soit directement en saisissant la date au format JJ/MM/AAAA HH:MM (jour/mois/année heure:minute).</li>
			<li>Date de fin de la maintenance : Ce champs est facultatif. Définir la date de réouverture, soit à l'aide du calendrier via l'icône <img alt="icône du calendrier" src="{$smarty.const.URL}/images/calendar.png" />, soit directement en saisissant la date au format JJ/MM/AAAA HH:MM (jour/mois/année heure:minute).</li>
			<li>Raison de la maintenance : Ce champs est facultatif. Il permet de renseigner l'utilisateur sur les raisons de la maintenance. Ce message ne doit pas être technique et compréhensible, plus ou moins, de tous.</li>
		</ul>
		<p>Lorsque vous avez appuyez sur le bouton "enregistrer", vous pouvez vérifier que votre évènement a bien été pris en compte, en vous rendant sur la page de <a href="{$smarty.const.URL}/index.php/liste" title="aller sur la page de vue en liste">vue en liste</a>. Les interruptions régulières ne sont pas répertoriées dans les actualités, ni dans le calendrier.</p>

		<h3><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="annoncer_un_evenement_extraordinaire">Annoncer un évènement extra-ordinaire</a></h3>
		<p>Annoncer un évènement extra-ordinaire permet d'afficher un bandeau sur toutes les pages d'ISOU.</p>
		<p>Pour activer ou désactiver le bandeau, il suffit de cocher ou décocher la case "Afficher l'annonce".</p>

	<h2><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="administration_avancee">Administration avancée</a></h2>
		<p>Cette partie concerne le gestionnaire de l'application. Il est préférable de s'adresser à lui pour toutes ces manipulations.</p>
		<h3><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="ajouter_son_service_dans_isou">Ajouter son service dans ISOU</a></h3>
			<h4><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="ajouter_un_service_dans_isou">Ajouter un service dans ISOU</a></h4>
				<p>
					Pour cela rendez-vous sur la <a href="{$smarty.const.URL}/index.php/services?service=isou" title="aller sur la page des services">page des "services"</a>. Choisissez le <a href="{$smarty.const.URL}/index.php/services?service=isou" title="aller sur la page des services ISOU">sous-onglet "Services ISOU"</a>.<br />
					Cliquez sur le bouton vert, "Ajouter un service ISOU".
				</p>
				<p>Il y a 3 champs à remplir.</p>
				<ul>
					<li>Nom du service pour les usagers : Saisir le nom du service désiré. Exemple: ENT.</li>
					<li>Nom de la catégorie du service : Saisir la catégorie dans laquelle doit apparaitre le service. Exemple: Général.</li>
					<li>Remarque : Ce champs est facultatif. Il permet d'afficher une remarque générale sur la page de <a href="{$smarty.const.URL}/index.php/liste" title="aller sur la page de vue en liste">vue en liste</a>.</li>
				</ul>
			<h4><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="ajouter_un_service_monitoré_par_nagios">Ajouter un service monitoré par Nagios</a></h4>
				<p>
					Pour cela rendez-vous sur la <a href="{$smarty.const.URL}/index.php/services?service=nagios" title="aller sur la page des services">page des "services"</a>. Choisissez le <a href="{$smarty.const.URL}/index.php/services?service=nagios" title="aller sur la page des services NAGIOS">sous-onglet "Services NAGIOS"</a>.<br />
					Cliquez sur le bouton vert, "Ajouter un service NAGIOS".<br />
					Vous avez 2 formulaires regroupant pour le premier les services monitorés par NAGIOS, et un second listant les hôtes. Dans la plupart des cas, c'est un service Nagios qu'on rajoute dans ISOU. Sélectionnez celui que vous souhaitez (exemple: WWW@ent.uhb.fr), et faites enregistrer.
				</p>
			<h4><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="affecter_des_dependances">Affecter des dépendances</a></h4>
				<p>
					Pour cela rendez-vous sur la <a href="{$smarty.const.URL}/index.php/dependances" title="aller sur la page des dépendances">page des "dépendances"</a>.<br />
					Cliquez sur le bouton vert, "Ajouter une dépendance". Sélectionnez dans la première liste déroulante, votre service ISOU crée précedemment (exemple: ENT). Dans le second menu déroulant, sélectionner votre service Nagios (exemple: WWW@ent.uhb.fr).<br />
					Si la case "Appliquer les états paire à paire" est cochée, ISOU affectera automatiquement la dépendance 1-1 et 2-2 à votre service. C'est à dire, lorsque Nagios détectera que WWW@ent.uhb.fr à l'état 1, ISOU passera le service ENT à l'état 1. Idem pour l'état 2.
				</p>
		<h3><a href="{$smarty.const.URL}/index.php/aide#sommaire" title="revenir au sommaire" name="gerer_les_categories">Gérer les catégories</a></h3>
			<p>
				Pour cela rendez-vous sur la <a href="{$smarty.const.URL}/index.php/categories" title="aller sur la page des catégories">page des "catégories"</a>.<br />
				Dans le formulaire intitulé "ajouter une catégorie", saisissez le nom et faites "enregistrer".
			</p>
	</div>
</div>

