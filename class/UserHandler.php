<?php
class UserHandler {
    public $id;
    public $nom;
    public $prenom;
    public $email;
    public $password;
    public $telephone;
    public $date_creation;
    public $role_id;
    public $role_name;

    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public static function authenticate(mysqli $conn, string $email, string $password) {
        $sql = "SELECT id, nom, prenom, email, password, telephone, date_creation, role_id, role_name
                FROM utilisateur
                WHERE email = ? AND password = ? AND role_id IN (1, 2, 3)  -- I added role_id = 3 for admins
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die('DB prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ss', $email, $password); // Binding email and password
        $stmt->execute();
        $stmt->bind_result(
            $id, $nom, $prenom, $emailDB, $pwdDB, 
            $telephone, $date_creation, $role_id, $role_name
        );
        
        if (!$stmt->fetch()) {
            $stmt->close();
            return false; // No match found
        }
        $stmt->close();
    
        return new self([
            'id' => $id,
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $emailDB,
            'password' => $pwdDB,
            'telephone' => $telephone,
            'date_creation' => $date_creation,
            'role_id' => $role_id,
            'role_name' => $role_name,
        ]);
    }
    
}