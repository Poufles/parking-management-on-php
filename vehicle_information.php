<?php
class vehicle_information
{
    private mysqli $connect;

    public function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    // ADDS INTO THE tbl_vehicle_types COLUMN
    public function addVehicle($plateNumber, $vehicleTypeId)
    {
        $sql = "INSERT INTO tbl_vehicles
                (PLATE_NUMBER, VEHICLE_TYPE_ID)
                VALUES
                ('$plateNumber', '$vehicleTypeId')";

        $this->connect->query($sql);
    }

    // THIS GETS THE VEHICLE ID SO THE tbl_vehicles CAN GET THE PRIMARY KEY of tbl_vehicle_types
    public function getVehicleTypeId($vehicleType)
    {
        $sql = "SELECT VEHICLE_TYPE_ID
                FROM tbl_vehicle_types
                WHERE VEHICLE_TYPE = '$vehicleType'";

        $result = $this->connect->query($sql);

        if ($row = $result->fetch_assoc()) {
            return $row['VEHICLE_TYPE_ID'];
        }

        return null;
    }

    // THIS ADDS INTO THE VEHICLE_ATTACHMENT COLUMN OF tbl_uploads
    public function addVehicleUpload($attachment)
    {
         $sql = "INSERT INTO tbl_uploads
            (UPLOADED_FILE)
            VALUES
            ('$attachment')";

        $this->connect->query($sql);
    }
}

?>