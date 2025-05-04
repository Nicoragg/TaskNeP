<?php
session_start();
require_once "./helpers/functions.php";

if (!isset($_SESSION['tarefas'])) {
  $_SESSION['tarefas'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $chave = ["nome", "descricao", "prazo", "responsavel", "prioridade"];
  [$nome, $descricao, $prazo, $responsavel, $prioridade] = array_map(
    fn($key) => validateInput($_POST[$key]),
    $chave
  );

  $_SESSION['tarefas'][] = [
    "id" => uniqid(),
    "nome" => $nome,
    "descricao" => $descricao,
    "prazo" => $prazo,
    "responsavel" => $responsavel,
    "prioridade" => $prioridade
  ];
}

$tarefas = $_SESSION['tarefas'];
?>

<h1>Tarefas</h1>
<table cellpadding="5">
  <thead>
    <tr>
      <th>Nome</th>
      <th>Descrição</th>
      <th>Prazo Final</th>
      <th>Responsável</th>
      <th>Prioridade</th>
      <th>Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($tarefas as $tarefa): ?>
      <tr>
        <td><?= htmlspecialchars($tarefa["nome"]) ?></td>
        <td><?= htmlspecialchars($tarefa["descricao"]) ?></td>
        <td><?= htmlspecialchars($tarefa["prazo"]) ?></td>
        <td><?= htmlspecialchars($tarefa["responsavel"]) ?></td>
        <td><?= htmlspecialchars($tarefa["prioridade"]) ?></td>
        <td>
          <a href="detalhes.php?id=<?= $tarefa["id"] ?>">Ver mais</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
