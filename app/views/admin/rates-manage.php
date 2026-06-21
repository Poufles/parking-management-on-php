<?php

/** @var array $response */

$currentPage = $_GET['page'] ?? '1';

$rates = RateModel::getInstance()->searchRates('', '', $currentPage);

$totalPages = $rates['results']['totalPages'];

if ($totalPages != 0 && $currentPage > $totalPages || $currentPage < 1) {
    header('location: ' . APP_URL . 'admin/rates?page=1');
    exit;
};

$vehicleTypesData = VehicleModel::getInstance()->getAllVehicleTypes();
$vehicleTypes = $vehicleTypesData['results']['rows'];

?>

<h4 class="page-title">Manage Rates</h4>

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
                    <th>Hours</th>
                    <th>Vehicle Type</th>
                    <th>Fee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($rates['results']['rows'] as $index => $row) {
                ?>
                    <tr class="">
                        <td><?= $index + 1 ?></td>
                        <td><?= $row['hours'] ?></td>
                        <td><?= $row['vehicle_type'] ?></td>
                        <td><?= $row['fee'] ?></td>
                        <td>
                            <a href="<?= APP_URL . "admin/rates?edit=true&rate_id=" . urlencode($row['rate_id']) ?>">Edit</a> |
                            <form action="<?= APP_URL . 'admin/rates' ?>" method="post" id="delete-form">
                                <input type="hidden" name="rate-id" value="<?= $row['rate_id'] ?>">
                                <input type="hidden" name="vehicle_type_id" value="<?= $row['vehicle_type_id'] ?>">
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
            <h4 class="mb-3">Create new rates</h4>
        <?php else : ?>
            <h4 class="mb-3">Edit Rate</h4>
        <?php endif; ?>
        <?php if (!isset($_GET['edit'])) : ?>
            <form action="<?= APP_URL . "admin/rates" ?>" method="post">
            <?php else : ?>
                <form action="<?= APP_URL . "admin/rates?edit=true&rate_id=" . urlencode($_GET['rate_id']) ?>" method="post">
                <?php endif; ?>
                <div class="mb-3" id="hours">
                    <label for="">Choose an hour</label>
                    <div class="radio-options">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="1" name="hours-id" id="12-hrs" checked>
                            <label class="form-check-label" for="12-hrs">
                                12 Hours
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" value="2" name="hours-id" id="24-hrs">
                            <label class="form-check-label" for="24-hrs">
                                24 Hours
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" id="vehicle-type-id" name="vehicle-type-id">
                        <?php foreach ($vehicleTypes as $index => $vehicle) : ?>
                            <option value="<?= $vehicle['VEHICLE_TYPE_ID'] ?>" <?php if ($index == 0) echo 'selected'; ?>><?= $vehicle['VEHICLE_TYPE'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="vehicle-type-id">Vehicle Type</label>
                </div>
                <div class="input-group has-validation mb-3">
                    <div class="form-floating <?php if (isset($response) && !$response['status']) echo 'is-invalid'; ?>">
                        <input type="text" class="form-control" id="input-fee"
                            placeholder="" name="fee">
                        <label for="input-fee">Rate Fee</label>
                    </div>
                    <div class="invalid-feedback">
                        <?= $response['message'] ?? null ?>
                    </div>
                </div>
                <div class="actions-container">
                    <?php if (!isset($_GET['edit'])) : ?>
                        <button type="submit" name="create" class="btn btn-success">Create Rate</button>
                    <?php else : ?>
                        <button type="submit" name="edit" class="btn btn-success">Edit Rate</button>
                    <?php endif; ?>
                    <button type="reset" name="reset" class="btn">Reset</button>
                </div>
                </form>
    </div>
</section>