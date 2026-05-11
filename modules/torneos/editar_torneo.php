<?php
require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: index.php");
    exit();
}

$id_torneo = $_GET['id'];
$mensaje = "";

// 1. Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $temporada = $_POST['temporada'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $estado = $_POST['estado'];

    $stmt = $conexion->prepare("UPDATE torneos SET nombre=?, temporada=?, fecha_inicio=?, fecha_fin=?, estado=? WHERE id_torneo=?");
    $stmt->bind_param("sssssi", $nombre, $temporada, $fecha_inicio, $fecha_fin, $estado, $id_torneo);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Torneo actualizado correctamente. <a href='index.php'>Volver</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar.</div>";
    }
}

// 2. Obtener datos actuales
$res = $conexion->query("SELECT * FROM torneos WHERE id_torneo = $id_torneo");
$torneo = $res->fetch_assoc();

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h4>Editar Torneo</h4></div>
        <div class="card-body">
            <?php echo $mensaje; ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $torneo['nombre']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Temporada</label>
                        <input type="text" name="temporada" class="form-control" value="<?php echo $torneo['temporada']; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $torneo['fecha_inicio']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fin</label>
                        <input type="date" name="fecha_fin" class="form-control" value="<?php echo $torneo['fecha_fin']; ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="inscripcion" <?php echo $torneo['estado'] == 'inscripcion' ? 'selected' : ''; ?>>Inscripción</option>
                        <option value="en_curso" <?php echo $torneo['estado'] == 'en_curso' ? 'selected' : ''; ?>>En Curso</option>
                        <option value="finalizado" <?php echo $torneo['estado'] == 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>