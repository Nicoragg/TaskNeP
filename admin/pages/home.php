<h1>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario']) ?></h1>

<p>Total de tarefas: <strong><?= $total ?></strong></p>
<p>Tarefas de alta prioridade: <strong><?= $altas ?></strong></p>

<a href="index.php?page=create">Criar Nova Tarefa</a><br>
<a href="index.php?page=tasks">Ver Tarefas</a><br><br>

<a href="index.php?logout=true">Sair</a>
``
