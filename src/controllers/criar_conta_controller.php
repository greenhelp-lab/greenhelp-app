<?php
class CriarContaController
{
    public static function criarUsuario($pdo, $nome, $email, $senha)
    {
        if (!isset($_SESSION['empresa_id'])) {
            throw new Exception('Nenhuma empresa vinculada. Crie uma empresa primeiro.');
        }

        $empresa_id = $_SESSION['empresa_id'];
        $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (empresa_id, nome, email, senha)
            VALUES (:empresa_id, :nome, :email, :senha)
        ");

        $stmt->execute([
            ':empresa_id' => $empresa_id,
            ':nome' => $nome,
            ':email' => $email,
            ':senha' => $senha_hash
        ]);

        // Limpa a sessão da empresa depois de criar o usuário
        unset($_SESSION['empresa_id']);
    }
}
