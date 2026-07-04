<?php

require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

require_once '../../includes/header.php';

// Consulta SQL avanzada para sumar estadísticas agrupadas por jugador
$query = "SELECT j.nombre_completo, e.nombre AS nombre_equipo, e.logo_url,
                 COUNT(ejp.id_partido) AS partidos_jugados,
                 SUM(ejp.puntos_anotados) AS total_puntos,
                 SUM(ejp.rebotes) AS total_rebotes,
                 SUM(ejp.asistencias) AS total_asistencias,
                 SUM(ejp.triples_anotados) AS total_triples,
                 ROUND(AVG(ejp.puntos_anotados), 1) AS promedio_puntos
          FROM estadisticas_jugador_partido ejp
          JOIN jugadores j ON ejp.id_jugador = j.id_jugador
          JOIN equipos e ON ejp.id_equipo = e.id_equipo
          GROUP BY ejp.id_jugador, j.nombre_completo, e.nombre, e.logo_url
          ORDER BY total_puntos DESC
          LIMIT 20";

$resultado = $conexion->query($query);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-chart-bar text-success"></i> Estadísticas de Jugadores</h2>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-medal text-warning"></i> Tabla de Líderes (Top Anotadores)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Rank</th>
                            <th class="text-start">Jugador</th>
                            <th class="text-start">Equipo</th>
                            <th>Partidos (PJ)</th>
                            <th>Promedio (PPG)</th>
                            <th class="text-warning">Puntos Totales</th>
                            <th>Triples (3P)</th>
                            <th>Rebotes (REB)</th>
                            <th>Asistencias (AST)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php 
                            $rank = 1;
                            while ($row = $resultado->fetch_assoc()): 
                            ?>
                            <tr>
                                <td class="fw-bold fs-5 text-muted"><?php echo $rank++; ?></td>
                                <td class="text-start fw-bold"><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                                <td class="text-start">
                                    <img src="../../<?php echo htmlspecialchars($row['logo_url']); ?>" width="25" height="25" class="rounded-circle me-1" style="object-fit: cover;">
                                    <small><?php echo htmlspecialchars($row['nombre_equipo']); ?></small>
                                </td>
                                <td><?php echo $row['partidos_jugados']; ?></td>
                                <td class="fw-bold text-primary"><?php echo $row['promedio_puntos']; ?></td>
                                <td class="fw-bold fs-5 text-success"><?php echo $row['total_puntos']; ?></td>
                                <td><?php echo $row['total_triples']; ?></td>
                                <td><?php echo $row['total_rebotes']; ?></td>
                                <td><?php echo $row['total_asistencias']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="p-5 text-muted">Aún no hay estadísticas registradas en los partidos.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>