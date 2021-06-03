
<?php
// IS RECEIVED SHORTCUT
if(isset($_GET['q'])){

	// VARIABLE
	$shortcut = htmlspecialchars($_GET['q']);

	// IS A SHORTCUT ?
	$bdd = new PDO('mysql:host=localhost;dbname=url;charset=utf8', 'root', '');
	$req =$bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE shortcut = ?');//on verifie le nombre de fois que notre requete a été effectué
	$req->execute(array($shortcut));


	while($result = $req->fetch()){//tans quil nous retourne des resultats

		if($result['x'] != 1){
			header('location:?error=true&message=Adresse url non connue');
			exit();
		}

	}

	// REDIRECTION
	$req = $bdd->prepare('SELECT * FROM links WHERE shortcut = ?');
	$req->execute(array($shortcut));

	while($result = $req->fetch()){

		header('location: '.$result['url']);
		exit();

	}

}

// IS SENDING A FORM
if(isset($_POST['url'])) {

	// VARIABLE
	$url = $_POST['url'];

	// VERIFICATION
	if(!filter_var($url, FILTER_VALIDATE_URL)) {//ON FILTRE
		// PAS UN LIEN
		header('location: ?error=true&message=Adresse url non valide');
		exit();
	}

	// SHORTCUT
	$shortcut = crypt($url, rand());

	// HAS BEEN ALREADY SEND ?
	$bdd = new PDO('mysql:host=localhost;dbname=url;charset=utf8', 'root', '');
	$req = $bdd->prepare('SELECT COUNT(*) AS x FROM links WHERE url = ?');
	$req->execute(array($url));

	while($result = $req->fetch()){

		if($result['x'] != 0){
			header('location:?error=true&message=Adresse déjà raccourcie');
			exit();
		}

	}

	// SENDING
	$req = $bdd->prepare('INSERT INTO links(url, shortcut) VALUES(?, ?)');
	$req->execute(array($url, $shortcut));

	header('location:?short='.$shortcut);
	exit();

}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Raccourcisseur d'url express</title>
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="icon" type="image/png" href="pictures/Sale2.png">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
	</head>

	<body>
		<!-- PRESENTATION -->
		<section id="hello">
			
			<!-- CONTAINER -->
			<div class="container">
				
				<!-- HEADER -->
				<header>
				<img src="pictures/rismo.png" alt="rismo devops" id="logo" width="110">
				</header>

				<!-- VP -->
				<h1>Une url longue ? Raccourcissez-là ?</h1>
			

				<!-- FORM -->
				<form method="post" action="index.php">
					<input type="url" name="url" placeholder="Collez un lien à raccourcir">
					<input type="submit" value="Raccourcir">
				</form>

				<?php if(isset($_GET['error']) && isset($_GET['message'])) { ?>
					<div class="center">
						<div id="result">
							<b><?php echo htmlspecialchars($_GET['message']); ?></b>
						</div>
					</div>
				<?php } else if(isset($_GET['short'])) { ?>
					<div class="center">
						<div id="result">
							<b>URL RACCOURCIE : </b>
							http://localhost/?q=<?php echo htmlspecialchars($_GET['short']); ?>
						</div>
					</div>
				<?php } ?>

			</div>

		</section>

		<!-- BRANDS -->
		<section id="brands">
			
			<!-- CONTAINER -->
			<div class="container">
			<img src="pictures/logo_udemy.png" alt="logo_udemy" width="200">
			</div>

		</section>

		<!-- FOOTER -->
		<footer>
    <img src="pictures/logo_footer.png" alt="rismo devops" width="150" id="logo2">
    <p class="text-white">&copy; 2021 <a href="https://rismo.fr" class="text-footer"> rismo.fr</a></p>
</footer>
	</body>
</html>