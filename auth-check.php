<?php
session_start();
if (empty($_SESSION['hyamax_academy_auth'])) {
    header('Location: login.php');
    exit;
}
