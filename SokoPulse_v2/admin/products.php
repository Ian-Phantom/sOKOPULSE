<?php
require 'auth.php';

$p = db();
$msg = '';
$error = '';

$uploadDir = __DIR__ . '/../assets/products/';
$uploadWebPath = 'assets/products/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function uploadProductImage(array $file, string $uploadDir, string $uploadWebPath): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image upload failed. Please try again.');
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Image is too large. Maximum size is 5 MB.');
    }

    $tmp = $file['tmp_name'] ?? '';
    if (!is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid image upload.');
    }

    $mime = '';
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
    } else {
        $mime = mime_content_type($tmp);
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Unsupported image type. Use JPG, PNG, GIF or WebP.');
    }

    if (@getimagesize($tmp) === false) {
        throw new RuntimeException('The selected file is not a valid image.');
    }

    $filename = 'product_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    $destination = $uploadDir . $filename;

    if (!move_uploaded_file($tmp, $destination)) {
        throw new RuntimeException('Could not save the uploaded image. Check folder permissions.');
    }

    return $uploadWebPath . $filename;
}

function deleteProductImage(?string $path): void
{
    if (!$path || $path === 'assets/placeholder.svg') {
        return;
    }

    $prefix = 'assets/products/';
    if (strpos($path, $prefix) !== 0) {
        return;
    }

    $file = __DIR__ . '/../' . $path;
    if (is_file($file)) {
        @unlink($file);
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $old = $p->prepare('SELECT image FROM products WHERE id=?');
    $old->execute([$id]);
    $oldImage = $old->fetchColumn();

    $p->prepare('UPDATE products SET active=0 WHERE id=?')->execute([$id]);
    deleteProductImage($oldImage);
    $msg = 'Product removed.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($name === '') {
        $error = 'Product name is required.';
    } elseif ($price < 0) {
        $error = 'Price cannot be negative.';
    } elseif ($stock < 0) {
        $error = 'Stock cannot be negative.';
    } else {
        try {
            $currentImage = 'assets/placeholder.svg';
            if ($id) {
                $s = $p->prepare('SELECT image FROM products WHERE id=?');
                $s->execute([$id]);
                $currentImage = $s->fetchColumn() ?: $currentImage;
            }

            $newImage = uploadProductImage($_FILES['image_file'] ?? [], $uploadDir, $uploadWebPath);
            $image = $newImage ?: $currentImage;

            if ($id) {
                $p->prepare('UPDATE products SET name=?,description=?,price=?,stock=?,image=?,active=1 WHERE id=?')
                    ->execute([$name, $description, $price, $stock, $image, $id]);
            } else {
                $p->prepare('INSERT INTO products(name,description,price,stock,image,active) VALUES(?,?,?,?,?,1)')
                    ->execute([$name, $description, $price, $stock, $image]);
            }

            if ($newImage && $id && $currentImage !== $newImage) {
                deleteProductImage($currentImage);
            }

            $msg = $id ? 'Product updated successfully.' : 'Product added successfully.';
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $s = $p->prepare('SELECT * FROM products WHERE id=?');
    $s->execute([(int)$_GET['edit']]);
    $edit = $s->fetch();
}

$ps = $p->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
$title = 'Manage Products - ' . APP_NAME;
include '../includes/header.php';
?>
<div class="page-heading">
    <div>
        <h1>Products</h1>
        <p>Manage your store inventory and product listings.</p>
    </div>
</div>

<?php if ($msg): ?>
<div class="notice success" style="padding:12px;border-radius:9px;background:#dcfce7;color:#166534;margin-bottom:18px">
    <?=htmlspecialchars($msg)?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="notice" style="padding:12px;border-radius:9px;background:#fee2e2;color:#991b1b;margin-bottom:18px">
    <?=htmlspecialchars($error)?>
</div>
<?php endif; ?>

<form class="admin-form" method="post" enctype="multipart/form-data">
    <div class="admin-form-grid">
        <input type="hidden" name="id" value="<?=htmlspecialchars($edit['id'] ?? 0)?>">

        <div class="admin-field">
            <label>Name</label>
            <input name="name" required value="<?=htmlspecialchars($edit['name'] ?? '')?>" placeholder="e.g. Wireless Headphones">
        </div>

        <div class="admin-field">
            <label>Price (KES)</label>
            <input type="number" step="0.01" min="0" name="price" required value="<?=htmlspecialchars($edit['price'] ?? '')?>" placeholder="2500">
        </div>

        <div class="admin-field full">
            <label>Description</label>
            <textarea name="description" placeholder="Describe the product..."><?=htmlspecialchars($edit['description'] ?? '')?></textarea>
        </div>

        <div class="admin-field">
            <label>Stock</label>
            <input type="number" min="0" name="stock" required value="<?=htmlspecialchars($edit['stock'] ?? 0)?>" placeholder="10">
        </div>

        <div class="admin-field">
            <label>Product Image</label>
            <input id="image_file" type="file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp">
            <small class="field-help">JPG, PNG, GIF or WebP · Maximum 5 MB</small>
            <div class="image-upload-preview">
                <img id="imagePreview" src="../<?=htmlspecialchars($edit['image'] ?? 'assets/placeholder.svg')?>" alt="Product image preview">
                <div>
                    <strong><?= $edit ? 'Replace product image' : 'Choose a product image' ?></strong>
                    <span>Use a clear photo with a simple background.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-actions">
        <button class="admin-btn" type="submit"><?= $edit ? 'Update' : 'Add' ?> Product</button>
        <?php if ($edit): ?><a class="admin-btn secondary" href="products.php">Cancel</a><?php endif; ?>
    </div>
</form>

<div class="table-wrap">
<table class="admin-table">
<thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($ps as $x): ?>
<tr>
    <td>
        <div class="product-cell">
            <img src="../<?=htmlspecialchars($x['image'] ?: 'assets/placeholder.svg')?>" alt="<?=htmlspecialchars($x['name'])?>">
            <div><strong><?=htmlspecialchars($x['name'])?></strong><small><?=htmlspecialchars(mb_strimwidth($x['description'] ?? '', 0, 55, '…'))?></small></div>
        </div>
    </td>
    <td>KES <?=number_format($x['price'], 2)?></td>
    <td><?=$x['stock']?></td>
    <td><span class="badge <?=$x['active'] ? 'badge-green' : 'badge-red'?>"><?=$x['active'] ? 'Active' : 'Inactive'?></span></td>
    <td>
        <a class="admin-btn secondary" style="padding:7px 10px" href="?edit=<?=$x['id']?>">Edit</a>
        <a class="admin-btn danger" style="padding:7px 10px" href="?delete=<?=$x['id']?>" onclick="return confirm('Remove product?')">Remove</a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<script>
document.getElementById('image_file')?.addEventListener('change', function () {
    const file = this.files && this.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) return;
    const preview = document.getElementById('imagePreview');
    preview.src = URL.createObjectURL(file);
});
</script>
<?php include '../includes/footer.php'; ?>
