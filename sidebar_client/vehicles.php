<div class="client-two">
    <section class="card">
        <h2>Add Vehicle <small>(<?= count($vehicles) ?>/3)</small></h2>
        <?php if (!$hasLicense) { ?>
            <div class="alert alert-error">Please upload your license first.</div>
            <a class="btn btn-primary" href="?page=settings#client-account">Upload</a>
        <?php } elseif (count($vehicles) >= 3) { ?>
            <div class="alert alert-error">Maximum of three vehicles reached.</div>
        <?php } else { ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?= csrf() ?>">
                <input type="hidden" name="action" value="add_vehicle">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Plate Number</label>
                        <input class="input" name="plate_number" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Type</label>
                        <select class="input" name="vehicle_type">
                            <option>Car</option>
                            <option>Motorcycle</option>
                            <option>Van</option>
                            <option>Pick Up</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label>Vehicle Attachments</label>
                        <input
                            class="input"
                            type="file"
                            name="registration_documents[]"
                            multiple
                        >
                    </div>
                    <div class="form-buttons full">
                        <button class="btn btn-primary" type="submit">Save Vehicle</button>
                        <button class="btn btn-dark" type="reset">Reset</button>
                    </div>
                </div>
            </form>
        <?php } ?>
    </section>

    <section class="card">
        <h2>My Vehicle Information</h2>
        <?php if (!$vehicles) { ?>
            <p class="muted">No vehicles registered.</p>
        <?php } else { ?>
            <div class="vehicle-list">
                <?php foreach ($vehicles as $v) { $attachments = $vehicleAttachments[$v['id']] ?? []; ?>
                    <article class="vehicle-card">
                        <div class="vehicle-details">
                            <h3><?= e($v['plate_number']) ?></h3>
                            <p><?= e($v['vehicle_type']) ?></p>
                            <strong>Uploaded files <?= $attachments ? '('.count($attachments).')' : '' ?></strong>
                            <?php if (empty($attachments)) { ?>
                                <small>No vehicle documents uploaded.</small>
                            <?php } else { ?>
                                <ul class="attachment-list">
                                    <?php foreach ($attachments as $attachment) { ?>
                                        <li>
                                            <a
                                                href="download.php?id=<?= $attachment['id'] ?>&view=1"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                <?= e($attachment['original_name']) ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } ?>
    </section>
</div>
