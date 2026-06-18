<?php

/** @var array $response */

// echo $response['message'];
// var_dump($response['results']['rows']);
var_dump($_POST);

$submitName = isset($_GET['rate_id']) ? 'edit' : 'add';
$submitText = isset($_GET['rate_id']) ? 'Edit' : 'Add';

// THIS IS FOR EDIT MODE
$isEdit = isset($_GET['rate_id']) ?? false;
$row = $isEdit
    ? $response['results']['rows'][0]
    : null;
$vehicle_type_id = $row['vehicle_type_id'] ?? null;
$vehicle_type = $row['vehicle_type'] ?? null;
$hours_id = $row['hours_id'] ?? null;
$hours = $row['hours'] ?? null;
$fee = $row['fee'] ?? null;

$actionURL = $isEdit ? "admin/rates/edit?rate_id=" . $_GET['rate_id'] : "admin/rates/add";

?>

<h1><?= $isEdit ? 'Edit' : 'Add' ?> Rate</h1>
<form action="<?= APP_URL . $actionURL ?>" method="post">
    <div class="field">
        <span>Vehicle Type:</span>
        <?php
        if ($isEdit) { // EDIT
        ?>
            <input type="hidden" name="type" id="" value="<?= $vehicle_type_id ?>">
            <input type="text" name="" id="" value="<?= $vehicle_type ?>" readonly>
        <?php
        } else { // ADD
        ?>
            <select name="type" id="">
                <?php
                $rows = VehicleModel::getInstance()->getAllVehicleTypes();
                $types = $rows['results']['rows'];

                foreach ($types as $type) {
                ?>
                    <option value="<?= $type['VEHICLE_TYPE_ID'] ?>"><?= $type['VEHICLE_TYPE'] ?></option>
                <?php
                }
                ?>
            </select>
        <?php
        }
        ?>
    </div>
    <div class="field">
        <span>Hours</span>
        <?php
        if ($isEdit) { // EDIT
        ?>
            <input type="hidden" name="hours" id="" value="<?= $hours_id ?>">
            <input type="text" name="" id="" value="<?= $hours ?>" readonly>
        <?php
        } else { // ADD
        ?>
            <select name="hours" id="">
                <?php
                $rows = HoursModel::getInstance()->getHours();
                $hours = $rows['results']['rows'];

                foreach ($hours as $hour) {
                    if ($hours_id == $hour['hours_id']) echo 'selected';
                ?>
                    <option value="<?= $hour['hours_id'] ?>" <?php if ($hours_id == $hour['hours_id']) echo 'selected' ?>><?= $hour['hours'] ?></option>
                <?php
                }
                ?>
            </select>
        <?php
        }
        ?>
    </div>
    <label class="field"><span>Fee: </span><input type="text" name="fee" value="<?php if ($isEdit) echo $fee ?>"></label>
    <button type="reset" name="reset">Reset</button>
    <button type="submit" name="<?= $submitName ?>"><?= $submitText ?></button>
</form>