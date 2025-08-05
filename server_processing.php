<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include 'db.php';

try {
    // Get DataTables parameters
    $draw = (int)($_POST['draw'] ?? 1);
    $start = (int)($_POST['start'] ?? 0);
    $length = (int)($_POST['length'] ?? 10);
    $search = $_POST['search']['value'] ?? '';
    
    // Column mapping
    $columns = ['id', 'image', 'name', 'email', 'phone'];
    $orderColumn = $columns[$_POST['order'][0]['column'] ?? 0] ?? 'id';
    $orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'asc' ? 'ASC' : 'DESC';

    // Base query
    $query = "SELECT id, image, name, email, phone FROM userrs";
    $where = '';
    $params = [];
    
    // Search filter
    if (!empty($search)) {
        $where = " WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?";
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    
    // Count total records
    $totalRecords = $conn->query("SELECT COUNT(*) FROM userrs")->fetch_row()[0];
    
    // Count filtered records
    $filteredQuery = "SELECT COUNT(*) FROM userrs $where";
    $stmt = $conn->prepare($filteredQuery);
    if (!empty($params)) $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    $filteredRecords = $stmt->get_result()->fetch_row()[0];
    
    // Data query
    $dataQuery = "$query $where ORDER BY $orderColumn $orderDir LIMIT ?, ?";
    $params[] = $start;
    $params[] = $length;
    
    $stmt = $conn->prepare($dataQuery);
    $types = (!empty($search) ? 'sss' : '') . 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Build response
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => (int)$row['id'],
            'image' => $row['image'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone']
        ];
    }
    
    // Return JSON
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => (int)$totalRecords,
        "recordsFiltered" => (int)$filteredRecords,
        "data" => $data
    ]);

} catch (Exception $e) {
    // Log error and return valid JSON
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        "draw" => $_POST['draw'] ?? 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => "An error occurred"
    ]);
}