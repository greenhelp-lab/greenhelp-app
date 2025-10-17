<?php include_once BASE_PATH . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>/public/css/conta/conta_cliente.css">
  <title>Conta</title>
</head>

<body>
  <?php include BASE_PATH . '/src/pages/partials/header.php'; ?> <!-- 'include' header php for code optimization -->
  <main class="account">
    <div class="conta-container">
      <h1 class="account-title">Minha Conta</h1>

      <div class="account-profile-image">
        <img src="<?php echo BASE_URL; ?>/public/imgs/add-photo.svg" alt="Placeholder de um ícone de imagem com um sinal de mais no canto para adicionar foto de perfil" class="profile-image"> <!-- imagem placeholder, substituir pela imagem do cliente -->
      </div>
      <!-- realizar requisição no código para mostrar foto, caso não tenha foto, permitir adicionar através de um botão específico próximo a foto -->

      <h2 class="account-name">Bem-vindo, [usuario.nome]!</h2> <!-- entre colchetes, o template não funcional representando o nome do usuário -->

      <section class="account-info">
        <div class="account-info-field">
          <span class="field-label">Nome</span>
          <span class="field-value"></span> <!-- conectado ao banco de dados, exibindo as informações do cliente cadastrado -->
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
          <div class="account-info-field">
            <span class="field-label">Empresa(s)</span>
            <span class="field-value"></span>
          </div>
      </section>
    </div>

    <div class="account-buttons">
      <button class="add-company-button">
        Adicionar Empresa
      </button>
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
  <img src="<?php echo BASE_URL; ?>/public/imgs/engines-icons.svg" alt="Ícones de engrenagens decorativas" class="engines-icons">

</body>

</html>