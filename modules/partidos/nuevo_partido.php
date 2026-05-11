<?php
// modules/partidos/nuevo_partido.php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

$mensaje = "";

// Cargar Torneos Activos
$torneos_res = $conexion->query("SELECT id_torneo, nombre FROM torneos WHERE estado != 'finalizado' ORDER BY nombre ASC");
// Cargar Equipos
$equipos_res_local = $conexion->query("SELECT id_equipo, nombre FROM equipos ORDER BY nombre ASC");
// Clonamos la consulta de equipos para el visitante
$equipos_res_visitante = $conexion->query("SELECT id_equipo, nombre FROM equipos ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_torneo = $_POST['id_torneo'];
    $jornada = $_POST['jornada'];
    $id_equipo_local = $_POST['id_equipo_local'];
    $id_equipo_visitante = $_POST['id_equipo_visitante'];
    $fecha_hora = $_POST['fecha_hora'];
    $lugar_cancha = $_POST['lugar_cancha'];

    // Validación básica: El equipo local no puede ser el mismo que el visitante
    if ($id_equipo_local == $id_equipo_visitante) {
        $mensaje = "<div class='alert alert-warning'>Un equipo no puede jugar contra sí mismo. Revisa tu selección.</div>";
    } else {
        $stmt = $conexion->prepare("INSERT INTO partidos (id_torneo, id_equipo_local, id_equipo_visitante, lugar_cancha, fecha_hora, jornada) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissi", $id_torneo, $id_equipo_local, $id_equipo_visitante, $lugar_cancha, $fecha_hora, $jornada);

        if ($stmt->execute()) {
            $mensaje = "<div class='alert alert-success'>Partido programado con éxito. <a href='index.php'>Ver calendario</a></div>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Programar Nuevo Partido</h4>
                </div>
                <div class="card-body">
                    <?php echo $mensaje; ?>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Torneo / Temporada</label>
                                <select name="id_torneo" class="form-select" required>
                                    <option value="">Seleccione el torneo...</option>
                                    <?php while($t = $torneos_res->fetch_assoc()): ?>
                                        <option value="<?php echo $t['id_torneo']; ?>"><?php echo htmlspecialchars($t['nombre']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Jornada / Fecha #</label>
                                <input type="number" name="jornada" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="row bg-light p-3 border rounded mb-3">
                            <div class="col-md-5 text-center">
                                <label class="form-label fw-bold text-primary">Equipo Local</label>
                                <select name="id_equipo_local" class="form-select" required>
                                    <option value="">Seleccione local...</option>
                                    <?php while($el = $equipos_res_local->fetch_assoc()): ?>
                                        <option value="<?php echo $el['id_equipo']; ?>"><?php echo htmlspecialchars($el['nombre']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2 text-center align-self-center">
                                <span class="fw-bold fs-4">VS</span>
                            </div>
                            <div class="col-md-5 text-center">
                                <label class="form-label fw-bold text-danger">Equipo Visitante</label>
                                <select name="id_equipo_visitante" class="form-select" required>
                                    <option value="">Seleccione visitante...</option>
                                    <?php while($ev = $equipos_res_visitante->fetch_assoc()): ?>
                                        <option value="<?php echo $ev['id_equipo']; ?>"><?php echo htmlspecialchars($ev['nombre']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha y Hora</label>
                                <input type="datetime-local" name="fecha_hora" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lugar / Cancha</label>
                                <input type="text" name="lugar_cancha" class="form-control" placeholder="Ej: Coliseo Cerrado" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Programar Partido</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>