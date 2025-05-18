<?php
session_start();
include 'conexao.php';

$produtos = [];
$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}

function isStaff($conn) {
    if (!isset($_SESSION['usuario'])) return false;
    $sql = "SELECT staff FROM usuarios WHERE nome = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return isset($user['staff']) && strtoupper($user['staff']) === 'OK';
}
$usuarioStaff = isStaff($conn);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lottus E-Commerce</title>
  <link rel="icon" href="imgs/favicon.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">
        <img src="imgs/Logo.png" alt="Logo" style="height:40px; width:auto;">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#home">Início</a></li>
          <li class="nav-item"><a class="nav-link" href="#produtos">Produtos</a></li>
          <li class="nav-item"><a class="nav-link" href="#sobre">Sobre Nós</a></li>
          <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
          <li class="nav-item dropdown">
            <?php if (isset($_SESSION['usuario'])): ?>
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($_SESSION['usuario']) ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                  <a class="dropdown-item text-danger" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Sair
                  </a>
                </li>
                <?php if ($usuarioStaff): ?>
                <li>
                  <a class="dropdown-item text-primary" href="produto.php">
                    <i class="bi bi-gear"></i> Configuração de Produtos
                  </a>
                </li>
                <?php endif; ?>
              </ul>
            <?php else: ?>
              <a class="nav-link" href="login.php">
                <i class="bi bi-box-arrow-in-right"></i>
                Login
              </a>
            <?php endif; ?>
          </li>
        </ul>
        <button class="btn btn-outline-light ms-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartCanvas">
          Carrinho
        </button>
      </div>
    </div>
  </nav>

  <section id="home">
  <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="imgs/banner01.png" class="d-block w-100 carousel-img" alt="...">
      </div>
      <div class="carousel-item">
        <img src="imgs/banner02.png" class="d-block w-100 carousel-img" alt="...">
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
  </div>
</section>

<section class="container py-5" id="produtos">
  <h2 class="text-center mb-4 text-white">Produtos</h2>
  <div class="row">
    <?php foreach ($produtos as $produto): ?>
      <div class="col-md-3 mb-4">
        <div class="card h-100 p-2 border-0 shadow-sm" style="background: #353353; border-radius: 16px;">
          <img src="<?= htmlspecialchars($produto['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($produto['nome']) ?>" style="border-radius: 12px;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-bold text-white mb-2"><?= htmlspecialchars($produto['nome']) ?></h5>
            <p class="card-text text-secondary mb-3" style="color: #bdbdc7;"><?= htmlspecialchars($produto['descricao']) ?></p>
            <h6 class="card-text fw-bold text-white mb-3">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></h6>
            <a href="#" 
               class="btn w-100 btn-add-cart" 
               style="background: #a18aff; color: #fff; font-weight: 500; border-radius: 8px;"
               data-id="<?= $produto['id'] ?>"
               data-nome="<?= htmlspecialchars($produto['nome']) ?>"
               data-preco="<?= number_format($produto['preco'], 2, '.', '') ?>"
               data-imagem="<?= htmlspecialchars($produto['imagem']) ?>"
            >
               Adicionar ao carrinho
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

  <section id="sobre" class="py-5" style="background: #353353;">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-5 text-center mb-4 mb-md-0">
          <img src="imgs/Logo.png" alt="Logo Lottus" style="max-width: 260px; width: 100%;">
        </div>
        <div class="col-md-1 d-none d-md-flex justify-content-center">
          <div style="border-left: 3px solid #fff; height: 120px; margin: 0 auto;"></div>
        </div>
        <div class="col-md-6 text-center text-md-start">
          <h2 class="fw-bold mb-3" style="color: #fff;">LOTTUS</h2>
          <p class="mb-0" style="color: #fff; font-weight: 600; font-size: 1.15rem;">
            SOMOS UMA LOJA FICTÍCIA ESPECIALIZADA EM PRODUTOS PERSONALIZADOS, GADGETS E ARTIGOS GEEKS. NOSSO OBJETIVO É OFERECER UMA EXPERIÊNCIA DE COMPRA AGRADÁVEL, MODERNA E ACESSÍVEL PARA TODOS OS PÚBLICOS.
          </p>
        </div>
      </div>
    </div>
  </section>

  <footer style="background: #2c2233; color: #fff; padding: 32px 0 18px 0; text-align: center;">
    <div style="font-weight: 600; font-size: 1rem;">
      &copy; 2025 Lottus. Todos os direitos reservados.
    </div>
    <div style="margin-top: 8px; font-size: 1rem;">
      Contato: contato@Lottus.com
    </div>
  </footer>

  <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="cartCanvas">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Carrinho de Compras</h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <ul class="list-group mb-3" id="cart-items"></ul>
      <h5>Total: R$ <span id="cart-total">0.00</span></h5>
      <button class="btn btn-primary mt-3 w-100" onclick="finalizarCompra()">Finalizar Compra</button>
    </div>
  </div>


<div id="cart-popup" class="position-fixed top-0 start-50 translate-middle-x" style="z-index: 2000; display: none; margin-top: 80px;">
  <div class="alert alert-success text-center" style="background: #a18aff; color: #fff; border-radius: 12px; border: none; font-weight: 600; box-shadow: 0 2px 16px #0003;">
    Produto adicionado ao carrinho!
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
  <script>

document.querySelectorAll('.btn-add-cart').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();

    const popup = document.getElementById('cart-popup');
    popup.style.display = 'block';
    setTimeout(() => {
      popup.style.display = 'none';
    }, 1500);
  });
});
</script>
</body>
</html>