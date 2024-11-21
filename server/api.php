<?php
include 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $filters = $data['filters'] ?? [];
    $sort = $data['sort'] ?? null;

    $query = "SELECT * FROM employees WHERE 1=1";
    $params = [];

    if (!empty($filters['country'])) {
        $query .= " AND country IN (" . implode(',', array_fill(0, count($filters['country']), '?')) . ")";
        $params = array_merge($params, $filters['country']);
    }

    if (!empty($filters['city'])) {
        $query .= " AND city IN (" . implode(',', array_fill(0, count($filters['city']), '?')) . ")";
        $params = array_merge($params, $filters['city']);
    }

    if ($sort) {
        $query .= " ORDER BY " . $sort['column'] . " " . strtoupper($sort['direction']);
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($employees);
}
?>
