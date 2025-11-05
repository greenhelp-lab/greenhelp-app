<?php
session_start();
session_unset();  // limpa todas as variáveis da sessão
session_destroy(); // encerra a sessão
header("Location: landing.php"); // redireciona para a página inicial
exit();
