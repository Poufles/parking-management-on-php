<?php

/** @var array $response */

$vehicles = VehicleModel::getInstance()->searchVehicles('username', $_SESSION['username']);

$totalPages = $vehicles['results']['totalPages'];
$currentPage = $_GET['page'] ?? '1';

$vehicleLimit = VehicleModel::getInstance()->checkClientVehicleLimit($_SESSION['uid']);
$isLimit = $vehicleLimit['results']['isLimit'];

$vehicle_types_row = VehicleModel::getInstance()->getAllVehicleTypes(false);
$vehicle_types = $vehicle_types_row['results']['rows'];

$results = $response['results'] ?? null;
$plateNumberValidation = $results['plate_number'] ?? null;
$vehicleTypeIDValidation = $results['vehicle_type_id'] ?? null;
$vehicleDocumentValidation = $results['vehicle_document'] ?? null;
?>

<h4 class="page-title">Manage Vehicles</h4>

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
                            <a href="<?= APP_URL . "client/vehicles/view?vehicle_id=" . $row['vehicle_id'] ?>" target="_blank" rel="noopener noreferrer">View Document</a> ||
                            <a href="<?= APP_URL . "client/vehicles?delete_vehicle_id=" . $row['vehicle_id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    if (!$isLimit) {
    ?>
        <div class="content crud">
            <h4>Add New Vehicle</h4>
            <div id="passwordHelpBlock" class="form-text">
                <ul>
                    <li>You can only add 3 vehicles</li>
                    <li>Plate number format: ABC 123(4)</li>
                    <li>Vehicle Document is required</li>
                </ul>
            </div>
            <form action="<?= APP_URL . "client/vehicles" ?>" method="post" enctype="multipart/form-data">
                <div class="input-group has-validation mb-3">
                    <div class="form-floating <?php if (isset($plateNumberValidation) && !$plateNumberValidation['status']) echo 'is-invalid'; ?>">
                        <input type="text" class="form-control" id="input-plate-number"
                            placeholder="Jeffrex" name="plate-number" value="<?= $_POST['plate-number'] ?? null ?>">
                        <label for="input-plate-number">Plate Number</label>
                    </div>
                    <div class="invalid-feedback">
                        <?= $plateNumberValidation['message'] ?? null ?>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" id="vehicle-type-id" name="vehicle-type-id">
                        <option value="default" <?= isset($_POST['vehicle-type-id']) ? null : 'selected' ?> disabled>Select vehicle type</option>
                        <?php
                        foreach ($vehicle_types as $type) {
                        ?>
                            <option value="<?= $type['vehicle_type_id'] ?>" <?php if (isset($_POST['vehicle-type-id']) && $_POST['vehicle-type-id'] == $type['vehicle_type_id']) echo 'selected'; ?>><?= $type['vehicle_type'] ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <label for="gender">Vehicle Type</label>
                </div>
                <div class="mb-3">
                    <label for="formFile" class="form-label">Vehicle Document</label>
                    <input class="form-control" name="vehicle-document" type="file" id="formFile">
                </div>
                <div class="actions-container">
                    <button type="submit" name="add" class="btn btn-success">Add Vehicle</button>
                    <button type="reset" name="reset" class="btn">Reset</button>
                </div>
            </form>
        </div>
    <?php
    }
    ?>
</section>