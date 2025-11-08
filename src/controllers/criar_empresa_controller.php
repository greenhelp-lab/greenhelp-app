<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/src/config/config.php';

class EmpresaController {
    public static function criar($dados): void {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=greenhelp_db;charset=utf8mb4", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO empresas (nome, cnpj, setor_atuacao, porte)
                    VALUES (:nome, :cnpj, :setor, :porte)";
                

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nome'  => $dados['business_name'],
                ':cnpj'  => $dados['business_cnpj'],
                ':setor' => $dados['business_industry'],
                ':porte' => $dados['business_size']
            ]);

        } catch (PDOException $e) {
            die("Erro ao inserir empresa: " . $e->getMessage());
        }
    }
}



