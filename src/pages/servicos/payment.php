<?php include_once BASE_PATH . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Pagamento</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/servicos/payment.css">
</head>

<body>
  <?php include BASE_PATH . '/src/pages/partials/header.php'; ?> <!-- 'include' header php for code optimization -->
  <main class="container">

    <h1 class="pagamento-title">
      Pagamento
      <img src="<?php echo BASE_URL; ?>/public/imgs/cart.png" alt="Carrinho" class="icon-cart">
    </h1>

    <form class="payment-form">

      <input type="text" placeholder="Primeiro e último nome">
      <input type="email" placeholder="Email">

      <select>
        <option value="" class="select">País</option>
        <option class="select">Brasil</option>
        <option class="select">Portugal</option>
        <option class="select">Estados Unidos</option>
      </select>

      <input type="text" placeholder="CEP">
      <input type="text" placeholder="Cidade/Estado">

      <h2 class="form-subtitle">Método de Pagamento</h2>

      <div class="payment-method">
        <select>
          <option class="select">Crédito</option>
          <option class="select">Débito</option>
          <option class="select">Pix</option>
        </select>
        <img src="../imgs/flags.png" alt="Bandeiras dos cartões" class="cards-img">
      </div>

      <div class="input-icon">
        <input type="text" placeholder="Número do cartão">
        <img src="../imgs/visa.png" alt="Visa" class="inline-icon">
      </div>

      <input type="text" placeholder="Data de vencimento">
      <input type="text" placeholder="Código de segurança">

      <div class="buttons">
        <button type="button" class="btn voltar">Voltar</button>
        <button type="submit" class="btn comprar">
          Comprar <img src="<?php echo BASE_URL; ?>/public/imgs/cart_azul.png" alt="Carrinho">
        </button>
      </div>
    </form>
  </main>
</body>

</html>