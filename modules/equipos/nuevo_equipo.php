<?php
// modules/equipos/nuevo_equipo.php

// 1. Conexión y configuración
require_once '../../config/database.php';

// 2. Seguridad
if (!isset($_SESSION['id_usuario'])) {
    redirigir("../auth/login.php");
}

$mensaje = "";

// 3. Procesar el formulario cuando se envía (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $entrenador = $_POST['entrenador'];
    $logo_url = 'assets/img/default-team.png'; // Valor por defecto

    // Manejo de la subida del Logo (Imagen)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $dir_subida = '../../assets/img/equipos/';
        
        // Crear la carpeta si no existe
        if (!file_exists($dir_subida)) {
            mkdir($dir_subida, 0777, true);
        }

        $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = time() . "_" . $nombre . "." . $extension;
        $ruta_destino = $dir_subida . $nombre_archivo;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino)) {
            // Guardamos la ruta relativa para la base de datos
            $logo_url = 'assets/img/equipos/' . $nombre_archivo;
        }
    }

    // Inserción en la base de datos usando MySQLi
    $stmt = $conexion->prepare("INSERT INTO equipos (nombre, entrenador, logo_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $entrenador, $logo_url);

    if ($stmt->execute()) {
        $mensaje = "<div class='alert alert-success'>Equipo registrado exitosamente. <a href='index.php'>Volver al listado</a></div>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al registrar: " . $conexion->error . "</div>";
    }
    $stmt->close();
}

require_once '../../includes/header.php';
?>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Registrar Nuevo Equipo</h4>
                </div>
                <div class="card-body">
                    <?php echo $mensaje; ?>
                    
                    <form action="nuevo_equipo.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre del Equipo</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Los Halcones de Abancay" required>
                        </div>

                        <div class="mb-3">
                            <label for="entrenador" class="form-label fw-bold">Nombre del Entrenador</label>
                            <input type="text" name="entrenador" id="entrenador" class="form-control" placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label fw-bold">Logo del Equipo</label>
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                            <small class="text-muted">Formatos permitidos: JPG, PNG. Tamaño recomendado: 400x400px.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Equipo
                            </button>
                            <a href="index.php" class="btn btn-light border">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>