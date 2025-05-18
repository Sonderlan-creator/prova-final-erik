<?php
include 'conexao.php';

$msg = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $preco = $_POST["preco"];


    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $pasta = "imgs/";
        $nomeImagem = uniqid() . "_" . basename($_FILES["imagem"]["name"]);
        $caminhoImagem = $pasta . $nomeImagem;

        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $caminhoImagem)) {
            $imagem = $caminhoImagem;
        } else {
            $msg = "Erro ao fazer upload da imagem.";
            $imagem = "";
        }
    } else {
        $msg = "Selecione uma imagem válida.";
        $imagem = "";
    }

    if ($imagem) {
        $sql = "INSERT INTO produtos (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssds", $nome, $descricao, $preco, $imagem);

        if ($stmt->execute()) {
            $msg = "Produto cadastrado com sucesso!";
        } else {
            $msg = "Erro ao cadastrar produto: " . $conn->error;
        }
        $stmt->close();
    }
}


if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM produtos WHERE id = $id");
    header("Location: produto.php");
    exit;
}


$produtos = [];
$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="produto.css" rel="stylesheet">
</head>
<body class="container py-5">

    <h2>Cadastrar Produto</h2>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Nome</label>
            <input type="text" name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Descrição</label>
            <textarea name="descricao" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label>Preço</label>
            <input type="number" step="0.01" name="preco" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Adicionar Imagem</label>
            <input type="file" name="imagem" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-success">Cadastrar</button>
    </form>
    <a href="index.php" class="btn btn-link mt-3">Voltar para a loja</a>

    <hr class="my-5">

    <h3>Produtos cadastrados</h3>
    <div class="table-responsive">
        <table class="table table-dark table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= $produto['id'] ?></td>
                    <td>
                        <img src="<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                    </td>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= htmlspecialchars($produto['descricao']) ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td>
                        <a href="produto_editar.php?id=<?= $produto['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="produto_excluir.php?id=<?= $produto['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>