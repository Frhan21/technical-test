<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Tambah User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Tambah User</h1>
</div>

<div class="panel form-card">
    <form action="<?= base_url('users/store') ?>" method="post">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= old('name') ?>" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
