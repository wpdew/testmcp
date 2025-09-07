<?php
class Auth {
    private $db;
    public function __construct(Database $db) {
        $this->db = $db;
    }
    public function login($username, $password) {
        // Валидация
        if (strlen($username) < 3) {
            return ['status' => 'danger', 'message' => 'Логин должен быть не менее 3 символов.'];
        }
        if (strlen($password) < 4) {
            return ['status' => 'danger', 'message' => 'Пароль должен быть не менее 4 символов.'];
        }
        $user = $this->db->fetch('SELECT * FROM users WHERE username = ?', [$username]);
        if ($user && isset($user['blocked']) && $user['blocked']) {
            return ['status' => 'danger', 'message' => 'Пользователь заблокирован.'];
        }
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $this->logAttempt($username, true);
            return ['status' => 'success', 'message' => 'Успешный вход.'];
        }
        $this->logAttempt($username, false);
        return ['status' => 'danger', 'message' => 'Неверный логин или пароль.'];
    }
    public function logout() {
        session_unset();
        session_destroy();
        return ['status' => 'success', 'message' => 'Вы успешно вышли.'];
    }
    public function createInitialAdminUser($username, $password) {
        if (!$this->db->fetch('SELECT * FROM users WHERE username = ?', [$username])) {
            $this->db->execute('INSERT INTO users (username, password) VALUES (?, ?)', [
                $username, password_hash($password, PASSWORD_DEFAULT)
            ]);
            return ['status' => 'success', 'message' => 'Администратор создан.'];
        }
        return ['status' => 'info', 'message' => 'Администратор уже существует.'];
    }
    private function logAttempt($username, $success) {
        // Можно расширить: сохранять в отдельную таблицу
        // $this->db->execute('INSERT INTO login_attempts (username, success, time) VALUES (?, ?, ?)', [$username, $success ? 1 : 0, date('Y-m-d H:i:s')]);
    }
}