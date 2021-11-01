<?php

include 'inc/init.inc.php';
include 'inc/function.inc.php';

// CODE ...

// Récupération du nombre d'article pour la pagination
//$pagination_article = $pdo->query("SELECT * FROM article ORDER BY categorie, titre");


// on récupère selon le $_GET['page'] l'information à mettre dans les requetes de recuperation article la valeur du :start
if(isset($_GET['page']) && is_numeric($_GET['page'])) {
    $start = ($_GET['page'] - 1) * 6;
} else{
    $start = 0;
}

//récupération des catégories
$liste_categorie = $pdo->query("SELECT DISTINCT categorie FROM article ORDER BY categorie");

// récupération des articles
if(isset($_GET['categorie'])){ // catégories
    $liste_article = $pdo->prepare("SELECT * FROM article WHERE categorie = :categorie ORDER BY categorie, titre LIMIT :start, 6");
    $liste_article->bindParam(":categorie", $_GET['categorie'], PDO::PARAM_STR);
    $liste_article->bindParam(":start", $start, PDO::PARAM_INT);
    $liste_article->execute();


    $pagination_article = $pdo->prepare("SELECT * FROM article WHERE categorie = :categorie ORDER BY categorie, titre");
    $pagination_article->bindParam(":categorie", $_GET['categorie'], PDO::PARAM_STR);
    $pagination_article->execute();

    $nb_article = $pagination_article->rowCount();
    // ceil() arrondi à l'entier supérieur
    $nb_page = ceil($nb_article/6);
}elseif(isset($_GET['couleur'])){ // couleur
    $liste_article = $pdo->prepare("SELECT * FROM article WHERE couleur = :couleur ORDER BY couleur, titre LIMIT :start, 6");
    $liste_article->bindParam(":couleur", $_GET['couleur'], PDO::PARAM_STR);
    $liste_article->bindParam(":start", $start, PDO::PARAM_INT);
    $liste_article->execute();

    $pagination_article = $pdo->prepare("SELECT * FROM article WHERE couleur = :couleur ORDER BY couleur, titre");
    $pagination_article->bindParam(":couleur", $_GET['couleur'], PDO::PARAM_STR);
    
    $pagination_article->execute();

    $nb_article = $pagination_article->rowCount();
    $nb_page = ceil($nb_article/6);
} else { // tout les produits
    $liste_article = $pdo->prepare("SELECT * FROM article ORDER BY categorie, titre LIMIT :start, 6");
    $liste_article->bindParam(":start", $start, PDO::PARAM_INT);
    $liste_article->execute();

    $pagination_article = $pdo->query("SELECT * FROM article ORDER BY couleur, titre");
    $nb_article = $pagination_article->rowCount();
    $nb_page = ceil($nb_article/6);
}

 
// debut des affichages dans la page :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

    <div class="starter-template">
        <h1><i class="far fa-money-bill-alt"></i> Boutique <i class="far fa-money-bill-alt"></i></h1>
        <p class="lead"><?php echo $msg; // pour afficher des messages utilisateur ?></p>
    </div>
    <div class="row">
        <div class="col-sm-2">
        <ul class="list-group">
            <li class="list-group-item active">Catégories</li>
            <li class="list-group-item"><a href="<?php echo URL ?>">Tous les produits</a></li>
        <?php
            // EXERCICE
            // Afficher la liste des catégories (présente dans la table article) sous forme de Lien a href dans une liste ul li (bootstrap : list-group)
            // Eviter les doublons des catégories.
            // Si on clic sur la catégorie pantalon, on passe dans l'url "categorie=pantalon"
            $liste_categorie = $pdo->query("SELECT DISTINCT categorie FROM article ORDER BY categorie");

            while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)){  
                echo '<li class="list-group-item"><a href="?categorie=' . $categorie['categorie'] . '">' . ucfirst($categorie['categorie']) . '</a>';
                echo '<li>';
            }  
        ?>
        </ul>
        <ul class="list-group">
            <li class="list-group-item active">Couleur</li>
            <li class="list-group-item"><a href="<?php echo URL ?>">Toutes les couleurs</a></li>
            <?php
            // Couleur
            $liste_couleur = $pdo->query("SELECT DISTINCT couleur FROM article ORDER BY couleur");

            while($couleur = $liste_couleur->fetch(PDO::FETCH_ASSOC)){
                echo '<li class="list-group-item"><a href="?couleur=' . $couleur['couleur'] . '">' . ucfirst($couleur['couleur']) . '</a>';
                echo '<li>';
            }

            ?>
        </ul>
        </div>
        <div class="col-sm-10">
            <div class="row">
            <?php
                while($article = $liste_article->fetch(PDO::FETCH_ASSOC)){
                    echo '<div class="col-sm-3 text-center bordure-article m-3">';
                    // faire un bloc pour afficher chaque article
                    // Afficher : l'image, le titre, la catégorie, le prix
                    // echo '<pre>'; print_r($article); echo '</pre>';
                    echo '<img src="' . URL . 'photo/' . $article['photo'] . '" class="img-thumbnail mb-3 mt-3 boutique-img " alt="photo article"><br>';
                    echo '<p>' . ucfirst($article['titre']) . '</p>';
                    echo '<p>' . ucfirst($article['categorie']) . '</p>';
                    echo '<p>' . ucfirst($article['prix']) . '€</p>';
                    echo '<button class="btn btn-primary btn-lg mb-2"><a href="fiche_article.php?id_article=' . $article['id_article'] .'" style="color: white;"> Fiche article </a></button>';
                    echo '</div>';
                }
            ?>
            <div class="col-12">
                <nav>
                    <ul class="pagination pagination-lg justify-content-center">
                        <?php
                            // params nous permet de récupérer les filtres présent dans l'url
                            $params = '&' . $_SERVER['QUERY_STRING'];
                            if(isset($_GET['page'])){
                                $params = strrchr($params, "&");
                            }
                            for($i = 1; $i <= $nb_page; $i++){
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $i . $params . '">' . $i . '</a></li>';
                            }
                        ?>                       
                    </ul>
                </nav>
            </div>
            </div>
        </div>
    </div>

<?php
include 'inc/footer.inc.php';

