-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2026 a las 16:16:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `liga_basket_sistema`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id_equipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `entrenador` varchar(100) DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT 'assets/img/default-team.png',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadisticas_jugador_partido`
--

CREATE TABLE `estadisticas_jugador_partido` (
  `id_estadistica` int(11) NOT NULL,
  `id_partido` int(11) NOT NULL,
  `id_jugador` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL COMMENT 'Asegura la integridad de a qué equipo pertenecía en ese momento',
  `minutos_jugados` int(3) DEFAULT 0,
  `puntos_anotados` int(3) DEFAULT 0,
  `triples_anotados` int(2) DEFAULT 0,
  `rebotes` int(3) DEFAULT 0,
  `asistencias` int(3) DEFAULT 0,
  `faltas_cometidas` int(1) DEFAULT 0 COMMENT 'Máximo 5 o 6 según reglamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id_jugador` int(11) NOT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `dni` varchar(15) DEFAULT NULL,
  `dorsal` int(2) NOT NULL,
  `posicion` enum('Base','Escolta','Alero','Ala-Pívot','Pívot') NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estatura_cm` int(3) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partidos`
--

CREATE TABLE `partidos` (
  `id_partido` int(11) NOT NULL,
  `id_torneo` int(11) NOT NULL,
  `id_equipo_local` int(11) NOT NULL,
  `id_equipo_visitante` int(11) NOT NULL,
  `lugar_cancha` varchar(150) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `puntos_local` int(11) DEFAULT 0,
  `puntos_visitante` int(11) DEFAULT 0,
  `jornada` int(2) NOT NULL COMMENT 'Número de fecha del torneo',
  `estado` enum('programado','en_juego','finalizado','suspendido') NOT NULL DEFAULT 'programado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL COMMENT 'Ej: Administrador, Arbitro, Entrenador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Arbitro/Mesa'),
(3, 'Entrenador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneos`
--

CREATE TABLE `torneos` (
  `id_torneo` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `temporada` varchar(9) NOT NULL COMMENT 'Ej: 2026-2027',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('inscripcion','en_curso','finalizado') DEFAULT 'inscripcion'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_completo` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Contraseñas hasheadas con Bcrypt',
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_completo`, `username`, `password`, `id_rol`, `activo`, `fecha_creacion`) VALUES
(1, 'Admin Liga', 'admin', '$2y$10$TA0Omy23p3aChga8NXy/BObvjBFvxlAkd5OSeO4hWdlpfEGPvblYG', 1, 1, '2026-05-11 14:15:33');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id_equipo`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `estadisticas_jugador_partido`
--
ALTER TABLE `estadisticas_jugador_partido`
  ADD PRIMARY KEY (`id_estadistica`),
  ADD KEY `fk_estadistica_partido` (`id_partido`),
  ADD KEY `fk_estadistica_jugador` (`id_jugador`),
  ADD KEY `fk_estadistica_equipo` (`id_equipo`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id_jugador`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD KEY `fk_jugador_equipo` (`id_equipo`);

--
-- Indices de la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD PRIMARY KEY (`id_partido`),
  ADD KEY `fk_partido_torneo` (`id_torneo`),
  ADD KEY `fk_partido_local` (`id_equipo_local`),
  ADD KEY `fk_partido_visitante` (`id_equipo_visitante`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD PRIMARY KEY (`id_torneo`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_usuario_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id_equipo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estadisticas_jugador_partido`
--
ALTER TABLE `estadisticas_jugador_partido`
  MODIFY `id_estadistica` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id_jugador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `partidos`
--
ALTER TABLE `partidos`
  MODIFY `id_partido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `torneos`
--
ALTER TABLE `torneos`
  MODIFY `id_torneo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `estadisticas_jugador_partido`
--
ALTER TABLE `estadisticas_jugador_partido`
  ADD CONSTRAINT `fk_estadistica_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `fk_estadistica_jugador` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id_jugador`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_estadistica_partido` FOREIGN KEY (`id_partido`) REFERENCES `partidos` (`id_partido`) ON DELETE CASCADE;

--
-- Filtros para la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD CONSTRAINT `fk_jugador_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id_equipo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `partidos`
--
ALTER TABLE `partidos`
  ADD CONSTRAINT `fk_partido_local` FOREIGN KEY (`id_equipo_local`) REFERENCES `equipos` (`id_equipo`),
  ADD CONSTRAINT `fk_partido_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_partido_visitante` FOREIGN KEY (`id_equipo_visitante`) REFERENCES `equipos` (`id_equipo`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
