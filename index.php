<?php

require_once 'config/database.php';

// Verificación de seguridad
if (!isset($_SESSION['id_usuario'])) {
    redirigir("modules/auth/login.php");
}

// 1. OBTENER ESTADÍSTICAS RÁPIDAS PARA LAS TARJETAS (Widgets)
$total_equipos = $conexion->query("SELECT COUNT(*) as total FROM equipos")->fetch_assoc()['total'];
$total_jugadores = $conexion->query("SELECT COUNT(*) as total FROM jugadores")->fetch_assoc()['total'];
$torneos_activos = $conexion->query("SELECT COUNT(*) as total FROM torneos WHERE estado != 'finalizado'")->fetch_assoc()['total'];
$partidos_pendientes = $conexion->query("SELECT COUNT(*) as total FROM partidos WHERE estado = 'programado'")->fetch_assoc()['total'];

// 2. OBTENER LOS ÚLTIMOS 5 PARTIDOS PROGRAMADOS O EN JUEGO
$query_partidos = "SELECT p.*, el.nombre AS local, ev.nombre AS visitante, t.nombre AS torneo
                   FROM partidos p
                   JOIN equipos el ON p.id_equipo_local = el.id_equipo
                   JOIN equipos ev ON p.id_equipo_visitante = ev.id_equipo
                   JOIN torneos t ON p.id_torneo = t.id_torneo
                   ORDER BY p.fecha_hora ASC LIMIT 5";
$ultimos_partidos = $conexion->query($query_partidos);

require_once 'includes/header.php';
?>

<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Dashboard Principal</h2>
            <p class="text-muted">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? 'Administrador'); ?></strong>. Resumen de la Liga.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2" style="border-left: 5px solid #4e73df;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Equipos Inscritos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 fs-2"><?php echo $total_equipos; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-3x text-gray-300" style="color: #dddfeb;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <a href="modules/equipos/index.php" class="text-primary text-decoration-none">Ver Detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2" style="border-left: 5px solid #1cc88a;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jugadores Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 fs-2"><?php echo $total_jugadores; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-running fa-3x text-gray-300" style="color: #dddfeb;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <a href="modules/jugadores/index.php" class="text-success text-decoration-none">Ver Detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2" style="border-left: 5px solid #f6c23e;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Torneos en Curso</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 fs-2"><?php echo $torneos_activos; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-3x text-gray-300" style="color: #dddfeb;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <a href="modules/torneos/index.php" class="text-warning text-decoration-none">Ver Detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2" style="border-left: 5px solid #e74a3b;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Partidos Pendientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 fs-2"><?php echo $partidos_pendientes; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-basketball-ball fa-3x text-gray-300" style="color: #dddfeb;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center">
                    <a href="modules/partidos/index.php" class="text-danger text-decoration-none">Ver Detalles <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-dark text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-alt"></i> Próximos Encuentros Programados</h6>
                    <a href="modules/partidos/index.php" class="btn btn-sm btn-light">Ver Calendario Completo</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Torneo</th>
                                    <th class="text-end">Local</th>
                                    <th>VS</th>
                                    <th class="text-start">Visitante</th>
                                    <th>Cancha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($ultimos_partidos && $ultimos_partidos->num_rows > 0): ?>
                                    <?php while($p = $ultimos_partidos->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold"><?php echo date('d/m/Y', strtotime($p['fecha_hora'])); ?></span><br>
                                            <small class="text-muted"><?php echo date('h:i A', strtotime($p['fecha_hora'])); ?></small>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['torneo']); ?></span></td>
                                        <td class="text-end fw-bold text-primary"><?php echo htmlspecialchars($p['local']); ?></td>
                                        <td><span class="badge bg-dark">VS</span></td>
                                        <td class="text-start fw-bold text-danger"><?php echo htmlspecialchars($p['visitante']); ?></td>
                                        <td><i class="fas fa-map-marker-alt text-muted"></i> <?php echo htmlspecialchars($p['lugar_cancha']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="p-4 text-muted">No hay partidos próximos programados en este momento.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>