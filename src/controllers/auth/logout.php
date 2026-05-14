<?php
session_start();
session_unset();
session_destroy();
header("Location: /greenhelp-app/public/index.html");
exit();
