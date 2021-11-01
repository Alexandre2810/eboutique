<?php

include '../inc/init.inc.php';
include '../inc/function.inc.php';
// restriction d'accès, si l'utilisateur n'est pas admin on le renvoie sur connexion.php
if(!user_is_admin()){
    header("location:" . URL . "connexion.php");
    exit();
}

// CODE ...
$id_article = ""; // pour la modification article
$reference = "";
$categorie = "";
$titre = "";
$description = "";
$couleur = "";
$taille = "";
$sexe = "";
$photo = "";
$prix = "";
$stock = "";

/******************************************************************/
/******************************************************************/
/**************** RECUPERATION DES ARTICLES A MODIFIER*************/
/******************************************************************/
/******************************************************************/
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id_article']) && is_numeric($_GET['id_article'])) {

    $recup_article = $pdo->prepare("SELECT * FROM article WHERE id_article = :id_article");
    $recup_article->bindParam(":id_article", $_GET['id_article'], PDO::PARAM_STR);
    $recup_article->execute();

    if($recup_article->rowCount() > 0) {
        $article_actuel = $recup_article->fetch(PDO::FETCH_ASSOC);

        $id_article = $article_actuel['id_article']; 
        $reference = $article_actuel['reference'];
        $categorie = $article_actuel['categorie'];
        $titre = $article_actuel['titre'];
        $description = $article_actuel['description'];
        $couleur = $article_actuel['couleur'];
        $taille = $article_actuel['taille'];
        $sexe = $article_actuel['sexe'];
        $photo_actuelle = $article_actuel['photo'];
        $prix = $article_actuel['prix'];
        $stock = $article_actuel['stock'];
    }
}
/******************************************************************/
/******************************************************************/
/**************** SUPPRESSION ARTICLE *****************************/
/******************************************************************/
/******************************************************************/
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id_article']) && is_numeric($_GET['id_article'])) {
    // Exercice : dans le cas d'une suppression, il faut aussi supprimer la photo de l'article
    // pour supprimer un fichier sur le serveur : unlink(chemin_du_fichier);
    $suppression = $pdo->prepare("DELETE FROM article WHERE id_article = :id_article");
    $suppression->bindParam(':id_article', $_GET['id_article'], PDO::PARAM_STR);
    $suppression->execute();


    // pour afficher le tableau
    $_GET['action'] = 'afficher';
}



/******************************************************************/
/******************************************************************/
/**************** ENREGISTREMENT DES ARTICLES *********************/
/******************************************************************/
/******************************************************************/

