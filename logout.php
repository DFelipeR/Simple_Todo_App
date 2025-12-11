<?php
session_start();
// Destruir la sesión y redirigir al login
session_unset();
session_destroy();
header('Location: login.php');
exit();
