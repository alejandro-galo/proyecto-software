<?php
// modules/perfil/index.php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

require_once '../../includes/header.php';

// Obtener datos frescos del usuario desde la base de datos
$id = $_SESSION['id_usuario'];
$stmt = $conexion->prepare("SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles r ON u.id_rol = r.id_rol WHERE u.id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white text-center py-4">
                    <i class="fas fa-user-circle fa-5x mb-3"></i>
                    <h3 class="mb-0">Mi Perfil</h3>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 border-bottom pb-2">
                        <label class="text-muted small text-uppercase fw-bold">Nombre Completo</label>
                        <p class="fs-5 mb-0"><?php echo htmlspecialchars($user_data['nombre_completo']); ?></p>
                    </div>
                    <div class="mb-3 border-bottom pb-2">
                        <label class="text-muted small text-uppercase fw-bold">Nombre de Usuario</label>
                        <p class="fs-5 mb-0"><?php echo htmlspecialchars($user_data['username']); ?></p>
                    </div>
                    <div class="mb-3 border-bottom pb-2">
                        <label class="text-muted small text-uppercase fw-bold">Rol en el Sistema</label>
                        <p class="mb-0"><span class="badge bg-primary fs-6"><?php echo htmlspecialchars($user_data['nombre_rol']); ?></span></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Miembro desde</label>
                        <p class="text-muted"><?php echo date('d/m/Y', strtotime($user_data['fecha_creacion'])); ?></p>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <a href="../auth/logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>