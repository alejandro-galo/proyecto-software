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
    
    <style>
        .navbar-custom {
            background: linear-gradient(90deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            padding: 12px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .navbar-custom .navbar-brand {
            font-size: 1.4rem;
            letter-spacing: 0.5px;
        }

        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.85);
            font-weight: 500;
            padding: 8px 16px !important;
            margin: 0 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        /* Efecto al pasar el mouse por los enlaces del menú */
        .navbar-custom .nav-link:hover {
            color: #f59e0b !important; /* Naranja básquetbol */
            background-color: rgba(255, 255, 255, 0.05);
            transform: translateY(-2px);
        }

        /* Estilo moderno para el menú desplegable del perfil */
        .dropdown-menu-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 10px 0;
        }

        .dropdown-menu-custom .dropdown-item {
            font-weight: 500;
            padding: 10px 20px;
            transition: background-color 0.2s ease;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #f59e0b;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/mi_sistema/index.php">
        <i class="fas fa-basketball-ball text-warning me-1"></i> LigaBasket PRO
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
        <?php if (isset($_SESSION['id_usuario'])): ?>
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/index.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/torneos/index.php"><i class="fas fa-trophy me-1"></i> Torneos</a>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/equipos/index.php"><i class="fas fa-users me-1"></i> Equipos</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/jugadores/index.php"><i class="fas fa-running me-1"></i> Jugadores</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/partidos/index.php"><i class="fas fa-calendar-alt me-1"></i> Partidos</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/estadisticas/index.php"><i class="fas fa-chart-bar me-1"></i> Estadísticas</a>
            </li>

            <?php if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 1): ?>
            <li class="nav-item">
              <a class="nav-link" href="/mi_sistema/modules/usuarios/index.php"><i class="fas fa-user-shield me-1"></i> Usuarios</a>
            </li>
            <?php endif; ?>
            
          </ul>
          
          <ul class="navbar-nav">
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                      <i class="fas fa-user-circle fs-5 align-middle me-1"></i> <?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Usuario'); ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                      <li><a class="dropdown-item" href="/mi_sistema/modules/perfil/index.php"><i class="fas fa-id-card me-2 text-secondary"></i> Mi Perfil</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="/mi_sistema/modules/auth/logout.php"><i class="fas fa-sign-out-alt text-danger me-2"></i> Cerrar Sesión</a></li>
                  </ul>
              </li>
          </ul>
        <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container mt-4">