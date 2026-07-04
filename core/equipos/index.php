<?php

// 1. Requerimos la base de datos PRIMERO (esto ya inicia la sesión gracias a tu database.php)
require_once '../../config/database.php';

// 2. Validación de seguridad
//if (!isset($_SESSION['id_usuario'])) {
//    redirigir("../auth/login.php"); // Usamos tu función personalizada redirigir()
//}

// 3. Incluir el header (menú de navegación)
require_once '../../includes/header.php';

// 4. Consulta adaptada a MYSQLI
$query = "SELECT * FROM equipos ORDER BY nombre ASC";
$resultado = $conexion->query($query);

// Verificamos si hubo un error en la consulta
if (!$resultado) {
    die("Error en la consulta: " . $conexion->error);
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestión de Equipos</h2>
        <a href="nuevo_equipo.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Registrar Nuevo Equipo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Nombre del Equipo</th>
                            <th>Entrenador</th>
                            <th>Fecha de Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($equipo = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $equipo['id_equipo']; ?></td>
                                <td>
                                    <img src="../../<?php echo htmlspecialchars($equipo['logo_url']); ?>" alt="Logo" width="40" height="40" class="rounded-circle" style="object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($equipo['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($equipo['entrenador'] ?? 'No asignado'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($equipo['fecha_registro'])); ?></td>
                                <td>
                                    <a href="editar_equipo.php?id=<?php echo $equipo['id_equipo']; ?>" class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay equipos registrados en la liga.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
require_once '../../includes/footer.php';
?>