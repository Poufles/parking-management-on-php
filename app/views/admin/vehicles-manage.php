<?php

/** @var array $response */

$types = VehicleModel::getInstance()->getAllVehicleTypes();
$rows = $types['results']['rows'];

$vehicleTypesData = VehicleModel::getInstance()->getAllVehicleTypes();
$vehicleTypes = $vehicleTypesData['results']['rows'];

?>

<h4 class="page-title">Vehicles Manage</h4>

<?php if (isset($_POST['delete-error'])) : ?>
    <section class="content-container error mb-3">
        <div class="content error">
            <p class="mb-0">Rate fee can't be deleted: Make sure no vehicles are parked !</p>
            <button type="button" id="close">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000">
                    <path d="m336-280-56-56 144-144-144-143 56-56 144 144 143-144 56 56-144 143 144 144-56 56-143-144-144 144Z" />
                </svg>
                <span>Close</span>
            </button>
        </div>
    </section>
<?php endif; ?>
<section class="content-container">
    <div class="content table">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vehicle Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($rows as $index => $row) {
                ?>
                    <tr class="">
                        <td><?= $index + 1 ?></td>
                        <td><?= $row['VEHICLE_TYPE'] ?></td>
                        <td>
                            <form action="<?= APP_URL . 'admin/vehicles' ?>" method="post" id="delete-form">
                                <input type="hidden" name="vehicle_type_id" value="<?= $row['VEHICLE_TYPE_ID'] ?>">
                                <button type="submit" name="delete">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="content crud">
        <?php if (isset($_GET['edit'])) : ?>
            <a href="<?= APP_URL . 'admin/rates' ?>" id="return">
                <span>Back</span>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000">
                    <path d="M560-280 360-480l200-200v400Z" />
                </svg>
            </a>
        <?php endif; ?>
        <?php if (!isset($_GET['edit'])) : ?>
            <h4 class="mb-3">Create Vehicle Type</h4>
        <?php else : ?>
            <h4 class="mb-3">Edit Vehicle Type</h4>
        <?php endif; ?>
        <?php if (!isset($_GET['edit'])) : ?>
            <form action="<?= APP_URL . "admin/vehicles" ?>" method="post">
            <?php else : ?>
                <form action="<?= APP_URL . "admin/vehicles?edit=true&id=" . urlencode($_GET['id']) ?>" method="post">
                <?php endif; ?>
                <div class="input-group has-validation mb-3">
                    <div class="form-floating <?php if (isset($response) && !$response['status']) echo 'is-invalid'; ?>">
                        <input type="text" class="form-control" id="input-vehicle"
                            placeholder="" name="vehicle">
                        <label for="input-vehicle">Vehicle Name</label>
                    </div>
                    <div class="invalid-feedback">
                        <?= $response['message'] ?? null ?>
                    </div>
                </div>
                <div class="actions-container">
                    <?php if (!isset($_GET['edit'])) : ?>
                        <button type="submit" name="create" class="btn btn-success">Create Vehicle</button>
                    <?php else : ?>
                        <button type="submit" name="edit" class="btn btn-success">Edit Vehicle</button>
                    <?php endif; ?>
                    <button type="reset" name="reset" class="btn">Reset</button>
                </div>
                </form>
    </div>
</section>

<?php

$currentPage = $_GET['page'] ?? '1';

$vehicles = VehicleModel::getInstance()->searchVehicles('', '', $currentPage);

$totalPages = $vehicles['results']['totalPages'];

$vehicleLimit = VehicleModel::getInstance()->checkClientVehicleLimit($_SESSION['uid']);
$isLimit = $vehicleLimit['results']['isLimit'];

$vehicle_types_row = VehicleModel::getInstance()->getAllVehicleTypes(false);
$vehicle_types = $vehicle_types_row['results']['rows'];

$results = $response['results'] ?? null;
$plateNumberValidation = $results['plate_number'] ?? null;
$vehicleTypeIDValidation = $results['vehicle_type_id'] ?? null;
$vehicleDocumentValidation = $results['vehicle_document'] ?? null;
?>

<section class="content-container divide">
    <div class="content table">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Plate Number</th>
                    <th>Vehicle Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($vehicles['results']['rows'] as $index => $row) {
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['plate_number'] ?></td>
                        <td><?= $row['vehicle_type'] ?></td>
                        <td>
                            <a href="<?= APP_URL . "client/vehicles/view?vehicle_id=" . $row['vehicle_id'] ?>" target="_blank" rel="noopener noreferrer">View Document</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php
                    $prevDisabled = $currentPage <= 1 ? 'd-none' : '';
                    ?>
                    <li class="page-item <?= $prevDisabled ?>">
                        <a class="page-link" href="<?= '?page=' . $currentPage - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <li class="page-item">
                        <p class="page-link"><?= $currentPage ?></a>
                    </li>

                    <?php
                    $nextDisabled = $currentPage >= $totalPages ? 'd-none' : '';
                    ?>
                    <li class="page-item <?= $nextDisabled ?>">
                        <a class="page-link" href="<?= '?page=' . $currentPage + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>