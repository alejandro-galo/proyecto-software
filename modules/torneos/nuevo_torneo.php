<?php

require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $temporada = $_POST['temporada'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("INSERT INTO torneos (nombre, temporada, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $temporada, $fecha_inicio, $fecha_fin, $estado);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Torneo creado con éxito. <a href='index.php'>Ver torneos</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al crear el torneo: " . $conexion->error . "</div>";
    }
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h4 class="mb-0"><i class="fas fa-trophy text-warning"></i> Apertura de Torneo</h4>
                </div>
                <div class="card-body">
                    <?php echo $mensaje; ?>
                    <form action="" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nombre del Torneo</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Liga de Verano Abancay" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Temporada</label>
                                <input type="text" name="temporada" class="form-control" placeholder="Ej: 2026-2027" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha de Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Fecha de Fin (Opcional)</label>
                                <input type="date" name="fecha_fin" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Estado Inicial</label>
                            <select name="estado" class="form-select" required>
                                <option value="inscripcion">Inscripciones Abiertas</option>
                                <option value="en_curso">En Curso (Jugando)</option>
                                <option value="finalizado">Finalizado</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning fw-bold"><i class="fas fa-save"></i> Guardar Torneo</button>
                            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>