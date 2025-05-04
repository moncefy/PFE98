<?php
session_start();

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/UserHandler.php';
require_once __DIR__ . '/adminHandler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize DB (replace with your actual database name)
    $db   = new Database('localhost', 'root', '', 'pfe');
    $conn = $db->getConnection();

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $password) {
        $_SESSION['login_error'] = 'Please enter a valid email and password.';
        header('Location: ../pages/login.php');
        exit;
    }

    // Client or gestionnaire
    $user = UserHandler::authenticate($conn, $email, $password);
    if ($user) {
        $_SESSION['user_id']   = $user->id;
        $_SESSION['nom']       = $user->nom;
        $_SESSION['prenom']    = $user->prenom;
        $_SESSION['email']     = $user->email;
        $_SESSION['role_id']   = $user->role_id;
        $_SESSION['role_name'] = $user->role_name;

        $dest = $user->role_id === 1
            ? '../pages/welcome.php'
            : '../pages/gestionnaire.php';

        header('Location: ' . $dest);
        exit;
    }

    // Admin fallback
    $adminId = Admin::loginA($conn, $email, $password);
    if ($adminId) {
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['role_id']  = 3;
        header('Location: ../pages/admin.php');
        exit;
    }

    // Invalid
    $_SESSION['login_error'] = 'Invalid credentials.';
    header('Location: ../pages/login.php');
    exit;
}