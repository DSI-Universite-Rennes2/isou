<!DOCTYPE html>
<html data-bs-theme="auto" lang="fr">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>ISOU - Problème d'accès à la base de données</title>

		<link rel="shortcut icon" type="image/png" href="{$smarty.const.URL}/themes/bootstrap3/favicon.png" />

		<link href="//unpkg.com/bootstrap@5.3/dist/css/bootstrap.min.css" type="text/css" media="screen" rel="stylesheet" />
		<link href="{$smarty.const.URL}/themes/bootstrap/css/common.css" type="text/css" media="screen" rel="stylesheet" />
	</head>
	<body role="document">

		<div class="container">
			<header class="page-header my-4" role="banner">
				<h1 id="isou-header">ISOU</h1>
			</header>
			<main id="content" role="main">
				<article id="content">
					<p class="alert alert-danger">La base de données est inaccessible.</p>
				</article>
			</main>
		</div>

		<script src="{$smarty.const.URL}/scripts/darkmode.js" type="text/javascript"></script>

	</body>
</html>
