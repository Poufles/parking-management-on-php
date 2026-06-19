<?php if (!$hasLicense) { ?>
    <section class="license-notice">
        <div>
            <strong>Please upload your license first.</strong>
        </div>
        <a class="btn btn-primary" href="?page=settings#client-account">Upload</a>
    </section>
<?php } ?>

<section class="stats">
    <div class="stat">
        <small>My Vehicles</small>
        <strong><?= count($vehicles) ?> / 3</strong>
    </div>
    <div class="stat">
        <small>Parked Vehicles</small>
        <strong><?= count($active) ?></strong>
    </div>
    <div class="stat">
        <small>Parking Visits</small>
        <strong><?= count($history) ?></strong>
    </div>
</section>

<section class="card">
    <h2>Parked Vehicles Summary</h2>
    <?php if (!$active) { ?>
        <p class="muted">You currently have no parked vehicles.</p>
    <?php } else { ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Parking Slot</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($active as $row) { ?>
                        <tr>
                            <td><strong><?= e($row['plate_number']) ?></strong></td>
                            <td><?= e($row['slot_code']) ?></td>
                            <td><?= e(date('M d, Y h:i A', strtotime($row['entry_time']))) ?></td>
                            <td>Currently parked</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>
