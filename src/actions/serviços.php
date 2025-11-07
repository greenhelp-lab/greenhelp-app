<?php
class Servico {
  private $pdo;

  public function __construct($pdo) {
    $this->pdo = $pdo;
  }

  public function listarTodos() {
    $stmt = $this->pdo->query("
      SELECT s.*, a.nome AS area_nome 
      FROM servicos s
      JOIN areas_sustentaveis a ON s.area_id = a.id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function buscarPorId($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM servicos WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function criar($dados) {
    $sql = "INSERT INTO servicos 
      (nome, descricao, preco, pontos, categoria, disponivel, descricao_longa, itens_incluidos, garantia, prazo, contato, area_id)
      VALUES (:nome, :descricao, :preco, :pontos, :categoria, :disponivel, :descricao_longa, :itens_incluidos, :garantia, :prazo, :contato, :area_id)";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($dados);
  }

  public function atualizar($id, $dados) {
    $sql = "UPDATE servicos SET 
      nome = :nome,
      descricao = :descricao,
      preco = :preco,
      pontos = :pontos,
      categoria = :categoria,
      disponivel = :disponivel,
      descricao_longa = :descricao_longa,
      itens_incluidos = :itens_incluidos,
      garantia = :garantia,
      prazo = :prazo,
      contato = :contato,
      area_id = :area_id
      WHERE id = :id";

    $stmt = $this->pdo->prepare($sql);
    $dados['id'] = $id;
    return $stmt->execute($dados);
  }

  public function deletar($id) {
    $stmt = $this->pdo->prepare("DELETE FROM servicos WHERE id = ?");
    return $stmt->execute([$id]);
  }

  public function listarAreas() {
    $stmt = $this->pdo->query("SELECT id, nome FROM areas_sustentaveis");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
