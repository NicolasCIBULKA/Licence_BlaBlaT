<!--
 Page affichant les commentaires d'un post
 -->
<?php 
require_once("class/User.class.php");
	session_start();
	if(empty($_SESSION["User"])){
		header("Location:index.php");
	}
	if(empty($_GET["idpost"])){
		header("Location:feed.php");
	}
 ?>
 <?php include("functions/function.inc.php"); ?>
<?php include ("templates/head.inc.php"); ?>
<?php require_once("class/Like.class.php"); ?>
	<?php include ("templates/header.inc.php"); ?>
	
	<?php include("templates/nav.inc.php"); ?>
	
	<h2 id="soustitre">Post</h2>

	<div class="container postelement">
		<div class="container">
			<div class="row">
				<p class="pseudopostelement">Pseudo -</p>
				<p class="idpostelement"> @Identifiant</p>
			</div>
			<div class="row textpostelement">
				<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam corporis, omnis tempora nam voluptates cum qui consectetur ipsum, tempore numquam autem necessitatibus harum hic in rerum molestias atque, veniam minus.
				Recusandae at facere unde quas cum, magnam, saepe minima tempore corporis, non odit iusto, odio consequuntur! Quisquam totam, similique, doloribus nemo earum possimus pariatur, atque id eaque placeat quia dolore.</p>
			</div>
			<div class="row">
				<p>Le 23/12/2020 à 12h23:43</p>
			</div>
			</div>
		</div>
	<h4 class="container soustitrecommentaires" >Ecrire un Commentaire</h4>
	<div class="container commentelement">
		<form class="align-items-center col-12 col-md-12 col-sm-12 col-xs-12 form-group" action="functions/createcomment.php" method="post">
			<label for="message" class="form-control-lg">Votre message : </label>
			<textarea class="form-control" name="message" id="message" cols="30" rows="5" aria-describedby="msg"></textarea>
			<small id="msg" class="form-text text-muted"><span id="undertext">Pas plus de 300 caractères ! / <span id="nbcara">300</span> restants</span></small>
			<input type="hidden" name="idpost" value=<?php echo $_GET["idpost"] ?>/>
			<input type="submit" id="submit" value="Écrire un nouveau post !">
		</form>
	</div>

	<h3 class="container soustitrecommentaires" >Commentaires</h3>


	<?php 
		// recuperation des commentaires associés au post affiché au dessus
		if(isset($_GET["idpost"])){
			$bdd = BDconnect();
			$reqComment = $bdd->prepare("SELECT id_comment, id_writer,id_post_commented, text, DATE_FORMAT(dateComment, '%d/%m/%Y à %Hh%imin%ss') AS date FROM Comments WHERE id_post_commented = ?");
			$reqComment->execute(array(intval($_GET["idpost"])));
			// On teste si le post à déja des commentaires
			if($reqComment->rowCount() > 0){
				while($rowComment = $reqComment->fetch()){
					$req = $bdd->prepare("SELECT username, id_user FROM Users WHERE id_user = ?");
					$req->execute(array($rowComment[1]));
					$rowUser = $req->fetch();


				$like = createCommentLike($rowComment[0]);

					// Affichage
					echo "<div id=\"".$rowComment[0]."\" class=\"container commentelement\">";
					echo "<div class=\"container\">";
					echo "<div class=\"row\">";
					echo "<p class=\"pseudopostelement\">".$rowUser[0]." -</p>";
					echo "<p class=\"idpostelement\"> @".$rowUser[1]."</p>";
					echo "</div>";
					echo "<div class=\"row textpostelement\">";
					echo "<p>".$rowComment[3]."</p>";
					echo "</div>";
					echo "<div class=\"row\">";
					echo "<p>".$rowComment[4]."</p>";
					echo "</div>";
					echo "<div class=\"row justify-content-center commentlike\" >";
					echo "<div class=\"btn btn-lg likebutton orangecolor\">";
					echo "<svg id=\"".$rowComment[0]."like\" width=\"1em\" height=\"1em\" viewBox=\"-0.5 -1 17 17\" class=\"bi bi-heart-fill\" fill=\"".userlike($like,$_SESSION['User'])."\" stroke=\"orange\" xmlns=\"http://www.w3.org/2000/svg\">\n";
	echo "<path fill-rule=\"evenodd\" d=\"M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z\"/>\n";
	echo "</svg>\n";
					echo "<span class=\"nblike\"> ".$like->getNumberLikes()."</span>\n";
					echo "</div>";
					echo "</div>";
					echo "</div>";
					echo "</div>";
				}
			}
			else{
				echo "<div class=\"container commentelement\"><p class=\"text-center\">Pas de commentaires pour le moment ...</p></div>";
			}

		}
		else{
			echo "<p class=\"text-center\">Erreur lors de l'accès au commentaires, retournez à la page d'accueil !</p>";
		}
	?>

	<script type="text/javascript" src="./scripts/postnbcara.js"></script>
	<script type="text/javascript" src="./scripts/likeanimation.js"></script>
	<?php include ("templates/footer.inc.php"); ?>
</body>
</html>