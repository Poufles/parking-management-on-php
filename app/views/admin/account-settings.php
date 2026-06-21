<?php
/** @var array $response */
$account = $response['results']['account'] ?? [];
$activeTab = $response['results']['tab'] ?? 'profile';
$message = $response['message'] ?? '';
$status = $response['status'] ?? null;

// Override tab from GET if no POST
if (!isset($_POST['edit_profile']) && !isset($_POST['change_password'])) {
    $activeTab = $_GET['tab'] ?? 'profile';
}
?>

<h4 class="page-title">Account Settings</h4>

<?php if ($message): ?>
    <div class="alert <?= $status ? 'alert-success' : 'alert-danger' ?>" style="margin-top:16px;">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Tabs -->
<div style="display:flex; gap:8px; margin-top:20px; margin-bottom:20px;">
    <a href="?tab=profile"
       style="padding:8px 20px; border-radius:8px; text-decoration:none; font-weight:600;
              background:<?= $activeTab === 'profile' ? '#283618' : '#fff' ?>;
              color:<?= $activeTab === 'profile' ? '#fff' : '#283618' ?>;
              border: 2px solid #283618;">
        Edit Profile
    </a>
    <a href="?tab=password"
       style="padding:8px 20px; border-radius:8px; text-decoration:none; font-weight:600;
              background:<?= $activeTab === 'password' ? '#283618' : '#fff' ?>;
              color:<?= $activeTab === 'password' ? '#fff' : '#283618' ?>;
              border: 2px solid #283618;">
        Change Password
    </a>
</div>

<div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:32px; max-width:500px;">

    <?php if ($activeTab === 'profile'): ?>
        <h5 style="margin-bottom:20px; color:#283618;">Edit Profile</h5>
        <form method="POST" action="<?= APP_URL ?>admin/account-settings">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($account['NAME'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control"
                       value="<?= htmlspecialchars($account['USERNAME'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($account['EMAIL_ADDRESS'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control"
                       value="<?= htmlspecialchars($account['PHONE'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="male" <?= ($account['GENDER'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= ($account['GENDER'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                </select>
            </div>
            <button type="submit" name="edit_profile" class="btn btn-success">Save Changes</button>
        </form>

    <?php elseif ($activeTab === 'password'): ?>
        <h5 style="margin-bottom:20px; color:#283618;">Change Password</h5>
        <form method="POST" action="<?= APP_URL ?>admin/account-settings">
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
        </form>
    <?php endif; ?>

    <hr style="margin-top:28px;">
    <form method="POST" action="<?= APP_URL ?>admin/account-settings">
        <button type="submit" name="logout" class="btn btn-danger">Logout</button>
    </form>

</div>