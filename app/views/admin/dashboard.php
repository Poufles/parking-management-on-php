<?php

$stats = $response['results'] ?? [];
$pendingCheckout = $stats['pending_checkout'] ?? 0;
?>

<h4 class="page-title">Dashboard</h4>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">

    <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #606C38; text-align: center;">
        <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Total Slots</div>
        <div style="font-size: 42px; font-weight: 700; color: #283618;"><?= $stats['total_slots'] ?? 0 ?></div>
    </div>

    <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #dc3545; text-align: center;">
        <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Occupied</div>
        <div style="font-size: 42px; font-weight: 700; color: #dc3545;"><?= $stats['occupied_slots'] ?? 0 ?></div>
    </div>

    <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #198754; text-align: center;">
        <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Available</div>
        <div style="font-size: 42px; font-weight: 700; color: #198754;"><?= $stats['available_slots'] ?? 0 ?></div>
    </div>

    <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #ffc107; text-align: center;">
        <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Pending Checkout</div>
        <div style="font-size: 42px; font-weight: 700; color: #ffc107;"><?= $pendingCheckout ?></div>
    </div>

    <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #DDA15E; text-align: center;">
        <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Total Clients</div>
        <div style="font-size: 42px; font-weight: 700; color: #BC6C25;"><?= $stats['total_clients'] ?? 0 ?></div>
    </div>

    <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-left: 5px solid #283618; text-align: center;">
        <div style="font-size: 13px; color: #888; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">Today's Revenue</div>
        <div style="font-size: 36px; font-weight: 700; color: #283618;">&#8369; <?= number_format($stats['today_revenue'] ?? 0, 2) ?></div>
    </div>

</div>

<?php if ($pendingCheckout > 0): ?>
    <div style="margin-top: 24px;">
        <a href="<?= APP_URL . 'admin/parking-slots' ?>" class="btn btn-warning">
            ⚠️ View <?= $pendingCheckout ?> Pending Checkout Request(s)
        </a>
    </div>
<?php endif; ?>
