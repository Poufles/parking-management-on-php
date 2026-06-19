<section class="card">
    <h2>Parking Rates</h2>
    <div class="cards rate-cards">
        <?php foreach ($parkingRates as $rate) { ?>
            <article>
                <strong><?= e($rate['label']) ?></strong>
                <p>PHP <?= number_format((float)$rate['amount'], 2) ?></p>
            </article>
        <?php } ?>
    </div>
</section>

<section class="card">
    <div class="slot-toolbar">
        <div>
            <h2>Parking Slot Availability</h2>
        </div>
        <form method="get">
            <input type="hidden" name="page" value="slots">
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
                href="?page=slots&level=<?= $i ?>&category=<?= $category ?>"
            >
                Level <?= $i ?>
            </a>
        <?php } ?>
    </div>
    <div class="category-tabs">
        <?php foreach (['A','B','C'] as $cat) { ?>
            <a
                class="<?= $category === $cat ? 'active' : '' ?>"
                href="?page=slots&level=<?= $level ?>&category=<?= $cat ?>"
            >
                Category <?= $cat ?>
            </a>
        <?php } ?>
    </div>
    <div class="parking-legend">
        <span><i class="legend-dot available-dot"></i>Available</span>
        <span><i class="legend-dot occupied-dot"></i>Occupied</span>
        <strong>
            <?= count(array_filter($filteredSlots, fn($slot) => $slot['status'] === 'available')) ?> of 10 available
        </strong>
    </div>
    <div class="parking-map" aria-label="Level <?= e($level) ?> Category <?= e($category) ?> parking slots">
        <?php foreach ($filteredSlots as $slot) { ?>
            <?php
            $occupied = $slot['status'] === 'occupied';
            $isMine = $occupied && (int) $slot['occupied_owner_id'] === $userId;
            $displayCode = str_replace('-', '_', $slot['slot_code']);
            ?>
            <article class="parking-bay <?= $occupied ? 'bay-occupied' : 'bay-available' ?>">
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
            </article>
        <?php } ?>
    </div>
</section>
