<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Capturar el ID desde la URL
$id_equipo = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_equipo) {
    die("Error: No se recibió el ID del equipo.");
}

$mensaje = "";

// PROCESAR ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $entrenador = $_POST['entrenador'];
    $logo_url = $_POST['logo_actual'];

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $dir_subida = '../../assets/img/equipos/';
        $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "", $nombre) . "." . $extension;
        $ruta_destino = $dir_subida . $nombre_archivo;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino)) {
            $logo_url = 'assets/img/equipos/' . $nombre_archivo;
        }
    }

    $stmt = $conexion->prepare("UPDATE equipos SET nombre=?, entrenador=?, logo_url=? WHERE id_equipo=?");
    $stmt->bind_param("sssi", $nombre, $entrenador, $logo_url, $id_equipo);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Equipo actualizado correctamente. <a href='index.php'>Volver</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error: " . $conexion->error . "</div>";
    }
}

// OBTENER DATOS ACTUALES
$query = "SELECT * FROM equipos WHERE id_equipo = " . intval($id_equipo);
$res = $conexion->query($query);
$equipo = $res->fetch_assoc();

if (!$equipo) {
    die("Error: El equipo con ID $id_equipo no existe en la base de datos.");
}

require_once '../../includes/header.php';
?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h4>Editar Equipo</h4></div>
        <div class="card-body">
            <?php echo $mensaje; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="logo_actual" value="<?php echo $equipo['logo_url']; ?>">
                <div class="mb-3">
                    <label class="form-label">Nombre del Equipo</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($equipo['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Entrenador</label>
                    <input type="text" name="entrenador" class="form-control" value="<?php echo htmlspecialchars($equipo['entrenador']); ?>">
                </div>
                <div class="mb-3">
                    <p>Logo actual:</p>
                    <img src="../../<?php echo $equipo['logo_url']; ?>" width="80" class="img-thumbnail">
                </div>
                <div class="mb-3">
                    <label class="form-label">Cambiar Logo</label>
                    <input type="file" name="logo" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>