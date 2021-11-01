<?php

include '../inc/init.inc.php';
include '../inc/function.inc.php';
if(!user_is_admin()){
    header("location:" . URL . "connexion.php");
    exit();
}






include '../inc/header.inc.php';
include '../inc/nav.inc.php';
?> 
    <div class="starter-template">
        <h1><i class="far fa-money-bill-alt"></i> Gestion commandes <i class="far fa-money-bill-alt"></i></h1>
        <p class="lead">
            <?php echo $msg; // pour afficher des messages utilisateur ?>
        </p>
    </div>

    <?php
    $liste_commande = $pdo->query("SELECT * FROM commande ORDER BY id_commande");
    $nb_commande = $liste_commande->rowCount();
    echo '<p>Voici la liste des commandes passé (' . $nb_commande . ')</p>';
    echo '<table class="table tableau-bordure">';
    echo '<tr class="bg-dark text-white">';
    echo '<th class="tableau-bordure">id_commande</th>';
    echo '<th class="tableau-bordure">id_membre</th>';
    echo '<th class="tableau-bordure">Montant</th>';
    echo '<th class="tableau-bordure">Date</th>';
    echo '<th class="tableau-bordure">Etat</th>';
    echo '<th class="tableau-bordure">Détail</th>';
    echo '</tr>';

    while($commande = $liste_commande->fetch(PDO::FETCH_ASSOC)){
        echo '<tr class="bg-white">';
        foreach($commande AS $ind => $val){
            echo '<td class="tableau-bordure">' . $val . '</td>';
        }
        echo '<td class="tableau-bordure"><a class="btn btn-primary" href="?action=detail&id_commande=' . $commande['id_commande'] .'">Afficher détail</a></td>';
        echo '</tr>';
    }

    echo '</table>';
    if(isset($_GET['action']) && !empty($_GET['id_commande']) && is_numeric($_GET['id_commande'])){
        $id_commande = $_GET['id_commande'];
        $detail_commande = $pdo->query("SELECT * FROM details_commande WHERE id_commande = $id_commande");
        echo '<p>Voici le détail de la commande ' . $id_commande .'</p>';
        echo '<table class="table tableau-bordure">';
        echo '<tr class="bg-dark text-white">';
        echo '<th class="tableau-bordure">id_detail_commande</th>';
        echo '<th class="tableau-bordure">id_commande</th>';
        echo '<th class="tableau-bordure">id_article</th>';
        echo '<th class="tableau-bordure">Quantite</th>';
        echo '<th class="tableau-bordure">Prix</th>';
        echo '</tr>';
        while($detail = $detail_commande->fetch(PDO::FETCH_ASSOC)){
            echo '<tr class="bg-white">';
            foreach($detail AS $ind => $val){
                echo '<td class="tableau-bordure">' . $val . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    ?>


<?php
include '../inc/footer.inc.php';