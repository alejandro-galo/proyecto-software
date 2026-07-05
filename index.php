<?php
// index.php
require_once 'config/database.php';

// Verificación de seguridad
if (!isset($_SESSION['id_usuario'])) {
    header("Location: modules/auth/login.php");
    exit();
}

// 1. OBTENER ESTADÍSTICAS RÁPIDAS PARA LAS TARJETAS (WIDGETS)
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

// 3. CONSULTAS PARA LOS GRÁFICOS INTERACTIVOS (CHART.JS)
// Gráfico 1: Puntos por Equipo
$query_grafico_equipos = "SELECT e.nombre, SUM(ejp.puntos_anotados) as puntos 
                          FROM estadisticas_jugador_partido ejp 
                          JOIN equipos e ON ejp.id_equipo = e.id_equipo 
                          GROUP BY e.id_equipo";
$res_grafico_eq = $conexion->query($query_grafico_equipos);
$nombres_eq = [];
$puntos_eq = [];
while($r = $res_grafico_eq->fetch_assoc()){
    $nombres_eq[] = $r['nombre'];
    $puntos_eq[] = $r['puntos'];
}

// Gráfico 2: Proporción de Estados de Partidos
$query_estados = "SELECT estado, COUNT(*) as cantidad FROM partidos GROUP BY estado";
$res_estados = $conexion->query($query_estados);
$estados_labels = [];
$estados_cantidades = [];
while($r = $res_estados->fetch_assoc()){
    $estados_labels[] = strtoupper($r['estado']);
    $estados_cantidades[] = $r['cantidad'];
}

require_once 'includes/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Estilos del Hero Banner Animado */
    .welcome-hero {
        position: relative;
        background: url('https://images.unsplash.com/photo-1519861531473-920026076fb1?q=80&w=1920&auto=format&fit=crop') no-repeat center center;
        background-size: cover;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 2.5rem;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        color: white;
        opacity: 0;
        animation: fadeInHero 1s ease-out forwards;
    }
    .welcome-overlay {
        background: linear-gradient(90deg, rgba(15,32,39,0.95) 0%, rgba(32,58,67,0.85) 50%, rgba(44,83,100,0.2) 100%);
        padding: 3.5rem 3rem;
    }
    .hero-title { font-weight: 800; font-size: 2.3rem; margin-bottom: 0.5rem; }
    .hero-subtitle { font-size: 1.05rem; max-width: 650px; color: #e2e8f0; }
    
    @keyframes fadeInHero { to { opacity: 1; } }
</style>

<div class="container-fluid mt-4 mb-5">
    
    <div class="welcome-hero">
        <div class="welcome-overlay">
            <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill fw-bold text-uppercase">Temporada 2026</span>
            <h1 class="hero-title"><i class="fas fa-basketball-ball text-warning me-2"></i>Bienvenido a LigaBasket PRO</h1>
            <p class="hero-subtitle mb-0">El centro analítico y administrativo de tu torneo. Gestiona equipos, registra puntuaciones en vivo y analiza el rendimiento de la liga de forma automatizada.</p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">Dashboard Principal</h3>
            <p class="text-muted small m-0">Resumen operativo de la Liga.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-primary border-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small fw-bold">Equipos Inscritos</span>
                        <h2 class="fw-bold m-0 mt-1"><?php echo $total_equipos; ?></h2>
                    </div>
                    <i class="fas fa-users text-primary fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-success border-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small fw-bold">Jugadores Activos</span>
                        <h2 class="fw-bold m-0 mt-1"><?php echo $total_jugadores; ?></h2>
                    </div>
                    <i class="fas fa-running text-success fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-warning border-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small fw-bold">Torneos en Curso</span>
                        <h2 class="fw-bold m-0 mt-1"><?php echo $torneos_activos; ?></h2>
                    </div>
                    <i class="fas fa-trophy text-warning fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 border-start border-danger border-4 bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-uppercase text-muted small fw-bold">Partidos Pendientes</span>
                        <h2 class="fw-bold m-0 mt-1"><?php echo $partidos_pendientes; ?></h2>
                    </div>
                    <i class="fas fa-basketball-ball text-danger fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="fas fa-chart-bar text-primary me-2"></i> Rendimiento Ofensivo (Puntos Totales por Equipo)</h5>
                <div style="position: relative; height:300px;">
                    <canvas id="chartEquipos"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <h5 class="fw-bold text-dark mb-4"><i class="fas fa-pie-chart text-danger me-2"></i> Estado de los Partidos</h5>
                <div style="position: relative; height:300px; display: flex; justify-content: center;">
                    <canvas id="chartPartidos"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3 border-0">
            <h5 class="m-0 fw-bold"><i class="fas fa-calendar-alt text-warning me-2"></i> Próximos Encuentros Programados</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th>Fecha</th>
                            <th>Torneo</th>
                            <th>Local</th>
                            <th>VS</th>
                            <th>Visitante</th>
                            <th>Cancha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ultimos_partidos && $ultimos_partidos->num_rows > 0): ?>
                            <?php while ($partido = $ultimos_partidos->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold">
                                    <?php echo date('d/m/Y', strtotime($partido['fecha_hora'])); ?><br>
                                    <small class="text-muted fw-normal"><?php echo date('h:i A', strtotime($partido['fecha_hora'])); ?></small>
                                </td>
                                <td><span class="badge bg-secondary rounded-pill px-3"><?php echo htmlspecialchars($partido['torneo']); ?></span></td>
                                <td class="fw-bold text-primary"><?php echo htmlspecialchars($partido['local']); ?></td>
                                <td><span class="badge bg-dark text-white rounded-3 px-2 py-1">VS</span></td>
                                <td class="fw-bold text-danger"><?php echo htmlspecialchars($partido['visitante']); ?></td>
                                <td class="text-muted"><i class="fas fa-map-marker-alt text-secondary me-1"></i> <?php echo htmlspecialchars($partido['lugar_cancha']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-5 text-muted">No hay encuentros programados actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    // Gráfico 1: Barras de Equipos
    const ctxEq = document.getElementById('chartEquipos').getContext('2d');
    new Chart(ctxEq, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($nombres_eq); ?>,
            datasets: [{
                label: 'Puntos Anotados',
                data: <?php echo json_encode($puntos_eq); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Gráfico 2: Anillo de Partidos
    const ctxSt = document.getElementById('chartPartidos').getContext('2d');
    new Chart(ctxSt, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($estados_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($estados_cantidades); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',  // Programados
                    'rgba(255, 206, 86, 0.7)',  // En Juego
                    'rgba(75, 192, 192, 0.7)'   // Finalizados
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>