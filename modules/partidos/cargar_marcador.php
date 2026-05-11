<?php
// modules/partidos/cargar_marcador.php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

// Validar que se reciba un ID
if (!isset($_GET['id']) && !isset($_POST['id_partido'])) {
    redirigir("index.php");
}

$id_partido = $_GET['id'] ?? $_POST['id_partido'];
$mensaje = "";

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $puntos_local = $_POST['puntos_local'];
    $puntos_visitante = $_POST['puntos_visitante'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("UPDATE partidos SET puntos_local = ?, puntos_visitante = ?, estado = ? WHERE id_partido = ?");
    $stmt->bind_param("iisi", $puntos_local, $puntos_visitante, $estado, $id_partido);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Marcador actualizado correctamente. <a href='index.php'>Volver al calendario</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar: " . $conexion->error . "</div>";
    }
}

// Obtener datos actuales del partido para mostrar en el formulario
$query = "SELECT p.*, el.nombre AS local, ev.nombre AS visitante 
          FROM partidos p 
          JOIN equipos el ON p.id_equipo_local = el.id_equipo 
          JOIN equipos ev ON p.id_equipo_visitante = ev.id_equipo 
          WHERE p.id_partido = ?";
$stmt_get = $conexion->prepare($query);
$stmt_get->bind_param("i", $id_partido);
$stmt_get->execute();
$partido = $stmt_get->get_result()->fetch_assoc();

if (!$partido) {
    die("Partido no encontrado.");
}

require_once '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-stopwatch"></i> Control de Marcador</h4>
                </div>
                <div class="card-body bg-light">
                    <?php echo $mensaje; ?>
                    
                    <div class="text-center mb-4">
                        <h5 class="text-muted">Jornada <?php echo $partido['jornada']; ?></h5>
                        <p class="mb-0"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($partido['lugar_cancha']); ?></p>
                    </div>

                    <form action="cargar_marcador.php" method="POST">
                        <input type="hidden" name="id_partido" value="<?php echo $partido['id_partido']; ?>">

                        <div class="row align-items-center text-center p-3 border bg-white rounded shadow-sm mb-4">
                            <div class="col-md-5">
                                <h4 class="text-primary fw-bold"><?php echo htmlspecialchars($partido['local']); ?></h4>
                                <label>Puntos Local</label>
                                <input type="number" name="puntos_local" class="form-control text-center fs-2 fw-bold" value="<?php echo $partido['puntos_local']; ?>" min="0" required>
                            </div>
                            
                            <div class="col-md-2">
                                <h2 class="text-muted">VS</h2>
                            </div>
                            
                            <div class="col-md-5">
                                <h4 class="text-danger fw-bold"><?php echo htmlspecialchars($partido['visitante']); ?></h4>
                                <label>Puntos Visitante</label>
                                <input type="number" name="puntos_visitante" class="form-control text-center fs-2 fw-bold" value="<?php echo $partido['puntos_visitante']; ?>" min="0" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Estado del Partido</label>
                            <select name="estado" class="form-select fs-5" required>
                                <option value="programado" <?php if($partido['estado'] == 'programado') echo 'selected'; ?>>Programado</option>
                                <option value="en_juego" <?php if($partido['estado'] == 'en_juego') echo 'selected'; ?>>En Juego</option>
                                <option value="finalizado" <?php if($partido['estado'] == 'finalizado') echo 'selected'; ?>>Finalizado</option>
                                <option value="suspendido" <?php if($partido['estado'] == 'suspendido') echo 'selected'; ?>>Suspendido</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-dark btn-lg"><i class="fas fa-check-circle"></i> Guardar Resultado</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>