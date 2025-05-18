<?php
session_start();
include 'conexao.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && $senha === $user['senha']) {
        $_SESSION['usuario'] = $user['nome'];
        header("Location: index.php");
        exit;
    } else {
        $msg = "Usuário ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Lottus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link href="login.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="text-center">
            <img src="imgs/Logo.png" alt="Logo Lottus" style="max-width: 120px; width: 100%; margin-bottom: 18px;">
        </div>
        <h2 class="login-title">Login</h2>
        <?php if ($msg): ?>
            <div class="alert alert-danger"><?= $msg ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="text" name="usuario" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login w-100">Entrar</button>
            <div class="text-center mt-3">
                Ainda não está cadastrado? <a href="cadastro.php" class="text-decoration-none" style="color:#a18aff;">Cadastre-se</a>
            </div>
        </form>
    </div>

    <footer style="background: #2c2233; color: #fff; padding: 32px 0 18px 0; text-align: center; position: fixed; left: 0; bottom: 0; width: 100%;">
        <div style="font-weight: 600; font-size: 1rem;">
            &copy; 2025 Lottus. Todos os direitos reservados.
        </div>
        <div style="margin-top: 8px; font-size: 1rem;">
            Contato: contato@Lottus.com
        </div>
    </footer>
</body>
</html>