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
        /* Fondo elegante con gradiente oscuro */
        body { 
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Tarjeta principal con sombra pronunciada para efecto flotante */
        .login-card { 
            border-radius: 20px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.5); 
            overflow: hidden;
        }
        
        /* Imagen lateral adaptada al borde */
        .bg-login-image { 
            background: url('https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=800&auto=format&fit=crop') center center; 
            background-size: cover; 
        }
        
        /* Estilización moderna de los inputs (cajas de texto) */
        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }
        .form-control {
            border-left: none;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        /* Efecto al hacer clic en los inputs (brillo naranja sutil) */
        .input-group:focus-within {
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25);
            border-radius: 0.375rem;
        }
        .input-group:focus-within .input-group-text, 
        .input-group:focus-within .form-control {
            border-color: #f59e0b;
        }

        /* Botón interactivo */
        .btn-login {
            background-color: #1a1a1a;
            color: white;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        /* Efecto Hover del botón: Cambia a naranja, se eleva un poco y proyecta sombra */
        .btn-login:hover {
            background-color: #f59e0b; 
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card border-0 login-card my-5">
                <div class="card-body p-0">
                    <div class="row g-0"> 
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                        <div class="col-lg-6 bg-white">
                            <div class="p-5">
                                <div class="text-center mb-4">
                                    <h1 class="h3 fw-bold text-gray-900 mb-2">
                                        <i class="fas fa-basketball-ball text-warning me-2"></i>LigaBasket PRO
                                    </h1>
                                    <p class="text-muted">Ingreso al panel de gestión deportiva</p>
                                </div>
                                
                                <?php if($error != ""): ?>
                                    <div class="alert alert-danger text-center rounded-3 shadow-sm"><?php echo $error; ?></div>
                                <?php endif; ?>

                                <form method="POST" action="login.php">
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-secondary">Usuario</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                            <input type="text" name="username" class="form-control" placeholder="Ingrese su usuario" required>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <label class="form-label fw-semibold text-secondary">Contraseña</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                                            <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
                                        </div>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-login btn-lg">Ingresar</button>
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