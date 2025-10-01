<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenHelp Home</title>
  <link rel="stylesheet" href="/src/views/assets/css/main.css">
  <link rel="stylesheet" href="/src/views/assets/css/client.css">
</head>
<body>
  <php? include 'src/views/partials/header.php'; ?> <!-- 'include' header php for code optimization -->
  <main class="home">
    <div class="home-container">
      <h1 class="home-title">Home</h1>

      <div class="home-profile-image">
        <img src="/src/views/assets/imgs/add-photo.png" alt="Placeholder de um ícone de imagem com um sinal de mais no canto para adicionar foto de perfil" class="profile-image"> <!-- imagem placeholder, substituir pela imagem do cliente -->
      </div>
      <!-- realizar requisição no código para mostrar foto, caso não tenha foto, permitir adicionar através de um botão específico próximo a foto -->

      <h2 class="home-company-name">Nome da Empresa X</h2> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->

      <section class="company-info">
        <div class="home-info-field">
          <span class="field-label">Nome</span>
          <span class="field-value"></span> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->
        </div>
        <div class="home-info-field">
          <span class="field-label">CNPJ</span>
          <span class="field-value"></span>
        </div>
        <div class="home-info-field">
          <span class="field-label">Indústria</span>
          <span class="field-value"></span>
        </div>
        <div class="home-info-field">
          <span class="field-label">Tamanho</span>
          <span class="field-value"></span>
        </div>
        <div class="home-info-field">
          <span class="field-label">Email do usuário</span>
          <span class="field-value"></span>
        </div>
        <div class="home-info-field">
          <span class="field-label">Usuário associado</span>
          <span class="field-value"></span>
        </div>
        <div class="home-info-field">
          <span class="field-label">ID da empresa</span>
          <span class="field-value"></span>
        </div>
      </section>
    </div>
  </main>
  <!-- seção de pontuação de sustentabilidade em T.I. da empresa do cliente -->
  <section class="points-section">
    <img src="/src/views/assets/imgs/badge-icon.svg" alt="ícone de medalha de pontuação" class="points-icon">
    <h2 class="points-title">Pontuações Verdes</h2>
    <p class="points-text">Ganhe mais pontos  através da compra de serviços e melhoras sustentáveis na sua empresa</p>
    <div class="points-container">

      <!-- Efficient Infrastructure Area Card -->
      <div class="points-card">
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

      <!-- Renewable Energy Area Card -->
      <div class="points-card">
        <div class="points-card-left">
          <h3 class="points-card-level">Nível 1 <!-- para substituir baseado no nível do cliente --></h3>
          <span class="points-card-points">Faltam <span class="points-card-points-number">500 <!-- para substituir baseado nos pontos dessa área específica do cliente --></span>pontos</span>
          <div class="points-card-progress-bar">
            <div class="points-card-progress"></div>
          </div>
        </div>
          <div class="points-card-right">>
            <h3 class="points-card-title">Energia Renovável</h3>
          </div>
      </div>

      <!-- E-Waste Discard Area Card -->
      <div class="points-card">
        <div class="points-card-left">
          <h3 class="points-card-level">Nível 1 <!-- para substituir baseado no nível do cliente --></h3>
          <span class="points-card-points">Faltam <span class="points-card-points-number">500 <!-- para substituir baseado nos pontos dessa área específica do cliente --></span>pontos</span>
          <div class="points-card-progress-bar">
            <div class="points-card-progress"></div>
          </div>
        </div>
          <div class="points-card-right">>
            <h3 class="points-card-title">Descarte de Lixo Eletrônico</h3>
          </div>
      </div>

      <!-- Cloud Computing Area Card -->
      <div class="points-card">
        <div class="points-card-left">
          <h3 class="points-card-level">Nível 1 <!-- para substituir baseado no nível do cliente --></h3>
          <span class="points-card-points">Faltam <span class="points-card-points-number">500 <!-- para substituir baseado nos pontos dessa área específica do cliente --></span>pontos</span>
          <div class="points-card-progress-bar">
            <div class="points-card-progress"></div>
          </div>
        </div>
          <div class="points-card-right">>
            <h3 class="points-card-title">Computação em Nuvem</h3>
          </div>
      </div>

      <!-- Green IT Politics Area Card -->
      <div class="points-card">
        <div class="points-card-left">
          <h3 class="points-card-level">Nível 1 <!-- para substituir baseado no nível do cliente --></h3>
          <span class="points-card-points">Faltam <span class="points-card-points-number">500 <!-- para substituir baseado nos pontos dessa área específica do cliente --></span>pontos</span>
          <div class="points-card-progress-bar">
            <div class="points-card-progress"></div>
          </div>
        </div>
          <div class="points-card-right">>
            <h3 class="points-card-title">Políticas de TI Verde</h3>
          </div>
      </div>

      <div class="points-total-points"> <!-- div de pontos totais do cliente, depois dos cards, mas ainda dentro do bloco (div) de pontuações verdes -->
        <span class="points-total-points-text"></span>
        <span class="points-total-points-number">2500 <!-- para substituir baseado no total de pontos do cliente --></span>
      </div>
    </div>

  </section>
  <php? include 'src/views/partials/footer.php'; ?> <!-- 'include' footer php for code optimization -->
</body>
</html>