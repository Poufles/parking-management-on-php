<div class="settings-actions">
    <input class="settings-check" type="checkbox" id="show-client-account">
    <input class="settings-check" type="checkbox" id="show-parking-help">

    <label class="settings-choice" for="show-client-account">
        <strong>Edit Account</strong>
    </label>

    <label class="settings-choice" for="show-parking-help">
        <strong>Help</strong>
    </label>

    <form method="post" class="settings-delete-form">
        <input type="hidden" name="csrf" value="<?= csrf() ?>">
        <input type="hidden" name="action" value="delete_account">
        <button class="settings-choice delete-choice" type="submit">
            <strong>Delete Account</strong>
        </button>
    </form>

    <section class="card settings-card settings-panel client-account-panel">
        <h2>Edit Account</h2>
        <?php if (!$hasLicense) { ?>
            <div class="alert alert-error">Please upload your license first.</div>
        <?php } ?>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf" value="<?= csrf() ?>">
            <input type="hidden" name="action" value="settings">
            <div class="form-grid">
                <div class="form-group full profile-upload-group">
                    <label>Profile Picture</label>
                    <label class="profile-upload">
                        <span class="profile-upload-circle">
                            <?php if (!empty($profileImagePath) && is_file(dirname(__DIR__, 2) . '/' . $profileImagePath)) { ?>
                                <img id="profile-preview" src="<?= e($profileImagePath) ?>" alt="Profile picture">
                            <?php } else { ?>
                                <span id="profile-initial"><?= e(strtoupper(substr($clientName, 0, 1))) ?></span>
                                <img id="profile-preview" src="" alt="Profile picture" hidden>
                            <?php } ?>
                        </span>
                        <input
                            id="profile-image-input"
                            type="file"
                            name="profile_image"
                            accept="image/jpeg,image/png,image/webp"
                        >
                    </label>
                </div>
                <div class="form-group full">
                    <label>Full Name</label>
                    <input class="input" name="full_name" value="<?= e($account['full_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input class="input" type="email" name="email" value="<?= e($account['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input class="input" name="phone" value="<?= e($account['phone']) ?>" required>
                </div>
                <div class="form-group full">
                    <label>Address</label>
                    <input class="input" name="address" value="<?= e($account['address']) ?>" required>
                </div>
                <div class="form-group full">
                    <label>Driver's License</label>
                    <input
                        class="input"
                        type="file"
                        name="license_images[]"
                        accept="image/*,.pdf"
                        multiple
                    >
                    <?php if ($accountAttachments) { ?>
                        <strong class="attachment-heading">Saved files (<?= count($accountAttachments) ?>/2)</strong>
                        <ul class="attachment-list">
                            <?php foreach ($accountAttachments as $attachment) { ?>
                                <li>
                                    <a href="download.php?id=<?= $attachment['id'] ?>">
                                        <?= e($attachment['original_name']) ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="form-group full">
                    <label>New Password </label>
                    <input class="input" type="password" name="password" minlength="6">
                </div>
                <div class="form-buttons full">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <button class="btn btn-dark" type="reset">Reset</button>
                </div>
            </div>
        </form>
    </section>

    <section class="card settings-card settings-panel parking-help-panel">
        <h2>Parking Help</h2>
        <div class="help-steps">
            <article>
                <strong>1. Upload your license</strong>
            </article>
            <article>
                <strong>2. Add your vehicle</strong>
            </article>
            <article>
                <strong>3. Check parking slots</strong>
            </article>
            <article>
                <strong>4. Park in and review history</strong>
            </article>
        </div>
    </section>
</div>

<script>
const profileInput = document.querySelector('#profile-image-input');
const profilePreview = document.querySelector('#profile-preview');
const profileInitial = document.querySelector('#profile-initial');

if (profileInput && profilePreview) {
    profileInput.addEventListener('change', function () {
        const selectedImage = this.files[0];
        if (!selectedImage) {
            return;
        }

        profilePreview.src = URL.createObjectURL(selectedImage);
        profilePreview.hidden = false;

        if (profileInitial) {
            profileInitial.hidden = true;
        }
    });
}
</script>
