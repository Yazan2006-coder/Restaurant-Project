<?php

session_start();

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

$_SESSION['cart'] = $_SESSION['cart'] ?? [];
$_SESSION['cart']['classic'] = $_SESSION['cart']['classic'] ?? 0;
$_SESSION['cart']['szechuan'] = $_SESSION['cart']['szechuan'] ?? 0;

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";
