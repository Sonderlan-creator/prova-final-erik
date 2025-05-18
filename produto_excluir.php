<?php
include 'conexao.php';

if (!isset($_GET['id'])) {
    header("Location: produto.php");
    exit;
}

$id = intval($_GET['id']);


$sql = "SELECT imagem FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();
$stmt->close();

if ($produto && file_exists($produto['imagem'])) {
    unlink($produto['imagem']);
}


$sql = "DELETE FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: produto.php");
exit;