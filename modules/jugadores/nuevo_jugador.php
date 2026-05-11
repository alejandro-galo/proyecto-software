<?php
// modules/jugadores/nuevo_jugador.php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

$mensaje = "";

// Obtener la lista de equipos para el select
$equipos_res = $conexion->query("SELECT id_equipo, nombre FROM equipos ORDER BY nombre ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_equipo = $_POST['id_equipo'];
    $nombre = $_POST['nombre_completo'];
    $dni = $_POST['dni'];
    $dorsal = $_POST['dorsal'];
    $posicion = $_POST['posicion'];
    $estatura = !empty($_POST['estatura_cm']) ? $_POST['estatura_cm'] : null;

    $stmt = $conexion->prepare("INSERT INTO jugadores (id_equipo, nombre_completo, dni, dorsal, posicion, estatura_cm) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issisi", $id_equipo, $nombre, $dni, $dorsal, $posicion, $estatura);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Jugador registrado con éxito. <a href='index.php'>Ver lista</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
    }
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus"></i> Inscripción de Jugador</h4>
                </div>
                <div class="card-body">
                    <?php echo $mensaje; ?>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Equipo</label>
                                <select name="id_equipo" class="form-select" required>
                                    <option value="">Seleccione un equipo...</option>
                                    <?php while($e = $equipos_res->fetch_assoc()): ?>
                                        <option value="<?php echo $e['id_equipo']; ?>"><?php echo $e['nombre']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" name="nombre_completo" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">DNI / Documento</label>
                                <input type="text" name="dni" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Dorsal (#)</label>
                                <input type="number" name="dorsal" class="form-control" min="0" max="99" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Estatura (cm)</label>
                                <input type="number" name="estatura_cm" class="form-control" placeholder="Ej: 185">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Posición en Cancha</label>
                            <select name="posicion" class="form-select" required>
                                <option value="Base">Base (Point Guard)</option>
                                <option value="Escolta">Escolta (Shooting Guard)</option>
                                <option value="Alero">Alero (Small Forward)</option>
                                <option value="Ala-Pívot">Ala-Pívot (Power Forward)</option>
                                <option value="Pívot">Pívot (Center)</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Registrar Jugador</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>