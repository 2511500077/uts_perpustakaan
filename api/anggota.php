<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers/response.php';

function getInputValue(array $data, string $key) {
    return $data[$key] ?? null;
}

$method = $_SERVER['REQUEST_METHOD'];
try {
    $conn = db();
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            if (!validPositiveInt($_GET['id'])) jsonResponse(400, 'error', 'Parameter id harus bilangan bulat positif.', null);
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare('SELECT id, no_anggota, nama, email, telepon, created_at FROM anggota WHERE id = ?');
            $stmt->bind_param('i', $id); $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if (!$row) jsonResponse(404, 'error', 'Anggota tidak ditemukan.', null);
            jsonResponse(200, 'success', 'Detail anggota berhasil diambil.', $row);
        }
        $search = trim($_GET['search'] ?? '');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        if ($page < 1 || $limit < 1 || $limit > 100) jsonResponse(400, 'error', 'page harus >= 1 dan limit 1–100.', null);
        $offset = ($page - 1) * $limit;
        $like = '%' . $search . '%';
        $stmt = $conn->prepare('SELECT id, no_anggota, nama, email, telepon, created_at FROM anggota WHERE no_anggota LIKE ? OR nama LIKE ? OR email LIKE ? ORDER BY id LIMIT ? OFFSET ?');
        $stmt->bind_param('sssii', $like, $like, $like, $limit, $offset); $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        jsonResponse(200, 'success', 'Daftar anggota berhasil diambil.', ['page'=>$page, 'limit'=>$limit, 'data'=>$rows]);
    }
    if ($method === 'POST') {
        $data = readJsonBody(); requireFields($data, ['no_anggota','nama','email','telepon']);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) jsonResponse(400, 'error', 'Format email tidak valid.', null);
        $stmt = $conn->prepare('SELECT id FROM anggota WHERE no_anggota = ? OR email = ?');
        $stmt->bind_param('ss', $data['no_anggota'], $data['email']); $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) jsonResponse(409, 'error', 'Nomor anggota atau email sudah digunakan.', null);
        $stmt = $conn->prepare('INSERT INTO anggota (no_anggota,nama,email,telepon) VALUES (?,?,?,?)');
        $stmt->bind_param('ssss', $data['no_anggota'], $data['nama'], $data['email'], $data['telepon']); $stmt->execute();
        jsonResponse(201, 'success', 'Anggota berhasil ditambahkan.', ['id'=>$conn->insert_id]);
    }
    if ($method === 'PUT') {
        if (!isset($_GET['id']) || !validPositiveInt($_GET['id'])) jsonResponse(400, 'error', 'Parameter id wajib berupa bilangan bulat positif.', null);
        $id = (int)$_GET['id']; $data = readJsonBody(); requireFields($data, ['no_anggota','nama','email','telepon']);
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) jsonResponse(400, 'error', 'Format email tidak valid.', null);
        $stmt = $conn->prepare('SELECT id FROM anggota WHERE id = ?'); $stmt->bind_param('i',$id); $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) jsonResponse(404, 'error', 'Anggota tidak ditemukan.', null);
        $stmt = $conn->prepare('SELECT id FROM anggota WHERE (no_anggota = ? OR email = ?) AND id <> ?');
        $stmt->bind_param('ssi',$data['no_anggota'],$data['email'],$id); $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) jsonResponse(409, 'error', 'Nomor anggota atau email sudah digunakan.', null);
        $stmt = $conn->prepare('UPDATE anggota SET no_anggota=?, nama=?, email=?, telepon=? WHERE id=?');
        $stmt->bind_param('ssssi',$data['no_anggota'],$data['nama'],$data['email'],$data['telepon'],$id); $stmt->execute();
        jsonResponse(200, 'success', 'Anggota berhasil diperbarui.', ['id'=>$id]);
    }
    if ($method === 'DELETE') {
        if (!isset($_GET['id']) || !validPositiveInt($_GET['id'])) jsonResponse(400, 'error', 'Parameter id wajib berupa bilangan bulat positif.', null);
        $id=(int)$_GET['id'];
        $stmt=$conn->prepare('SELECT id FROM anggota WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) jsonResponse(404, 'error', 'Anggota tidak ditemukan.', null);
        $stmt = $conn->prepare('SELECT id FROM peminjaman WHERE anggota_id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            jsonResponse(409, 'error', 'Anggota masih memiliki data peminjaman.', null);
        }
        $stmt = $conn->prepare('DELETE FROM anggota WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        jsonResponse(200, 'success', 'Anggota berhasil dihapus.', ['id'=>$id]);
    }
    header('Allow: GET, POST, PUT, DELETE');
    jsonResponse(405, 'error', 'Method tidak diizinkan.', null);
} catch (Throwable $e) { handleApiError($e); }
