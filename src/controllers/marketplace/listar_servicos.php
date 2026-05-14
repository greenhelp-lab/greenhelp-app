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
$servicos = $stmt->fetchAll();

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
                    <img class="add-cart-icon" src="/greenhelp-app/public/icons/cart.svg" alt="">
                </button>
            </div>
        </div>';
    }
} elseif ($termo !== '') {
    echo '<p style="text-align:center;color:var(--accent);">Nenhum serviço encontrado :(</p>';
} else {
    echo '<p style="text-align:center;color:var(--accent);">Nenhum serviço disponível no momento.</p>';
}
