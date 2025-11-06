<?php
session_start();

// Si déjà connecté, envoye au dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
require_once 'db.php';
require_once 'classes/Utilisateur.php';
$utilisateurManager = new Utilisateur($pdo);
$erreurs = [];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire.";
    }
    if (empty($password)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    }
    if (empty($erreurs)) {
        $utilisateur = $utilisateurManager->connecter($email, $password);
        
        if ($utilisateur) {
            $_SESSION['user_id'] = $utilisateur['id'];
            $_SESSION['user_nom'] = $utilisateur['nom'];
            $_SESSION['user_email'] = $utilisateur['email'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erreurs[] = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Bibliothèque Efrei</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Connexion</h1>
        
        <?php if (!empty($erreurs)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>
        
        <div class="links">
            <a href="index.php"><- Retour à l'accueil</a>
            <span class="separator">|</span>
            <a href="register.php">Pas encore de compte ? S'inscrire</a>
        </div>
    </div>
</body>
</html>