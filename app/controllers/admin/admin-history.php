<?php

function AdminHistoryController()
{
    $currentPage  = max(1, (int) ($_GET['page'] ?? 1));
    $search       = $_GET['search'] ?? '';
    $filterDate   = $_GET['filter_date'] ?? '';
    $filterType   = $_GET['filter_type'] ?? '';
    $filterAcct   = $_GET['filter_acct'] ?? '';
    $dateFrom     = $_GET['date_from'] ?? '';
    $dateTo       = $_GET['date_to'] ?? '';
    $limit        = 10;
    $offset       = ($currentPage - 1) * $limit;

    $conditions = [];
    $params     = [];
    $types      = '';

    if (!empty($search)) {
        $like = '%' . $search . '%';
        $conditions[] = "(a.NAME LIKE ? OR a.USERNAME LIKE ? OR v.PLATE_NUMBER LIKE ? OR vt.VEHICLE_TYPE LIKE ?)";
        $params = array_merge($params, [$like, $like, $like, $like]);
        $types .= 'ssss';
    }

    if (!empty($filterDate)) {
        switch ($filterDate) {
            case 'today':
                $conditions[] = "DATE(FROM_UNIXTIME(h.TIME_IN)) = CURDATE()";
                break;
            case 'week':
                $conditions[] = "YEARWEEK(FROM_UNIXTIME(h.TIME_IN), 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $conditions[] = "MONTH(FROM_UNIXTIME(h.TIME_IN)) = MONTH(CURDATE()) AND YEAR(FROM_UNIXTIME(h.TIME_IN)) = YEAR(CURDATE())";
                break;
            case 'custom':
                if (!empty($dateFrom) && !empty($dateTo)) {
                    $conditions[] = "DATE(FROM_UNIXTIME(h.TIME_IN)) BETWEEN ? AND ?";
                    $params[] = $dateFrom;
                    $params[] = $dateTo;
                    $types .= 'ss';
                }
                break;
        }
    }

    if (!empty($filterType)) {
        $conditions[] = "vt.VEHICLE_TYPE_ID = ?";
        $params[] = $filterType;
        $types .= 'i';
    }

    if (!empty($filterAcct)) {
        $conditions[] = "a.ACCOUNT_TYPE = ?";
        $params[] = $filterAcct;
        $types .= 's';
    }

    $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $sql = "
        SELECT
            a.NAME           AS name,
            a.USERNAME       AS username,
            a.ACCOUNT_TYPE   AS account_type,
            v.PLATE_NUMBER   AS plate_number,
            vt.VEHICLE_TYPE  AS vehicle_type,
            h.TIME_IN        AS time_in,
            h.TIME_OUT       AS time_out,
            h.AMOUNT_TO_PAY  AS amount_to_pay,
            h.PAYMENT        AS payment
        FROM tbl_payment_history h
        INNER JOIN tbl_accounts a        ON a.UID              = h.UID
        INNER JOIN tbl_vehicles v        ON v.VEHICLE_ID       = h.VEHICLE_ID
        INNER JOIN tbl_vehicle_types vt  ON vt.VEHICLE_TYPE_ID = v.VEHICLE_TYPE_ID
        $where
        ORDER BY h.TIME_IN DESC
        LIMIT ? OFFSET ?
    ";

    $allParams = array_merge($params, [$limit, $offset]);
    $allTypes  = $types . 'ii';

    $stmt = DB_CONNECT->prepare($sql);
    if (!empty($allParams)) {
        $stmt->bind_param($allTypes, ...$allParams);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $countSql = "
        SELECT COUNT(*) AS total
        FROM tbl_payment_history h
        INNER JOIN tbl_accounts a        ON a.UID              = h.UID
        INNER JOIN tbl_vehicles v        ON v.VEHICLE_ID       = h.VEHICLE_ID
        INNER JOIN tbl_vehicle_types vt  ON vt.VEHICLE_TYPE_ID = v.VEHICLE_TYPE_ID
        $where
    ";

    $countStmt = DB_CONNECT->prepare($countSql);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $total = $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $vtResult = VehicleModel::getInstance()->getAllVehicleTypes();
    $vehicleTypes = $vtResult['results']['rows'] ?? [];

    return [
        'status'  => true,
        'message' => 'History fetched successfully',
        'results' => [
            'rows'         => $rows,
            'total'        => (int) $total,
            'page'         => $currentPage,
            'limit'        => $limit,
            'total_pages'  => (int) ceil($total / $limit),
            'search'       => $search,
            'filter_date'  => $filterDate,
            'filter_type'  => $filterType,
            'filter_acct'  => $filterAcct,
            'date_from'    => $dateFrom,
            'date_to'      => $dateTo,
            'vehicle_types' => $vehicleTypes,
        ]
    ];
}