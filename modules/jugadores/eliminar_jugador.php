<?php

require_once '../../config/database.php';

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conexion->prepare("DELETE FROM jugadores WHERE id_jugador = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?msg=eliminado");
    } else {
        header("Location: index.php?msg=error");
    }
}