<div class="settings-profile-page">
    <input class="settings-check" type="checkbox" id="edit-client-profile">
    <input class="settings-check" type="checkbox" id="edit-client-password">

    <section class="card settings-profile-card">
        <form id="profile-update-form" method="post" enctype="multipart/form-data" class="profile-form">
            <input type="hidden" name="csrf" value="<?= csrf() ?>">
            <input type="hidden" name="action" value="settings">

            <div class="profile-summary">
                <div>
                    <p class="eyebrow">MY PROFILE</p>
                    <div class="profile-view">
                        <h2><?= e($account['full_name']) ?></h2>
                    </div>
                    <div class="profile-edit-fields">
                        <label>Full Name</label>
                        <input class="input" name="full_name" value="<?= e($account['full_name']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="profile-details">
                <div>
                    <label>Account No.</label>
                    <strong class="profile-view"></strong>
                    <input class="input profile-edit-field" disabled aria-label="Account number">
                </div>
                <div>
                    <label>Email Address</label>
                    <strong class="profile-view"><?= e($account['email']) ?></strong>
                    <input class="input profile-edit-field" type="email" name="email" value="<?= e($account['email']) ?>" required>
                </div>
                <div>
                    <label>Gender</label>
                    <strong class="profile-view"><?= e($account['gender'] ?? 'Prefer not to say') ?></strong>
                    <select class="input profile-edit-field" name="gender">
                        <?php foreach (['Female', 'Male', 'Prefer not to say'] as $gender) { ?>
                            <option value="<?= e($gender) ?>" <?= ($account['gender'] ?? 'Prefer not to say') === $gender ? 'selected' : '' ?>><?= e($gender) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label>Contact Number</label>
                    <strong class="profile-view"><?= e($account['phone']) ?></strong>
                    <input class="input profile-edit-field" name="phone" value="<?= e($account['phone']) ?>" required>
                </div>
                <div class="full-detail">
                    <label>Address</label>
                    <strong class="profile-view"><?= e($account['address']) ?></strong>
                    <input class="input profile-edit-field" name="address" value="<?= e($account['address']) ?>" required>
                </div>
                <div class="full-detail">
                    <label>Driver's License</label>
                    <strong class="profile-view"><?= $hasLicense ? 'Uploaded' : 'Not uploaded' ?></strong>
                    <div class="profile-edit-field">
                        <input class="input" type="file" name="license_images[]" accept="image/*,.pdf" multiple>
                        <?php if ($accountAttachments) { ?>
                            <strong class="attachment-heading">Saved files (<?= count($accountAttachments) ?>/2)</strong>
                            <ul class="attachment-list">
                                <?php foreach ($accountAttachments as $attachment) { ?>
                                    <li><a href="download.php?id=<?= $attachment['id'] ?>"><?= e($attachment['original_name']) ?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                </div>
                <div class="full-detail password-settings">
                    <div class="password-fields">
                        <div>
                            <label>New Password</label>
                            <input class="input" type="password" name="new_password" minlength="6" autocomplete="new-password">
                        </div>
                        <div>
                            <label>Confirm Password</label>
                            <input class="input" type="password" name="confirm_password" minlength="6" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$hasLicense) { ?>
                <div class="alert alert-error">Please upload your driver's license.</div>
            <?php } ?>

        </form>

        <div class="profile-footer-actions">
            <label class="btn btn-primary profile-view profile-standard-action" for="edit-client-profile">Edit Profile</label>
            <label class="btn btn-light profile-view profile-standard-action" for="edit-client-password">Edit Password</label>

            <div class="profile-edit-buttons">
                <button class="btn btn-primary" type="submit" form="profile-update-form">Save Changes</button>
                <button class="btn btn-light" type="reset" form="profile-update-form">Reset</button>
                <label class="btn btn-dark" for="edit-client-profile">Cancel</label>
            </div>

            <div class="password-edit-buttons">
                <button class="btn btn-primary" type="submit" form="profile-update-form">Save Password</button>
                <button class="btn btn-light" type="reset" form="profile-update-form">Reset</button>
                <label class="btn btn-dark" for="edit-client-password">Cancel</label>
            </div>

            <form method="post" class="delete-account-form profile-view profile-standard-action" onsubmit="return confirm('Delete your account permanently?');">
                <input type="hidden" name="csrf" value="<?= csrf() ?>">
                <input type="hidden" name="action" value="delete_account">
                <button class="btn btn-danger" type="submit">Delete Account</button>
            </form>
        </div>
    </section>
</div>
