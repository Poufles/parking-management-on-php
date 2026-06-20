<?php

/** @var array $response */

$results = ParkingModel::getInstance()->getCheckoutRequestDetails($_GET['slot_id']);

$details = $results['results']['details'] ?? null;

$paymentValidation = $response['results']['payment'] ?? null;
?>

<h4 class="page-title">Manage Request</h4>

<div class="content-container">
    <div class="content" id="receipt">
        <p class="content-title">
            <?= $details['plate_number'] . " @ " . $details['slot'] ?>
        </p>
        <div class="row head">
            <label for="" class="col-sm-5 col-form-label">Description</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="Details">
            </div>
        </div>
        <hr>
        <div class="row">
            <label for="" class="col-sm-5 col-form-label">Client Name</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?= $details['name'] ?>">
            </div>
        </div>
        <div class="row">
            <label for="" class="col-sm-5 col-form-label">Vehicle Type</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?= $details['vehicle_type'] ?>">
            </div>
        </div>
        <div class="row">
            <label for="" class="col-sm-5 col-form-label">Time In:</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?= date('h:s A', $details['time_in']) ?>">
            </div>
        </div>
        <div class="row">
            <label for="" class="col-sm-5 col-form-label">Time Out</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?= date('h:s A', $details['time_out']) ?>">
            </div>
        </div>
        <div class="row">
            <label for="" class="col-sm-5 col-form-label">Duration</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?= date('g', $details['time_out'] - $details['time_in']) . " hour(s)" ?>">
            </div>
        </div>
        <hr>
        <div class="row">
            <label for="" class="col-sm-5 col-form-label">Rate to pay</label>
            <div class="col-sm-7">
                <input type="text" readonly class="form-control-plaintext" id="" value="<?= "₱ " . $details['fee']['results']['fee'] ?>">
            </div>
        </div>
    </div>
    <form action="<?= APP_URL . "admin/parking-slots/manage-request?slot_id=" . $_GET['slot_id'] ?>" method="POST" class="content" id="payment">
        <input type="hidden" name="slot_id" value="<?= $_GET['slot_id'] ?>">
        <input type="hidden" name="amount_to_pay" value="<?= $details['fee']['results']['fee'] ?>">
        <div class="input-group has-validation">
            <div class="input-container <?php if (isset($paymentValidation) && !$paymentValidation['status']) echo 'is-invalid'; ?>">
                <input type="text" name="payment" class="form-control" id="input-payment" placeholder="Payment" value="<?= $_POST['payment'] ?? null ?>">
            </div>
            <div class="invalid-feedback">
                <?= $paymentValidation['message'] ?? null ?>
            </div>
        </div>
        <button type="submit" name="register" class="btn btn-success">Register</button>
    </form>
</div>