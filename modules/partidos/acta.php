<?php
// modules/partidos/acta.php
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Obtener el ID del partido de la URL
$id_partido = $_GET['id'] ?? 0;

// Consulta para obtener los jugadores del partido (puedes ajustar esto según tu estructura exacta de id_equipo_local y visitante)
$query_jugadores = "
    SELECT ejp.id_jugador, j.nombre_completo, j.dorsal, e.id_equipo, e.nombre AS nombre_equipo 
    FROM estadisticas_jugador_partido ejp
    JOIN jugadores j ON ejp.id_jugador = j.id_jugador
    JOIN equipos e ON ejp.id_equipo = e.id_equipo
    WHERE ejp.id_partido = ?
    ORDER BY e.nombre, j.dorsal
";
$stmt = $conexion->prepare($query_jugadores);
$stmt->bind_param("i", $id_partido);
$stmt->execute();
$resultado = $stmt->get_result();

$jugadores = [];
while ($row = $resultado->fetch_assoc()) {
    $jugadores[$row['nombre_equipo']][] = $row;
}
?>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-dark text-white py-3">
            <h4 class="mb-0 fw-bold"><i class="fas fa-desktop text-warning me-2"></i> Mesa de Control en Vivo</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($jugadores as $equipo => $roster): ?>
                <div class="col-md-6 mb-4">
                    <h5 class="bg-light p-3 rounded text-center fw-bold text-uppercase border-bottom border-warning border-3">
                        <?php echo htmlspecialchars($equipo); ?>
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <tbody>
                                <?php foreach ($roster as $jugador): ?>
                                <tr>
                                    <td class="fw-bold text-muted" style="width: 5%;">#<?php echo $jugador['dorsal']; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($jugador['nombre_completo']); ?></td>
                                    <td class="text-end">
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'tiro_libre')" class="btn btn-sm btn-outline-secondary fw-bold">+1</button>
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'doble')" class="btn btn-sm btn-outline-success fw-bold">+2</button>
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'triple')" class="btn btn-sm btn-outline-primary fw-bold">+3</button>
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'falta')" class="btn btn-sm btn-danger fw-bold"><i class="fas fa-hand-paper"></i> F</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
function registrarAccion(id_partido, id_jugador, id_equipo, accion) {
    // Usamos formData para emular un envío de formulario
    let formData = new FormData();
    formData.append('id_partido', id_partido);
    formData.append('id_jugador', id_jugador);
    formData.append('id_equipo', id_equipo);
    formData.append('accion', accion);

    fetch('guardar_estadistica.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            // Un pequeño efecto visual (Opcional, usando toast de SweetAlert)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'Registrado'
            });
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php require_once '../../includes/footer.php'; ?>