<?php
require_once "./helpers/functions.php";
if (isset($_POST["nome"])) {
  $chave = ["nome", "descricao", "prazo", "responsavel"];
  [$nome, $descricao, $prazo, $atribuido] = array_map(fn($key) => validateInput($_POST[$key]), $chave);
}

?>
<h1>Tarefas</h1>
<?php if (isset($alerta)) foreach ($alerta as $a) echo $a ?>

<table>
  <thead>
    <tr>
      <th>Nome</th>
      <th>descricao</th>
      <th>Prazo Final</th>
      <th>Atribuído a</th>
      <th>Ver mais</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?= $nome ?? "" ?></td>
      <td><?= $descricao ?? "" ?></td>
      <td><?= $prazo ?? "" ?></td>
      <td><?= $atribuido ?? "" ?></td>
      <td>Ver mais</td>
    </tr>
  </tbody>
</table>
