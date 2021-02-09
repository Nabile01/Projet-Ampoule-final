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
    header("Location:login.php");
    exit;
}


try {       // CONNEXION A LA BASE DE DONNEE
    $bdd = new PDO('mysql:host=localhost;dbname=dashboard', 'root');
} catch (PDOException $e) {
    print "Erreur :" . $e->getMessage();
}

if (isset($_GET['submit'])) { //SI LE BOUTON SUBMIT A ETE DECLENCHE

    $date = $_GET['date'];
    $position = $_GET['position'];
    $etage = $_GET['etage'];
    $prix = $_GET['prix'];

    if (!empty($_GET['idamp'])) {
        // si l'input hidden est rempli = modifier
        $modif = $bdd->prepare('UPDATE ampoules SET date=:date1, etage=:etage,  position=:position, prix=:prix WHERE id=:num3');
        $modif->bindParam(':num3', $_GET['idamp']);
        $modif->bindParam(':date1', $_GET['date']);
        $modif->bindParam(':etage', $_GET['etage']);
        $modif->bindParam(':position', $_GET['position']);
        $modif->bindParam(':prix', $_GET['prix']);
        $modif->execute();
    } else { // sinon : ajouter
        if (!empty($date) && !empty($position) && !empty($etage) && !empty($prix)) {
            $inser = $bdd->prepare('INSERT INTO ampoules(date, etage, position, prix) VALUES (:date, :etage, :position, :prix)');
            $inser->bindValue(':date', $_GET['date']);
            $inser->bindValue(':etage', $_GET['etage']);
            $inser->bindValue(':position', $_GET['position']);
            $inser->bindValue(':prix', $_GET['prix']);
            $inser->execute();
        }
    }
}

// RECUPERER LES INFOS DE l'ID POUR LEDITION DU FORMULAIRE
$select = $bdd->prepare('SELECT * FROM ampoules WHERE id=:num2');
$select->bindParam(':num2', $_GET['numAmpoulemodif']);
$select->execute();

$info = $select->fetch();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="style.css">
    <title>Gestion des ampoules</title>
</head>

<body class="form">
    <header>
        <nav>
            <li><a href="form.php">Ajouter une ampoule</a></li>
            <li><a href="historique.php">Historique</a></li>
            <li><a href="?deco">Déconnexion</a></li>
        </nav>
    </header>

    <main>
        <form action="http://ampoule/form.php?numAmpoulemodif" method="GET">
            <fieldset>
                <legend>Gestion des ampoules</legend>
                <div>
                    <input type="hidden" name="idamp" value="<?= $info['id'] ?>">
                </div>

                <div>
                    <select name="etage" id="etage">
                        <option value="Etage 1" <?= ($info['etage'] == "Etage 1") ? "selected" : "" ?>>Etage 1</option>
                        <option value="Etage 2" <?= ($info['etage'] == "Etage 2") ? "selected" : "" ?>>Etage 2</option>
                        <option value="Etage 3" <?= ($info['etage'] == "Etage 3") ? "selected" : "" ?>>Etage 3</option>
                        <option value="Etage 4" <?= ($info['etage'] == "Etage 4") ? "selected" : "" ?>>Etage 4</option>
                        <option value="Etage 5" <?= ($info['etage'] == "Etage 5") ? "selected" : "" ?>>Etage 5</option>
                        <option value="Etage 6" <?= ($info['etage'] == "Etage 6") ? "selected" : "" ?>>Etage 6</option>
                        <option value="Etage 7" <?= ($info['etage'] == "Etage 7") ? "selected" : "" ?>>Etage 7</option>
                        <option value="Etage 8" <?= ($info['etage'] == "Etage 8") ? "selected" : "" ?>>Etage 8</option>
                        <option value="Etage 9" <?= ($info['etage'] == "Etage 9") ? "selected" : "" ?>>Etage 9</option>
                        <option value="Etage 10" <?= ($info['etage'] == "Etage 10") ? "selected" : "" ?>>Etage 10</option>
                        <option value="Etage 11" <?= ($info['etage'] == "Etage 11") ? "selected" : "" ?>>Etage 11</option>
                    </select>
                </div>

                <div>
                    <select name="position" id="postion">
                        <option value="Droit" <?= ($info['position'] == "Droit") ? "selected" : "" ?>>Côté droit</option>
                        <option value="Gauche" <?= ($info['position'] == "Gauche") ? "selected" : "" ?>>Côté gauche</option>
                        <option value="Fond" <?= ($info['position'] == "Fond") ? "selected" : "" ?>>Au fond</option>
                    </select>
                </div>


                <div>
                    <label for="prix"></label>
                    <input type="date" name="date" required value="<?= $info['date'] ?>">

                    <label for="prix"></label>
                    <input type="text" name="prix" placeholder="Prix" required value="<?= $info['prix'] ?>">
                </div>

                <input type="submit" name="submit" value="Valider">
            </fieldset>
        </form>
    </main>
</body>

</html>