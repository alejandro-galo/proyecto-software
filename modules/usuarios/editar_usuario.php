<?php

require_once '../../config/database.php';

// Seguridad: Solo admin (rol 1) entra aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../index.php");
    exit();
}

$id_user = $_GET['id'] ?? null;
if (!$id_user) header("Location: index.php");

$mensaje = "";

// 1. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre_completo'];
    $username = $_POST['username'];
    $id_rol = $_POST['id_rol'];
    $activo = $_POST['activo'];

    // Si se escribió una nueva contraseña, la hasheamos
    if (!empty($_POST['nueva_password'])) {
        $pass_hash = password_hash($_POST['nueva_password'], PASSWORD_BCRYPT);
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre_completo=?, username=?, password=?, id_rol=?, activo=? WHERE id_usuario=?");
        $stmt->bind_param("sssiii", $nombre, $username, $pass_hash, $id_rol, $activo, $id_user);
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET nombre_completo=?, username=?, id_rol=?, activo=? WHERE id_usuario=?");
        $stmt->bind_param("ssiii", $nombre, $username, $id_rol, $activo, $id_user);
    }

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Usuario actualizado. <a href='index.php'>Volver al listado</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
    }
}

// 2. Obtener datos actuales
$res_user = $conexion->query("SELECT * FROM usuarios WHERE id_usuario = $id_user");
$u = $res_user->fetch_assoc();
$roles_res = $conexion->query("SELECT * FROM roles ORDER BY id_rol ASC");

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark"><h4><i class="fas fa-user-edit"></i> Editar Usuario</h4></div>
                <div class="card-body">
                    <?php echo $mensaje; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nombre Completo</label>
                            <input type="text" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($u['nombre_completo']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($u['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nueva Contraseña (Dejar vacío para no cambiar)</label>
                            <input type="password" name="nueva_password" class="form-control" placeholder="********">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Rol</label>
                                <select name="id_rol" class="form-select">
                                    <?php while($rol = $roles_res->fetch_assoc()): ?>
                                        <option value="<?php echo $rol['id_rol']; ?>" <?php echo ($rol['id_rol'] == $u['id_rol']) ? 'selected' : ''; ?>>
                                            <?php echo $rol['nombre_rol']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <select name="activo" class="form-select">
                                    <option value="1" <?php echo $u['activo'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo $u['activo'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning fw-bold">Actualizar Usuario</button>
                            <a href="index.php" class="btn btn-light border">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>