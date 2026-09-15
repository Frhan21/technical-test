<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Product<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Edit Product</h1>
</div>

<div class="panel form-card">
    <form action="<?= base_url('products/update/' . $product['id']) ?>" method="post">
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label for="product_name">Name</label>
            <input type="text" name="product_name" id="product_name" class="form-control" value="<?= old('product_name', $product['product_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="<?= old('price', $product['price']) ?>" min="0" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="qty_in_stock">Stock</label>
            <input type="number" name="qty_in_stock" id="qty_in_stock" class="form-control" value="<?= old('qty_in_stock', $product['qty_in_stock']) ?>" min="0" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('products') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
