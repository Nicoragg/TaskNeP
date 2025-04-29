<h1>Formulário</h1>

<form method="post" action="?page=create">
    <label for="nome">Nome da Tarefa:</label>
    <input type="text" name="nome" id="nome"><br>

    <label for="descricao">Descrição:</label>
    <input type="textarea" name="descricao" id="descricao"><br>

    <label for="responsavel">Responsável:</label>
    <input type="text" name="responsavel" id="responsavel"><br>

    <label for="prazo">Prazo Final:</label>
    <input type="date" name="prazo" id="prazo"><br>

    <label for="prioridade">Prioridade</label>
    <select name="prioridade" id="prioridade">
        <option value="ma">Muito Alta</option>
        <option value="al">Alta</option>
        <option value="me">Média</option>
        <option value="ba">Baixa</option>
        <option value="mb">Muito Baixa</option>
    </select><br><br>

    <button type="submit">Enviar</button>
</form>