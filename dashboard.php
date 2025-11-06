<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';
require_once 'classes/Livre.php';
require_once 'classes/Utilisateur.php';
require_once 'classes/Favoris.php';

$livreManager = new Livre($pdo);
$utilisateurManager = new Utilisateur($pdo);
$favorisManager = new Favoris($pdo);

$utilisateur = $utilisateurManager->getUtilisateurParId($_SESSION['user_id']);
$livres = $livreManager->getTousLesLivres();
$mesFavoris = $favorisManager->getFavorisUtilisateur($_SESSION['user_id']);

$message_edition = '';
$erreurs_edition = [];
$message_favori = '';
$message_livre = '';
$erreurs_livre = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editer') {
    $nouveau_nom = trim($_POST['nom'] ?? '');
    $nouveau_email = trim($_POST['email'] ?? '');
    
    if (empty($nouveau_nom)) {
        $erreurs_edition[] = "Le nom est obligatoire.";
    }
    
    if (empty($nouveau_email)) {
        $erreurs_edition[] = "L'email est obligatoire.";
    } elseif (!filter_var($nouveau_email, FILTER_VALIDATE_EMAIL)) {
        $erreurs_edition[] = "L'email n'est pas valide.";
    }
    
    if (empty($erreurs_edition)) {
        try {
            if ($nouveau_email !== $utilisateur['email']) {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ? AND id != ?");
                $stmt->execute([$nouveau_email, $_SESSION['user_id']]);
                if ($stmt->fetchColumn() > 0) {
                    $erreurs_edition[] = "Cet email est déjà utilisé par un autre compte.";
                }
            }
            
            if (empty($erreurs_edition)) {
                $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, email = ? WHERE id = ?");
                $stmt->execute([$nouveau_nom, $nouveau_email, $_SESSION['user_id']]);
                
                $_SESSION['user_nom'] = $nouveau_nom;
                $_SESSION['user_email'] = $nouveau_email;

                $message_edition = "Votre compte a été mis à jour avec succès !";
                $utilisateur = $utilisateurManager->getUtilisateurParId($_SESSION['user_id']);
            }
        } catch (PDOException $e) {
            $erreurs_edition[] = "Erreur lors de la mise à jour.";
        }
    }
}

// Favoris
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_favori') {
    $livre_id = intval($_POST['livre_id'] ?? 0);
    
    if ($livre_id > 0) {
        if ($favorisManager->ajouterFavori($_SESSION['user_id'], $livre_id)) {
            $message_favori = "Livre ajouté aux favoris !";
            $mesFavoris = $favorisManager->getFavorisUtilisateur($_SESSION['user_id']);
        } else {
            $message_favori = "Erreur lors de l'ajout aux favoris.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'retirer_favori') {
    $livre_id = intval($_POST['livre_id'] ?? 0);
    
    if ($livre_id > 0) {
        if ($favorisManager->retirerFavori($_SESSION['user_id'], $livre_id)) {
            $message_favori = "Livre retiré des favoris !";
            $mesFavoris = $favorisManager->getFavorisUtilisateur($_SESSION['user_id']);
        } else {
            $message_favori = "Erreur lors du retrait des favoris.";
        }
    }
}

// Ajouter un livre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_livre') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    
    if (empty($titre)) {
        $erreurs_livre[] = "Le titre est obligatoire.";
    }
    
    if (empty($auteur)) {
        $erreurs_livre[] = "L'auteur est obligatoire.";
    }
    
    if (empty($erreurs_livre)) {
        if ($livreManager->ajouterLivre($titre, $auteur, $_SESSION['user_id'])) {
            $message_livre = "Livre ajouté avec succès !";
            $livres = $livreManager->getTousLesLivres();
        } else {
            $erreurs_livre[] = "Erreur lors de l'ajout du livre.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bibliothèque Efrei</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Tableau de bord</h1>
            <p>Bienvenue, <?php echo htmlspecialchars($utilisateur['nom']); ?> !</p>
            <div class="auth-links">
                <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
                <a href="logout.php" class="btn btn-primary">Se déconnecter</a>
            </div>
        </header>
        
        <?php if (!empty($message_favori)): ?>
            <div class="success">
                <?php echo htmlspecialchars($message_favori); ?>
            </div>
        <?php endif; ?>
        
        <!-- Section du compte -->
        <div class="section">
            <h2>Mon compte</h2>
            
            <?php if (!empty($erreurs_edition)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($erreurs_edition as $erreur): ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($message_edition)): ?>
                <div class="success">
                    <?php echo htmlspecialchars($message_edition); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="editer">
                
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($utilisateur['nom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required>
                </div>
                
                <button type="submit" class="btn">Mettre à jour</button>
            </form>
        </div>
        
        <div class="section">
            <h2>Ajouter un livre au catalogue</h2>
            
            <?php if (!empty($erreurs_livre)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($erreurs_livre as $erreur): ?>
                            <li><?php echo htmlspecialchars($erreur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($message_livre)): ?>
                <div class="success">
                    <?php echo htmlspecialchars($message_livre); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="ajouter_livre">
                
                <div class="form-group">
                    <label for="titre">Titre du livre</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                
                <div class="form-group">
                    <label for="auteur">Auteur</label>
                    <input type="text" id="auteur" name="auteur" required>
                </div>
                
                <button type="submit" class="btn">Ajouter le livre</button>
            </form>
        </div>
        
        <!-- Section Mes favoris -->
        <div class="section">
            <h2>Mes favoris</h2>
            
            <?php if (empty($mesFavoris)): ?>
                <p>Vous n'avez pas encore de livres favoris.</p>
            <?php else: ?>
                <div class="livres-list">
                    <?php foreach ($mesFavoris as $livre): ?>
                        <div class="livre-item">
                            <h3><?php echo htmlspecialchars($livre['titre']); ?></h3>
                            <p>Auteur : <?php echo htmlspecialchars($livre['auteur']); ?></p>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="action" value="retirer_favori">
                                <input type="hidden" name="livre_id" value="<?php echo $livre['id']; ?>">
                                <button type="submit" class="btn btn-small">Retirer des favoris</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="section">
            <h2>Livres disponibles</h2>
            
            <?php if (empty($livres)): ?>
                <p>Aucun livre disponible pour le moment.</p>
            <?php else: ?>
                <div class="livres-list">
                    <?php foreach ($livres as $livre): ?>
                        <div class="livre-item">
                            <h3><?php echo htmlspecialchars($livre['titre']); ?></h3>
                            <p>Auteur : <?php echo htmlspecialchars($livre['auteur']); ?></p>
                            
                            <?php if ($favorisManager->estFavori($_SESSION['user_id'], $livre['id'])): ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="retirer_favori">
                                    <input type="hidden" name="livre_id" value="<?php echo $livre['id']; ?>">
                                    <button type="submit" class="btn btn-small">Retirer des favoris</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="ajouter_favori">
                                    <input type="hidden" name="livre_id" value="<?php echo $livre['id']; ?>">
                                    <button type="submit" class="btn btn-small">Ajouter aux favoris</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>