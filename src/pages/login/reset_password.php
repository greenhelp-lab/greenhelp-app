<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <h1 style="font-size: 22px; margin-bottom: 10px;">Password Recovery</h1>
        <h2 style="font-size: 16px; margin-bottom: 10px;">We’ve sent you an email confirmation</h2>
        <p style="font-size: 13px;">Paste the 6-digit code:</p>
        <form action="password_recovery.html" method="get">
          <label for="codigo" style="font-size: 14px;">Code</label>
          <input type="number" id="codigo" name="codigo" required style="width:90%;padding:8px 12px;margin:8px auto 8px auto;border-radius:18px;border:1px solid #5B8EED;background-color:transparent;color:white;font-size:15px;display:block;">
          <button type ="submit" style="width:90px;padding:8px;border-radius:18px;font-size:15px;margin-top:10px;">Confirmar</button>
        </form>
        <p style="font-size: 13px; margin-top: 16px;">Didn’t receive the code? Check your email spam.<br><button type="botao_enviar_novamente" style="width:120px;padding:8px;border-radius:18px;font-size:13px;margin-top:8px;">Send code again</button></p>
        <div style="text-align: left; margin-top: 10px;">
          <a href="login.html" class="botao-voltar"><img src="img_icons/arrow-left.svg" alt="botao-voltar"></a>
        </div>
      </section>
    </section>
  </main>
  <footer></footer>
</body>
</html>