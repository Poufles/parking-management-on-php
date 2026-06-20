<?php

$vehiclesData = VehicleModel::getInstance()->searchVehicles('uid', $_SESSION['uid']);
$vehicles = $vehiclesData['results']['rows'];

?>
<h4 class="page-title">Park In</h4>

<div class="content-container">
    <div class="content slot-box">
        <div class="slot">
            <p id="slot-information"><?= $_GET['level'] . ' - ' . $_GET['section'] . $_GET['place'] ?></p>
            <p id="status">Available</p>
        </div>
    </div>
    <div class="content">
        <h4 class="title">
            Select a valid car
        </h4>
        <ul>
            <li>A valid vehicle is a vehicle with a document</li>
        </ul>
        <form action="<?= APP_URL . 'client/parking-slots/park-in?slot_id=' . $_GET['slot_id'] . '&level=' . $_GET['level'] . '&section=' . $_GET['section'] . '&place=' . $_GET['place'] ?>" method="post">
            <?php if (count($vehicles) != 0): ?>
                <select class="form-select" name="vehicle-id" aria-label="vehicle">
                    <?php
                    foreach ($vehicles as $vehicle) :
                        $isParked = ParkingModel::getInstance()->isVehicleAlreadyParked($vehicle['vehicle_id']);
                        if (!$isParked) :
                    ?>
                            <option value="<?= $vehicle['vehicle_id'] ?>"><?= $vehicle['plate_number'] ?></option>
                    <?php
                        endif;
                    endforeach
                    ?>
                </select>
            <?php else: ?>
                <input class="form-control" id="empty-set" type="text" value="You have no vehicles..." aria-label="empty-set" disabled readonly>
            <?php endif ?>
            <button type="submit" name="park-in" class="btn btn-success" <?php if (count($vehicles) == 0) echo 'disabled' ?>>Park in</button>
        </form>
    </div>
</div>