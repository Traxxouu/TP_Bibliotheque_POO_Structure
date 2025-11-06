<?php
session_start();
require_once 'db.php';
require_once 'classes/Livre.php';
$livreManager = new Livre($pdo);

// Récuperer les livres
$livres = $livreManager->getTousLesLivres();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque Efrei - Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Bibliothèque Efrei</h1>
            <p>Découvrez notre catalogue de livres</p>
            <div class="auth-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="logout.php" class="btn btn-secondary">Se déconnecter</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">S'inscrire</a>
                    <a href="login.php" class="btn btn-secondary">Se connecter</a>
                <?php endif; ?>
            </div>
        </header>
        
        <div class="livres-section">
            <h2>Livres disponibles</h2>
            <?php if (empty($livres)): ?>
                <div class="info-box">
                    <strong>Attention !!!:</strong> Aucun livre n'est encore disponible dans la base de données. 
                    Pensez à créer la base de données et insérer quelques livres pour les tests !
                </div>
                <div class="no-livres">
                    Aucun livre présent pour le moment.
                </div>
            <?php else: ?>
                <div class="livres-grid">
                    <?php foreach ($livres as $livre): ?>
                        <div class="livre-card">
                            <h3><?php echo htmlspecialchars($livre['titre']); ?></h3>
                            <p><strong>Auteur :</strong> <?php echo htmlspecialchars($livre['auteur']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>Fait par Barbe Maël dans le cadre d'un projet à Efrei Paris</p>
    </footer>
</body>
</html>
