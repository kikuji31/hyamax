<?php
require __DIR__ . '/session.php';
if (empty($_SESSION['hyamax_academy_auth'])) {
    header('Location: login.php');
    exit;
}
