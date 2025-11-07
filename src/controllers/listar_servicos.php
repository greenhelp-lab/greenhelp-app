<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/conexao.php';

$termo = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT s.id, s.nome, s.descricao, s.preco, 
               a.nome AS area_nome, a.imagem_url AS area_img
        FROM servicos s
        LEFT JOIN areas_sustentaveis a ON s.area_id = a.id
        WHERE s.disponivel = 1";

if ($termo !== '') {
    $sql .= " AND (s.nome LIKE :termo OR s.descricao LIKE :termo OR a.nome LIKE :termo)";
}

$sql .= " ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);

if ($termo !== '') {
    $stmt->bindValue(':termo', "%$termo%", PDO::PARAM_STR);
}

$stmt->execute();
$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($servicos) {
    foreach ($servicos as $servico) {
        $areaNome = $servico['area_nome'] ?? 'Não definida';
        $areaImg = $servico['area_img'] ?? '';

        echo '
        <div class="service-card" data-area="' . htmlspecialchars($areaNome) . '" data-id="' . htmlspecialchars($servico['id']) . '">
            <div class="service-header">
                <h4>' . htmlspecialchars($servico['nome']) . '</h4>
                <img class="service-card-icon" src="' . htmlspecialchars($areaImg) . '" alt="' . htmlspecialchars($areaNome) . '">
            </div>
            <p class="service-description">' . htmlspecialchars($servico['descricao']) . '</p>
            <span class="area-name">' . htmlspecialchars($areaNome) . '</span>
            <div class="service-footer">
                <p>R$ ' . number_format($servico['preco'], 2, ',', '.') . '</p>
                <button class="btn-add-cart" data-id="' . htmlspecialchars($servico['id']) . '">
                    <span>Adicionar</span>
                    <svg class="add-cart-icon" viewBox="0 0 42 39" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.16233 7.36962C6.8847 5.62044 5.40643 4.33522 3.67211 4.33522H2.12146C0.949817 4.33522 0 3.36474 0 2.16761C0 0.970474 0.949817 0 2.12146 0H3.67211C7.18499 0 10.2204 2.39683 11.1564 5.7803H35.2849C36.8471 5.7803 38.1254 7.07769 37.9902 8.66784C37.6473 12.695 36.6477 16.1226 35.7143 18.4632C35.0309 20.1765 33.5883 21.4005 31.8139 21.7702C29.9622 22.1562 27.1377 22.5471 23.2933 22.5471C20.5408 22.5471 18.2219 22.3467 16.3839 22.0913C15.3514 21.9478 14.4221 21.5632 13.6236 20.9974L14.0524 23.6995C14.33 25.4486 15.8083 26.7339 17.5426 26.7339H33.2364C34.408 26.7339 35.3578 27.7044 35.3578 28.9015C35.3578 30.0986 34.408 31.0691 33.2364 31.0691H17.5426C13.7272 31.0691 10.4749 28.2417 9.86418 24.3934L7.16233 7.36962ZM14.4359 39C16.1843 39 17.6016 37.552 17.6016 35.7656C17.6016 33.9792 16.1843 32.531 14.4359 32.531C12.6876 32.531 11.2703 33.9792 11.2703 35.7656C11.2703 37.552 12.6876 39 14.4359 39ZM36.4022 35.7656C36.4022 37.552 34.9848 39 33.2364 39C31.488 39 30.0709 37.552 30.0709 35.7656C30.0709 33.9792 31.488 32.531 33.2364 32.531C34.9848 32.531 36.4022 33.9792 36.4022 35.7656Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>';
    }
} elseif ($termo !== '') {
    echo '<p style="text-align:center;color:var(--accent);">Nenhum serviço encontrado :(</p>';
} else {
    echo '<p style="text-align:center;color:var(--accent);">Nenhum serviço disponível no momento.</p>';
}
