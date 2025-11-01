<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/conta/conta_cliente.css">
    <title>Conta</title>
</head>

<body>
    <?php include BASE_PATH . '/src/pages/partials/header.php'; ?>

    <main class="account-main">
        <div class="conta-container">
            <h1 class="account-title">Meu Usuário</h1>

            <div class="user-photo">
                <button>
                    <img src="<?php echo BASE_URL; ?>/public/imgs/add-photo.svg" alt="Alterar foto do usuário">
                </button>
            </div>

            <form class="account-form">
                <input type="text" placeholder="Nome Completo">
                <input type="tel" placeholder="Telefone">
                <input type="email" placeholder="Email">
                <input type="password" placeholder="Senha">
                <input type="text" placeholder="Empresa">

                <div class="action-buttons-top">
                    <button type="button" class="btn swap-account-button">
                        Trocar Conta
                    </button>
                    <button type="button" class="btn edit-button">
                        Editar
                    </button>
                </div>

                <div class="action-buttons-bottom">
                    <button type="button" class="btn delete-account-button">
                        Deletar Conta
                    </button>
                    <button type="submit" class="btn save-button">
                        Salvar
                    </button>
                </div>
            </form>

        </div>
    </main>
    <img src="<?php echo BASE_URL; ?>/public/imgs/engines-icons.svg" alt="Ícones de engrenagens decorativas" class="engines-icons">

</body>

</html>