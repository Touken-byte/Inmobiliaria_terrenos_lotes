<?php
try {
    echo "Probando conexion con contraseña vacia...\n";
    $pdo1 = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "¡Conexión exitosa con contraseña vacía!\n";
    
    // Verificar si la base de datos existe
    $stmt = $pdo1->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'BD_Gestion_t_l'");
    if ($stmt->fetch()) {
        echo "La base de datos BD_Gestion_t_l ya existe.\n";
    } else {
        echo "La base de datos BD_Gestion_t_l NO existe. Creándola...\n";
        $pdo1->exec("CREATE DATABASE BD_Gestion_t_l CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "¡Base de datos BD_Gestion_t_l creada con éxito!\n";
    }
} catch (Exception $e1) {
    echo "Fallo con contraseña vacía: " . $e1->getMessage() . "\n\n";
    
    try {
        echo "Probando conexion con contraseña 'Mysql123'...\n";
        $pdo2 = new PDO('mysql:host=127.0.0.1;port=3306', 'root', 'Mysql123');
        echo "¡Conexión exitosa con contraseña 'Mysql123'!\n";
        
        $stmt = $pdo2->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'BD_Gestion_t_l'");
        if ($stmt->fetch()) {
            echo "La base de datos BD_Gestion_t_l ya existe.\n";
        } else {
            echo "La base de datos BD_Gestion_t_l NO existe. Creándola...\n";
            $pdo2->exec("CREATE DATABASE BD_Gestion_t_l CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "¡Base de datos BD_Gestion_t_l creada con éxito!\n";
        }
    } catch (Exception $e2) {
        echo "Fallo con contraseña 'Mysql123': " . $e2->getMessage() . "\n";
    }
}
