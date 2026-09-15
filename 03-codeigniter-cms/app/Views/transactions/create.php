<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Tambah Transaction<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Tambah Transaction</h1>
</div>

<div class="panel form-card">
    <form action="<?= base_url('transaction/store') ?>" method="post">
        <div class="form-group">
            <label for="user_id">User</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Pilih User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                        <?= esc($user['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">-- Pilih Product --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>" <?= old('product_id') == $product['id'] ? 'selected' : '' ?>>
                        <?= esc($product['product_name']) ?> - Rp <?= number_format($product['price'], 0, ',', '.') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="qty">Qty</label>
            <input type="number" name="qty" id="qty" class="form-control" value="<?= old('qty') ?>" min="1" required>
        </div>
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="">-- Pilih Metode --</option>
                <option value="cash" <?= old('payment_method') === 'cash' ? 'selected' : '' ?>>Cash</option>
                <option value="transfer" <?= old('payment_method') === 'transfer' ? 'selected' : '' ?>>Transfer</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('transaction') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
