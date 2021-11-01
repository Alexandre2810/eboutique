<?php

include 'inc/init.inc.php';
include 'inc/function.inc.php';

// CODE ...
if (isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
    session_destroy();
    // unset ($_SESSION['membre']);
}

// si l'utilisateur est connecté on le renvoie sur le profil
if(user_is_connected()){
    header('location:profil.php');
}

$pseudo = '';
$mdp = '';
if(isset($_POST['pseudo']) && isset($_POST['mdp'])) {
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);

    // on interroge la BDD sur la base du pseudo
    $connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $connexion->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $connexion->execute();

    // on vérifie s'il y a une ligne dans la réponse BDD
    if($connexion->rowCount() > 0) {
        $infos_bdd = $connexion->fetch(PDO::FETCH_ASSOC);
        // on vérifie le mot de passe
        if(password_verify($mdp, $infos_bdd['mdp'])){
            //password_verify() permet de vérifier si la saisie correspond à l'information cryptée avec password_hash()
            $_SESSION['membre'] = array();
            $_SESSION['membre']['id_membre'] = $infos_bdd['id_membre'];
            $_SESSION['membre']['pseudo'] = $infos_bdd['pseudo'];
            $_SESSION['membre']['nom'] = $infos_bdd['nom'];
            $_SESSION['membre']['prenom'] = $infos_bdd['prenom'];
            $_SESSION['membre']['email'] = $infos_bdd['email'];
            $_SESSION['membre']['sexe'] = $infos_bdd['sexe'];
            $_SESSION['membre']['ville'] = $infos_bdd['ville'];
            $_SESSION['membre']['cp'] = $infos_bdd['cp'];
            $_SESSION['membre']['adresse'] = $infos_bdd['adresse'];
            $_SESSION['membre']['statut'] = $infos_bdd['statut'];

            foreach($_SESSION['membre'] AS $ind => $val) {
                if($ind != 'email') {
                    $_SESSION['membre'][$ind] = htmlentities($val, ENT_QUOTES);
                }
            }

            // on redirige vers profil.php
            header('location:profil.php');
            // header nous empeche de voir les erreurs car on change de page. mettre en commentaire pour tester les erreurs
        } else {
            // mot de passe incorrect
            $msg .= '<div class="alert alert-danger mt-3">Erreur sur le pseudo et/ou le mot de passe</div>';
        }
    } else {
        $msg .= '<div class="alert alert-danger mt-3">Erreur sur le pseudo et/ou le mot de passe</div>';    
    }
}

 
// debut des affichages dans la page :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; print_r($_POST); echo '</pre>';
// echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>

    <div class="starter-template">
        <h1><i class="fas fa-sign-in-alt"></i> Connexion</h1>
        <p class="lead"><?php echo $msg; // pour afficher des messages utilisateur ?></p>
    </div>

    <div class="row">
        <div class="col-4 mx-auto">
            <form method="post" action="">
                <div class="form-group">
                    <label for="pseudo">Pseudo</label>
                    <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo $pseudo; ?>">
                </div>
                <div class="form-group">
                    <label for="mdp">Mdp <i class="fas fa-eye-slash" style="cursor: pointer" id="show-password"></i></label>
                    <input type="password" class="form-control" id="mdp" name="mdp" autocomplete="off" value="">
                </div>
                <div class="form-group">
                    <button type="submit" name="connexion" id="connexion" class="btn btn-primary w-100">Connexion<i class="fas fa-check"></i></button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/mon_script.js"></script>

<?php
include 'inc/footer.inc.php';

