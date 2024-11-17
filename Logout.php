<?php
session_start();

// Destrói a sessão e redireciona para o login
session_destroy();
header('Location: Login.html');
exit();
