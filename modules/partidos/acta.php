<?php
// modules/partidos/acta.php
require_once '../../config/database.php';
require_once '../../includes/header.php';

// Obtener el ID del partido de la URL
$id_partido = $_GET['id'] ?? 0;

// Consulta para obtener los jugadores del partido
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

<div class="container-fluid mt-4 mb-5">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        
        <div class="card-header bg-dark text-white py-3 border-0">
            <h4 class="mb-0 fw-bold"><i class="fas fa-desktop text-warning me-2"></i> Mesa de Control en Vivo</h4>
        </div>

        <div class="bg-dark text-white p-4 border-bottom border-warning border-4">
            <div class="row align-items-center text-center">
                <?php 
                // Extraemos los nombres y IDs de los dos equipos para el marcador
                $nombres_equipos = array_keys($jugadores);
                $equipo1 = $nombres_equipos[0] ?? 'Local';
                $equipo2 = $nombres_equipos[1] ?? 'Visitante';
                
                // Necesitamos los IDs para que JavaScript sepa qué número actualizar
                $id_eq1 = $jugadores[$equipo1][0]['id_equipo'] ?? 1;
                $id_eq2 = $jugadores[$equipo2][0]['id_equipo'] ?? 2;
                ?>
                
                <div class="col-5">
                    <h3 class="text-uppercase text-light mb-2 fw-light fs-4"><?php echo htmlspecialchars($equipo1); ?></h3>
                    <div class="display-1 fw-bold text-warning" id="score-<?php echo $id_eq1; ?>">0</div>
                </div>
                
                <div class="col-2">
                    <div class="display-6 fw-bold text-muted">VS</div>
                    <div class="badge bg-danger mt-2 px-3 py-2 fs-6 rounded-pill">EN JUEGO</div>
                </div>
                
                <div class="col-5">
                    <h3 class="text-uppercase text-light mb-2 fw-light fs-4"><?php echo htmlspecialchars($equipo2); ?></h3>
                    <div class="display-1 fw-bold text-warning" id="score-<?php echo $id_eq2; ?>">0</div>
                </div>
            </div>
        </div>
        <div class="card-body bg-light">
            <div class="row mt-3">
                <?php foreach ($jugadores as $equipo => $roster): ?>
                <div class="col-md-6 mb-4">
                    <h5 class="bg-white shadow-sm p-3 rounded text-center fw-bold text-uppercase border-start border-warning border-5 mb-3">
                        <?php echo htmlspecialchars($equipo); ?>
                    </h5>
                    
                    <div class="table-responsive bg-white rounded shadow-sm">
                        <table class="table table-hover align-middle mb-0">
                            <tbody>
                                <?php foreach ($roster as $jugador): ?>
                                <tr>
                                    <td class="fw-bold text-muted ps-3" style="width: 10%;">#<?php echo $jugador['dorsal']; ?></td>
                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($jugador['nombre_completo']); ?></td>
                                    <td class="text-end pe-3">
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'tiro_libre')" class="btn btn-sm btn-outline-secondary fw-bold shadow-sm rounded-pill px-3">+1</button>
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'doble')" class="btn btn-sm btn-outline-success fw-bold shadow-sm rounded-pill px-3 mx-1">+2</button>
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'triple')" class="btn btn-sm btn-outline-primary fw-bold shadow-sm rounded-pill px-3">+3</button>
                                        <button onclick="registrarAccion(<?php echo $id_partido; ?>, <?php echo $jugador['id_jugador']; ?>, <?php echo $jugador['id_equipo']; ?>, 'falta')" class="btn btn-sm btn-danger fw-bold shadow-sm rounded-pill px-3 ms-2"><i class="fas fa-hand-paper"></i> F</button>
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
    let formData = new FormData();
    formData.append('id_partido', id_partido);
    formData.append('id_jugador', id_jugador);
    formData.append('id_equipo', id_equipo);
    formData.append('accion', accion);

    // Determinar cuántos puntos sumar visualmente
    let puntosASumar = 0;
    if (accion === 'tiro_libre') puntosASumar = 1;
    if (accion === 'doble') puntosASumar = 2;
    if (accion === 'triple') puntosASumar = 3;

    fetch('guardar_estadistica.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            
            // Actualizar el marcador gigante si fue una anotación
            if (puntosASumar > 0) {
                let scoreElement = document.getElementById('score-' + id_equipo);
                let currentScore = parseInt(scoreElement.innerText);
                scoreElement.innerText = currentScore + puntosASumar;
                
                // Efecto de parpadeo para que se note el cambio visualmente
                scoreElement.style.color = '#ffffff';
                setTimeout(() => { scoreElement.style.color = '#ffc107'; }, 300);
            }

            // Notificación flotante de confirmación (SweetAlert)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true
            });
            
            let mensaje = accion === 'falta' ? 'Falta registrada' : '¡Puntos anotados!';
            let icono = accion === 'falta' ? 'warning' : 'success';
            
            Toast.fire({
                icon: icono,
                title: mensaje
            });
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php require_once '../../includes/footer.php'; ?>