if(
    isset($_POST['id_article']) &&
    isset($_POST['reference']) &&
    isset($_POST['categorie']) &&
    isset($_POST['titre']) &&
    isset($_POST['description']) &&
    isset($_POST['couleur']) &&
    isset($_POST['taille']) &&
    isset($_POST['sexe']) &&
    //isset($_POST['photo']) &&
    isset($_POST['prix']) &&
    isset($_POST['stock'])
) {
    foreach($_POST AS $indice => $valeur) {
        $_POST[$indice] = trim($valeur); // pour appliquer un trim() à tous les éléments de $_POST
    }

    $id_article = $_POST['id_article'];
    $reference = $_POST['reference'];
    $categorie = $_POST['categorie'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $couleur = $_POST['couleur'];
    $taille = $_POST['taille'];
    $sexe = $_POST['sexe'];
    //$photo = $_POST['photo'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];

    // contrôle le prix doit être numérique
    if(!is_numeric($prix)){
        $msg .= '<div class="alert alert-danger mt-3">Le prix doit être numérique</div>';
    }

    // contrôle le stock doit être numérique
    if(!is_numeric($stock)){
        $msg .= '<div class="alert alert-danger mt-3">Le stock doit être numérique</div>';
    }
    
    // contrôle de la reference car unique en BDD
    $verif_reference = $pdo->prepare("SELECT * FROM article WHERE reference = :reference");
    $verif_reference->bindParam(":reference", $reference , PDO::PARAM_STR);
    $verif_reference->execute();
    
    // on vérifie s'il y a une ligne dans notre réponse bdd, si c'est le cas, alors la référence est déjà prise.
    if($verif_reference->rowCount() > 0 && empty($id_article)){
        $msg.= '<div class="alert alert-danger mt-3">Référence indisponible</div>';
    } else {
        //on récupère l'ancienne photo si c'est une modif.
        if(!empty($_POST['photo_actuelle'])) {
            $photo = $_POST['photo_actuelle'];
        }

        // on contrôle le format de la photo avant l'enregistrement.
        // si une photo a été chargé par l'utilisateur
        if(!empty($_FILES['photo']['name'])){
            // on vérifie le format de la photo via son extension
            $extension = strrchr($_FILES['photo']['name'], '.');
            // strrchr() permet de découper une chaine en partant de la fin selon un charactère fourni en deuxième argument. Le caractère est inclu.
            // exemple => mon_image.jpg on récupère .jpg // photo354.PNG on récupère .PNG 
            $extension = strtolower(substr($extension, 1));
            // strtolower() pour passer en minuscule
            // substr() pour enlever le "."
            // exemple => .Png on récupere png / .jpeg on récupere jpeg

            // on déclare un tableau contenant les extension autorisées :
            $tab_extension_valide = array('png','gif','jpg','jpeg');

            // in_array permet de tester si le premier argument fait partie d'une des valeurs d'un tableau array fourni en deuxieme argument.
            $verif_extension = in_array($extension, $tab_extension_valide);

            if($verif_extension){
                // traitement photo
                // on renomme le nom de la photo pour ne pas écraser une photo du même nom sur le serveur
                $photo = $reference . $_FILES['photo']['name'];
                // pour enlever les caractères spéciaux dans le nom de la photo
                $photo = preg_replace("/[^a-zA-Z0-9.]/", "", $photo);

                $dossier_copie_photo = SERVEUR_ROOT . SITE_ROOT . "photo/" .$photo;
                // var_dump($dossier_copie_photo);

                // pour copier l'image : fonction prédéfinie copy(chemin_origine, chemin_cible)
                copy($_FILES['photo']['tmp_name'], $dossier_copie_photo);
            } else {
                $msg .= '<div class="alert alert-danger mt-3">L\'extension de la photo n\'est pas valide, format : png, jpg, jpeg & gif</div>';
            }
        }
        // enregistrement
        if(empty($msg)) {
            // si $msg est vide, alors il n'y a pas eu d'erreur dans nos controles, on peut déclencher l'enregistrement.
            if(empty($id_article)){
                $enregistrement = $pdo->prepare("INSERT INTO article (reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :photo, :prix, :stock)");
            } else {
                $enregistrement = $pdo->prepare("UPDATE article SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, sexe = :sexe,photo = :photo, prix = :prix, stock = :stock WHERE id_article = :id_article");
                $enregistrement->bindParam(':id_article', $id_article);
                $_GET['action'] = 'afficher';
            }

            $enregistrement->bindParam(':reference', $reference, PDO::PARAM_STR);
            $enregistrement->bindParam(':categorie', $categorie, PDO::PARAM_STR);
            $enregistrement->bindParam(':titre', $titre, PDO::PARAM_STR);
            $enregistrement->bindParam(':description', $description, PDO::PARAM_STR);
            $enregistrement->bindParam(':couleur', $couleur, PDO::PARAM_STR);
            $enregistrement->bindParam(':taille', $taille, PDO::PARAM_STR);
            $enregistrement->bindParam(':sexe', $sexe, PDO::PARAM_STR);
            $enregistrement->bindParam(':photo', $photo, PDO::PARAM_STR);
            $enregistrement->bindParam(':prix', $prix, PDO::PARAM_STR);
            $enregistrement->bindParam(':stock', $stock, PDO::PARAM_STR);
            $enregistrement->execute();
                
        }
        
    }
}




// debut des affichages dans la page :
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; print_r($_POST); echo '</pre>';
// echo '<pre>'; print_r($_FILES); echo '</pre>';
// echo '<pre>'; print_r($photo); echo '</pre>';
// echo '<pre>'; print_r($_SERVER); echo '</pre>';
?>

    <div class="starter-template">
        <h1><i class="far fa-money-bill-alt"></i> Article <i class="far fa-money-bill-alt"></i></h1>
        <p class="lead">
            <a href="?action=ajouter" class="btn btn-outline-primary">Ajouter article</a>
            <a href="?action=afficher" class="btn btn-outline-danger">Afficher article</a>
            <?php echo $msg; // pour afficher des messages utilisateur ?>
        </p>
    </div>

    <div class="row">
        <div class="col-12">
        <?php
            //********************************************************
            //********************************************************
            //***********AFFICHAGE DU TABLEAU DES ARTICLES
            //********************************************************
            //********************************************************
            if(isset($_GET['action']) && $_GET['action'] == 'afficher'){
            // faire une requete de récupération de tous les produits en bdd et afficher le nombre d'article.
                $liste_article = $pdo->query("SELECT * FROM article ORDER BY reference");
                echo '<p>Le nombre d\'article est de : <small>(' . $liste_article->rowCount() . ')</small></p>';
                
                echo '<table class="table table-border">';
                echo '<tr>';
                echo '<th>id_article</th>';
                echo '<th>Référence</th>';
                echo '<th>Catégorie</th>';
                echo '<th>Titre</th>';
                echo '<th>Description</th>';
                echo '<th>Couleur</th>';
                echo '<th>Taille</th>';
                echo '<th>Sexe</th>';
                echo '<th>Photo</th>';
                echo '<th>Prix</th>';
                echo '<th>Stock</th>';
                echo '<th>Modif</th>';
                echo '<th>Suppression</th>';
                echo '</tr>';
            
                while($article = $liste_article->fetch(PDO::FETCH_ASSOC)){
                    echo '<tr>';
                    foreach($article AS $indice => $valeur){
                        if($indice == 'photo'){
                            echo '<td><img src="' . URL . 'photo/' . $valeur . '" class="img-thumbnail" width="100  " alt="photo article"></td>';
                        } elseif($indice == "description"){
                            echo '<td>' . iconv_substr($valeur, 0, 14) . '</td>';
                        }else{
                            echo '<td>' . $valeur . '</td>';
                        }
                    }
                    echo '<td><a href="?action=modification&id_article=' . $article['id_article'] . '" class="btn btn-warning"><i class="fas fa-edit"></i></a></td>';
                    echo '<td><a href="?action=suppression&id_article=' . $article['id_article'] . '" class="btn btn-danger" onclick="return(confirm(\'Etes-vous sûr ?\'))"><i class="fas fa-trash-alt"></i></a></td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
            //********************************************************
            //********************************************************
            //***********AFFICHAGE DU TABLEAU DES ARTICLES
            //********************************************************
            //********************************************************
        ?>
        <?php //affichage si $_GET['action']== 'ajouter'
            //********************************************************
            //********************************************************
            //********************************************************
            //*****************AFFICHAGE DU FORMULAIRE D'AJOUT********
            //********************************************************
            //********************************************************
            //********************************************************
            //********************************************************
            if(isset($_GET['action']) && ($_GET['action'] == 'ajouter' || $_GET['action'] == 'modification')){ 
        ?>
            <form method="post" action="" enctype="multipart/form-data" class="form-article">
            <!-- 
                textarea => description
                select => categorie / couleur / sexe / taille
                input type text => reference / titre / prix / stock
                input type file => photo
             -->
                <div class="row justify-content-center">
                    <div class="col-sm-6 form-contour">
                        <input type="hidden" class="form-control" id="id_article" name="id_article" value="<?php echo $id_article; ?>">

                        <div class="form-group bordure">
                            <label for="reference">Référence</label>
                            <input type="text" class="form-control" id="reference" <?php if(!empty($id_article)) {echo 'readonly';} ?> name="reference" value="<?php echo $reference; ?>">
                        </div>
                        <div class="form-group bordure">
                            <label for="titre">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo $titre; ?>">
                        </div>
                        <div class="form-group bordure">
                            <label for="prix">Prix</label>
                            <input type="text" class="form-control" id="prix" name="prix" value="<?php echo $prix; ?>">
                        </div>
                        <div class="form-group bordure">
                            <label for="stock">Stock</label>
                            <input type="text" class="form-control" id="stock" name="stock" value="<?php echo $stock; ?>">
                        </div>
                        <?php
                            if(!empty($photo_actuelle)){
                                echo '<div class="form-group"><label>Photo actuelle</label><hr>';
                                echo '<img src="' . URL . 'photo/' . $photo_actuelle . '" class="img-thumbnail" width="140">';
                                echo '<input type="hidden" name="photo_actuelle" value="' . $photo_actuelle . '">';
                                echo '</div>';
                            }
                        ?>
                        <div class="form-group bordure">
                            <label for="photo">Photo</label>
                            <input type="file" class="form-control" id="photo" name="photo" value="<?php echo $photo; ?>">
                        </div>
                    </div>
                    <div class="co-sm-6 form-contour">
                        <div class="form-group bordure">
                            <label for="description">Description</label>
                            <textarea name="description" id="" cols="30" rows="1" class="form-control"><?php echo $description; ?></textarea>
                        </div>
                        <div class="form-group bordure">
                            <label for="categorie">Categorie</label>
                            <select name="categorie" id="categorie" class="form-control">
                                <option value="chemise">Chemise</option>
                                <option value="pantalon" <?php  if($categorie == "pantalon") {echo 'selected';} ?>>Pantalon</option>
                                <option value="t-shirt" <?php  if($categorie == "t-shirt") {echo 'selected';} ?>>T-shirt</option>
                                <option value="sweat" <?php  if($categorie == "sweat") {echo 'selected';} ?>>Sweat</option>
                            </select>
                        </div>
                        <div class="form-group bordure">
                            <label for="couleur">Couleur</label>
                            <select name="couleur" id="couleur" class="form-control">
                                <option value="rouge">Rouge</option>
                                <option value="bleu" <?php  if($couleur == "bleu") {echo 'selected';} ?>>Bleu</option>
                                <option value="jaune" <?php  if($couleur == "jaune") {echo 'selected';} ?>>Jaune</option>
                                <option value="blanc" <?php  if($couleur == "blanc") {echo 'selected';} ?>>Blanc</option>
                            </select>
                        </div>
                        <div class="form-group bordure">
                            <label for="sexe">Sexe</label>
                            <select name="sexe" id="sexe">
                                <option value ='m'>Homme</option>
                                <option value = 'f'<?php  if($sexe == "f") {echo 'selected';} ?>>Femme</option>
                            </select>
                        </div>
                        <div class="form-group bordure">
                            <label for="taille">Taille</label>
                            <select name="taille" id="taille" class="form-control">
                                <option value="XS">XS</option>
                                <option value="S" <?php  if($taille == "S") {echo 'selected';} ?>>S</option>
                                <option value="M" <?php  if($taille == "M") {echo 'selected';} ?>>M</option>
                                <option value="L" <?php  if($taille == "L") {echo 'selected';} ?>>L</option>
                                <option value="XL" <?php  if($taille == "XL") {echo 'selected';} ?>>XL</option>
                                <option value="XXL" <?php  if($taille == "XXL") {echo 'selected';} ?>>XXL</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="validation" id="validation" class="btn btn-primary w-100" style="margin-top: 14px">Valider <i class="fas fa-check"></i></button>
                        </div>
                    </div>
                </div>
            </form>
            <?php } ?>
        </div>
    </div>
<?php
include '../inc/footer.inc.php';