<?php
/** @var array $response */

$totalPages = $response['results']['totalPages'];
$currentPage = $_GET['page'] ?? '1';
?>

<h1>Manage Rates</h1>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
<?php endif; ?>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Hours</th>
            <th>Vehicle Type</th>
            <th>Fee</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach($response['results']['rows'] as $index => $row) {
        ?>
        <tr class="">
            <td><?= $index + 1 ?></td>
            <td><?= $row['hours'] ?></td>
            <td><?= $row['vehicle_type'] ?></td>
            <td><?= $row['fee'] ?></td>
            <td><a href="<?= APP_URL . "admin/rates/edit?rate_id=" . urlencode($row['rate_id']) ?>">Edit</a> | <a href="<?= APP_URL . "admin/rates/delete?rate_id=" . urlencode($row['rate_id']) ?>" onclick="return confirm('Are you sure you want to delete this rate?');">Delete</a></td>
        </tr>
        <?php 
        }
        ?>
    </tbody>
</table>