<?php
include 'db.php';
header('Content-Type: application/json');

// Security and error handling
ini_set('display_errors', 0);
error_reporting(0);

// Image configuration
define('UPLOAD_DIR', 'uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

try {
    // Validate and sanitize inputs
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $search = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
    $orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
    $orderDir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) 
                ? $_POST['order'][0]['dir'] 
                : 'asc';

    // Column configuration (must match DataTables columns)
    $columns = ['id', 'image', 'name', 'email', 'phone'];
    $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'id';
    $orderBy .= " $orderDir";

    // Base query with parameterized statements
    $baseQuery = "SELECT id, image, name, email, phone FROM userrs";
    $whereClause = '';
    $params = [];
    $types = '';

    // Search filtering
    if (!empty($search)) {
        $whereClause = " WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
        $searchTerm = "%$search%";
        $params = array_fill(0, 3, $searchTerm);
        $types = 'sss';
    }

    // Main data query
    $query = "$baseQuery $whereClause ORDER BY $orderBy LIMIT ?, ?";
    $params[] = $start;
    $params[] = $length;
    $types .= 'ii';

    // Prepare and execute main query
    $stmt = $conn->prepare($query);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Process data with image handling
    $data = [];
    while ($row = $result->fetch_assoc()) {
        // Secure image path handling
        $imagePath = null;
        if (!empty($row['image'])) {
            $ext = strtolower(pathinfo($row['image'], PATHINFO_EXTENSION));
            if (in_array($ext, ALLOWED_EXTENSIONS)) {
                $imagePath = htmlspecialchars(basename($row['image']));
            }
        }

        $data[] = [
            'id' => (int)$row['id'],
            'image' => $imagePath,
            'name' => htmlspecialchars($row['name'] ?? ''),
            'email' => htmlspecialchars($row['email'] ?? ''),
            'phone' => htmlspecialchars($row['phone'] ?? '')
        ];
    }

    // Count queries
    $totalRecords = $conn->query("SELECT COUNT(*) FROM userrs")->fetch_row()[0];
    
    if (!empty($search)) {
        $countQuery = "$baseQuery $whereClause";
        $countStmt = $conn->prepare($countQuery);
        $countStmt->bind_param($types, ...array_slice($params, 0, count($params)-2));
        $countStmt->execute();
        $filteredRecords = $countStmt->get_result()->fetch_row()[0];
    } else {
        $filteredRecords = $totalRecords;
    }

    // Prepare final response
    $response = [
        "draw" => $draw,
        "recordsTotal" => (int)$totalRecords,
        "recordsFiltered" => (int)$filteredRecords,
        "data" => $data
    ];

    echo json_encode($response);

} catch (Exception $e) {
    // Log error for debugging
    error_log("DataTables Error: " . $e->getMessage());
    
    // Return safe error response
    echo json_encode([
        "error" => "An error occurred while processing your request",
        "draw" => isset($draw) ? $draw : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
}

// Close connection
if (isset($conn)) {
    $conn->close();
}