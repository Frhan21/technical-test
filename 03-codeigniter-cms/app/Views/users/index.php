<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <h1>Users</h1>
    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">+ Tambah User</a>
</div>

<div class="panel">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="empty-state">Belum ada data user.</td>
                </tr>
            <?php endif; ?>
            <?php $no = 1;
            foreach ($users as $user): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($user['name']) ?></td>
                    <td><?= esc($user['created_at']) ?></td>
                    <td><?= esc($user['updated_at']) ?></td>
                    <td>
                        <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-edit">Edit</a>
                        <form action="<?= base_url('users/delete/' . $user['id']) ?>" method="post" style="display:inline" onsubmit="return confirm('Hapus user ini?')">
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