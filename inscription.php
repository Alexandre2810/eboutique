<?php

include 'inc/init.inc.php';
include 'inc/function.inc.php';

    // CODE ...
$pseudo = '';
$mdp = '';
$nom = '';
$prenom = '';
$email = '';
$sexe = '';
$ville = '';
$cp = '';
$adresse = '';


// si le formulaire à été validé 
if(
    isset($_POST['pseudo']) &&
    isset($_POST['mdp']) &&
    isset($_POST['nom']) &&
    isset($_POST['prenom']) &&
    isset($_POST['email']) &&
    isset($_POST['sexe']) &&
    isset($_POST['ville']) &&
    isset($_POST['cp']) &&
    isset($_POST['adresse'])) {
    
    /*
    foreach($_POST AS $indice => $valeur) {
        $_POST[$indice] = trim($valeur); // pour appliquer un trim() à tous les éléments de $_POST
    }

    extract($_POST); // crée une variable de chaque indice présent dans $_POST avec sa valeur correspondante
    */


    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $sexe = trim($_POST['sexe']);
    $ville = trim($_POST['ville']);
    $cp = trim($_POST['cp']);
    $adresse = trim($_POST['adresse']);
    } 


// contrôle de la validité des caractère du pseudo via une expression régulière
$verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#', $pseudo);
/*
preg_match() permet de vérifié une chaine de caractère fournie en deuxième argument selon une expression régulière fournie en premier argument.
Renvoie 1 (true) si les caractères sont ok sinon 0 (false)

# => les # représentent le début et la fin de l'expression
^ => signifie que la chaine ne peut pas commencer par autre chose que les caractère de l'expression
$ => signifie que la chaine ne peut pas finir par autre chose que les caractère de l'expression
+ => signifie que l'on peut retrouver plusieur fois le même caractère.
[] => entre les crochets se trouve les caractères autorisés
*/
if(empty($pseudo) && empty($mdp) && empty($nom) && empty($prenom) && empty($email) && empty($ville) && empty($cp) && empty($adresse)){
    $msg.='';
}else{
    if(!$verif_caractere && !empty($pseudo)) {
        $msg .= '<div class="alert alert-danger">Erreur sur le pseudo, <br>Caractère autorisé A à Z, 0  à 9</div>';
    }
    
    // verification de la taille du pseudo entre 4 et 14 caractères inclus
    if(iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14){
        $msg .= '<div class="alert alert-danger mt-3">Erreur sur le pseudo, <br>Le pseudo doit avoir entre 4 et 14 caractères inclus</div>';
    }
    
    // controle sur le format du mail
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg .= '<div class="alert alert-danger mt-3"> Erreur sur l\'email, <br>Format de l\'email n\'est pas valide</div>';
    }
    
    // vérification si le code postal a 5 caractère de type numérique
    if(iconv_strlen($cp) != 5 || !is_numeric($cp)) {
        $msg .= '<div class="alert alert-danger mt-3">Erreur sur le code postal, <br>Le code postal doit avoir 5 caractère de type numérique</div>';
    }
    if(empty($msg)) {
        // si $msg est vide alors il n'y a pas eu d'erreur dans nos contrôle au dessus.
        $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
        $verif_pseudo->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
        $verif_pseudo->execute();
    
        if($verif_pseudo->rowCount() > 0) {
            $msg.= '<div class="alert alert-danger mt-3">Pseudo indisponible</div>';
        } else {
    
            // cryptage (hashage) du mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    
            // enregistrement en BDD
            $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, sexe, ville, cp, adresse, statut) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :sexe, :ville, :cp, :adresse, 1)");
            $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
            $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
            $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
            $enregistrement->bindParam(':sexe', $sexe, PDO::PARAM_STR);
            $enregistrement->bindParam(':ville', $ville, PDO::PARAM_STR);
            $enregistrement->bindParam(':cp', $cp, PDO::PARAM_STR);
            $enregistrement->bindParam(':adresse', $adresse, PDO::PARAM_STR);
            $enregistrement->execute();
            header('location:connexion.php');
        }
    }
}




// rajouter des contrôles


    
// debut des affichages dans la page :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>

    <div class="starter-template">
        <h1><i class="fas fa-sign-in-alt"></i> Inscription</h1>
        <p class="lead"><?php echo $msg; // pour afficher des messages utilisateur ?></p>
    </div>

    <div class="row">
        <div class="col-12">
            <form method="post" action="">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="pseudo">Pseudo</label>
                            <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo $pseudo; ?>">
                        </div>
                        <div class="form-group">
                            <label for="mdp">Mdp</label>
                            <input type="password" class="form-control" id="mdp" autocomplete="off" name="mdp" value="">
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>">
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prenom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">email</label>
                            <input type="text" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="sexe">Sexe</label>
                            <select name="sexe" id="sexe" class="form-control">
                                <option value="m">Homme</option>
                                <option value="f" <?php if($sexe == 'f') { echo 'selected'; } ?>>Femme</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="ville">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" value="<?php echo $ville; ?>">
                        </div>
                        <div class="form-group">
                            <label for="cp">Code postal</label>
                            <input type="text" class="form-control" id="cp" name="cp" value="<?php echo $cp; ?>">
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <textarea name="adresse" id="adresse" cols="30" rows="1" class="form-control"><?php echo $adresse; ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="inscription" id="inscription" class="btn btn-primary w-100" style="margin-top: 14px">Inscription <i class="fas fa-check"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php
include 'inc/footer.inc.php';

