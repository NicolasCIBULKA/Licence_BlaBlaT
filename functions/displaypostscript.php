<?php

include("function.inc.php");
include("../class/User.class.php");
include("../class/Post.class.php");
include("../class/Like.class.php");

session_start(); 
    if(empty($_SESSION["User"])){
        header("Location:index.php");
    }

if(isset($_GET["iduser"])) {
	$userIndex = 0;
	$followedIndex = 0;
	$allPostIndex = 0;
	$postIndex = 0;
	$comIndex = 0;

	$bdd = BDConnect();

	$req = $bdd->prepare("(SELECT * FROM Post WHERE id_writer = ?) UNION (SELECT id_post, id_writer, text, url_image, datePost FROM Post NATURAL JOIN LikePost WHERE id_user = ?) ORDER BY id_post DESC");
	$req->execute(array($_GET["iduser"],$_GET["iduser"]));
	if($req->rowCount() == 0) {
		echo "<p class=\"text-center\">Vous n'avez rédigé aucun post</p>";
	}
	else {
		while(($row = $req->fetch()) && ($userIndex < 3)) {

			$req2 = $bdd->prepare("SELECT * FROM Users WHERE id_user = ?");
			$req2->execute(array($row[1]));
			$rowuser = $req2->fetch();
			echo "<div class=\"container postelement\">";
			echo "<div class=\"row\">";
			echo "<div class=\"col\">";
			if($row[1] != $_GET["iduser"]) {
				echo "<p class=\"font-italic\"> Vous avez aimé ce post </p>";
			}
			$user = new User($rowuser[0], $rowuser[1], $rowuser[5] , $rowuser[2] , $rowuser[3] , $rowuser[4] , $rowuser[7], $rowuser[6]);
			$icon = returnpp($user);
			echo "<div>";
			echo "<a class=\"nameidpost\" href=\"profil.php?iduser=".$row[1]."\"><img class=\"rounded-circle p-2 bd-highlight\" width=\"80px\" height=\"80px\" src=\"".$icon."\" alt=\"Profil Picture\">".$rowuser[1]." - @".$row[1]."</a>";
			$date = date_create($row[4]);
			echo "<p>".$row[2]."</p>";
			if(!is_null($row[3])) {
				echo "<img class=\"rounded mx-auto d-block\"src=\"".$row[3]."\" width=\"30%\" alt=\"post image\">";
			}
			echo "<p> Le ".date_format($date, 'Y-m-d \à H:i')."</p>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
			$userIndex++;
		}
	}

	$followedReq = $bdd->prepare("SELECT id_followed FROM Follow WHERE id_follower = ?");
	$followedReq->execute(array($_GET["iduser"]));
	if($followedReq->rowCount() == 0) {
			$allPostReq = $bdd->prepare("SELECT * FROM Post WHERE id_writer != ? ORDER BY id_post DESC");
			$allPostReq->execute(array($_GET["iduser"]));
			if($allPostReq->rowCount() == 0) {
				echo "<p class=\"text-center\">Il n'y a aucun post sur le réseau social Blabla't</p>";
			}
			else {
				while($allPostRow = $allPostReq->fetch()) {
					$allPostLike = createPostLike($allPostRow[0]);
					$allPostDate = date_create($allPostRow[4]);
					$allPost = new Post($allPostRow[0],$allPostRow[2],$allPostRow[3],$allPostLike,$allPostRow[1],$allPostDate);
					displayPost($allPost);
					$allPostIndex++;
				}
			}

		}
	else {
		while($followedRow = $followedReq->fetch()) {
			$postReq = $bdd->prepare("(SELECT * FROM Post WHERE id_writer = ?) UNION (SELECT * FROM Post NATURAL JOIN LikePost WHERE id_user = ?) ORDER BY id_post DESC");
			$postReq->execute(array($followedRow[0],$followedRow[0]));
			while($postRow = $postReq->fetch()) {
				$postLike = createPostLike($postRow[0]);
				$date = date_create($postRow[4]);
				$post = new Post($postRow[0],$postRow[2],$postRow[3],$postLike,$postRow[1],$date);
				//Ce qu'il y a dans le "if" ne s'affiche pas !!!
				if($postRow[1] != $followedRow[0]) {
					echo "<p class=\"font-italic\">".$followedRow[0]." à aimé le post suivant</p>";
				}
				displayPost($post);
				$postIndex++;
			}
			$followedIndex++;
		}
	}
}
else {
	echo "<p class=\"text-center\">Erreur lors de l'affichage des posts ...</p>";
}

?>