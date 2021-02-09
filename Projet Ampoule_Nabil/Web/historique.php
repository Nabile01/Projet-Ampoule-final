<?php
session_start();

if (empty($_SESSION['login'])) {
    header("location:login.php");
    exit;
}

if (isset($_GET['deco'])) {
    $_SESSION = array();
    session_destroy();
    unset($_SESSION);
    unset($_COOKIE);
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Location:index.php");
    exit;
}

try {       // CONNEXION A LA BASE DE DONNEE
    $bdd = new PDO('mysql:host=localhost;dbname=dashboard', 'root');
} catch (PDOException $e) {
    print "Erreur :" . $e->getMessage();
}

// REQUETE SUPPRESSION
if (isset($_GET['numAmpoule'])) {
    $suppr = $bdd->prepare('DELETE FROM ampoules WHERE id=:num LIMIT 1');
    $suppr->bindValue(':num', $_GET['numAmpoule']); // affecter la valeur de l'url dans le parametre :num
    $suppr->execute();
}

// PAGINATION
$ampouleParPage = 11;
$ampouleTotalReq = $bdd->query('SELECT id FROM ampoules'); // Requete pour recup toutes les ampoules qu'il y'a en bdd
$ampouleTotal = $ampouleTotalReq->rowCount(); // Compte le nombre d'entrée de la table
$nombrePage = ceil($ampouleTotal / $ampouleParPage); // divise le nombre de ligne par le nombre de ligne par page que l'on souhaite (et arrondi grace a ceil)

if (isset($_GET['page']) && ($_GET['page'] > 0) && ($_GET['page'] <= $nombrePage)) {
    $_GET['page'] = intval($_GET['page']); //pour éviter les injections
    $pageCourante = $_GET['page'];
} else {
    $pageCourante = 1;
}

$start = ($pageCourante - 1) * $ampouleParPage;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Historique</title>
</head>

<body class="histo">
    <header>
        <nav>
            <li><a href="form.php">Ajouter une ampoule</a></li>
            <li><a href="?deco">Déconnexion</a></li>
        </nav>
    </header>

    <main>
        <h1>HISTORIQUE</h1>
        <div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DATE</th>
                        <th>ETAGE</th>
                        <th>POSITION</th>
                        <th>PRIX</th>
                        <th>SUPPRIMER</th>
                        <th>MODIFIER</th>
                    </tr>
                </thead>
                <!-- Affichage des données de la bdd dans un tableau -->
                <?php
                $req = $bdd->query('SELECT * FROM ampoules ORDER BY id ASC LIMIT ' . $start . ',' . $ampouleParPage); // L'id ou on commence et de combien d'id on continue
                while ($donnees = $req->fetch()) :
                ?>
                    <tbody>
                        <tr>
                            <td><?= $donnees['id'] ?></td>
                            <td><?= $donnees['date'] ?></td>
                            <td><?= $donnees['etage'] ?></td>
                            <td><?= $donnees['position'] ?></td>
                            <td><?= $donnees['prix'] ?></td>
                            <td><button><a href="http://ampoule/historique.php?numAmpoule=<?= $donnees['id'] ?>" onclick="return(confirm('Etes-vous sûr de vouloir supprimer ?'));">Supprimer </a></button></td>
                            <td><button><a href="http://ampoule/form.php?numAmpoulemodif=<?= $donnees['id'] ?>">Modifier</a></button></td>
                        </tr>
                    </tbody>
                <?php endwhile; ?>
            </table>
        </div>
        <?php for ($i = 1; $i <= $nombrePage; $i++) {
            if ($i == $pageCourante) {
                echo $i . ' ';
            } else {
                echo '<a class="pagination" href="http://ampoule/historique.php?page=' . $i . '">' . $i . '</a> ';
            }
        } ?>
    </main>
</body>

</html>