<?php

class FileModel
{
    private static ?FileModel $instance = null;
    private mysqli $connect;
    private const TABLE = 'tbl_uploads';
    public const TABLE_FILE_TYPES = 'tbl_file_types';

    private function __construct()
    {
        $this->connect = DB_CONNECT;
    }

    public static function getInstance(): FileModel
    {
        if (self::$instance === null) {
            self::$instance = new FileModel();
        }

        return self::$instance;
    }

    public function getFileTypes()
    {
        try {
            $query = "
            SELECT *
            FROM " . self::TABLE_FILE_TYPES . "
            ";

            $results = $this->connect->query($query);
            $rows = $results->fetch_all(MYSQLI_ASSOC);

            return [
                'status' => $results,
                'message' => $results
                    ? "File added successfully"
                    : "Something went wrong...",
                'results' => [
                    'rows' => $rows
                ]
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        }
    }

    public function uploadFile($uid, $file_type_id, $file, $vehicle_id = null)
    {
        try {
            $query = "
            INSERT INTO " . self::TABLE . " (
            uid, vehicle_id, file_type_id, uploaded_file 
            ) VALUES (?, ?, ?, ?)
            ";

            $filename = $file['name'];

            $stmt = $this->connect->prepare($query);
            $stmt->bind_param(
                'iiis',
                $uid,
                $vehicle_id,
                $file_type_id,
                $filename
            );

            $results = $stmt->execute();

            $fileTmpPath = $file['tmp_name'];
            $uploadFolder = __DIR__ . "/../uploads/$uid/";

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }

            $safeFileName = $filename;
            $destination = $uploadFolder . $safeFileName;

            move_uploaded_file($fileTmpPath, $destination);

            return [
                'status' => $results,
                'message' => $results
                    ? "File added successfully"
                    : "Something went wrong...",
                'results' => []
            ];
        } catch (Exception $err) {
            return [
                'status' => false,
                'message' => $err->getMessage(),
                'results' => []
            ];
        }
    }

    public function getFile($uid, $file_type_id, $vehicle_id = null) {

    }
}
