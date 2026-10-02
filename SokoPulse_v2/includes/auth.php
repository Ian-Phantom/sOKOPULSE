<?php
if(session_status()!==PHP_SESSION_ACTIVE)session_start();require_once __DIR__.'/db.php';
function customer_logged_in(){return !empty($_SESSION['customer_id']);}
function require_customer(){if(!customer_logged_in()){header('Location: login.php?redirect=checkout.php');exit;}}
function current_customer(){if(!customer_logged_in())return null;$s=db()->prepare('SELECT id,name,email,phone,address,created_at FROM customers WHERE id=?');$s->execute([$_SESSION['customer_id']]);return $s->fetch()?:null;}
function admin_logged_in(){return !empty($_SESSION['admin_id']);}
function require_admin(){if(!admin_logged_in()){header('Location: login.php');exit;}}
