<style>
  .user-card {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.5rem;
    background-color: #ffffff;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  .user-card h2 {
    margin: 0;
    color: #333;
    font-size: 1.5rem;
  }

  .user-card p {
    margin: 0.5rem 0;
    color: #666;
    font-size: 1rem;
  }

  .user-actions {
    margin-top: 1rem;
  }

  .logout-button button {
    background-color: #f7ff14;
    max-width: 150px;
    margin: 0 auto;
  }

  .logout-button button:hover {
    background-color: #e6ee00;
  }
</style>
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
