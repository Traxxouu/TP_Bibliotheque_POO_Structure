<?php
session_start();

// Si l'utilisateur est déjà connecté, l'envoyer vers le dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'db.php';
require_once 'classes/Utilisateur.php';

$utilisateurManager = new Utilisateur($pdo);

$erreurs = [];
$succes = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($nom)) {
        $erreurs[] = "Le nom est obligatoire.";
    }
    
    if (empty($email)) {
        $erreurs[] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs[] = "L'email n'est pas valide.";
    }
    
    if (empty($password)) {
        $erreurs[] = "Le mot de passe est obligatoire.";
    } elseif (strlen($password) < 6) {
        $erreurs[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    
    if ($password !== $password_confirm) {
        $erreurs[] = "Les mots de passe ne correspondent pas.";
    }
    
    if (empty($erreurs)) {
        if ($utilisateurManager->emailExiste($email)) {
            $erreurs[] = "AH !! Cet email est déjà utilisé.";
        } else {
            if ($utilisateurManager->inscrire($nom, $email, $password)) {
                $succes = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header('refresh:2;url=login.php');
            } else {
                $erreurs[] = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Bibliothèque de Efrei</title>  
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>
        
        <?php if (!empty($erreurs)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($erreurs as $erreur): ?>
                        <li><?php echo htmlspecialchars($erreur); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($succes)): ?>
            <div class="success">
                <?php echo htmlspecialchars($succes); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="nom">Nom complet</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="password_confirm">Confirmer le mot de passe</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            
            <button type="submit" class="btn">S'inscrire</button>
        </form>
        
        <div class="links">
            <a href="index.php"><- Retour à l'accueil</a>
            <span class="separator">|</span>
            <a href="login.php">Déjà un compte ? Se connecter</a>
        </div>
    </div>
</body>
</html>