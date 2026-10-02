<?php
require 'auth.php';
$db=db();
$p=(int)$db->query('SELECT COUNT(*) c FROM products WHERE active=1')->fetch()['c'];
$o=(int)$db->query('SELECT COUNT(*) c FROM orders')->fetch()['c'];
$c=(int)$db->query('SELECT COUNT(*) c FROM customers')->fetch()['c'];
$r=(float)($db->query("SELECT COALESCE(SUM(total),0) t FROM orders WHERE payment_status='Paid'")->fetch()['t']??0);
$low=(int)$db->query('SELECT COUNT(*) c FROM products WHERE active=1 AND stock<=5')->fetch()['c'];
$recent=$db->query('SELECT id,customer_name,total,payment_status,order_status,created_at FROM orders ORDER BY id DESC LIMIT 6')->fetchAll();
$title='Admin Dashboard - '.APP_NAME;
include '../includes/header.php';
?>
<div class="page-heading"><div><h1>Dashboard</h1><p>Welcome back, <?=htmlspecialchars($_SESSION['admin_username']??'Admin')?>. Here's what's happening in your store.</p></div><a class="admin-btn" href="products.php">+ Add Product</a></div>
<div class="stats-grid">
<div class="stat-card"><div class="stat-top"><span class="stat-label">Active Products</span><div class="stat-icon">P</div></div><div class="stat-value"><?=$p?></div><div class="stat-foot">Products currently visible in store</div></div>
<div class="stat-card"><div class="stat-top"><span class="stat-label">Total Orders</span><div class="stat-icon">O</div></div><div class="stat-value"><?=$o?></div><div class="stat-foot">All orders received</div></div>
<div class="stat-card"><div class="stat-top"><span class="stat-label">Customers</span><div class="stat-icon">C</div></div><div class="stat-value"><?=$c?></div><div class="stat-foot">Registered customers</div></div>
<div class="stat-card"><div class="stat-top"><span class="stat-label">Paid Revenue</span><div class="stat-icon">K</div></div><div class="stat-value">KES <?=number_format($r,0)?></div><div class="stat-foot">From completed payments</div></div>
</div>
<div class="dashboard-grid">
<section class="panel"><div class="panel-head"><h2>Recent Orders</h2><a href="orders.php">View all</a></div><?php if(!$recent):?><div class="empty">No orders have been placed yet.</div><?php else:?><div style="overflow:auto"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead><tbody><?php foreach($recent as $x):?><tr><td><strong>#<?=$x['id']?></strong></td><td><?=htmlspecialchars($x['customer_name'])?></td><td>KES <?=number_format($x['total'],2)?></td><td><span class="badge <?= $x['payment_status']==='Paid'?'badge-green':($x['payment_status']==='Failed'?'badge-red':'badge-orange')?>"><?=htmlspecialchars($x['payment_status'])?></span></td><td><span class="badge badge-blue"><?=htmlspecialchars($x['order_status'])?></span></td></tr><?php endforeach;?></tbody></table></div><?php endif;?></section>
<section class="panel"><div class="panel-head"><h2>Quick Actions</h2></div><div class="panel-body"><div class="quick-grid"><a class="quick-action" href="products.php"><strong>Manage Products</strong><span>Add, edit or remove products</span></a><a class="quick-action" href="orders.php"><strong>Manage Orders</strong><span>Update customer orders</span></a><a class="quick-action" href="../products.php"><strong>View Store</strong><span>See the customer storefront</span></a><a class="quick-action" href="logout.php"><strong>Sign Out</strong><span>End your admin session</span></a></div><?php if($low):?><div class="notice" style="margin-top:18px;padding:14px;border-radius:10px;background:#fff7ed;color:#9a3412"><strong><?=$low?> product<?= $low===1?'':'s'?> low on stock.</strong><br><small>Review inventory in Products.</small></div><?php endif;?></div></section>
</div>
<?php include '../includes/footer.php';?>
