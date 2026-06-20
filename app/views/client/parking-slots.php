<?php

/** @var array $response */

$results   = $response['response'] ?? [];
$slots     = $results['slots'] ?? [];
$level     = $_GET['level'];
$section     = $_GET['section'];

$levels   = ['L1', 'L2', 'L3'];
$sections = ['A', 'B', 'C'];

$queryParams = $_GET;

?>
<h4 class="page-title">Parking Slots</h4>

<section class="content-container">

    <div class="content" id="parking">

        <div class="parking-nav">
            <div class="nav-group">
                <strong>Level:</strong>
                <?php foreach ($levels as $lvl):
                    $params = $queryParams;
                    $params['level'] = $lvl;
                    $active = $lvl === $level ? 'active' : '';
                ?>
                    <a class="btn-nav <?= $active ?>" href="<?= '?level=' . $lvl . '&section=' . $_GET['section'] ?>"><?= $lvl ?></a>
                <?php endforeach; ?>
            </div>

            <div class="nav-group">
                <strong>Section:</strong>
                <?php foreach ($sections as $sec):
                    $params = $queryParams;
                    $params['section'] = $sec;
                    $active = $sec === $section ? 'active' : '';
                ?>
                    <a class="btn-nav <?= $active ?>" href="<?= '?level=' . $_GET['level'] . '&section=' . $sec ?>"><?= $sec ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="parking-legend">
            <div class="legend-item"><span class="legend-box available"></span> Available</div>
            <div class="legend-item"><span class="legend-box occupied"></span> Occupied</div>
            <div class="legend-item"><span class="legend-box parked"></span> Parked</div>
        </div>

        <div class="slot-grid">
            <?php foreach ($slots as $slot): ?>

                <?php if ($slot['status'] === 'available'): ?>
                    <a class="slot available"
                        href="<?= APP_URL . 'client/parking-slots/park-in?slot_id=' . $slot['slot_id'] ?>&level=<?= $level ?>&section=<?= $section . '&place=' . $slot['slot_number'] ?>">
                        <span class="slot-number"><?= $level . ' - ' . $section . $slot['slot_number'] ?></span>
                        <span class="slot-meta">Available</span>
                    </a>

                <?php elseif ($slot['status'] === 'occupied'): ?>
                    <div class="slot occupied">
                        <span class="slot-number"><?= $level . ' - ' . $section . $slot['slot_number'] ?></span>
                        <span class="slot-meta">Occupied</span>
                    </div>

                <?php else: // parked 
                ?>
                    <div class="slot parked">
                        <span class="slot-number"><?= $level . ' - ' . $section . $slot['slot_number'] ?></span>
                        <span class="slot-meta"><?= $slot['vehicle_type'] ?></span>
                        <span class="slot-meta">Time in: <?= date('h:i A', $slot['time_in']) ?></span>
                        <?php if ($slot['time_out'] == null) : ?>
                            <form method="post" action="<?= APP_URL . 'client/parking-slots' ?>">
                                <input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
                                <button type="submit" name="time-out" class="timeout-btn">Time out</button>
                            </form>
                        <?php else : ?>
                            <span class="slot-meta">Time out: <?= date('h:i A', $slot['time_out']) ?> (Pending)</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div>

</section>