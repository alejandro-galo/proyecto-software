<?php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../../includes/header.php';

// Consulta SQL (Mantenemos tu excelente lógica relacional)
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

// Extraemos todos los resultados a un arreglo para poder separar al MVP del resto
$ranking = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $ranking[] = $row;
    }
}

// El MVP es el primer elemento del arreglo (índice 0)
$mvp = $ranking[0] ?? null;
?>

<style>
    /* Estilos Premium para la tarjeta del MVP */
    .mvp-card {
        background: linear-gradient(135deg, #1f1c2c 0%, #928dab 100%);
        border: none;
        border-radius: 20px;
        color: white;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    .mvp-crown {
        font-size: 4rem;
        color: #f59e0b; /* Oro */
        position: absolute;
        top: -10px;
        right: 20px;
        opacity: 0.2;
    }
    .stat-box {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 15px;
        backdrop-filter: blur(5px);
        text-align: center;
    }
    .table-ranking th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="container-fluid mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-chart-bar text-warning me-2"></i> Centro de Estadísticas</h2>
            <p class="text-muted">Rendimiento en tiempo real de la temporada</p>
        </div>
    </div>

    <?php if ($mvp): ?>
    <div class="card mvp-card mb-5">
        <i class="fas fa-crown mvp-crown"></i>
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-5 text-center text-md-start mb-4 mb-md-0">
                    <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill fw-bold">JUGADOR MÁS VALIOSO (MVP)</span>
                    <h1 class="display-5 fw-bold mb-1"><?php echo htmlspecialchars($mvp['nombre_completo']); ?></h1>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mt-3">
                        <?php if(!empty($mvp['logo_url'])): ?>
                            <img src="../../<?php echo htmlspecialchars($mvp['logo_url']); ?>" width="40" height="40" class="rounded-circle me-2 border border-2 border-white" style="object-fit: cover;">
                        <?php else: ?>
                            <i class="fas fa-shield-alt fs-3 me-2"></i>
                        <?php endif; ?>
                        <h4 class="mb-0 fw-light"><?php echo htmlspecialchars($mvp['nombre_equipo']); ?></h4>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <div class="row g-3">
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-6 text-uppercase text-light mb-1">PTS Totales</div>
                                <div class="fs-3 fw-bold text-warning"><?php echo $mvp['total_puntos']; ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-6 text-uppercase text-light mb-1">Promedio</div>
                                <div class="fs-3 fw-bold"><?php echo $mvp['promedio_puntos']; ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-6 text-uppercase text-light mb-1">Rebotes</div>
                                <div class="fs-3 fw-bold"><?php echo $mvp['total_rebotes']; ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="stat-box">
                                <div class="fs-6 text-uppercase text-light mb-1">Triples</div>
                                <div class="fs-3 fw-bold text-info"><?php echo $mvp['total_triples']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-list-ol text-secondary me-2"></i> Ranking General de Anotadores</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-ranking mb-0 text-center">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="py-3">Pos</th>
                            <th class="text-start py-3">Jugador</th>
                            <th class="text-start py-3">Equipo</th>
                            <th class="py-3">PJ</th>
                            <th class="py-3">PPG</th>
                            <th class="text-dark fw-bold py-3">Total Puntos</th>
                            <th class="py-3">3P</th>
                            <th class="py-3">REB</th>
                            <th class="py-3">AST</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($ranking) > 1): ?>
                            <?php 
                            // Comenzamos el bucle desde el índice 1 (el puesto 2), ya que el índice 0 es el MVP
                            for ($i = 1; $i < count($ranking); $i++): 
                                $row = $ranking[$i];
                                $posicion = $i + 1;
                            ?>
                            <tr>
                                <td class="fw-bold fs-5">
                                    <?php if ($posicion == 2): ?>
                                        <i class="fas fa-medal text-secondary" title="Plata"></i>
                                    <?php elseif ($posicion == 3): ?>
                                        <i class="fas fa-medal" style="color: #cd7f32;" title="Bronce"></i>
                                    <?php else: ?>
                                        <span class="text-muted"><?php echo $posicion; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-start fw-bold text-dark"><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                                <td class="text-start">
                                    <?php if(!empty($row['logo_url'])): ?>
                                        <img src="../../<?php echo htmlspecialchars($row['logo_url']); ?>" width="28" height="28" class="rounded-circle me-2 shadow-sm" style="object-fit: cover;">
                                    <?php endif; ?>
                                    <small class="text-muted fw-semibold"><?php echo htmlspecialchars($row['nombre_equipo']); ?></small>
                                </td>
                                <td class="text-muted"><?php echo $row['partidos_jugados']; ?></td>
                                <td class="fw-semibold"><?php echo $row['promedio_puntos']; ?></td>
                                <td class="fw-bold fs-6 bg-light text-dark"><?php echo $row['total_puntos']; ?></td>
                                <td class="text-muted"><?php echo $row['total_triples']; ?></td>
                                <td class="text-muted"><?php echo $row['total_rebotes']; ?></td>
                                <td class="text-muted"><?php echo $row['total_asistencias']; ?></td>
                            </tr>
                            <?php endfor; ?>
                        <?php elseif (count($ranking) == 0): ?>
                            <tr>
                                <td colspan="9" class="p-5 text-muted fs-5">
                                    <i class="fas fa-basketball-ball fs-1 text-light mb-3 d-block"></i>
                                    Aún no hay estadísticas registradas en los partidos.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>