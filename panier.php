<?php
include 'inc/init.inc.php';
include 'inc/function.inc.php';

// vider le panier
if(isset($_GET['action']) && $_GET['action'] == 'vider') {
	unset($_SESSION['panier']);
}

// Payer le panier
if(isset($_GET['action']) && $_GET['action'] == 'payer') {
	// avant de déclencher le paiement on doit vérifier le stock restant pour chaque article
	for($i = 0; $i < count($_SESSION['panier']['quantite']); $i++) {
		// on va en BDD récupérer les informations de l'article afin de récupérer son stock restant.
		$infos_article = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
		$infos_article->bindParam(":id_article", $_SESSION['panier']['id_article'][$i], PDO::PARAM_STR);
		$infos_article->execute();

		$article = $infos_article->fetch(PDO::FETCH_ASSOC);

		// on compare la quantite avec le stock restant
		if($article['stock'] < $_SESSION['panier']['quantite'][$i]) {
			// si le stock est inférieur à la quantite => erreur

			// 2 possibilité : stock à 0 ou il en reste mais moins que la quantité demandée
			if($article['stock'] > 0) {
				$_SESSION['panier']['quantite'][$i] = $article['stock'];
				$msg .= '<div class="alert alert-danger">La quantite de l\'article <b>"' . $_SESSION['panier']['titre'][$i] . '"</b> a été réduite car notre stock est insuffisant. <br>Veuillez vérifier votre panier.</div>';
			} else {
				// stock =0
				$msg .= '<div class="alert alert-danger">L\'article a été retirer de votre panier car nous sommes en rupture de stock.<br>Veuillez vérifier votre panier.</div>';
				remove_cart_item($_SESSION['panier']['id_article'][$i]);
				$i--; // si on retire un article l'élément suivant remplace celui que l'on vient de retirer et récupère son indice. Du coup on enleve 1 à $i pour repasser sur cet indice pour faire le controle des quantites.
			}
		}
	}
	// on vérifie si il ya eu une erreur afin d'enregistrer la commande
	if(empty($msg)){
		$pdo->query("INSERT INTO commande (id_membre, montant, date) VALUES (". $_SESSION['membre']['id_membre'] . "," . total_amount() . ", NOW())");

		$id_commande = $pdo->lastInsertId(); // on récupère l'id de la commande

		// on enregistre les détails de la commande
		for($i = 0; $i < count($_SESSION['panier']['id_article']); $i++) {
			$id_article = $_SESSION['panier']['id_article'][$i];
			$quantite = $_SESSION['panier']['quantite'][$i];
			$prix = $_SESSION['panier']['prix'][$i];
			$pdo->query("INSERT INTO details_commande (id_commande, id_article, quantite, prix) VALUES ($id_commande, $id_article, $quantite, $prix)");

			// mise à jour du stock
			$pdo->query("UPDATE article SET stock = stock - $quantite WHERE id_article = $id_article");
		}
		// on vide le panier
		unset($_SESSION['panier']);
		// on envoie un mail au client
		// mail($_SESSION['membre']['email'], "Confirmation de commande", "Merci pour votre commande, votre numéro de suivi est le $id_commande", "From:vendeur@eboutique.com");
	}
}


// creation du panier
create_panier();

// Retirer un article du panier
if(isset($_GET['action']) && $_GET['action'] == 'retirer' && isset($_GET['id_article']) && is_numeric($_GET['id_article'])) {
	remove_cart_item($_GET['id_article']);
}

// incrémentation ou décrémentation de la quantité
if(isset($_GET['action']) && isset($_GET['position']) && is_numeric($_GET['position'])) {
	if($_GET['action'] == 'plus'){
		if(isset($_SESSION['panier']['quantite'][$_GET['position']])) {
			$_SESSION['panier']['quantite'][$_GET['position']]++;
		}
	}elseif($_GET['action'] == 'moins') {
		if(isset($_SESSION['panier']['quantite'][$_GET['position']]) && $_SESSION['panier']['quantite'][$_GET['position']] > 1) {
			$_SESSION['panier']['quantite'][$_GET['position']]--;
		}
	}
}


