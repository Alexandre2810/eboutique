<?php

include '../inc/init.inc.php';
include '../inc/function.inc.php';
if(!user_is_admin()){
    header("location:" . URL . "connexion.php");
    exit();
}



if(isset($_POST['statut'])) {
    $statut = $_POST['statut'];
    $id_membre = $_POST['id_membre'];
    $liste_membre = $pdo->query("UPDATE membre SET statut = $statut WHERE id_membre = $id_membre");
}



include '../inc/header.inc.php';
include '../inc/nav.inc.php';
echo '<pre>';print_r($_POST); echo '</pre>';
?> 
    <div class="starter-template">
        <h1><i class="far fa-money-bill-alt"></i> Gestion membres <i class="far fa-money-bill-alt"></i></h1>
        <p class="lead">
            <?php echo $msg; // pour afficher des messages utilisateur ?>
        </p>
    </div>
    <?php
    $liste_membre = $pdo->query("SELECT * FROM membre ORDER BY id_membre");
    $nb_membre = $liste_membre->rowCount();
    echo '<p>Voici la liste des membres du site (' . $nb_membre . ')</p>';
    echo '<table class="table tableau-bordure">';
    echo '<tr class="bg-dark text-white">';
    echo '<th class="tableau-bordure">id_membre</th>';
    echo '<th class="tableau-bordure">Pseudo</th>';
    echo '<th class="tableau-bordure">Mdp</th>';
    echo '<th class="tableau-bordure">Nom</th>';
    echo '<th class="tableau-bordure">Prenom</th>';
    echo '<th class="tableau-bordure">Email</th>';
    echo '<th class="tableau-bordure">Sexe</th>';
    echo '<th class="tableau-bordure">Ville</th>';
    echo '<th class="tableau-bordure">Code postale</th>';
    echo '<th class="tableau-bordure">Adresse</th>';
    echo '<th class="tableau-bordure">Statut</th>';
    echo '<th class="tableau-bordure">Changer statut</th>';
    echo '</tr>';
    

    while($membre = $liste_membre->fetch(PDO::FETCH_ASSOC)) {
        $statut = $membre['statut'];
        $id_membre = $membre['id_membre'];
        echo '<tr class="bg-white">';
        foreach($membre AS $ind => $val){
            echo '<td class="tableau-bordure">' . $val . '</td>';
        }
        if($statut == '1'){
            echo '<td><form method="post" action=""><input type="hidden" name="id_membre" value="'. $id_membre . '"><input class="btn btn-danger" type="submit" value="2" name="statut"></form></td>';
        }else{
            echo '<td><form method="post" action=""><input type="hidden" name="id_membre" value="'. $id_membre . '"><input class="btn btn-danger" type="submit" value="1" name="statut"></form></td>';
        }
        echo '</tr>';
    }

    echo '</table>';
    ?>
<?php
include '../inc/footer.inc.php';