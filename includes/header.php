<?php
// Carga la configuración y la base de datos.
require_once __DIR__ . '/../config/database.php';

// Asegurarnos de que la sesión esté iniciada
if (session_status() == PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LigaBasket PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/mi_sistema/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/mi_sistema/index.php">
        <i class="fas fa-basketball-ball text-warning"></i> LigaBasket PRO
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <?php if (isset($_SESSION['id_usuario'])): ?>
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/torneos/index.php"><i class="fas fa-trophy"></i> Torneos</a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/equipos/index.php"><i class="fas fa-users"></i> Equipos</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/jugadores/index.php"><i class="fas fa-running"></i> Jugadores</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/partidos/index.php"><i class="fas fa-calendar-alt"></i> Partidos</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/estadisticas/index.php"><i class="fas fa-chart-bar"></i> Estadísticas</a>
            </li>

            <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/usuarios/index.php"><i class="fas fa-user-shield"></i> Usuarios</a>
            </li>
            <?php endif; ?>
            
          </ul>
          <ul class="navbar-nav">
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                      <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario'); ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                      <li><a class="dropdown-item" href="/mi_sistema/modules/perfil/index.php">Mi Perfil</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="/mi_sistema/modules/auth/logout.php"><i class="fas fa-sign-out-alt text-danger"></i> Cerrar Sesión</a></li>
                  </ul>
              </li>
          </ul>
        <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container mt-4">