<?php
class Admin {
    public static function loginA(mysqli $conn, string $username, string $password) {
        $sql = "SELECT u.id
                FROM utilisateur u
                JOIN admin a ON u.id = a.id
                WHERE a.username = ? AND u.password = ? AND u.role_id = 3
                LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (! $stmt) {
            die('DB prepare failed: ' . $conn->error);
        }
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $stmt->bind_result($id);
        if (! $stmt->fetch()) {
            $stmt->close();
            return false;
        }
        $stmt->close();
        return $id;
    }
}