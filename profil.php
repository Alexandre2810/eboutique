<?php

include 'inc/init.inc.php';
include 'inc/function.inc.php';

// CODE ...
// si l'utilisateur n'est pas connecté, on l'envoie sur la page connexion.php
if(!user_is_connected()){
    header("location:connexion.php");
}


// debut des affichages dans la page :
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
//echo '<pre>'; print_r($_SESSION); echo '</pre>';
?>

    <div class="starter-template">
        <h1><i class="fas fa-user"></i> Profil</h1>
        <p class="lead"><?php echo $msg; // pour afficher des messages utilisateur ?></p>
    </div>
    <h2>Bienvenue <?php echo $_SESSION['membre']['prenom'] . ' ' . $_SESSION['membre']['nom']?></h2>
    <div class="row">
        <div class="col-6">
            <ul class="list-group">
                <li class="list-group-item">Vos informations</li>
                <li class="list-group-item"><b>Pseudo : <span class='float-right'><?php echo $_SESSION['membre']['pseudo'] ?></span></b></li>
                <li class="list-group-item"><b>Nom : <span class='float-right'><?php echo $_SESSION['membre']['nom'] ?></span></b></li>
                <li class="list-group-item"><b>Prenom : <span class='float-right'><?php echo $_SESSION['membre']['prenom'] ?></span></b></li>
                <li class="list-group-item"><b>Email : <span class='float-right'><?php echo $_SESSION['membre']['email'] ?></span></b></li>
                <?php
                    if($_SESSION['membre']['sexe'] == 'm') {
                        $sexe = 'Homme';
                    } else {
                        $sexe = 'Femme';
                    }
                ?>
                <li class="list-group-item"><b>Sexe : <span class='float-right'><?php echo $sexe; ?></span></b></li>
                <li class="list-group-item"><b>Ville : <span class='float-right'><?php echo $_SESSION['membre']['ville'] ?></span></b></li>
                <li class="list-group-item"><b>cp : <span class='float-right'><?php echo $_SESSION['membre']['cp'] ?></span></b></li>
                <li class="list-group-item"><b>Adresse : <span class='float-right'><?php echo $_SESSION['membre']['adresse'] ?></span></b></li>
                <?php
                    if($_SESSION['membre']['statut'] == 1) {
                        $statut = 'membre';
                    } else {
                        $statut = 'administrateur';
                    }
                ?>
                <li class="list-group-item"><b>Statut : <span class='float-right'><?php echo $statut ?></span></b></li>
            </ul>
        </div>
        <div class="col-6">
            <img src="img/ninja.jpg" alt="fisherman" class="img-thumbnail">
        </div>
    </div>

<?php
include 'inc/footer.inc.php';

