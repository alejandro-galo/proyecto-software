<?php

// 1. ESTA LÍNEA ES VITAL: Llama a la base de datos (y de paso inicia la sesión)
require_once '../../config/database.php';

// Si ya tiene sesión, mandarlo directo al Dashboard
if (isset($_SESSION['id_usuario'])) {
    header("Location: ../../index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validación extra por seguridad: Si por algún motivo la conexión falla antes de llegar aquí
    if (!isset($conexion)) {
        die("Error Crítico: No se pudo cargar la conexión a la base de datos desde config/database.php");
    }

    // Consulta con sentencias preparadas para evitar Inyección SQL
    $stmt = $conexion->prepare("SELECT id_usuario, nombre_completo, password, id_rol, activo FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Verificar si el usuario está activo
        if ($usuario['activo'] == 1) {
            // Verificar contraseña hasheada
            if (password_verify($password, $usuario['password'])) {
                // Crear variables de sesión
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
                $_SESSION['id_rol'] = $usuario['id_rol'];
                
                header("Location: ../../index.php");
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "Usuario desactivado. Contacte al administrador.";
        }
    } else {
        $error = "El usuario no existe en el sistema.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LigaBasket PRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .login-card { border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .bg-login-image { background: url('https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=800&auto=format&fit=crop') center center; background-size: cover; border-top-left-radius: 15px; border-bottom-left-radius: 15px; }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card border-0 login-card my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <h1 class="h4 text-gray-900 mb-2"><i class="fas fa-basketball-ball text-warning"></i> LigaBasket PRO</h1>
                                    <p class="text-muted">Ingreso al panel de gestión deportiva</p>
                                </div>
                                
                                <?php if($error != ""): ?>
                                    <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                                <?php endif; ?>

                                <form method="POST" action="login.php">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Usuario</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="username" class="form-control" placeholder="Ingrese su usuario" required>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-dark btn-lg">Ingresar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>