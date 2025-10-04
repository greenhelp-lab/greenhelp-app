<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conta</title>
  <link rel="stylesheet" href="/greenhelp-app/public/css/main.css">
  <link rel="stylesheet" href="/greenhelp-app/public/css/admin.css">
</head>
<body>
   <?php include __DIR__ . '/../partials/header.php'; ?> <!-- 'include' header php for code optimization -->
  <main class="account">
    <div class="account-container">
      <h1 class="account-title">Minha Conta</h1>
      <div class="account-profile-image">
        <img src="/greenhelp-app/public/imgs/add-photo.png" alt="Placeholder de um ícone de imagem com um sinal de mais no canto para adicionar foto de perfil" class="profile-image"> <!-- imagem placeholder, substituir pela imagem do cliente -->
      </div>
      <!-- realizar requisição no código para mostrar foto, caso não tenha foto, permitir adicionar através de um botão específico próximo a foto -->

      <h2 class="account-name">Nome da Empresa X</h2> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->

      <section class="account-info">
        <div class="account-info-field">
          <span class="field-label">Nome</span>
          <span class="field-value"></span> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->
        </div>
        <div class="account-info-field">
          <span class="field-label">Cargo</span>
          <span class="field-value"></span>
        </div>
        <div class="account-info-field">
          <span class="field-label">Perfil</span>
          <span class="field-value"></span>
        </div>
        <div class="account-info-field">
          <span class="field-label">Telefone</span>
          <span class="field-value"></span>
        </div>
        <div class="account-info-field">
          <span class="field-label">Email do usuário</span>
          <span class="field-value"></span>
        </div>
        <div class="account-info-field">
          <span class="field-label">Senha</span>
          <span class="field-value"></span>
        </div>
        <div class="account-info-field">
          <span class="field-label">Empresa</span>
          <span class="field-value">GreenHelp</span>
        </div>
      </section>
    </div>

    <div class="account-buttons">
      <button class="edit-button">
        Editar
      </button>
      <button class="save-button">
        Salvar
      </button>
      <button class="logout-button">
        Sair
      </button>
      <button class="delete-account-button">
        Excluir Conta
      </button>
    </div>
  </main>
  <img src="/greenhelp-app/public/imgs/engines-icons.svg" alt="Ícones de engrenagens decorativas" class="engines-icons">
  
</body>
</html>