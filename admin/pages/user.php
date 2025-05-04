<link rel="stylesheet" href="../../assets/stylesheets/card.css">

<div class="card-title">
  <h1>Perfil de Usuário</h1>
</div>

<div class="user-container">
  <div class="user-card">
    <h2><?= $_SESSION['user']; ?></h2>
    <p><?= $_SESSION['email']; ?></p>
    <div class="user-actions">
      <a href="?page=logout" class="logout-button">
        <button type="button">Sair</button>
      </a>
    </div>
  </div>
</div>
