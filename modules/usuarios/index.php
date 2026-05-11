<?php
require_once '../../config/database.php';

// Seguridad: Solo admin (rol 1) entra aquí
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../includes/header.php';

// Consulta para listar usuarios y sus roles
$query = "SELECT u.*, r.nombre_rol FROM usuarios u 
          INNER JOIN roles r ON u.id_rol = r.id_rol 
          ORDER BY u.nombre_completo ASC";
$resultado = $conexion->query($query);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios</h2>
        <a href="nuevo_usuario.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Crear Usuario
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>Username</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo $row['nombre_rol']; ?></span></td>
                        <td>
                            <?php echo $row['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>'; ?>
                        </td>
                        <td>
                            
                        <a href="editar_usuario.php?id=<?php echo $row['id_usuario']; ?>" class="btn btn-sm btn-warning" title="Editar Usuario">
                             <i class="fas fa-edit"></i>
                        </a>

                            <?php if ($row['id_usuario'] != $_SESSION['id_usuario']): ?>
                        <a href="eliminar_usuario.php?id=<?php echo $row['id_usuario']; ?>" 
                            class="btn btn-sm btn-danger" 
                            title="Eliminar" 
                            onclick="return confirm('¿Estás seguro de eliminar a este usuario? No podrá volver a ingresar.')">
                            <i class="fas fa-trash"></i>
                        </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>