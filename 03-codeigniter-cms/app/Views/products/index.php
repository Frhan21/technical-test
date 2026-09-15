<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Products<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Products</h1>
    <a href="<?= base_url('products/create') ?>" class="btn btn-primary">+ Tambah Product</a>
</div>

<div class="panel">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="6" class="empty-state">Belum ada data product.</td></tr>
            <?php endif; ?>
            <?php $no = 1; foreach ($products as $product): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($product['product_name']) ?></td>
                    <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                    <td><?= esc($product['qty_in_stock']) ?></td>
                    <td><?= esc($product['created_at']) ?></td>
                    <td>
                        <a href="<?= base_url('products/edit/' . $product['id']) ?>" class="btn btn-sm btn-edit">Edit</a>
                        <form action="<?= base_url('products/delete/' . $product['id']) ?>" method="post" style="display:inline" onsubmit="return confirm('Hapus product ini?')">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
