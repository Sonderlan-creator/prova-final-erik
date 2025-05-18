<?php
session_start();
include 'conexao.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $data_nasc = $_POST['data_nasc'];
    $endereco = trim($_POST['endereco']);
    $cpf = trim($_POST['cpf']);


    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $msg = "E-mail já cadastrado!";
    } else {

        $sql = "INSERT INTO usuarios (nome, email, senha, data_nasc, endereco, cpf) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nome, $email, $senha, $data_nasc, $endereco, $cpf);
        if ($stmt->execute()) {
            $msg = "Cadastro realizado com sucesso!";
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 1800);
            </script>";
        } else {
            $msg = "Erro ao cadastrar: " . $conn->error;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro - Lottus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="text-center">
            <img src="imgs/Logo.png" alt="Logo Lottus" style="max-width: 100px; width: 100%; margin-bottom: 0.1px;">
        </div>
        <h2 class="login-title">Cadastro</h2>
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= $msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Data de Nascimento</label>
                <input type="date" name="data_nasc" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" class="form-control" required maxlength="14" placeholder="000.000.000-00">
            </div>
            <button type="submit" class="btn btn-login w-100">Cadastrar</button>
            <div class="text-center mt-3">
                Já tem conta? <a href="login.php" class="text-decoration-none" style="color:#a18aff;">Entrar</a>
            </div>
        </form>
    </div>

    <footer style="background: #2c2233; color: #fff; padding: 12px 0 18px 0; text-align: center; position: fixed; left: 0; bottom: 0; width: 100%;">
        <div style="font-weight: 600; font-size: 1rem;">
            &copy; 2025 Lottus. Todos os direitos reservados.
        </div>
        <div style="margin-top: 8px; font-size: 1rem;">
            Contato: contato@Lottus.com
        </div>
    </footer>
</body>
</html>