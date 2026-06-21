<?php
$availableFiltered = array_values(array_filter($filteredSlots, fn($slot) => $slot['status'] === 'available'));
$defaultSelectedSlot = $availableFiltered[0] ?? null;
$parkedVehicleIds = array_map(
    fn($row) => (int) $row['vehicle_id'],
    array_filter($history, fn($row) => empty($row['exit_time']))
);
$availableVehicles = array_values(
    array_filter($vehicles, fn($vehicle) => !in_array((int) $vehicle['id'], $parkedVehicleIds, true))
);
$defaultVehicleId = $availableVehicles ? (int) $availableVehicles[0]['id'] : null;
?>

<?php if ($parkStep === 'start') { ?>
    <section class="card park-start-card">
        <h2>Find Your Parking Space</h2>
        <a class="btn btn-primary" href="?page=park&step=slots&level=1&category=A">View Slots</a>
    </section>
<?php } else { ?>
    <section class="card">
        <div class="slot-toolbar">
            <div>
                <h2>Choose an Available Slot</h2>
            </div>
            <form method="get">
                <input type="hidden" name="page" value="park">
                <input type="hidden" name="step" value="slots">
                <label>
                    Level
                    <select class="input" name="level">
                        <?php for ($i = 1; $i <= 3; $i++) { ?>
                            <option value="<?= $i ?>" <?= $level === (string) $i ? 'selected' : '' ?>>
                                Level <?= $i ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>
                <label>
                    Category
                    <select class="input" name="category">
                        <?php foreach (['A','B','C'] as $cat) { ?>
                            <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                Category <?= $cat ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>
                <button class="btn btn-dark" type="submit">Apply</button>
            </form>
        </div>
        <div class="level-tabs">
            <?php for ($i = 1; $i <= 3; $i++) { ?>
                <a
                    class="<?= $level === (string) $i ? 'active' : '' ?>"
                    href="?page=park&step=slots&level=<?= $i ?>&category=<?= $category ?>"
                >
                    Level <?= $i ?>
                </a>
            <?php } ?>
        </div>
        <div class="category-tabs">
            <?php foreach (['A','B','C'] as $cat) { ?>
                <a
                    class="<?= $category === $cat ? 'active' : '' ?>"
                    href="?page=park&step=slots&level=<?= $level ?>&category=<?= $cat ?>"
                >
                    Category <?= $cat ?>
                </a>
            <?php } ?>
        </div>
        <div class="parking-legend">
            <span><i class="legend-dot available-dot"></i>Available</span>
            <span><i class="legend-dot occupied-dot"></i>Occupied</span>
            <strong>Select one slot below</strong>
        </div>
        <?php if (!$hasLicense) { ?>
            <div class="alert alert-error">Please upload your license first.</div>
            <a class="btn btn-primary" href="?page=settings#client-account">Upload</a>
        <?php } elseif (!$vehicles) { ?>
            <div class="alert alert-error">Add a vehicle first before parking.</div>
        <?php } elseif (!$availableVehicles) { ?>
            <div class="alert alert-error">All registered vehicles are currently parked.</div>
        <?php } elseif (!$availableFiltered) { ?>
            <div class="alert alert-error">No available slot in this category.</div>
        <?php } else { ?>
            <form method="post">
                <input type="hidden" name="csrf" value="<?= csrf() ?>">
                <input type="hidden" name="action" value="park_in">
                <div class="parking-map selectable-map">
                    <?php foreach ($filteredSlots as $slot) { ?>
                        <?php
                        $occupied = $slot['status'] === 'occupied';
                        $isMine = $occupied && (int) $slot['occupied_owner_id'] === $userId;
                        $displayCode = str_replace('-', '_', $slot['slot_code']);
                        $isSelected = !$occupied
                            && $defaultSelectedSlot
                            && (int) $slot['id'] === (int) $defaultSelectedSlot['id'];
                        $bayClass = $occupied ? 'bay-occupied' : 'bay-available';
                        ?>
                        <label class="parking-bay <?= $bayClass ?>">
                            <?php if (!$occupied) { ?>
                                <input
                                    class="slot-radio"
                                    type="radio"
                                    name="slot_id"
                                    value="<?= (int) $slot['id'] ?>"
                                    <?= $isSelected ? 'checked' : '' ?>
                                    required
                                >
                            <?php } ?>

                            <?php if ($occupied) { ?>
                                <div class="occupied-label">Occupied</div>
                            <?php } else { ?>
                                <div class="parking-p">P</div>
                            <?php } ?>

                            <div class="bay-info">
                                <strong><?= e($displayCode) ?></strong>
                                <span><?= $occupied ? ($isMine ? e($account['full_name']) : 'Occupied') : 'Available' ?></span>
                                <small>
                                    <?=
                                        $occupied
                                            ? ($isMine ? 'Your occupied slot' : 'Occupied')
                                            : 'PHP ' . number_format((float) $slot['hourly_rate'], 0) . '/hr'
                                    ?>
                                </small>
                            </div>
                        </label>
                    <?php } ?>
                </div>

                <div class="form-grid park-form-grid">
                    <div class="form-group full">
                        <label>Your Vehicle</label>
                        <select class="input" name="vehicle_id" required>
                            <?php foreach ($vehicles as $vehicle) { ?>
                                <?php $isParkedVehicle = in_array((int) $vehicle['id'], $parkedVehicleIds, true); ?>
                                <option
                                    value="<?= $vehicle['id'] ?>"
                                    <?= $isParkedVehicle ? 'disabled' : '' ?>
                                    <?= (int) $vehicle['id'] === $defaultVehicleId ? 'selected' : '' ?>
                                >
                                    <?=
                                        e(
                                            $vehicle['plate_number']
                                            . ' - '
                                            . $vehicle['vehicle_type']
                                        )
                                    ?>
                                    <?= $isParkedVehicle ? ' (Currently parked)' : '' ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button
                        class="btn btn-primary full"
                        <?= $defaultSelectedSlot ? '' : 'disabled' ?>
                    >
                        Proceed to Park
                    </button>
                </div>
            </form>
        <?php 
        } 
        ?>
    </section>
<?php 
} 
?>