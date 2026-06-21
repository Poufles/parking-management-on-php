<?php
/** @var array $response */
$rows   = $response['results']['rows'] ?? [];
$editId = $_GET['edit_id'] ?? null;
$error  = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<h4 class="page-title">Vehicle Types</h4>

<?php if ($error): ?>
    <div class="alert alert-danger" style="margin-top:16px;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success" style="margin-top:16px;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-top:20px;">

    <!-- Left: List -->
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden;">
        <table class="table table-hover mb-0">
            <thead style="background:#283618; color:#fff;">
                <tr>
                    <th style="padding:14px 16px;">#</th>
                    <th style="padding:14px 16px;">Vehicle Type</th>
                    <th style="padding:14px 16px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">No vehicle types yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $index => $row): ?>
                        <tr <?= $editId == $row['VEHICLE_TYPE_ID'] ? 'style="background:#fffbea;"' : '' ?>>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['VEHICLE_TYPE']) ?></td>
                            <td style="display:flex; gap:6px;">
                                <a href="<?= APP_URL ?>admin/vehicles?edit_id=<?= $row['VEHICLE_TYPE_ID'] ?>"
                                   class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= APP_URL ?>admin/vehicles?delete_id=<?= $row['VEHICLE_TYPE_ID'] ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete \'<?= htmlspecialchars($row['VEHICLE_TYPE']) ?>\'? This will fail if vehicles are still using this type.')">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:24px;">

        <?php if ($editId): ?>
            <?php
                $editing = null;
                foreach ($rows as $row) {
                    if ($row['VEHICLE_TYPE_ID'] == $editId) {
                        $editing = $row;
                        break;
                    }
                }
            ?>
            <h5 style="margin-bottom:16px; color:#283618;">Edit Vehicle Type</h5>
            <form method="POST" action="<?= APP_URL ?>admin/vehicles">
                <input type="hidden" name="edit_id" value="<?= $editId ?>">
                <div class="mb-3">
                    <label class="form-label">Vehicle Type Name</label>
                    <input type="text" name="vehicle_type" class="form-control"
                           value="<?= htmlspecialchars($editing['VEHICLE_TYPE'] ?? '') ?>" required>
                    <div class="form-text">Letters only (e.g. Sedan, Motorcycle, SUV)</div>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-warning">Save Changes</button>
                    <a href="<?= APP_URL ?>admin/vehicles" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>

        <?php else: ?>
            <h5 style="margin-bottom:16px; color:#283618;">Add New Vehicle Type</h5>
            <form method="POST" action="<?= APP_URL ?>admin/vehicles/create">
                <div class="mb-3">
                    <label class="form-label">Vehicle Type Name</label>
                    <input type="text" name="vehicle-type" class="form-control"
                           placeholder="e.g. Motorcycle" required>
                    <div class="form-text">Letters only (e.g. Sedan, Motorcycle, SUV)</div>
                </div>
                <button type="submit" class="btn btn-success">Add Vehicle Type</button>
            </form>
        <?php endif; ?>

    </div>

</div>