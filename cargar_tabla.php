<?php
include 'conexion.php';

$table = $_GET['table'] ?? '';

if ($table) {
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);

    $columns = [];
    $rows = [];

    if ($result->num_rows > 0) {
        // Obtiene nombres de columnas y filtra la columna de contraseña lo que tenga contraseña se vuelve false y no muestra
        $all_columns = array_keys($result->fetch_assoc());
        $columns = array_filter($all_columns, function($col) {
            return stripos($col, 'contrasena') === false;
        });

        // Restablecer puntero de resultados y obtener filas sin la columna de contraseña
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $filtered_row = [];
            foreach ($columns as $column) {
                $filtered_row[] = $row[$column];
            }
            $rows[] = $filtered_row;
        }
    }

    echo json_encode(['columns' => array_values($columns), 'rows' => $rows]);
} else {
    echo json_encode(['columns' => [], 'rows' => []]);
}
?>