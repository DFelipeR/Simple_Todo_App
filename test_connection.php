<?php
// test_connection.php - Archivo temporal para probar la conexión

require_once 'config.php';

echo "<h1>🧪 Test de Conexión a Base de Datos</h1>";

// Probar la conexión
testConnection();

// Probar una consulta simple
try {
    $connection = getDBConnection();
    $stmt = $connection->query("SELECT COUNT(*) as total FROM tasks");
    $result = $stmt->fetch();
    echo "📋 Total de tareas en la base de datos: " . $result['total'] . "<br>";
    echo "🎉 ¡La conexión y consulta funcionan perfectamente!";
} catch(Exception $e) {
    echo "❌ Error en la consulta: " . $e->getMessage();
}
?>