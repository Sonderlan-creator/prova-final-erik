<?php
include 'conexao.php';

$msg = "";


if (!isset($_GET['id'])) {
    header("Location: produto.php");
    exit;
}
$id = intval($_GET['id']);
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();
$stmt->close();

if (!$produto) {
    $msg = "Produto não encontrado!";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = $_POST["preco"];
    $imagem = $produto['imagem'];


    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $pasta = "imgs/";
        $nomeImagem = uniqid() . "_" . basename($_FILES["imagem"]["name"]);
        $caminhoImagem = $pasta . $nomeImagem;
        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoImagem)) {
            $imagem = $caminhoImagem;
        } else {
            $msg = "Erro ao fazer upload da imagem.";
        }
    }

    $sql = "UPDATE produtos SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);

    if ($stmt->execute()) {
        $msg = "Produto atualizado com sucesso!";

        $produto['nome'] = $nome;
        $produto['descricao'] = $descricao;
        $produto['preco'] = $preco;
        $produto['imagem'] = $imagem;
    } else {
        $msg = "Erro ao atualizar produto: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="produto_editar.css" rel="stylesheet">
</head>
<body class="container py-5">
    <h2>Editar Produto</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <?php if ($produto): ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($produto['nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control" required><?= htmlspecialchars($produto['descricao']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Preço</label>
            <input type="number" step="0.01" name="preco" class="form-control" value="<?= htmlspecialchars($produto['preco']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Imagem Atual</label><br>
            <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="Imagem atual" style="width:100px; border-radius:8px;">
        </div>
        <div class="mb-3">
            <label>Nova Imagem (opcional)</label>
            <input type="file" name="imagem" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="produto.php" class="btn btn-secondary">Voltar</a>
    </form>
    <?php endif; ?>
</body>
</html>