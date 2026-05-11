<?php

require_once '../../config/database.php';

// Seguridad: Solo admin (rol 1) y no puede eliminarse a sí mismo
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: index.php");
    exit();
}

$id_user = $_GET['id'];

if ($id_user == $_SESSION['id_usuario']) {
    header("Location: index.php?msg=error_mismo_usuario");
    exit();
}

$stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_user);

if ($stmt->execute()) {
    header("Location: index.php?msg=usuario_eliminado");
} else {
    header("Location: index.php?msg=error_delete");
}