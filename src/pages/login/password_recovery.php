<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../public/css/login/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
 
  <main class="page">
    <!-- CONTAINER PRINCIPAL (coluna central) -->
    <section class="panel">
      <section style="max-width: 340px; margin: 0 auto; padding: 18px 18px 10px 18px; border-radius: 18px; background: rgba(0,0,0,0.03); box-shadow: 0 4px 16px rgba(0,0,0,0.10);">
        <h1 id="n_password" style="font-size: 22px; margin-bottom: 10px;">Great! Create a new password <br>(one you will remember)</h1>
        <form action="" method="post">
          <label for="new_password">Password</label>
          <input type="password" id="new_password" name="new_password" placeholder="Enter your password" style="width:90%;padding:8px 12px;margin:8px auto 8px auto;border-radius:18px;border:1px solid #5B8EED;background-color:transparent;color:white;font-size:15px;display:block;">
          <label for="confirm_new_password">Confirm new password</label>
          <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Confirm new password" style="width:90%;padding:8px 12px;margin:8px auto 8px auto;border-radius:18px;border:1px solid #5B8EED;background-color:transparent;color:white;font-size:15px;display:block;">
          <button type="submit" style="width:90px;padding:8px;border-radius:18px;font-size:15px;margin-top:10px;">Create Password</button>
        </form>
        <div style="text-align: left; margin-top: 10px;">
          <a href="login.html" class="botao-voltar"><img src="img_icons/arrow-left.svg" alt="botao-voltar"></a>
        </div>
      </section>
    </section>
  </main>
</body>
</html>