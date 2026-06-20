<?php

/** @var array $response */

if (!isset($_SESSION['parking-level'])) $_SESSION['parking-level'] = 'L1';

if (!isset($_SESSION['parking-section'])) $_SESSION['parking-section'] = "A";

$totalPages = $response['results']['totalPages'];
$currentPage = $_GET['page'] ?? '1';
?>

<h4 class="page-title">Parking Slots</h4>

<div class="content-container">
    <div class="content table">

        <!-- Filtres -->
        <div class="mb-4">
            <form action="" method="GET" class="d-flex gap-4">
                <!-- Level -->
                <div>
                    <label class="form-label fw-bold">Level</label><br>
                    <?php foreach (['L1', 'L2', 'L3'] as $l): ?>
                        <a href="?level=<?= $l ?>&section=<?= $response['results']['currentSection'] ?? 'A' ?>"
                            class="btn <?= ($response['results']['currentLevel'] ?? 'L1') === $l ? 'btn-success' : 'btn-warning' ?> me-1">
                            <?= $l ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Section -->
                <div>
                    <label class="form-label fw-bold">Section</label><br>
                    <?php foreach (['A', 'B', 'C'] as $s): ?>
                        <a href="?level=<?= $response['results']['currentLevel'] ?? 'L1' ?>&section=<?= $s ?>"
                            class="btn <?= ($response['results']['currentSection'] ?? 'A') === $s ? 'btn-success' : 'btn-warning' ?> me-1">
                            <?= $s ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </form>
        </div>

        <!-- Table -->
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Parking Slot</th>
                    <th>Client Name</th>
                    <th>Plate Number</th>
                    <th>Vehicle Type</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($response['results']['rows'] as $i => $row): ?>
                    <?php
                    $class = $row['status'] === 'available' ? 'table-success' : ($row['status'] === 'completed' ? 'table-warning' : 'table-danger');
                    ?>
                    <tr class="<?= $class ?>">
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($row['slot']) ?></strong></td>
                        <td><?= htmlspecialchars($row['client_name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['plate_number'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['vehicle_type'] ?? '-') ?></td>
                        <td>
                            <?= $row['time_in'] ? date('h:i A', strtotime($row['time_in'])) : '-' ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'occupied'): ?>
                                <span class="text-danger">Pending</span>
                            <?php elseif ($row['status'] === 'completed'): ?>
                                <a href="<?= APP_URL . 'admin/parking-slots/manage-request?slot_id=' . $row['slot_id'] ?>" class="text-success">Check out Request</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php
        $currentPage = $response['results']['page'] ?? 1;
        $totalPages  = $response['results']['totalPages'] ?? 1;
        ?>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>&level=<?= $response['results']['currentLevel'] ?>&section=<?= $response['results']['currentSection'] ?>">
                                &laquo; Précédent
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item active"><span class="page-link"><?= $currentPage ?></span></li>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>&level=<?= $response['results']['currentLevel'] ?>&section=<?= $response['results']['currentSection'] ?>">
                                Suivant &raquo;
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

    </div>
</div>