<?php
header('Content-Type: application/json');
require 'conexao.php';


$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['items']) || !is_array($input['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$items = $input['items'];


$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['qty'];
}

$conn->begin_transaction();

try {

    $stmt = $conn->prepare("INSERT INTO orders (total) VALUES (?)");
    $stmt->bind_param("d", $total);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();


    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");

    foreach ($items as $item) {
        $stmt->bind_param("iiid", $orderId, $item['id'], $item['qty'], $item['price']);
        $stmt->execute();
    }

    $stmt->close();
    $conn->commit();

    echo json_encode(['success' => true, 'orderId' => $orderId]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar pedido']);
}
$conn->close();
