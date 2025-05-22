<?php
require_once 'Database.php';

class GestionnaireHandler {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database('localhost', 'root', '', 'pfe');
        $this->conn = $this->db->getConnection();
    }

    public function login($email, $password) {
        try {
            // First check if the user exists and is a gestionnaire
            $stmt = $this->conn->prepare("
                SELECT u.*, g.poste, g.departement 
                FROM utilisateur u
                JOIN gestionnaire g ON u.id = g.id
                WHERE u.email = ? AND u.role_id = 2
            ");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }

            // Verify password
            if (!password_verify($password, $user['mot_de_passe'])) {
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }

            // Start session and store user data
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['poste'] = $user['poste'];
            $_SESSION['departement'] = $user['departement'];

            return [
                'success' => true,
                'message' => 'Connexion réussie',
                'redirect' => 'pages/gestionnaire.php'
            ];

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la connexion'
            ];
        }
    }

    public function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']) && $_SESSION['role_id'] === 2;
    }

    public function getGestionnaireInfo($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.*, g.poste, g.departement 
                FROM utilisateur u
                JOIN gestionnaire g ON u.id = g.id
                WHERE u.id = ? AND u.role_id = 2
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error getting gestionnaire info: " . $e->getMessage());
            return null;
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        return [
            'success' => true,
            'message' => 'Déconnexion réussie',
            'redirect' => 'Login.php'
        ];
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
} 