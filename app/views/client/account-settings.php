<?php

/** @var array $response */

$results = AccountModel::getInstance()->getAccountInfo($_SESSION['uid']) ?? null;
$accountDetails = $results['account'];

?>

<h4 class="page-title">Account Settings</h4>

<section>
    <div class="card" style="max-width: 420px; width: 100%;">

        <div class="profile-header card-header py-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="ps-2">
                        <h3 id="name" class="mb-0 fw-bold"><?= $accountDetails['name'] ?? null ?></h3>
                        <p id="username" class="mb-0 opacity-75">@<?= $accountDetails['username'] ?? null ?></p>
                    </div>
                </div>

                <a href="<?= APP_URL . 'client/account-settings/edit' ?>" class="btn btn-light btn-sm d-flex align-items-center gap-2">
                    Edit
                </a>
            </div>
        </div>

        <div class="card-body p-4">

            <div class="info-row d-flex justify-content-between align-items-center">
                <span class="text-muted">Email</span>
                <span id="email" class="fw-medium"><?= $accountDetails['email_address'] ?? null ?></span>
            </div>

            <div class="info-row d-flex justify-content-between align-items-center">
                <span class="text-muted">Gender</span>
                <span id="gender" class="fw-medium"><?= ucwords(strtolower($accountDetails['gender'])) ?? null ?></span>
            </div>

            <div class="info-row d-flex justify-content-between align-items-center">
                <span class="text-muted">Phone no.</span>
                <span id="phone" class="fw-medium">+<?= $accountDetails['phone'] ?? null ?></span>
            </div>

            <div class="info-row d-flex justify-content-between align-items-center w-button">
                <span class="text-muted">Licence</span>
                <?php if ($accountDetails['licence'] ?? null) : ?>
                    <button class="btn btn-sm d-flex align-items-center gap-2">
                        View
                    </button>
                <?php else : ?>
                    <a href="<?= APP_URL . 'client/account-settings/edit' ?>" class="btn btn-sm d-flex align-items-center gap-2">
                        Add Licence
                    </a>
                <?php endif; ?>
            </div>

            <div class="info-row d-flex justify-content-between align-items-center w-button">
                <span class="text-muted">Password</span>
                <a href="<?= APP_URL . 'client/account-settings/edit' ?>" class="btn btn-sm d-flex align-items-center gap-2">
                    Change
                </a>
            </div>

        </div>

        <form class="card-footer p-4" action="<?= APP_URL . "client/account-settings" ?>" method="post">
            <button name="logout" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                Logout
            </button>
        </form>
    </div>
</section>