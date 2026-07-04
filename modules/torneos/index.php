<?php

require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

require_once '../../includes/header.php';

$query = "SELECT * FROM torneos ORDER BY fecha_inicio DESC";
$resultado = $conexion->query($query);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-trophy text-warning"></i> Gestión de Torneos</h2>
        <a href="nuevo_torneo.php" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Crear Nuevo Torneo
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre del Torneo</th>
                            <th>Temporada</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['temporada']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['fecha_inicio'])); ?></td>
                                <td><?php echo $row['fecha_fin'] ? date('d/m/Y', strtotime($row['fecha_fin'])) : 'Por definir'; ?></td>
                                <td>
                                    <?php 
                                        if($row['estado'] == 'inscripcion') echo '<span class="badge bg-primary">Inscripciones Abiertas</span>';
                                        elseif($row['estado'] == 'en_curso') echo '<span class="badge bg-success">En Curso</span>';
                                        else echo '<span class="badge bg-secondary">Finalizado</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="editar_torneo.php?id=<?php echo $row['id_torneo']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    <a href="eliminar_torneo.php?id=<?php echo $row['id_torneo']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('¿Estás seguro de eliminar este torneo? Se borrarán todos los partidos asociados.')">
                                       <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay torneos registrados en el sistema.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>