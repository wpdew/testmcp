<?php

class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAllUsers($search = '') {
        $where = $search ? 'WHERE username LIKE ?' : '';
        $params = $search ? ["%$search%"] : [];
        return $this->db->fetchAll("SELECT * FROM users $where ORDER BY id ASC", $params);
    }

    public function getUserById($id) {
        return $this->db->fetch('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public function getUserByUsername($username) {
        return $this->db->fetch('SELECT * FROM users WHERE username = ?', [$username]);
    }

    public function addUser($username, $password) {
        if (strlen($username) < 3) {
            return ['status' => 'danger', 'message' => 'Логин должен быть не менее 3 символов.'];
        }
        if (strlen($password) < 4) {
            return ['status' => 'danger', 'message' => 'Пароль должен быть не менее 4 символов.'];
        }
        if ($this->getUserByUsername($username)) {
            return ['status' => 'danger', 'message' => 'Пользователь с таким логином уже существует.'];
        }
        $this->db->execute('INSERT INTO users (username, password) VALUES (?, ?)', [
            $username, password_hash($password, PASSWORD_DEFAULT)
        ]);
        return ['status' => 'success', 'message' => 'Пользователь успешно добавлен.'];
    }

    public function deleteUser($id) {
        $user = $this->getUserById($id);
        if ($user && $user['username'] !== 'admin') {
            $this->db->execute('DELETE FROM users WHERE id = ?', [$id]);
            return ['status' => 'success', 'message' => 'Пользователь удалён.'];
        }
        return ['status' => 'danger', 'message' => 'Нельзя удалить пользователя admin или пользователь не найден.'];
    }

    public function updateUser($id, $username, $password = null) {
        if (strlen($username) < 3) {
            return ['status' => 'danger', 'message' => 'Логин должен быть не менее 3 символов.'];
        }
        if ($this->db->fetch('SELECT * FROM users WHERE username = ? AND id != ?', [$username, $id])) {
            return ['status' => 'danger', 'message' => 'Пользователь с таким логином уже существует.'];
        }

        if ($password) {
            if (strlen($password) < 4) {
                return ['status' => 'danger', 'message' => 'Пароль должен быть не менее 4 символов.'];
            }
            $this->db->execute('UPDATE users SET username = ?, password = ? WHERE id = ?', [
                $username, password_hash($password, PASSWORD_DEFAULT), $id
            ]);
        } else {
            $this->db->execute('UPDATE users SET username = ? WHERE id = ?', [
                $username, $id
            ]);
        }
        return ['status' => 'success', 'message' => 'Данные пользователя обновлены.'];
    }
}