// ajout dans le panier
if(isset($_POST['ajout_panier']) && isset($_POST['id_article']) && is_numeric($_POST['id_article']) && isset($_POST['quantite']) && is_numeric($_POST['quantite'])) {
	// on va en BDD chercher le prix de l'article
	$resultat = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
	$resultat->bindParam(':id_article', $_POST['id_article'], PDO::PARAM_STR);
	$resultat->execute();
	
	if($resultat->rowCount() > 0) {
		$infos = $resultat->fetch(PDO::FETCH_ASSOC);
		add_item_in_cart($_POST['id_article'], $infos['titre'], $infos['prix'], $_POST['quantite'], $infos['photo']);
		header("location:panier.php"); // pour ne pas rajouter le même article avec F5
		// header("location:" . $_SERVER['SCRIPT_NAME']); // pour ne pas rajouter le même article avec F5
	}
	
}	
	
	

// début des affichages dans la page :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; print_r($_POST); echo '</pre>';
// echo '<pre>'; print_r($_SESSION); echo '</pre>';
// echo '<pre>'; print_r($_SERVER); echo '</pre>';
?>   



	<div class="starter-template">
		<h1><i class="fas fa-ghost" style="color: #7950f2;"></i> Panier <i class="fas fa-ghost" style="color: #7950f2;"></i></h1>
		<p class="lead"><?php echo $msg; // pour afficher des messages utilisateur ?></p>
	</div>

	<div class="row">
		<div class="col-12 mt-3">
			<table class="table table-bordered">
				<tr>
					<th colspan="6" class="text-center bg-dark text-white">PANIER</th>
				</tr>
				<tr>
					<th>Retirer</th>
					<th>N°article</th>
					<th>Titre</th>
					<th>Photo</th>
					<th>Quantité</th>
					<th>Prix unitaire</th>
				</tr>
				
				<?php
					if(empty($_SESSION['panier']['id_article'])) {
						echo '<tr><td colspan="6" class="text-center">Votre panier est vide</td></tr>';
					} else {
						// le panier n'est pas vide, on affiche les articles
						for($i = 0; $i < count($_SESSION['panier']['prix']); $i++) {
							echo '<tr>';
							
							echo '<td><a href="?action=retirer&id_article=' . $_SESSION['panier']['id_article'][$i] . '" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a></td>';
							echo '<td>' . $_SESSION['panier']['id_article'][$i] . '</td>';
							echo '<td>' . $_SESSION['panier']['titre'][$i] . '</td>';
							
							echo '<td class="text-center"><img src="' . URL . 'photo/' . $_SESSION['panier']['photo'][$i] . '" width="100" alt="Photo article : ' . $_SESSION['panier']['titre'][$i] . '" class="img-thumbnail"></td>';
							
							$bouton_plus = '<a href="?action=plus&position=' . $i . '" class="btn btn-primary btn-sm float-right"> <i class="fas fa-plus"> </i></a>';
							
							if($_SESSION['panier']['quantite'][$i] > 1) {
								$bouton_moins = '<a href="?action=moins&position=' . $i . '" class="btn btn-danger btn-sm float-right mr-3"> <i class="fas fa-minus "></i> </a>';
							} else {
								$bouton_moins = '';
							}
							
							echo '<td>' . $_SESSION['panier']['quantite'][$i] . ' ' . $bouton_plus . ' ' . $bouton_moins .  '</td>';
							
							echo '<td>' . $_SESSION['panier']['prix'][$i] . '€</td>';
							
							echo '</tr>';
						}
						// Affichage du montant total du panier
						echo '<tr>';
						echo '<td colspan="5" class="text-right"><b>Montant total TTC </b><small>TVA : 20%</small></td>';
						echo '<td>' . total_amount() . '€</td>';
						
						echo '</tr>';
						
						
						// bouton vider le panier
						echo '<tr><td colspan="6" class="text-center"><a href="?action=vider" class="btn btn-danger w-50">Vider le panier</a></td></tr>';
						
						// rajouter une ligne dans le tableau : 
						// si l'utilisateur est connecté, on affiche un bouton payer (href="?action=payer")
						// Sinon afficher un message exemple : Veuillez vous connecter ou vous inscrire pour payer votre panier. 
						// les mots connecter et inscrire doivent être des liens vers les pages respectives.
						
						if(user_is_connected()) {
							echo '<tr><td colspan="6" class="text-center"><a href="?action=payer" class="btn btn-success w-50">Payer le panier</a></td></tr>';
						} else {
							echo '<tr><td colspan="6" class="text-center">Veuillez vous <a href="connexion.php">connecter</a> ou vous <a href="inscription.php">inscrire</a> pour payer votre panier.</td></tr>';
						}
					}
				?>
				
			</table>
		</div>
	</div>


<?php
include 'inc/footer.inc.php';