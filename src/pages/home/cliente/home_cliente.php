<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GreenHelp Home</title>
  <link rel="stylesheet" href="../../../assets/global.css">
  <link rel="stylesheet" href="/greenhelp-app/src/pages/home/cliente/home_cliente.css">
</head>
<body>
  <!-- <?php phpinfo(); ?> -->
  <?php include __DIR__ . '../../../partials/header/header.php'; ?> <!-- 'include' header php for code optimization -->
   <main class="container">
    <h1>Home</h1>

    <div class="user-photo">
      <img src="../imgs/foto_user.png" alt="Alterar foto do usuário">
    </div>

    <h1>Nome da empresa</h1>

    <form>
      <input type="text" placeholder="Nome Completo">
      <input type="text" placeholder="Cargo">
      <input type="text" placeholder="Perfil">
      <input type="tel" placeholder="Telefone">
      <input type="email" placeholder="Email">
      <input type="password" placeholder="Senha">
      <input type="text" placeholder="ID da Empresa">

    <section class="pontuacoes">
        <div class="pontuacoes-header">
          <img src="../imgs/pontuação_verde.png" alt="Pontuações Verdes" class="pontuacoes-img">
          <h2 class="pontuacoes-title">Pontuações Verdes</h2>
        </div>
      
        <p class="pontuacoes-desc">
          Ganhe mais pontos através da <span class="cor_verde">compra de serviços</span> e 
          <span class="cor_verde">melhoras sustentáveis</span> na sua empresa
        </p>
      
        <div class="niveis">
          <div class="nivel-card">
            <div class="nivel-left">
              <span class="nivel">Nível 7</span>
              <span class="faltam">Faltam 1435 pontos</span>
            </div>
            <div class="nivel-desc">Infraestrutura Eficiente</div>
            <div class="progress-bar">
              <div class="progress" style="width: 60%;"></div>
            </div>
          </div>
      
          <div class="nivel-card">
            <div class="nivel-left">
              <span class="nivel">Nível 7</span>
              <span class="faltam">Faltam 1435 pontos</span>
            </div>
            <div class="nivel-desc">Energia Renovável</div>
            <div class="progress-bar">
              <div class="progress" style="width: 60%;"></div>
            </div>
          </div>
      
          <div class="nivel-card">
            <div class="nivel-left">
              <span class="nivel">Nível 7</span>
              <span class="faltam">Faltam 1435 pontos</span>
            </div>
            <div class="nivel-desc">Descarte de Lixo Eletrônico</div>
            <div class="progress-bar">
              <div class="progress" style="width: 60%;"></div>
            </div>
          </div>
      
          <div class="nivel-card">
            <div class="nivel-left">
              <span class="nivel">Nível 7</span>
              <span class="faltam">Faltam 1435 pontos</span>
            </div>
            <div class="nivel-desc">Computação em Nuvem</div>
            <div class="progress-bar">
              <div class="progress" style="width: 60%;"></div>
            </div>
          </div>
      
          <div class="nivel-card">
            <div class="nivel-left">
              <span class="nivel">Nível 7</span>
              <span class="faltam">Faltam 1435 pontos</span>
            </div>
            <div class="nivel-desc">Políticas de TI Verde</div>
            <div class="progress-bar">
              <div class="progress" style="width: 60%;"></div>
            </div>
          </div>
        </div>
      
        <p class="pontuacao-total">Pontuação Total: <strong>4769</strong></p>
      </section>

  </section>
  <?php include __DIR__ . '../../../partials/footer/footer.php'; ?> <!-- 'include' footer php for code optimization -->
</body>
</html>