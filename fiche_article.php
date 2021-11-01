<?php
include 'inc/init.inc.php';
include 'inc/function.inc.php';

if(isset($_GET['id_article']) && is_numeric($_GET['id_article'])) {
	// on récupère les infos en BDD de l'article via son id_article présent dans l'url
	$article = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
	$article->bindParam(":id_article", $_GET['id_article'], PDO::PARAM_STR);
	$article->execute();
	
	// on vérifie s'il y a une ligne (si l'article existe en BDD)
	if($article->rowCount() > 0) {
		
		$infos_article = $article->fetch(PDO::FETCH_ASSOC);
		
	} else {
		// si l'article n'existe pas, on redirige vers index.php
		header("location:" . URL);
	}
	
} else {
	header("location:" . URL);
}
	
	
	

// début des affichages dans la page :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; print_r($infos_article); echo '</pre>';
// Afficher les informations de l'article
// si le stock est à zéro : on affiche rupture de stock pour ce produit.
// proposer un lien retour vers index avec la catégorie de l'article de cette page affiché
?>   



	<div class="starter-template">
		<h1><i class="fas fa-ghost" style="color: #7950f2;"></i> <?php echo $infos_article['titre']; ?> <i class="fas fa-ghost" style="color: #7950f2;"></i></h1>
		<p class="lead"><?php echo $msg; // pour afficher des messages utilisateur ?></p>
	</div>
	
	<div class="row">
		<div class="col-sm-12 text-center mb-3">
			<a href="<?php echo URL . 'index.php?categorie=' . $infos_article['categorie']; ?>" class="btn btn-primary">Retour vers la boutique</a>
		</div>
		<div class="col-sm-6">
			<ul class="list-group">
				<li class="list-group-item"><b>Id article : </b><?php echo $infos_article['id_article']; ?></li>
				<li class="list-group-item"><b>Référence : </b><?php echo $infos_article['reference']; ?></li>
				<li class="list-group-item"><b>Catégorie : </b><?php echo $infos_article['categorie']; ?></li>
				<li class="list-group-item"><b>Couleur : </b><?php echo $infos_article['couleur']; ?></li>
				<li class="list-group-item"><b>Taille : </b><?php echo $infos_article['taille']; ?></li>
				<?php 
					if($infos_article['sexe'] == 'm') {
						$sexe = 'Homme';
					} else {
						$sexe = 'Femme';
					}
				?>
				<li class="list-group-item"><b>Sexe : </b><?php echo $sexe; ?></li>
				<li class="list-group-item"><b>Prix : </b><?php echo $infos_article['prix']; ?>€</li>
				<?php 
					if($infos_article['stock'] > 0) {
						$stock = $infos_article['stock'];
					} else {
						$stock = '<span class="text-danger">Rupture de stock pour ce produit</span>';
					}
				?>
				<li class="list-group-item"><b>Stock : </b><?php echo $stock; ?></li>
				<li class="list-group-item"><b>Description : </b><?php echo $infos_article['description'] ?></li>

			</ul>
		</div>
		<div class="col-sm-6">
			<?php
				if($infos_article['stock'] > 0) {
			?>
			<form method="post" action="panier.php">
				<input type="hidden" name="id_article" value="<?php echo $infos_article['id_article']; ?>">
				<div class="form-row">
					<div class="col form-group">
						<select name="quantite" class="form-control" id="quantite">
<?php 
	for($i = 1; $i <= $infos_article['stock'] && $i <= 5; $i++) {
		echo '<option>' . $i . '</option>';
	}
?>
						</select>
					</div>
					<div class="col form-group">
						<button type="submit" name="ajout_panier" class="btn btn-success w-100">Ajouter au panier</button>
					</div>
				</div>
			</form>
			<?php } ?>
			<hr>
			<img src="<?php echo URL . 'photo/' . $infos_article['photo']; ?>" alt="photo article : <?php echo $infos_article['titre']; ?>" class="img-thumbnail w-100">
		</div>
	</div>



<?php
include 'inc/footer.inc.php';









