<?php
include '/laragon/www/Sistema_entrega_turnos_HSC/conexion.php';

session_start();
if (!isset($_SESSION['id_usuarios'])) {
    header("Location: index.php");
    exit();
}
include 'conexion.php';

// Variables para la paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener el término de búsqueda si existe
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Función para eliminar un funcionario
if (isset($_POST['eliminar'])) {
    $id_funcionario = $_POST['id_funcionario'];
    $tabla = $_POST['tipo'] === 'UTI' ? 'funcionarios_uti' : 'funcionarios_uci';

    $sql_eliminar = "DELETE FROM $tabla WHERE id_funcionarios = ?";
    $stmt = $conn->prepare($sql_eliminar);
    $stmt->bind_param("i", $id_funcionario);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Funcionario eliminado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['eliminar'])) {
    $nombre = $_POST['nombre'];
    $rut = $_POST['rut'];
    $pin = $_POST['pin'];
    $id_profesion = $_POST['id_profesion'];
    $id_servicio = $_POST['id_servicio'];

    // Determinar en qué tabla guardar
    $tabla = ($id_servicio >= 5) ? 'funcionarios_uci' : 'funcionarios_uti';

    // Insertar datos en la tabla correspondiente
    $sql = "INSERT INTO $tabla (id_profesion, nombre_funcionarios, rut_funcionarios, pin_funcionarios, id_servicio) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $id_profesion, $nombre, $rut, $pin, $id_servicio);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Funcionario registrado correctamente en $tabla</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Obtener lista de funcionarios con paginación y búsqueda
$sql_lista = "(SELECT f.id_funcionarios, f.nombre_funcionarios, f.rut_funcionarios, p.nombre_profesion, f.id_servicio, 'UTI' AS tipo
               FROM funcionarios_uti f
               INNER JOIN profesiones p ON f.id_profesion = p.id_profesion
               WHERE f.nombre_funcionarios LIKE '%$buscar%' OR f.rut_funcionarios LIKE '%$buscar%')
              UNION
              (SELECT f.id_funcionarios, f.nombre_funcionarios, f.rut_funcionarios, p.nombre_profesion, f.id_servicio, 'UCI' AS tipo
               FROM funcionarios_uci f
               INNER JOIN profesiones p ON f.id_profesion = p.id_profesion
               WHERE f.nombre_funcionarios LIKE '%$buscar%' OR f.rut_funcionarios LIKE '%$buscar%')
              LIMIT $inicio, $registros_por_pagina";

$resultado = $conn->query($sql_lista);

// Contar el total de registros para la paginación
$sql_total = "(SELECT COUNT(*) AS total FROM funcionarios_uti WHERE nombre_funcionarios LIKE '%$buscar%' OR rut_funcionarios LIKE '%$buscar%')
              UNION
              (SELECT COUNT(*) AS total FROM funcionarios_uci WHERE nombre_funcionarios LIKE '%$buscar%' OR rut_funcionarios LIKE '%$buscar%')";
$total_result = $conn->query($sql_total);
$total_registros = 0;
while ($fila = $total_result->fetch_assoc()) {
    $total_registros += $fila['total'];
}

$total_paginas = ceil($total_registros / $registros_por_pagina);
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Funcionario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-3">

    <!-- Botón de Cerrar Sesión -->
    <div class="text-end mb-3">
        <a href="/cerrarsesion.php" class="btn btn-danger btn-sm">Cerrar sesión</a>
    </div>

    <h3 class="text-center mb-3">Registro de Funcionarios</h3>

    <!-- Formulario de Registro -->
    <div class="card p-3 mb-3">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">RUT:</label>
                    <input type="text" name="rut" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">PIN:</label>
                    <input type="password" name="pin" class="form-control form-control-sm" required>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Profesión:</label>
                    <select name="id_profesion" class="form-select form-select-sm" required>
                        <option value="1">Médico</option>
                        <option value="2">Enfermero</option>
                        <option value="3">TENS</option>
                        <option value="4">Tecnólogo Médico</option>
                        <option value="5">Auxiliar</option>
                        <option value="6">Kinesiólogo</option>
                    </select>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">Servicio:</label>
                    <select name="id_servicio" class="form-select form-select-sm" required>
                        <option value="1">UTI Enfermeros</option>
                        <option value="2">UTI TENS</option>
                        <option value="3">UTI Kinesiólogos</option>
                        <option value="4">UPC Médicos</option>
                        <option value="5">UCI Enfermeros</option>
                        <option value="6">UCI Kinesiólogos</option>
                        <option value="7">UCI TENS</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-danger w-100 btn-sm">Registrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Formulario de Búsqueda -->
    <div class="card p-3 mb-3">
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label class="form-label">Buscar Funcionario:</label>
                    <input type="text" name="buscar" class="form-control form-control-sm" value="<?= htmlspecialchars($buscar) ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100 btn-sm">Buscar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Funcionarios -->
    <h4 class="text-center mb-2">Lista de Funcionarios</h4>
    <table class="table table-striped table-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>RUT</th>
                <th>Profesión</th>
                <th>Servicio</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id_funcionarios'] ?></td>
                    <td><?= $fila['nombre_funcionarios'] ?></td>
                    <td><?= $fila['rut_funcionarios'] ?></td>
                    <td><?= $fila['nombre_profesion'] ?></td>
                    <td><?= $fila['id_servicio'] ?></td>
                    <td><?= $fila['tipo'] ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="id_funcionario" value="<?= $fila['id_funcionarios'] ?>">
                            <input type="hidden" name="tipo" value="<?= $fila['tipo'] ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este registro?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Paginación -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($pagina_actual > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $pagina_actual - 1 ?>&buscar=<?= htmlspecialchars($buscar) ?>">Anterior</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="page-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $i ?>&buscar=<?= htmlspecialchars($buscar) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($pagina_actual < $total_paginas): ?>
                <li class="page-item">
                    <a class="page-link" href="?pagina=<?= $pagina_actual + 1 ?>&buscar=<?= htmlspecialchars($buscar) ?>">Siguiente</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

</body>
</html>
