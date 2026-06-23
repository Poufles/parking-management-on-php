<?php

$results = AccountModel::getInstance()->getAccountInfo($_SESSION['uid']) ?? null;
$accountDetails = $results['account'];

?>

<h4 class="page-title">Edit Account</h4>

<section id="edit">

    <div class="card" style="max-width: 420px; width: 100%;">

        <form action="<?= APP_URL . 'client/account-settings-edit' ?>" method="post" enctype="multipart/form-data" class="card-body p-4">

            <div class="input-group has-validation mb-3">
                <div class="form-floating <?php if (isset($plateNumberValidation) && !$plateNumberValidation['status']) echo 'is-invalid'; ?>">
                    <input type="text" class="form-control" id="input-name"
                        placeholder="Jeffrex" name="name" value="<?= $accountDetails['name'] ?? null ?>">
                    <label for="input-name">Name</label>
                </div>
                <div class="invalid-feedback">
                    <?= $plateNumberValidation['message'] ?? null ?>
                </div>
            </div>

            <div class="input-group has-validation mb-3">
                <div class="form-floating <?php if (isset($plateNumberValidation) && !$plateNumberValidation['status']) echo 'is-invalid'; ?>">
                    <input type="text" class="form-control" id="input-username"
                        placeholder="Jeffrex" name="username" value="<?= $accountDetails['username'] ?? null ?>">
                    <label for="input-username">Username</label>
                </div>
                <div class="invalid-feedback">
                    <?= $plateNumberValidation['message'] ?? null ?>
                </div>
            </div>

            <div class="input-group has-validation mb-3">
                <div class="form-floating <?php if (isset($plateNumberValidation) && !$plateNumberValidation['status']) echo 'is-invalid'; ?>">
                    <input type="text" class="form-control" id="input-email"
                        placeholder="Jeffrex" name="email" value="<?= $accountDetails['email_address'] ?? null ?>">
                    <label for="input-email">Email Address</label>
                </div>
                <div class="invalid-feedback">
                    <?= $plateNumberValidation['message'] ?? null ?>
                </div>
            </div>

            <div class="form-floating mb-3">
                <select class="form-select" id="gender" name="gender">
                    <option value="male" <?php if ($accountDetails['gender'] == 'male') echo 'selected' ?>>Male</option>
                    <option value="femmale" <?php if ($accountDetails['gender'] == 'female') echo 'selected' ?>>Female</option>
                    <option value="others" <?php if ($accountDetails['gender'] == 'others') echo 'selected' ?>>Others</option>
                </select>
                <label for="gender">Gender</label>
            </div>

            <div class="input-group has-validation mb-3">
                <div class="form-floating <?php if (isset($plateNumberValidation) && !$plateNumberValidation['status']) echo 'is-invalid'; ?>">
                    <input type="text" class="form-control" id="input-phone"
                        placeholder="Jeffrex" name="phone" value="<?= $accountDetails['phone'] ?? null ?>">
                    <label for="input-phone">Phone No.</label>
                </div>
                <div class="invalid-feedback">
                    <?= $plateNumberValidation['message'] ?? null ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Licence</label>
                <input type="file" id="licence" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                <?php if ($accountDetails['licence'] == null) : ?>
                    <small class="text-muted current-file mt-1 d-block">
                        No File Uploaded
                    </small>
                <?php else : ?>
                    <small class="text-muted current-file mt-1 d-block">
                        Actual File : <?= $accountDetails['licence'] ?>
                    </small>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="text" id="password" placeholder="New Password" class="form-control" value="">
            </div>

            <button type="submit" class="btn w-100 mb-3 py-3 fw-bold" id="save" style="background-color: var(--secondary-color); color: var(--primary-color)">
                Save Changes
            </button>
                </form>
    </div>
</section>