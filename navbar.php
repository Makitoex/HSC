<?php
include 'conexion.php'; // Conexión a la base de datos
include 'navbar.php'; // Integración del navbar

// Consulta a la base de datos
$query = "SELECT * FROM formulario_turnos_uti_enfermeros";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Formularios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* Contenedor más ancho */
        .container {
            width: 95%;
            max-width: 1200px; /* Ajustado para mejor distribución */
            margin: 20px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        /* Tabla responsive */
        .table-responsive {
            overflow-x: auto; /* Evita desbordes */
        }

        /* Estilización de la tabla */
        .table {
            border-collapse: collapse;
            width: 100%;
            text-align: center;
        }

        /* Filas de la tabla */
        .table thead {
            background-color: #e74c3c;
            color: white;
        }

        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background-color: rgba(231, 76, 60, 0.2);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4">Listado de Formularios</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Servicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo $row['servicio']; ?></td>
                        <td>
                            <a href="ver_formulario.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="descargar_pdf.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
