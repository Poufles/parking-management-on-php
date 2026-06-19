<section class="card">
    <?php if (!$history) { ?>
        <p class="muted">No parking history yet.</p>
    <?php } else { ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Slot ID</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Duration</th>
                        <th>To Pay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row) { ?>
                        <?php
                        $end = $row['exit_time'] ? strtotime($row['exit_time']) : time();
                        $minutes = max(1, (int) ceil(($end - strtotime($row['entry_time'])) / 60));
                        $toPay = parking_charge($row['entry_time'], $row['exit_time']);
                        ?>
                        <tr>
                            <td><strong><?= e($row['plate_number']) ?></strong></td>
                            <td><?= e($row['slot_code']) ?></td>
                            <td><?= e(date('M d, Y h:i A', strtotime($row['entry_time']))) ?></td>
                            <td>
                                <?php if ($row['exit_time']) { ?>
                                    <?= e(date('M d, Y h:i A', strtotime($row['exit_time']))) ?>
                                <?php } elseif (!empty($row['checkout_requested_at'])) { ?>
                                    <span class="badge occupied">Checkout requested</span>
                                <?php } else { ?>
                                    <form method="post">
                                        <input type="hidden" name="csrf" value="<?= csrf() ?>">
                                        <input type="hidden" name="action" value="request_checkout">
                                        <input type="hidden" name="history_id" value="<?= (int) $row['id'] ?>">
                                        <button class="btn btn-dark">Check Out</button>
                                    </form>
                                <?php } ?>
                            </td>
                            <td><?= $minutes ?> min</td>
                            <td>
                                <strong>
                                    PHP <?= number_format($row['fee'] !== null ? (float) $row['fee'] : $toPay, 2) ?>
                                </strong>
                                <?php if ($row['paid_amount'] !== null) { ?>
                                    <small class="muted">
                                        Paid: PHP <?= number_format((float) $row['paid_amount'], 2) ?>
                                    </small>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>
