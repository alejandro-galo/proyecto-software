<?php
// 1. FORZAR VISUALIZACIÓN DE ERRORES
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_jugador = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_jugador) {
    die("Error: No se recibió el ID del jugador.");
}

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_equipo = $_POST['id_equipo'];
    $nombre = $_POST['nombre_completo'];
    $dorsal = $_POST['dorsal'];
    $posicion = $_POST['posicion'];
    $activo = $_POST['activo'];

    $stmt = $conexion->prepare("UPDATE jugadores SET id_equipo=?, nombre_completo=?, dorsal=?, posicion=?, activo=? WHERE id_jugador=?");
    $stmt->bind_param("isisii", $id_equipo, $nombre, $dorsal, $posicion, $activo, $id_jugador);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Jugador actualizado. <a href='index.php'>Volver</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
    }
}

$jugador = $conexion->query("SELECT * FROM jugadores WHERE id_jugador = " . intval($id_jugador))->fetch_assoc();
$equipos_res = $conexion->query("SELECT id_equipo, nombre FROM equipos ORDER BY nombre ASC");

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h4>Editar Jugador</h4></div>
        <div class="card-body">
            <?php echo $mensaje; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Equipo</label>
                    <select name="id_equipo" class="form-select" required>
                        <?php while($e = $equipos_res->fetch_assoc()): ?>
                            <option value="<?php echo $e['id_equipo']; ?>" <?php echo ($e['id_equipo'] == $jugador['id_equipo']) ? 'selected' : ''; ?>>
                                <?php echo $e['nombre']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($jugador['nombre_completo']); ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Dorsal</label>
                        <input type="number" name="dorsal" class="form-control" value="<?php echo $jugador['dorsal']; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Posición</label>
                        <select name="posicion" class="form-select">
                            <option value="Base" <?php echo $jugador['posicion'] == 'Base' ? 'selected' : ''; ?>>Base</option>
                            <option value="Escolta" <?php echo $jugador['posicion'] == 'Escolta' ? 'selected' : ''; ?>>Escolta</option>
                            <option value="Alero" <?php echo $jugador['posicion'] == 'Alero' ? 'selected' : ''; ?>>Alero</option>
                            <option value="Ala-Pívot" <?php echo $jugador['posicion'] == 'Ala-Pívot' ? 'selected' : ''; ?>>Ala-Pívot</option>
                            <option value="Pívot" <?php echo $jugador['posicion'] == 'Pívot' ? 'selected' : ''; ?>>Pívot</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="activo" class="form-select">
                            <option value="1" <?php echo $jugador['activo'] == 1 ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo $jugador['activo'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>