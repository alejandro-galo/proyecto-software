<?php
require_once '../../config/database.php';

// Seguridad: Solo administradores pueden eliminar torneos
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Preparar la eliminación
    $stmt = $conexion->prepare("DELETE FROM torneos WHERE id_torneo = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=eliminado");
    } else {
        header("Location: index.php?msg=error");
    }
}