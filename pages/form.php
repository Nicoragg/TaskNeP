<h1>Formulário</h1>

<form method="post" action="tarefas.php">
  <label for="nome">Nome da Tarefa:</label>
  <input type="text" name="nome" id="nome" required><br><br>

  <label for="descricao">Descrição:</label>
  <input type="textarea" name="descricao" id="descricao" required><br><br>

  <label for="responsavel">Responsável:</label>
  <input type="text" name="responsavel" id="responsavel" required><br><br>

  <label for="prazo">Prazo Final:</label>
  <input type="date" name="prazo" id="prazo" required><br><br>

  <label for="prioridade">Prioridade:</label>
  <select name="prioridade" id="prioridade">
    <option value="very-high">Muito Alta</option>
    <option value="high">Alta</option>
    <option value="medium">Média</option>
    <option value="low">Baixa</option>
    <option value="very-low">Muito Baixa</option>
  </select><br><br>

  <button type="submit">Enviar</button>
</form>
