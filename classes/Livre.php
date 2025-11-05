<?php
class Livre {
    private $pdo;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * @return array liste des livres
     */
    public function getTousLesLivres() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM livres ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * @param int 
     * @return array|false Les données du livre ou false si il y a pas
     */
    public function getLivreParId($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM livres WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>