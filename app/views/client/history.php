<?php
/** @var array $response */
$results      = $response['results'] ?? [];
$rows         = $results['rows'] ?? [];
$currentPage  = $results['page'] ?? 1;
$totalPages   = $results['total_pages'] ?? 1;
$limit        = $results['limit'] ?? 10;
$total        = $results['total'] ?? 0;
$search       = $results['search'] ?? '';
$filterDate   = $results['filter_date'] ?? '';
$filterType   = $results['filter_type'] ?? '';
$filterAcct   = $results['filter_acct'] ?? '';
$dateFrom     = $results['date_from'] ?? '';
$dateTo       = $results['date_to'] ?? '';
$vehicleTypes = $results['vehicle_types'] ?? [];

function buildQuery($overrides = [])
{
    global $search, $filterDate, $filterType, $filterAcct, $dateFrom, $dateTo;
    $params = array_merge([
        'search'      => $search,
        'filter_date' => $filterDate,
        'filter_type' => $filterType,
        'filter_acct' => $filterAcct,
        'date_from'   => $dateFrom,
        'date_to'     => $dateTo,
    ], $overrides);
    return '?' . http_build_query(array_filter($params, fn($v) => $v !== ''));
}
?>

<h4 class="page-title">Parking History</h4>

<form method="GET" action="<?= APP_URL ?>client/history"
    style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:20px; margin-top:20px; margin-bottom:20px;">

    <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">

        <div style="display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:12px; color:#666; font-weight:600;">SEARCH</label>
            <input type="text" name="search" class="form-control" style="width:220px;"
                placeholder="Name, plate, vehicle type..."
                value="<?= htmlspecialchars($search) ?>">
        </div>

        <div style="display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:12px; color:#666; font-weight:600;">DATE</label>
            <select name="filter_date" class="form-select" style="width:150px;"
                onchange="this.form.submit()">
                <option value="">All Time</option>
                <option value="today" <?= $filterDate === 'today'  ? 'selected' : '' ?>>Today</option>
                <option value="week" <?= $filterDate === 'week'   ? 'selected' : '' ?>>This Week</option>
                <option value="month" <?= $filterDate === 'month'  ? 'selected' : '' ?>>This Month</option>
            </select>
        </div>

        <div style="display:flex; flex-direction:column; gap:4px;">
            <label style="font-size:12px; color:#666; font-weight:600;">VEHICLE TYPE</label>
            <select name="filter_type" class="form-select" style="width:150px;"
            onchange="this.form.submit()">
                <option value="">All Types</option>
                <?php foreach ($vehicleTypes as $vt): ?>
                    <option value="<?= $vt['VEHICLE_TYPE_ID'] ?>"
                        <?= $filterType == $vt['VEHICLE_TYPE_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($vt['VEHICLE_TYPE']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-secondary">Apply</button>
            <a href="<?= APP_URL ?>client/history" class="btn btn-outline-secondary">Clear</a>
        </div>

    </div>

    <div style="margin-top:12px; font-size:13px; color:#888;">
        <?= number_format($total) ?> record(s) found
        <?php if ($search || $filterDate || $filterType || $filterAcct): ?>
            <span style="color:#606C38; font-weight:600;">— filters active</span>
        <?php endif; ?>
    </div>

</form>

<div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden;">
    <table class="table table-hover mb-0">
        <thead style="background:#283618; color:#fff;">
            <tr>
                <th style="padding:14px 16px;">#</th>
                <th style="padding:14px 16px;">Client Name</th>
                <th style="padding:14px 16px;">Plate Number</th>
                <th style="padding:14px 16px;">Vehicle Type</th>
                <th style="padding:14px 16px;">Time In</th>
                <th style="padding:14px 16px;">Time Out</th>
                <th style="padding:14px 16px;">Parking Fee</th>
                <th style="padding:14px 16px;">Amount Paid</th>
                <th style="padding:14px 16px;">Change</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="11" class="text-center py-4 text-muted">No history records found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $index => $data): ?>
                    <?php $change = $data['payment'] - $data['amount_to_pay']; ?>
                    <tr>
                        <td><?= ($currentPage - 1) * $limit + $index + 1 ?></td>
                        <td><?= htmlspecialchars($data['name']) ?></td>
                        <td><?= htmlspecialchars($data['plate_number']) ?></td>
                        <td><?= htmlspecialchars($data['vehicle_type']) ?></td>
                        <td><?= date('d M, Y | h:i A', $data['time_in']) ?></td>
                        <td><?= date('d M, Y | h:i A', $data['time_out']) ?></td>
                        <td>&#8369; <?= number_format($data['amount_to_pay'], 2) ?></td>
                        <td>&#8369; <?= number_format($data['payment'], 2) ?></td>
                        <td>&#8369; <?= number_format($change, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
    <div style="display:flex; justify-content:center; gap:8px; margin-top:20px; flex-wrap:wrap;">
        <?php if ($currentPage > 1): ?>
            <a href="<?= buildQuery(['page' => $currentPage - 1]) ?>"
                class="btn btn-sm btn-outline-secondary">&laquo; Prev</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?= buildQuery(['page' => $i]) ?>"
                class="btn btn-sm <?= $i == $currentPage ? 'btn-success' : 'btn-outline-secondary' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= buildQuery(['page' => $currentPage + 1]) ?>"
                class="btn btn-sm btn-outline-secondary">Next &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>