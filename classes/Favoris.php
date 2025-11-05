<?php
class Favoris {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * @param int 
     * @param int 
     * @return bool 
     */
    public function ajouterFavori($utilisateur_id, $livre_id) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO favoris (utilisateur_id, livre_id) VALUES (?, ?)");
            return $stmt->execute([$utilisateur_id, $livre_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * @param int 
     * @param int 
     * @return bool 
     */
    public function retirerFavori($utilisateur_id, $livre_id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM favoris WHERE utilisateur_id = ? AND livre_id = ?");
            return $stmt->execute([$utilisateur_id, $livre_id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    /**
     * @param int 
     * @return array 
     */
    public function getFavorisUtilisateur($utilisateur_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT l.* 
                FROM livres l
                INNER JOIN favoris f ON l.id = f.livre_id
                WHERE f.utilisateur_id = ?
                ORDER BY f.created_at DESC
            ");
            $stmt->execute([$utilisateur_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    /**
     * @param int 
     * @param int 
     * @return bool 
     */
    public function estFavori($utilisateur_id, $livre_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM favoris WHERE utilisateur_id = ? AND livre_id = ?");
            $stmt->execute([$utilisateur_id, $livre_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>