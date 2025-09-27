<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenHelp Home</title>
</head>
<body>
  <php? include 'src/views/partials/header.php'; ?> <!-- header php inserido para otimização do código -->
  <main class="home">
    <div class="home-container">
      <h1 class="home-title">Home</h1>

      <div class="home-profile-image">
        <img src="/src/views/assets/imgs/company-placeholder.svg" alt="Imagem de perfil da empresa">
      </div>
      <!-- realizar requisição no código para mostrar foto, caso não tenha foto, permitir adicionar através de um botão específico próximo a foto -->

      <h2 class="home-company-name">Nome da Empresa X</h2> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->

      <section class="company-info">
        <div class="info-field">
          <span class="field-label">Nome:</span>
          <span class="field-value"></span> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->
        </div>
        <div class="info-field">
          <span class="field-label">CNPJ:</span>
          <span class="field-value"></span>
        </div>
        <div class="info-field">
          <span class="field-label">Indústria:</span>
          <span class="field-value"></span>
        </div>
        <div class="info-field">
          <span class="field-label">Tamanho:</span>
          <span class="field-value"></span>
        </div>
        <div class="info-field">
          <span class="field-label">Email do usuário:</span>
          <span class="field-value"></span>
        </div>
        <div class="info-field">
          <span class="field-label">Usuário associado:</span>
          <span class="field-value"></span>
        </div>
        <div class="info-field">
          <span class="field-label">ID da empresa:</span>
          <span class="field-value"></span>
        </div>
      </section>
    </div>
  </main>
  <!-- seção de pontuação de sustentabilidade em T.I. da empresa do cliente -->
  <section class="points-section">
    <h2 class="points-title">Pontuações Verdes</h2>
    <p class="points-text">Ganhe mais pontos  através da compra de serviços e melhoras sustentáveis na sua empresa</p>
    <div class="points-container">
      <div class="points-card"> <!-- card de pontos, um específico a cada área de progresso (5); repetir esta div "points-card" para os outros cards -->
        <div class="points-card-left">
          <h3 class="points-card-level">Nível 1 <!-- para substituir baseado no nível do cliente --></h3>
          <span class="points-card-points">Faltam <span class="points-card-points-number">500 <!-- para substituir baseado nos pontos dessa área específica do cliente --></span>pontos</span>
          <div class="points-card-progress-bar">
            <div class="points-card-progress"></div>
          </div>
        </div>
          <div class="points-card-right">>
            <h3 class="points-card-title">Infraestrutura Eficiente</h3>
          </div>
      </div>
      <!-- repetir a div "points-card" para os outros cards -->

      <div class="points-total-points"> <!-- div de pontos totais do cliente, depois dos cards mas ainda dentro do bloco de pontuações verdes -->
        <span class="points-total-points-text"></span>
        <span class="points-total-points-number">1500 <!-- para substituir baseado no total de pontos do cliente --></span>
      </div>
    </div>

  </section>
  <php? include 'src/views/partials/footer.php'; ?> <!-- footer php inserido para otimização do código -->
</body>
</html>