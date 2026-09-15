<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Edit User</h1>
</div>

<div class="panel form-card">
    <form action="<?= base_url('users/update/' . $user['id']) ?>" method="post">
        <input type="hidden" name="_method" value="PUT">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $user['name']) ?>" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('users') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
