<?php

/** @var array $response */
var_dump($response['results']['page']);
var_dump($response['results']['limit']);
var_dump($response['results']['totalPages']);

$totalPages = $response['results']['totalPages'];
$currentPage = $_GET['page'] ?? '1';
?>
<h1>Manage Vehicles</h1>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>Client Name</th>
            <th>Plate Number</th>
            <th>Vehicle Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($response['results']['rows'] as $index => $row) {
        ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['plate_number'] ?></td>
                <td><?= $row['vehicle_type'] ?></td>
                <td><a href="<?= APP_URL . "client/vehicle/edit?vehicle_id=". $row['vehicle_id'] ?>">Edit</a></td>
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