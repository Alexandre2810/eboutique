<?php
// fonction pour savoir si l'utilisateur est connecté
function user_is_connected(){
    if(isset($_SESSION['membre'])){
        return true;
    }
    return false;
}


// Fonction pour savoir si l'utilisateur est connecté et aussi s'il a le statut admin
function user_is_admin(){
    if(user_is_connected() && $_SESSION['membre']['statut'] == 2){
        return true;
    }else {
        return false;
    }
}
// fonction pour créer le panier dans la $_SESSION
function create_panier () {
	if(!isset($_SESSION['panier'])) {
		$_SESSION['panier'] = array();
		$_SESSION['panier']['id_article'] = array();
		$_SESSION['panier']['quantite'] = array();
		$_SESSION['panier']['prix'] = array();
		$_SESSION['panier']['titre'] = array();
		$_SESSION['panier']['photo'] = array();
	}
}

// fonction pour ajouter un article dans le panier
function add_item_in_cart($id_article, $titre, $prix, $quantite, $photo) {
	// avant d'ajouter au panier, on vérifie si l'article est déjà présent dans le panier.
	// si c'est le cas, on ne modifie que la quantité de cet article.
	
	// array_search() cherche si une valeur fournie en premier argument est présente dans les valeurs d'un tableau array fourni en deuxième argument.
	// Si c'est le cas, nous renvoie son indice !
	
	$position_article = array_search($id_article, $_SESSION['panier']['id_article']);
	
	// comparaison stricte (!==) car il est possible d'obtenir l'indice 0
	if($position_article !== false) {
		$_SESSION['panier']['quantite'][$position_article] += $quantite;
	} else {
		// sinon on met l'article dans le panier.
		$_SESSION['panier']['id_article'][] = $id_article;
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['prix'][] = $prix;
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['photo'][] = $photo;
	}
	
}


// Fonction pour avoir le montant total du panier
function total_amount() {
	$total = 0;
	for($i = 0; $i < sizeof($_SESSION['panier']['quantite']); $i++) {
		$total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];
	}
	// ajout de la TVA
	$total *= 1.2;
	return round($total, 2);
}


// Fonction pour retirer un article du panier
function remove_cart_item($id_article) {
    // on cherche l'indice correspondant à cette article dans le panier
    $position_article = array_search($id_article, $_SESSION['panier']['id_article']);

    if($position_article !== false) {
        // array_splice permet de retirer un élément d'un tableau array et surtout de réordonner les indices afin qu'il n'y ai pas de trou
        array_splice($_SESSION['panier']['id_article'], $position_article, 1);
        array_splice($_SESSION['panier']['quantite'], $position_article, 1);
        array_splice($_SESSION['panier']['titre'], $position_article, 1);
        array_splice($_SESSION['panier']['prix'], $position_article, 1);
        array_splice($_SESSION['panier']['photo'], $position_article, 1);
    }
}
function active_class($href){
    $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    if($url == URL . $href) {echo 'active';}
}