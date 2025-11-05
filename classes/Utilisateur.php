<?php
class Utilisateur {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @param string 
     * @param string  
     * @param string 
     * @return bool 
     */
    public function inscrire($nom, $email, $password) {
        try {
            // vérifier si l'email existe deja
            if ($this->emailExiste($email)) {
                return false;
            }
            // Hacher le mdp
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            // Mettre l'utilisateur
            $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (nom, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$nom, $email, $passwordHash]);
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * @param string 
     * @return bool 
     */
    public function emailExiste($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * @param string
     * @param string 
     * @return array|false
     */
    public function connecter($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($utilisateur && password_verify($password, $utilisateur['password'])) {
                return $utilisateur;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * @param int 
     * @return array|false 
     */
    public function getUtilisateurParId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>