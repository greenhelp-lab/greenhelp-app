<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../../../public/css/login/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

</head>
<body>
  <header>
    <!--<img src="img_icons/Vector (1).png" class="Logo1">
    <img src="img_icons/Vector (2).png" class="Logo2">-->
  </header>
  <main class="page">
    <!-- CONTAINER PRINCIPAL (coluna central) -->
    <section class="panel">
      <section style="max-width: 340px; margin: 0 auto; padding: 18px 18px 10px 18px; border-radius: 18px; background: rgba(0,0,0,0.03); box-shadow: 0 4px 16px rgba(0,0,0,0.10);">
        <h1 id="business_h" style="font-size: 22px; margin-bottom: 10px;">Create Account</h1>
        <p class="lead" style="font-size: 14px; margin-bottom: 18px;">Use a trusted email (we will contact you through it).</p>
        <form action="" method="post">
          <label for="email_business">Email</label>
          <input type="email" name="email_business" placeholder="Enter your business email" style="width:90%;padding:8px 12px;margin:8px auto 8px auto;border-radius:18px;border:1px solid #5B8EED;background-color:transparent;color:white;font-size:15px;display:block;">
          <p style="font-size:13px;margin:10px 0 4px 0;">Create a strong password:</p>
          <label for="business_password">Password</label>
          <input type="password" id="business_password" name="business_password" placeholder="Enter your password" style="width:90%;padding:8px 12px;margin:8px auto 8px auto;border-radius:18px;border:1px solid #5B8EED;background-color:transparent;color:white;font-size:15px;display:block;">
          <label for="password_again">Confirm password</label>
          <input type="password" id="password_again" name="password_again" placeholder="Confirm your password" style="width:90%;padding:8px 12px;margin:8px auto 8px auto;border-radius:18px;border:1px solid #5B8EED;background-color:transparent;color:white;font-size:15px;display:block;">
          <button type="submit" style="width:90px;padding:8px;border-radius:18px;font-size:15px;margin-top:10px;">Next</button>
        </form>
        <p style="font-size: 13px; margin-top: 16px;">Already have an Account? <a href="login.html"><br>Login</a></p>
        <div style="text-align: left; margin-top: 10px;">
          <a href="create_account.html" class="botao-voltar"><img src="img_icons/arrow-left.svg" alt="botao-voltar"></a>
        </div>
      </section>
    </section>
  </main>
  <footer></footer>
</body>
</html>