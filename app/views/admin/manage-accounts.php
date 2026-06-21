<?php
/** @var array $response */
$rows = $response['results']['rows'] ?? [];
$totalPages = $response['results']['totalPages'] ?? 1;
$currentPage = $_GET['p'] ?? 1;
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';
?>

<h4 class="page-title">Manage Accounts</h4>

<form method="GET" action="<?= APP_URL ?>admin/manage-accounts" style="display:flex; gap:10px; margin-top:20px; margin-bottom:20px; flex-wrap:wrap;">
    <select name="filter" class="form-select" style="width:160px;">
        <option value="" <?= $filter === '' ? 'selected' : '' ?>>All Fields</option>
        <option value="name" <?= $filter === 'name' ? 'selected' : '' ?>>Name</option>
        <option value="username" <?= $filter === 'username' ? 'selected' : '' ?>>Username</option>
        <option value="email_address" <?= $filter === 'email_address' ? 'selected' : '' ?>>Email</option>
        <option value="phone" <?= $filter === 'phone' ? 'selected' : '' ?>>Phone</option>
    </select>
    <input type="text" name="search" class="form-control" style="width:220px;" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn btn-secondary">Search</button>
    <?php if ($search): ?>
        <a href="<?= APP_URL ?>admin/manage-accounts" class="btn btn-outline-secondary">Clear</a>
    <?php endif; ?>
</form>

<div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden;">
    <table class="table table-hover mb-0">
        <thead style="background:#283618; color:#fff;">
            <tr>
                <th style="padding:14px 16px;">#</th>
                <th style="padding:14px 16px;">Name</th>
                <th style="padding:14px 16px;">Username</th>
                <th style="padding:14px 16px;">Email</th>
                <th style="padding:14px 16px;">Phone</th>
                <th style="padding:14px 16px;">Gender</th>
                <th style="padding:14px 16px;">Type</th>
                <th style="padding:14px 16px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No accounts found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $index => $row): ?>
                    <tr>
                        <td><?= ($currentPage - 1) * 10 + $index + 1 ?></td>
                        <td><?= htmlspecialchars($row['NAME']) ?></td>
                        <td><?= htmlspecialchars($row['USERNAME']) ?></td>
                        <td><?= htmlspecialchars($row['EMAIL_ADDRESS']) ?></td>
                        <td><?= htmlspecialchars($row['PHONE']) ?></td>
                        <td><?= ucfirst($row['GENDER']) ?></td>
                        <td>
                            <span class="badge <?= $row['ACCOUNT_TYPE'] === 'admin' ? 'bg-danger' : 'bg-success' ?>">
                                <?= ucfirst($row['ACCOUNT_TYPE']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= APP_URL ?>admin/manage-accounts?delete_uid=<?= $row['UID'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($row['NAME']) ?>?')">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div style="display:flex; justify-content:center; gap:8px; margin-top:20px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?p=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= urlencode($filter) ?>"
           class="btn btn-sm <?= $i == $currentPage ? 'btn-success' : 'btn-outline-secondary' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>