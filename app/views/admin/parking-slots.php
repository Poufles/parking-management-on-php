<?php

/** @var array $response */
// var_dump($response['results']['rows'][0]);
// var_dump($response['results']['page']);
// var_dump($response['results']['limit']);
// var_dump($response['results']['totalPages']);

$totalPages = $response['results']['totalPages'];
$currentPage = $_GET['page'] ?? '1';
?>

<h1>Parking Slots</h1>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Parking Slot</th>
            <th>Client Name</th>
            <th>Plate Number</th>
            <th>Vehicle Type</th>
            <th>Time in</th>
            <th>Time out</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($response['results']['rows'] as $index => $row) {
        ?>
            <?php
            if ($row['status'] === 'available') {
            ?>
                <tr class="table-success">
                <?php
            } else {
                ?>
                <tr class="table-danger">
                <?php
            }
                ?>
                <td><?= $index + 1 ?></td>
                <td><?= $row['slot_id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['plate_number'] ?></td>
                <td><?= $row['vehicle_type'] ?></td>
                <td><?= $row['time_in'] ?></td>
                <?php
                if ($row['status'] === 'available') {
                ?>
                    <td><?= $row['time_out'] ?></td>
                <?php
                }

                if ($row['status'] === 'occupied' && $row['time_out'] === null) {
                ?>
                    <td>Pending</td>
                <?php
                }

                if ($row['status'] === 'completed') {
                ?>
                    <td><a href="<?= APP_URL . "admin/parking-slots/receipt" ?>">Check out Request</a></td>
                <?php
                }
                ?>
                </tr>
            <?php
        }
            ?>
    </tbody>
</table>
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php
        if (isset($currentPage) && $currentPage != 1) {
        ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php
        }
        ?>
        <li class="page-item"><span class="page-link"><?= $currentPage ?></span></li>
        <?php
        if (isset($currentPage) && $currentPage < $totalPages) {
        ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php
        }
        ?>
    </ul>
</nav>