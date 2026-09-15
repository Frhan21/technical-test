<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Transactions<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Transactions</h1>
    <a href="<?= base_url('transaction/create') ?>" class="btn btn-primary">+ Tambah Transaction</a>
</div>

<div class="panel">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="8" class="empty-state">Belum ada data transaction.</td></tr>
            <?php endif; ?>
            <?php $no = 1; foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($transaction['user_name']) ?></td>
                    <td><?= esc($transaction['product_name']) ?></td>
                    <td><?= esc($transaction['qty']) ?></td>
                    <td>Rp <?= number_format($transaction['qty'] * $transaction['product_price'], 0, ',', '.') ?></td>
                    <td><span class="badge"><?= esc($transaction['payment_method']) ?></span></td>
                    <td><?= esc($transaction['created_at']) ?></td>
                    <td>
                        <a href="<?= base_url('transaction/edit/' . $transaction['id']) ?>" class="btn btn-sm btn-edit">Edit</a>
                        <form action="<?= base_url('transaction/delete/' . $transaction['id']) ?>" method="post" style="display:inline" onsubmit="return confirm('Hapus transaction ini?')">
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
