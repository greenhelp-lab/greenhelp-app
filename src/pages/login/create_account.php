<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

</head>
<body>
  
  <main class="page">
    <!-- CONTAINER PRINCIPAL (coluna central) -->
    <section class="panel">
      <section style="max-width: 340px; margin: 0 auto; padding: 18px 18px 10px 18px; border-radius: 18px; background: rgba(0,0,0,0.03); box-shadow: 0 4px 16px rgba(0,0,0,0.10);">
        <h1 id="business_h" style="font-size: 22px; margin-bottom: 10px;">Create Account</h1>
        <p class="lead" style="font-size: 14px; margin-bottom: 18px;">Follow the steps to create your account:</p>
        <form action="" method="post">
          <label for="businessname">Business Name</label>
          <input type="text" name="name_business" placeholder="Enter your business name">
          <label for="business_cnpj">Business CNPJ</label>
          <input type="number" id="business_cnpj" name="business_cnpj" placeholder="Enter your business CNPJ">
          <label for="business_industry">Business Industry</label>
          <input type="text" id="business_industry" name="business_industry" placeholder="Enter your business industry">
          <label for="business_size">Business Size</label>
          <input type="text" id="business_size" name="business_size" placeholder="Enter your business size">
          <button type="submit"><a href="Create_account2.html">Next</a></button>
        </form>
        <p style="font-size: 13px; margin-top: 16px;">Already have an Account? <a href="login.html"><br>Login</a></p>
        <div style="text-align: left; margin-top: 10px;">
          <a href="login.html" class="botao-voltar"><img src="img_icons/arrow-left.svg" alt="botao-voltar"></a>
        </div>
      </section>
    </section>
  </main>
 
 <footer></footer>
  </body>
</html>