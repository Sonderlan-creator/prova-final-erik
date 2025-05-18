<?php
include 'db.php';

if ($conn) {
    echo "✅ Conexão bem-sucedida com o banco de dados!";
} else {
    echo "❌ Falha na conexão: " . mysqli_connect_error();
}
?>