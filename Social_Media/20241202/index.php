<?php 

	require "config.php";
	
	session_start();
	
	// Ha nincs bejelentkezett felhasználó, átirányítunk a reg.php oldalra
	if(!isset($_SESSION['id'])){
		
		header("Location: reg.php");
		
	}
	// Van bejelentkezett felhasználó
	else{
		
		// Bejelentkezéskor elmentett azonosítója a felhasználónak
		$id = $_SESSION['id'];
		
		// Lekérdezzük a bejelentkezett felhasználó nevét
		$lekerdezes = "SELECT * FROM users WHERE id=$id";
		$talalt_felhasznalo = $conn->query($lekerdezes);
		$felhasznalo = $talalt_felhasznalo->fetch_assoc();
		
		echo "<h1>Üdv, $felhasznalo[username]!</h1>";
		
		echo "<a href='profil.php?userid=$id' style='margin-right: 10px;'>Profilod</a>";

		// Értesítések lekérdezése
		
		$lekerdezes = "SELECT * FROM ertesitesek WHERE ertesitettid = $id";
		$talalt_ertesitesek = $conn->query($lekerdezes);
		
		$ertesitesek_szama = mysqli_num_rows($talalt_ertesitesek);
		echo "<a style='margin-right: 10px;'>Értesítések [$ertesitesek_szama]</a>";
		
		echo "<a href='logout.php'>Kijelentkezés</a>";
		
	}
	
	// Poszt feltöltése
	if(isset($_POST['post-btn'])){
		
		$userid = $_SESSION['id'];
		
		$szoveg = $_POST['szoveg'];
		
		// Feltöltés
		$conn->query("INSERT INTO posztok VALUES(id, '$userid', '$szoveg')");
		
	}
	
	// Poszt törlése
	if(isset($_POST['del-btn'])){
		
		// Linkből elmentjük, hogy melyik posztot kell törölni
		$posztid = $_GET['posztid'];
		
		// Poszt törlése
		$conn->query("DELETE FROM posztok WHERE id=$posztid");
		
	}
	
	// Like feltöltése
	if(isset($_POST['like-btn'])){
		
		$posztid = $_GET['posztid'];
		
		$conn->query("INSERT INTO likes VALUES(id, $userid, $posztid)");
		
	}

	if(isset($_POST['dislike-btn'])){
		
		$posztid = $_GET['posztid'];
		
		$conn->query("DELETE FROM likes WHERE userid=$id AND posztid=$posztid");
		
	}

?>

<hr>

	<form method="post">
	
		<input type="text" name="szoveg" placeholder="Írj valamit...">
		
		<input type="submit" name="post-btn">
	
	</form>

<hr>

<h3 style="text-align: center">Posztok</h3>

<?php 

	$lekerdezes = "SELECT * FROM posztok ORDER BY id DESC";
	$talalt_posztok = $conn->query($lekerdezes);
	while($poszt=$talalt_posztok->fetch_assoc()){
		
		$lekerdezes = "SELECT * FROM users WHERE id=$poszt[userid]";
		$talalt_iro = $conn->query($lekerdezes);
		$iro = $talalt_iro->fetch_assoc();
		
		echo '<p style="max-width: 200px; padding: 10px; border: 1px solid black; margin: 10px auto;">';
		
		echo "<a href='profil.php?userid=$iro[id]'>".$iro['username']."</a>: "; 
		
		echo $poszt['szoveg'];
		
		echo "<br><br>";
		
		$lekerdezes = "SELECT * FROM likes WHERE postid=$poszt[id]";
		$talalt_sorok = $conn->query($lekerdezes);
		
		$likeok_szama = mysqli_num_rows($talalt_sorok);
		
		echo $likeok_szama." kedvelés";
		
			echo "<form style='max-width: 200px; margin: 10px auto' method='post' action='index.php?posztid=$poszt[id]'>";

			// Lekérdezzük, hogy a bejelentkezett felhasználó kedvelte-e már a posztor
			$lekerdezes = "SELECT * FROM likes WHERE userid=$id AND postid=$poszt[id]";
			$talalt_kedveles = $conn->query($lekerdezes);
			
			if(mysqli_num_rows($talalt_kedveles) > 0){
				
				echo "<input type='submit' name='like-btn' value='Tetszik'>";
				
			} else {
				echo "<input type='submit' name='like-btn' value='NemTetszik'>";
			}
			
			if($poszt['userid'] == $_SESSION['id']){
				
				echo "<input type='submit' name='del-btn' value='Törlés'>";
				
			}
				
				if($poszt['userid'] == $_SESSION['id']){
				
					echo "<input type='submit' name='del-btn' value='Törlés'>";
				
				}
				
			echo "</form>";
			
		echo "</p>";
		
	}

?>