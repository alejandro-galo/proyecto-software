<?php
// modules/partidos/index.php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

require_once '../../includes/header.php';

// Consulta avanzada con múltiples JOINs para obtener nombres de torneos y equipos
$query = "SELECT p.*, t.nombre AS torneo_nombre, 
                 el.nombre AS local_nombre, el.logo_url AS local_logo,
                 ev.nombre AS visitante_nombre, ev.logo_url AS visitante_logo
          FROM partidos p
          INNER JOIN torneos t ON p.id_torneo = t.id_torneo
          INNER JOIN equipos el ON p.id_equipo_local = el.id_equipo
          INNER JOIN equipos ev ON p.id_equipo_visitante = ev.id_equipo
          ORDER BY p.fecha_hora DESC";
$resultado = $conexion->query($query);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-calendar-alt text-primary"></i> Calendario de Partidos</h2>
        <a href="nuevo_partido.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Programar Partido
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha y Hora</th>
                            <th>Torneo (Jornada)</th>
                            <th colspan="3">Encuentro</th>
                            <th>Cancha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($row['fecha_hora'])); ?></strong><br>
                                    <small class="text-muted"><?php echo date('h:i A', strtotime($row['fecha_hora'])); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($row['torneo_nombre']); ?></span><br>
                                    <small>Jornada <?php echo $row['jornada']; ?></small>
                                </td>
                                
                                <td class="text-end" style="width: 20%;">
                                    <?php echo htmlspecialchars($row['local_nombre']); ?>
                                    <img src="../../<?php echo htmlspecialchars($row['local_logo']); ?>" width="30" height="30" class="rounded-circle ms-2" style="object-fit: cover;">
                                </td>
                                
                                <td style="width: 10%; font-size: 1.2rem; font-weight: bold; background-color: #f8f9fa;">
                                    <?php echo $row['puntos_local']; ?> - <?php echo $row['puntos_visitante']; ?>
                                </td>
                                
                                <td class="text-start" style="width: 20%;">
                                    <img src="../../<?php echo htmlspecialchars($row['visitante_logo']); ?>" width="30" height="30" class="rounded-circle me-2" style="object-fit: cover;">
                                    <?php echo htmlspecialchars($row['visitante_nombre']); ?>
                                </td>

                                <td><?php echo htmlspecialchars($row['lugar_cancha']); ?></td>
                                <td>
                                    <?php 
                                        if($row['estado'] == 'programado') echo '<span class="badge bg-primary">Programado</span>';
                                        elseif($row['estado'] == 'en_juego') echo '<span class="badge bg-warning text-dark">En Juego</span>';
                                        elseif($row['estado'] == 'finalizado') echo '<span class="badge bg-success">Finalizado</span>';
                                        else echo '<span class="badge bg-danger">Suspendido</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="cargar_marcador.php?id=<?php echo $row['id_partido']; ?>" class="btn btn-sm btn-dark" title="Cargar Marcador"><i class="fas fa-stopwatch"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center p-4">No hay partidos programados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>