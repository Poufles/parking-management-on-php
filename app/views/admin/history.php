<?php

/** @var array $response */

$currentPage = $_GET['page'] ?? '1';

$totalPages = $response['results']['total_pages'];

$results = $response['results'] ?? [];
$row = $results['rows'] ?? [];
$limit = $results['limit'] ?? 10;

$queryParams = $_GET;

?>

<h4 class="page-title">Parking History</h4>

<section class="content-container divide">
    <div class="content table">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client Name</th>
                    <th>Plate Number</th>
                    <th>Vehicle Type</th>
                    <th>Time in</th>
                    <th>Time out</th>
                    <th>Parking Fee</th>
                    <th>Amount Payed</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($row as $index => $data) {
                    $rowNumber = (($currentPage - 1) * $limit) + $index + 1;
                ?>
                    <tr>
                        <td><?= $rowNumber ?></td>
                        <td><?= $data['name'] ?></td>
                        <td><?= $data['plate_number'] ?></td>
                        <td><?= $data['vehicle_type'] ?></td>
                        <td><?= date('d M, Y | h:i A', $data['time_in']) ?></td>
                        <td><?= date('d M, Y | h:i A', $data['time_out']) ?></td>
                        <td><?= $data['amount_to_pay'] ?></td>
                        <td><?= $data['payment'] ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php
                    $prevDisabled = $currentPage <= 1 ? 'd-none' : '';
                    ?>
                    <li class="page-item <?= $prevDisabled ?>">
                        <a class="page-link" href="<?= '?page=' . $currentPage - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <li class="page-item">
                        <p class="page-link"><?= $currentPage ?></a>
                    </li>

                    <?php
                    $nextDisabled = $currentPage >= $totalPages ? 'd-none' : '';
                    ?>
                    <li class="page-item <?= $nextDisabled ?>">
                        <a class="page-link" href="<?= '?page=' . $currentPage + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>