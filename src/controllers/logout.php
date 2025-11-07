<?php
session_start();
session_unset();  // limpa todas as variáveis da sessão
session_destroy(); // encerra a sessão
header("Location: /greenhelp-app/public/sobre_nos.html"); // redireciona para a página sobre_nós, famosa landing page!
exit();
