<?php
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
$cc=0; foreach($_SESSION['cart']??[] as $q) $cc+=(int)$q;
$isAdmin=str_contains(str_replace('\\','/',$_SERVER['SCRIPT_NAME']),'/admin/');
$isAdminLogin=$isAdmin && basename($_SERVER['SCRIPT_NAME'])==='login.php';
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=htmlspecialchars($title??APP_NAME)?></title>
<?php if($isAdmin): ?><link rel="stylesheet" href="../assets/admin.css"><?php else: ?><link rel="stylesheet" href="assets/style.css"><?php endif; ?></head>
<?php if($isAdminLogin): ?>
<body class="admin-auth"><div class="auth-card"><div class="auth-brand"><div class="logo-mark">S</div>SokoPulse</div>
<?php elseif($isAdmin): ?>
<body class="admin-body"><div class="admin-layout"><aside class="admin-sidebar"><a class="admin-logo" href="index.php"><div class="logo-mark">S</div><span>SokoPulse</span></a><nav class="admin-nav"><a href="index.php">▦ <span>Dashboard</span></a><a href="products.php">▣ <span>Products</span></a><a href="orders.php">▤ <span>Orders</span></a><a href="../index.php">⌂ <span>View Store</span></a><a class="logout" href="logout.php">↪ <span>Logout</span></a></nav></aside><section class="admin-main"><div class="admin-topbar"><div><strong>Administration</strong><br><small>Manage your SokoPulse store</small></div><div class="admin-user"><strong><?=htmlspecialchars($_SESSION['admin_username']??'Admin')?></strong><div class="avatar">A</div></div></div><main class="admin-content">
<?php else: ?>
<body><header><div class="wrap nav"><a class="brand" href="index.php">SokoPulse</a><nav><a href="index.php">Shop</a><a href="products.php">Products</a><a href="cart.php">Cart (<?=$cc?>)</a><?php if(!empty($_SESSION['customer_id'])):?><a href="orders.php">My Orders</a><a href="account.php">My Account</a><a href="logout.php">Logout</a><?php else:?><a href="login.php">Login</a><a href="register.php">Register</a><?php endif;?><a href="admin/login.php">Admin</a></nav></div></header><main class="wrap">
<?php endif; ?>
