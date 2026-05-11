<?php
// modules/jugadores/index.php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

require_once '../../includes/header.php';

// Consulta con JOIN para obtener el nombre del equipo
$query = "SELECT j.*, e.nombre AS nombre_equipo 
          FROM jugadores j 
          LEFT JOIN equipos e ON j.id_equipo = e.id_equipo 
          ORDER BY j.nombre_completo ASC";
$resultado = $conexion->query($query);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-running"></i> Gestión de Jugadores</h2>
        <a href="nuevo_jugador.php" class="btn btn-success">
            <i class="fas fa-user-plus"></i> Registrar Nuevo Jugador
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Dorsal</th>
                            <th>Nombre Completo</th>
                            <th>Equipo</th>
                            <th>Posición</th>
                            <th>DNI</th>
                            <th>Estatura</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold text-center"><?php echo $row['dorsal']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <?php echo htmlspecialchars($row['nombre_equipo'] ?? 'Sin Equipo'); ?>
                                    </span>
                                </td>
                                <td><?php echo $row['posicion']; ?></td>
                                <td><?php echo $row['dni']; ?></td>
                                <td><?php echo $row['estatura_cm'] ? $row['estatura_cm'] . ' cm' : '-'; ?></td>
                                <td>
                                    <?php echo $row['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>
                                </td>
                                <td>
                                    <a href="editar_jugador.php?id=<?php echo $row['id_jugador']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay jugadores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>