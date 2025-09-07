<?php

require_once __DIR__ . '/../Models/User.php';

class UserController {
    private $userModel;
    private $alert = '';
    private $alertType = 'success';

    public function __construct(Database $db) {
        $this->userModel = new User($db);
    }

    public function processUsersPage() { // Переименован из handleRequest
        $search = trim($_GET['search'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add'])) {
                $result = $this->userModel->addUser($_POST['username'], $_POST['password']);
                $this->setAlert($result['message'], $result['status']);
            } elseif (isset($_POST['edit'])) {
                $result = $this->userModel->updateUser($_POST['id'], $_POST['username'], $_POST['password']);
                $this->setAlert($result['message'], $result['status']);
            }
        } elseif (isset($_GET['delete'])) {
            $result = $this->userModel->deleteUser((int)$_GET['delete']);
            $this->setAlert($result['message'], $result['status']);
        }

        $users = $this->userModel->getAllUsers($search);
        
        return [
            'users' => $users,
            'alert' => $this->alert,
            'alertType' => $this->alertType,
            'search' => $search // Передаем значение поиска обратно в представление для формы
        ];
    }

    private function setAlert($message, $type) {
        $this->alert = $message;
        $this->alertType = $type;
    }
}