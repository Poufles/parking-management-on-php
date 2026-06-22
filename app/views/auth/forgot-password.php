<?php

/** @var array $response */

$emailValidation = $response['results']['email'] ?? null;
$otpValidation = $response['results']['otp'] ?? null;
$passwordValidation = $response['results']['password'] ?? null;
?>

<div class="view-content">
    <div class="sides img-container" id="left-content">
        <img src="<?= APP_URL . "assets/images/reid-naaykens-kKyqSiu_KAs-unsplash.jpg" ?>" alt="">
    </div>
    <div class="sides interface-container" id="right-content">
        <div class="form-wrapper">
            <a href="<?= APP_URL ?>" class="logo-container mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev/svgjs" viewBox="0 0 600 600">
                    <path d="M401.7015686035156,205.7591552734375C473.4293149312337,162.30365626017252,562.303653717041,77.74868901570638,508.5078430175781,56.54450225830078C454.71203231811523,35.34031550089519,108.1151860555013,23.167540232340492,78.92670440673828,78.5340347290039C49.738222757975265,133.90052922566733,286.38742701212567,380.4973971048991,333.376953125,388.74346923828125C380.36647923787433,396.9895413716634,402.3560053507487,117.80104573567708,360.8638610839844,128.01046752929688C319.37171681722003,138.21988932291666,76.70157368977864,419.50261942545575,84.42408752441406,450C92.14660135904948,480.49738057454425,396.33507029215497,362.69632212320965,407.1989440917969,310.9947509765625C418.0628178914388,259.29317982991535,134.42408243815103,100.1308848063151,149.60733032226562,139.7905731201172C164.79057820638022,179.45026143391928,493.1937204996745,477.3560256958008,498.2984313964844,548.952880859375C503.40314229329425,620.5497360229492,188.87433878580728,582.5915985107422,180.235595703125,569.3717041015625C171.59685262044272,556.1518096923828,463.4816780090332,511.6492156982422,446.4659729003906,469.6335144042969C429.45026779174805,427.61781311035156,85.6020991007487,361.2565561930339,78.14136505126953,317.2774963378906C70.68063100179036,273.2984364827474,329.97382227579754,249.21465428670248,401.7015686035156,205.7591552734375C473.4293149312337,162.30365626017252,562.303653717041,77.74868901570638,508.5078430175781,56.54450225830078" fill="var(--secondary-color)" transform="matrix(1,0,0,1,-0.7258529663085938,-14.998641967773438)"></path>
                </svg>
                <span class="mb-0">Parcheggiamo</span>
            </a>
            <?php if (!isset($_SESSION['otp']) && !isset($_SESSION['reset-password'])) : ?>
                <div class="titles-container">
                    <h1 class="mb-0">Help us find your account</h1>
                    <p class="mb-4">Please enter your email address</p>
                </div>
                <form action="<?= APP_URL . "auth/forgot-password" ?>" method="post">
                    <div class="input-group has-validation mb-3">
                        <div class="form-floating <?php if (isset($emailValidation) && !$emailValidation['status']) echo 'is-invalid'; ?>">
                            <input type="text" class="form-control" id="input-email" placeholder="" name="email">
                            <label for="input-email">Email Address</label>
                        </div>
                        <div class="invalid-feedback">
                            <?= $emailValidation['message'] ?? null ?>
                        </div>
                    </div>
                    <div class="actions-container">
                        <div class="actions">
                            <button type="submit" name="send" class="btn btn-success">Send OTP</button>
                        </div>
                    </div>
                </form>
            <?php 
            endif;

            if (isset($_SESSION['otp']) && !isset($_SESSION['reset-password'])) : 
            ?>
                <div class="titles-container">
                    <h1 class="mb-0">OTP Sent !</h1>
                    <p class="mb-4">Please enter the OTP sent to your email</p>
                </div>
                <form action="<?= APP_URL . "auth/forgot-password" ?>" method="post">
                    <div class="input-group has-validation mb-3">
                        <div class="form-floating <?php if (isset($otpValidation) && !$otpValidation['status']) echo 'is-invalid'; ?>">
                            <input type="text" class="form-control" id="input-otp" placeholder="Jeffrex" name="otp">
                            <label for="input-otp">OTP Code</label>
                        </div>
                        <div class="invalid-feedback">
                            <?= $otpValidation['message'] ?? null ?>
                        </div>
                    </div>
                    <div class="actions-container">
                        <div class="actions">
                            <button type="submit" name="confirm" class="btn btn-success">Confirm OTP</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
            <?php if (isset($_SESSION['reset-password'])) : ?>
                <div class="titles-container">
                    <h1 class="mb-0">Great ! Last step to login</h1>
                    <p class="mb-4">Enter your new password and make sure to remember it</p>
                </div>
                <form action="<?= APP_URL . "auth/forgot-password" ?>" method="post">
                    <div class="input-group has-validation mb-3">
                        <div class="form-floating <?php if (isset($passwordValidation) && !$passwordValidation['status']) echo 'is-invalid'; ?>">
                            <input type="text" class="form-control" id="input-password" placeholder="Jeffrex" name="password">
                            <label for="input-password">New Password</label>
                        </div>
                        <div class="invalid-feedback">
                            <?= $passwordValidation['message'] ?? null ?>
                        </div>
                        <div id="passwordHelpBlock" class="form-text">
                            <span>
                                Your password must contain:
                            </span>
                            <ul>
                                <li>At least 8 characters long</li>
                                <li>At least 1 upper case letter</li>
                                <li>At least 1 lower case letter</li>
                                <li>At least 1 number</li>
                                <li>At least 1 special character</li>
                            </ul>
                        </div>
                    </div>
                    <div class="actions-container">
                        <div class="actions">
                            <button type="submit" name="reset-password" class="btn btn-success">Reset Password</button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>