import React, { useState, useEffect } from 'react';

function Checkout() {
  const [step, setStep] = useState(1);
  const [cart, setCart] = useState([]);
  const [shippingAddress, setShippingAddress] = useState({
    name: '',
    address: '',
    houseNumber: '',
    complement: '',
    referencePoint: '',
    phone: '',
  });
  const [shippingCost, setShippingCost] = useState(0);
  const [paymentMethod, setPaymentMethod] = useState("");
  const [orderSummary, setOrderSummary] = useState({});

  useEffect(() => {
    const fetchCart = async () => {
      try {
        const response = await fetch('http://localhost/Lottus/#');
        if (!response.ok) {
          throw new Error('Erro ao buscar o carrinho.');
        }
        const data = await response.json();
        setCart(data);
      } catch (error) {
        console.error('Erro ao buscar o carrinho:', error);
      }
    };

    fetchCart();
  }, []);

  // Função simulada para calcular frete com base no endereço
  // Em um sistema real, essa parte integraria com uma API de frete
  const calculateShippingCost = () => {
    // Exemplo simples: frete fixo ou zero se campo endereço vazio
    if (!shippingAddress.address || !shippingAddress.houseNumber) {
      return 0;
    }
    // Pode adicionar lógica mais complexa aqui
    return 15.99; // valor fixo fictício para o frete
  };

  useEffect(() => {
    const cost = calculateShippingCost();
    setShippingCost(cost);
  }, [shippingAddress]);

  const handleNext = () => {
    // Validação simples para o passo de endereço
    if (step === 2) {
      if (!shippingAddress.name || !shippingAddress.address || !shippingAddress.houseNumber || !shippingAddress.phone) {
        alert("Por favor, preencha todos os campos obrigatórios do endereço.");
        return;
      }
    }
    // Validação forma de pagamento no passo 3
    if (step === 3) {
      if (!paymentMethod) {
        alert("Por favor, selecione uma forma de pagamento.");
        return;
      }
    }
    setStep(step + 1);
  };

  const handleBack = () => setStep(step - 1);

  const calculateTotal = () => {
    return cart.reduce((total, item) => total + item.price * item.quantity, 0) + shippingCost;
  };

  const saveOrder = async (orderData) => {
    try {
      const response = await fetch('http://localhost/Lottus/#', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(orderData),
      });

      if (!response.ok) {
        throw new Error('Erro ao salvar pedido.');
      }

      const data = await response.json();
      console.log('Pedido salvo:', data);
      return data;
    } catch (error) {
      console.error('Erro ao salvar pedido:', error);
      return null;
    }
  };

  const handleConfirm = async () => {
    const summary = {
      cart,
      shippingAddress,
      paymentMethod,
      shippingCost,
      total: calculateTotal(),
    };

    setOrderSummary(summary);
    const result = await saveOrder(summary);

    if (result) {
      alert("Pedido confirmado!");
      console.log(summary);
    } else {
      alert("Ocorreu um erro ao confirmar o pedido.");
    }
  };

  return (
    <div className="p-6 max-w-md mx-auto bg-white rounded-lg shadow-md">
      {step === 1 && (
        <div>
          <h2 className="text-2xl font-bold mb-6">Resumo do Carrinho</h2>
          <ul className="mb-6">
            {cart.map((item) => (
              <li key={item.id} className="flex justify-between mb-2 border-b pb-2">
                <span>{item.name} x{item.quantity}</span>
                <span>R$ {(item.price * item.quantity).toFixed(2)}</span>
              </li>
            ))}
          </ul>
          <div className="text-right font-bold mb-6 text-lg">
            Subtotal: R$ {cart.reduce((t, i) => t + i.price * i.quantity, 0).toFixed(2)}
          </div>
          <button className="btn btn-primary w-full py-3 text-white bg-blue-600 rounded hover:bg-blue-700" onClick={handleNext}>Continuar</button>
        </div>
      )}

      {step === 2 && (
        <div>
          <h2 className="text-2xl font-bold mb-6">Endereço de Entrega</h2>
          <input
            type="text"
            placeholder="Nome"
            className="input input-bordered w-full mb-4 px-3 py-2 border rounded"
            value={shippingAddress.name}
            onChange={(e) => setShippingAddress({ ...shippingAddress, name: e.target.value })}
            required
          />
          <input
            type="text"
            placeholder="Endereço"
            className="input input-bordered w-full mb-4 px-3 py-2 border rounded"
            value={shippingAddress.address}
            onChange={(e) => setShippingAddress({ ...shippingAddress, address: e.target.value })}
            required
          />
          <input
            type="text"
            placeholder="Número da Casa"
            className="input input-bordered w-full mb-4 px-3 py-2 border rounded"
            value={shippingAddress.houseNumber}
            onChange={(e) => setShippingAddress({ ...shippingAddress, houseNumber: e.target.value })}
            required
          />
          <input
            type="text"
            placeholder="Complemento"
            className="input input-bordered w-full mb-4 px-3 py-2 border rounded"
            value={shippingAddress.complement}
            onChange={(e) => setShippingAddress({ ...shippingAddress, complement: e.target.value })}
          />
          <input
            type="text"
            placeholder="Ponto de Referência"
            className="input input-bordered w-full mb-4 px-3 py-2 border rounded"
            value={shippingAddress.referencePoint}
            onChange={(e) => setShippingAddress({ ...shippingAddress, referencePoint: e.target.value })}
          />
          <input
            type="text"
            placeholder="Telefone"
            className="input input-bordered w-full mb-4 px-3 py-2 border rounded"
            value={shippingAddress.phone}
            onChange={(e) => setShippingAddress({ ...shippingAddress, phone: e.target.value })}
            required
          />
          <div className="mb-6 text-right font-semibold">
            Frete estimado: R$ {shippingCost.toFixed(2)}
          </div>
          <div className="flex justify-between">
            <button className="btn btn-secondary py-2 px-4 bg-gray-300 rounded hover:bg-gray-400" onClick={handleBack}>Voltar</button>
            <button className="btn btn-primary py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700" onClick={handleNext}>Continuar</button>
          </div>
        </div>
      )}

      {step === 3 && (
        <div>
          <h2 className="text-2xl font-bold mb-6">Forma de Pagamento</h2>
          <div className="flex gap-4 mb-6">
            <button
              className={`btn py-2 px-4 rounded ${paymentMethod === 'boleto' ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}
              onClick={() => setPaymentMethod("boleto")}
            >
              Boleto
            </button>
            <button
              className={`btn py-2 px-4 rounded ${paymentMethod === 'pix' ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}
              onClick={() => setPaymentMethod("pix")}
            >
              Pix
            </button>
            <button
              className={`btn py-2 px-4 rounded ${paymentMethod === 'cartao' ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}
              onClick={() => setPaymentMethod("cartao")}
            >
              Cartão
            </button>
          </div>
          <div className="flex justify-between">
            <button className="btn btn-secondary py-2 px-4 bg-gray-300 rounded hover:bg-gray-400" onClick={handleBack}>Voltar</button>
            <button className="btn btn-primary py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700" onClick={handleNext}>Continuar</button>
          </div>
        </div>
      )}

      {step === 4 && (
        <div>
          <h2 className="text-2xl font-bold mb-6">Confirmação do Pedido</h2>
          <ul className="mb-6">
            {cart.map((item) => (
              <li key={item.id} className="flex justify-between mb-2 border-b pb-2">
                <span>{item.name} x{item.quantity}</span>
                <span>R$ {(item.price * item.quantity).toFixed(2)}</span>
              </li>
            ))}
          </ul>
          <div className="mb-2">
            <strong>Endereço de Entrega:</strong> {shippingAddress.address}, {shippingAddress.houseNumber} {shippingAddress.complement && `- ${shippingAddress.complement}`}<br/>
            {shippingAddress.referencePoint && (<span><strong>Ponto de Referência:</strong> {shippingAddress.referencePoint}<br/></span>)}
            <strong>Contato:</strong> {shippingAddress.name} - {shippingAddress.phone}
          </div>
          <p className="mb-2"><strong>Frete:</strong> R$ {shippingCost.toFixed(2)}</p>
          <p className="mb-2"><strong>Pagamento:</strong> {paymentMethod}</p>
          <div className="text-right font-bold mb-6 text-lg">Total: R$ {calculateTotal().toFixed(2)}</div>
          <button className="btn btn-primary w-full py-3 text-white bg-blue-600 rounded hover:bg-blue-700" onClick={handleConfirm}>Confirmar Pedido</button>
          <button className="btn btn-secondary w-full py-3 mt-4 bg-gray-300 rounded hover:bg-gray-400" onClick={handleBack}>Voltar</button>
        </div>
      )}
    </div>
  );
}

export default Checkout;