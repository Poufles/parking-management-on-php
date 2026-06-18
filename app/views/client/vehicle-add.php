<?php
/** @var array $response */

$rows_vehicle_types = VehicleModel::getInstance()->getAllVehicleTypes();
$rows = $rows_vehicle_types['results']['rows'];
?>

<h1>Add Vehicle</h1>
<form action="<?= APP_URL . "client/vehicles/add" ?>" method="post" enctype="multipart/form-data">
    <label class="field"><span>Plate Number</span><input type="text" name="plate-number"></label>
    <label class="field">
        <select name="vehicle-type" id="">
            <?php
            foreach ($rows as $row) {
            ?>
                <option value="<?= $row['VEHICLE_TYPE_ID'] ?>"><?= $row['VEHICLE_TYPE'] ?></option>
            <?php
            }
            ?>
        </select>
    </label>
    <label class="field"><span>File</span><input type="file" name="vehicle-document"></label>
    <button type="submit" name="add">Add</button>
    <button type="reset" name="reset">Reset</button>
</form>