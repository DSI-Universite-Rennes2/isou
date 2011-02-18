<?php
	header("Content-type: application/xhtml+xml; charset=utf-8");

	$content = file_get_contents('index.php');
	
	preg_match('#<header>.*</header>#s', $content, $header);
	// grep first section (option U)
	preg_match('#<section>.*</section>#Us', $content, $section);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<title>Fisou - Extension d'Isou pour Firefox</title>
</head>
<body>
	<?php echo $header[0].$section[0];?>
	<footer>CRI Rennes 2 - 2010</footer>
</body>
</html>

