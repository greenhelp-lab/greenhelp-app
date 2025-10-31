<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/greenhelp-app/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | GreenHelp</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/global.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/login.css">
</head>

<body>
  <main class="page">
    <section class="panel">
      <div class="auth-container">
        <div class="auth-header">
          <h1>Login</h1>
          <p class="lead">Bem-vindo, GreenHelper!<br>Por favor, faça login para continuar.</p>
        </div>

        <form class="auth-form" action="" method="post">
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email">
          </div>

          <div class="form-group">
            <label for="password">Senha</label>
            <input type="password" id="password" name="password" placeholder="Digite sua senha">
          </div>

          <button type="submit">Login</button>
        </form>

        <div class="auth-links">
          <a href="<?php echo BASE_URL; ?>/login/reset-password">Esqueceu sua senha?</a>
          <p class="help-text">Não tem uma conta? <a href="<?php echo BASE_URL; ?>/login/create-account">Crie uma</a></p>
        </div>

        <a href="<?php echo BASE_URL; ?>" class="back-button">
          <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" fill="none">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </a>
      </div>
    </section>
  </main>
</body>

</html>

<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'your_database');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      session_start();
      $_SESSION['username'] = $username;
      echo "Login successful! Welcome, " . htmlspecialchars($username);
    } else {
      echo "Invalid password.";
    }
  } else {
    echo "User not found.";
  }
  $stmt->close();
  $conn->close();
}
?>
<form method="POST">
  <input type="text" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit">Login</button>
</form>