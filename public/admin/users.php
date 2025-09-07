<?php
session_start();
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Controllers/UserController.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$db = new Database(__DIR__ . '/config/database.sqlite');
$controller = new UserController($db);
$data = $controller->processUsersPage();

$users = $data['users'];
$alert = $data['alert'];
$alertType = $data['alertType'];
$search = $data['search']; // Передаем значение поиска обратно в форму

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи | Админка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="index.php" class="nav-link">Главная</a>
            </li>
        </ul>
        <form method="get" class="d-flex ms-3" style="max-width: 350px;">
            <input type="text" name="search" class="form-control me-2" placeholder="Поиск по логину" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Поиск</button>
        </form>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <span class="nav-link"><?php echo htmlspecialchars($_SESSION['user']); ?></span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php">Выйти</a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="index.php" class="brand-link">
            <span class="brand-text font-weight-light">AdminLTE PHP</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-item">
                        <a href="index.php" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Главная</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="users.php" class="nav-link active">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Пользователи</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Настройки</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Пользователи</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="container-fluid">
                <?php if ($alert): ?>
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
                    <div id="alertToast" class="toast align-items-center text-bg-<?php echo $alertType; ?> border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php echo $alert; ?>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <script>
                setTimeout(function() {
                    var toastEl = document.getElementById('alertToast');
                    if (toastEl) {
                        var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
                        toast.hide();
                    }
                }, 3000);
                </script>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-end align-items-center">
                        <div id="datatable-search-container" class="d-flex align-items-center"></div>
                        <button type="button" class="btn btn-success ms-2 px-2 d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#addUserModal" title="Добавить пользователя">
                            <i class="fas fa-user-plus fa-lg"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="usersTable" class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Логин</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td>
                                            <?php if ($user['username'] !== 'admin'): ?>
                                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $user['id']; ?>"><i class="fas fa-edit"></i></button>
                                                <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить пользователя?');"><i class="fas fa-trash"></i></a>
                                            <?php else: ?>
                                                <span class="text-muted">admin</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                          <form method="post">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="editUserModalLabel<?php echo $user['id']; ?>">Редактировать пользователя</h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                              <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                              <div class="mb-3">
                                                <label>Логин</label>
                                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                              </div>
                                              <div class="mb-3">
                                                <label>Новый пароль (не менять — оставить пустым)</label>
                                                <input type="password" name="password" class="form-control">
                                              </div>
                                            </div>
                                            <div class="modal-footer">
                                              <button type="submit" name="edit" class="btn btn-warning">Сохранить</button>
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <footer class="main-footer text-center">
        <strong>AdminLTE PHP &copy; 2025</strong>
    </footer>
</div>
<!-- Add Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Добавить пользователя</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Логин</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Пароль</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="add" class="btn btn-success">Добавить</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var table = $('#usersTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/ru.json'
            },
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            info: true,
            initComplete: function() {
                var searchBox = $('#usersTable_filter');
                // Перемещаем поисковую строку DataTables в наш контейнер
                $('#datatable-search-container').append(searchBox.find('label').contents());
                searchBox.remove(); // Удаляем оригинальный div поиска DataTables
            }
        });
    });
</script>
</body>
</html>
