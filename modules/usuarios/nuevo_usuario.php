<?php

require_once '../../config/database.php';

// Seguridad: Solo el Administrador (rol 1) puede crear usuarios
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../index.php");
    exit();
}

$mensaje = "";

// Obtener los roles disponibles desde la base de datos
$roles_res = $conexion->query("SELECT * FROM roles ORDER BY id_rol ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_completo = $_POST['nombre_completo'];
    $username = $_POST['username'];
    $password_plana = $_POST['password'];
    $id_rol = $_POST['id_rol'];

    // Encriptar la contraseña por seguridad (Bcrypt)
    $password_hash = password_hash($password_plana, PASSWORD_BCRYPT);

    // Verificar que el 'username' no exista ya en el sistema
    $check = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $mensaje = "<div class='alert alert-warning'>El nombre de usuario '<b>$username</b>' ya está en uso. Por favor, elige otro.</div>";
    } else {
        // Insertar el nuevo usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre_completo, username, password, id_rol) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre_completo, $username, $password_hash, $id_rol);

        if ($stmt->execute()) {
            $mensaje = "<div class='alert alert-success'>Usuario registrado exitosamente. <a href='index.php'>Ver lista de usuarios</a></div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al registrar: " . $conexion->error . "</div>";
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus text-warning"></i> Registrar Nuevo Usuario</h4>
                </div>
                <div class="card-body bg-light">
                    <?php echo $mensaje; ?>
                    
                    <form action="nuevo_usuario.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre_completo" class="form-control" placeholder="Ej: Juan Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre de Usuario (Username)</label>
                            <input type="text" name="username" class="form-control" placeholder="Ej: jperez" required>
                            <small class="text-muted">Este nombre se usará para iniciar sesión.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Crea una contraseña segura" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Rol en el Sistema</label>
                            <select name="id_rol" class="form-select" required>
                                <option value="">Seleccione un rol...</option>
                                <?php while($rol = $roles_res->fetch_assoc()): ?>
                                    <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Usuario</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>