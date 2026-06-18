<h1>Create Vehicle Type</h1>
<form action="<?= APP_URL . "admin/vehicles/create" ?>" method="post">
    <label class="field"><span>New Vehicle Type: </span><input type="text" name="vehicle-type"></label>
    <button type="reset" name="reset">Reset</button>
    <button type="submit" name="add">Add</button>
</form>