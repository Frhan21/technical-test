<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Dashboard</h1>
</div>

<div class="cards">
    <a href="<?= base_url('users') ?>" class="card">
        <div class="card-label">Jumlah Pengguna</div>
        <div class="card-value"><?= esc($users) ?></div>
    </a>
    <a href="<?= base_url('products') ?>" class="card">
        <div class="card-label">Jumlah Produk</div>
        <div class="card-value"><?= esc($products) ?></div>
    </a>
    <a href="<?= base_url('transaction') ?>" class="card">
        <div class="card-label">Jumlah Transaksi</div>
        <div class="card-value"><?= esc($transactions) ?></div>
    </a>
</div>
<?= $this->endSection() ?>