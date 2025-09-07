<?php
require_once __DIR__ . '/../Models/Auth.php';
class AuthController {
    public $authModel;
    public function __construct(Database $db) {
        $this->authModel = new Auth($db);
    }
    public function handleLogin($post) {
        $alert = '';
        $alertType = 'success';
        if (isset($_SESSION['user'])) {
            header('Location: index.php');
            exit;
        }
        if (isset($post['login'])) {
            $username = trim($post['username']);
            $password = trim($post['password']);
            $result = $this->authModel->login($username, $password);
            $alert = $result['message'];
            $alertType = $result['status'];
            if ($result['status'] === 'success') {
                header('Location: index.php');
                exit;
            }
        }
        return ['alert' => $alert, 'alertType' => $alertType];
    }
    public function processLogout() {
        $this->authModel->logout();
        header('Location: login.php');
        exit;
    }
}