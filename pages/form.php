<h1>Formulário</h1>

<form method="post" action="?page=create">
  <label for="name">Nome da Tarefa:</label>
  <input type="text" name="name" id="name"><br>
  <br>
  <label for="description">Descrição:</label>
  <input type="textarea" name="description" id="description"><br>
  <br>
  <label for="responsible">Responsável:</label>
  <input type="text" name="responsible" id="responsible"><br>
  <br>
  <label for="time">Prazo Final:</label>
  <input type="date" name="time" id="time"><br>
  <br>
  <label for="priority">Prioridade</label>
  <select name="priority" id="priority">
    <option value="very-high">Muito Alta</option>
    <option value="high">Alta</option>
    <option value="medium">Média</option>
    <option value="low">Baixa</option>
    <option value="very-low">Muito Baixa</option>
  </select><br><br>

  <button type="submit">Enviar</button>
</form>
