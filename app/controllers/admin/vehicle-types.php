<?php

function VehicleTypesController()
{
    if (isset($_GET['delete_id'])) {
        $id = (int) $_GET['delete_id'];

        $stmt = DB_CONNECT->prepare("SELECT COUNT(*) as total FROM tbl_vehicles WHERE vehicle_type_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $count = $stmt->get_result()->fetch_assoc()['total'];

        if ($count > 0) {
            header('location: ' . APP_URL . 'admin/vehicles?error=' . urlencode("Cannot delete: $count vehicle(s) are still using this type."));
            exit;
        }

        $stmt2 = DB_CONNECT->prepare("DELETE FROM tbl_vehicle_types WHERE vehicle_type_id = ?");
        $stmt2->bind_param('i', $id);
        $stmt2->execute();

        header('location: ' . APP_URL . 'admin/vehicles?success=' . urlencode("Vehicle type deleted successfully."));
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
        $id       = (int) $_POST['edit_id'];
        $new_name = trim($_POST['vehicle_type'] ?? '');

        if (empty($new_name)) {
            header('location: ' . APP_URL . 'admin/vehicles?edit_id=' . $id . '&error=' . urlencode("Vehicle type name cannot be empty."));
            exit;
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $new_name)) {
            header('location: ' . APP_URL . 'admin/vehicles?edit_id=' . $id . '&error=' . urlencode("Letters only, no numbers or special characters."));
            exit;
        }

        $stmt = DB_CONNECT->prepare("UPDATE tbl_vehicle_types SET vehicle_type = ? WHERE vehicle_type_id = ?");
        $stmt->bind_param('si', $new_name, $id);
        $stmt->execute();

        header('location: ' . APP_URL . 'admin/vehicles?success=' . urlencode("Vehicle type updated successfully."));
        exit;
    }

    $response = VehicleModel::getInstance()->getAllVehicleTypes();
    return $response;
}