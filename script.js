async function finalizarCompra() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  if (!cart.length) return alert('Carrinho vazio.');

  const resp = await fetch('http://localhost/Lottus/checkout.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ items: cart })
  });
  const data = await resp.json();
  if (data.success) {
    alert('Pedido #' + data.orderId + ' finalizado!');
    localStorage.removeItem('cart');
    atualizarCarrinhoUI();
  } else {
    alert('Erro: ' + (data.error || 'desconhecido'));
  }
}

function getCart() {
  return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
}

function atualizarCarrinhoUI() {
  const cart = getCart();
  const list = document.getElementById('cart-items');
  const totalEl = document.getElementById('cart-total');

  list.innerHTML = '';
  let total = 0;

  cart.forEach(item => {
    total += item.preco * item.qtd;
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center bg-dark text-white';
    li.innerHTML = `
      <div class="d-flex align-items-center">
        <img src="${item.imagem}" alt="${item.nome}" style="width:40px;height:40px;object-fit:cover;border-radius:5px;margin-right:10px;">
        <span>${item.nome}</span>
        <div class="input-group ms-3" style="width: 110px;">
          <button class="btn btn-outline-light btn-sm btn-qty" data-id="${item.id}" data-action="minus" type="button">-</button>
          <input type="text" class="form-control text-center bg-dark text-white border-0" value="${item.qtd}" style="width: 35px;" readonly>
          <button class="btn btn-outline-light btn-sm btn-qty" data-id="${item.id}" data-action="plus" type="button">+</button>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <span class="me-2">R$ ${(item.preco * item.qtd).toFixed(2).replace('.', ',')}</span>
        <button class="btn btn-sm btn-danger btn-remove" data-id="${item.id}">&times;</button>
      </div>
    `;

    li.querySelector('.btn-remove').onclick = () => {
      removeFromCart(item.id);
    };

    li.querySelectorAll('.btn-qty').forEach(btn => {
      btn.onclick = () => {
        alterarQuantidade(item.id, btn.getAttribute('data-action'));
      };
    });
    list.appendChild(li);
  });

  totalEl.textContent = total.toFixed(2).replace('.', ',');
}


function addToCart(prod) {
  const cart = getCart();
  const exists = cart.find(i => i.id === prod.id);
  if (exists) {
    exists.qty++;
  } else {
    cart.push({ ...prod, qty: 1 });
  }
  saveCart(cart);
  atualizarCarrinhoUI();
}


function removeFromCart(prodId) {
  let cart = getCart().filter(i => i.id !== prodId);
  saveCart(cart);
  atualizarCarrinhoUI();
}


document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const id = this.getAttribute('data-id');
      const nome = this.getAttribute('data-nome');
      const preco = parseFloat(this.getAttribute('data-preco'));
      const imagem = this.getAttribute('data-imagem');

      let cart = getCart();
      let item = cart.find(prod => prod.id === id);

      if (item) {
        item.qtd += 1;
      } else {
        cart.push({ id, nome, preco, imagem, qtd: 1 });
      }

      saveCart(cart);
      atualizarCarrinhoUI();
    });
  });

  atualizarCarrinhoUI();
});


function atualizarCarrinho() {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  let cartItems = document.getElementById('cart-items');
  let cartTotal = document.getElementById('cart-total');
  if (!cartItems || !cartTotal) return;
  cartItems.innerHTML = '';
  let total = 0;

  cart.forEach(item => {
    total += item.preco * item.qtd;
    let li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center bg-dark text-white';
    li.innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${item.imagem}" alt="${item.nome}" style="width:40px;height:40px;object-fit:cover;border-radius:5px;margin-right:10px;">
                <span>${item.nome} x${item.qtd}</span>
            </div>
            <span>R$ ${(item.preco * item.qtd).toFixed(2).replace('.', ',')}</span>
        `;
    cartItems.appendChild(li);
  });

  cartTotal.textContent = total.toFixed(2).replace('.', ',');
}


function finalizarCompra() {
  alert('Compra finalizada! Obrigado! ❤');
  localStorage.removeItem('cart');
  atualizarCarrinhoUI();
}


function alterarQuantidade(prodId, action) {
  let cart = getCart();
  let item = cart.find(i => i.id === prodId);
  if (!item) return;
  if (action === 'plus') {
    item.qtd++;
  } else if (action === 'minus' && item.qtd > 1) {
    item.qtd--;
  }
  saveCart(cart);
  atualizarCarrinhoUI();
}
