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
            $id=(int)$_GET['id'];
            $stmt=$conn->prepare('SELECT id,kode_buku,judul,pengarang,tahun_terbit,stok,created_at FROM buku WHERE id=?');
            $stmt->bind_param('i',$id); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
            if (!$row) jsonResponse(404, 'error', 'Buku tidak ditemukan.', null);
            jsonResponse(200, 'success', 'Detail buku berhasil diambil.', $row);
        }
        $search=trim($_GET['search'] ?? ''); $page=isset($_GET['page'])?(int)$_GET['page']:1; $limit=isset($_GET['limit'])?(int)$_GET['limit']:10;
        if ($page<1 || $limit<1 || $limit>100) jsonResponse(400,'error','page harus >= 1 dan limit 1–100.',null);
        $offset=($page-1)*$limit; $like='%'.$search.'%';
        $stmt=$conn->prepare('SELECT id,kode_buku,judul,pengarang,tahun_terbit,stok,created_at FROM buku WHERE kode_buku LIKE ? OR judul LIKE ? OR pengarang LIKE ? ORDER BY id LIMIT ? OFFSET ?');
        $stmt->bind_param('sssii',$like,$like,$like,$limit,$offset); $stmt->execute();
        jsonResponse(200,'success','Daftar buku berhasil diambil',['page'=>$page,'limit'=>$limit,'data'=>$stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
    }
    if ($method === 'POST') {
        $data=readJsonBody(); requireFields($data,['kode_buku','judul','pengarang','tahun_terbit','stok']);
        if (!validYear($data['tahun_terbit']) || !validNonNegativeInt($data['stok'])) jsonResponse(400,'error','tahun_terbit atau stok tidak valid.',null);
        $stmt=$conn->prepare('SELECT id FROM buku WHERE kode_buku=?'); $stmt->bind_param('s',$data['kode_buku']); $stmt->execute();
        if ($stmt->get_result()->num_rows>0) jsonResponse(409,'error','Kode buku sudah digunakan.',null);
        $tahun=(int)$data['tahun_terbit']; $stok=(int)$data['stok'];
        $stmt=$conn->prepare('INSERT INTO buku (kode_buku,judul,pengarang,tahun_terbit,stok) VALUES (?,?,?,?,?)');
        $stmt->bind_param('sssii',$data['kode_buku'],$data['judul'],$data['pengarang'],$tahun,$stok); $stmt->execute();
        jsonResponse(201,'success','Buku berhasil ditambahkan',['id'=>$conn->insert_id]);
    }
    if ($method === 'PUT') {
        if (!isset($_GET['id']) || !validPositiveInt($_GET['id'])) jsonResponse(400,'error','Parameter id wajib berupa bilangan bulat positif.',null);
        $id=(int)$_GET['id']; $data=readJsonBody(); requireFields($data,['kode_buku','judul','pengarang','tahun_terbit','stok']);
        if (!validYear($data['tahun_terbit']) || !validNonNegativeInt($data['stok'])) jsonResponse(400,'error','tahun_terbit atau stok tidak valid.',null);
        $stmt=$conn->prepare('SELECT id FROM buku WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) jsonResponse(404,'error','Buku tidak ditemukan.',null);
        $stmt=$conn->prepare('SELECT id FROM buku WHERE kode_buku=? AND id<>?'); $stmt->bind_param('si',$data['kode_buku'],$id); $stmt->execute();
        if ($stmt->get_result()->num_rows>0) jsonResponse(409,'error','Kode buku sudah digunakan.',null);
        $tahun=(int)$data['tahun_terbit']; $stok=(int)$data['stok'];
        $stmt=$conn->prepare('UPDATE buku SET kode_buku=?,judul=?,pengarang=?,tahun_terbit=?,stok=? WHERE id=?');
        $stmt->bind_param('sssiii',$data['kode_buku'],$data['judul'],$data['pengarang'],$tahun,$stok,$id); $stmt->execute();
        jsonResponse(200,'success','Buku berhasil diperbarui',['id'=>$id]);
    }
    if ($method === 'DELETE') {
        if (!isset($_GET['id']) || !validPositiveInt($_GET['id'])) jsonResponse(400,'error','Parameter id wajib berupa bilangan bulat positif.',null);
        $id=(int)$_GET['id']; $stmt=$conn->prepare('SELECT id FROM buku WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) jsonResponse(404,'error','Buku tidak ditemukan.',null);
        $stmt=$conn->prepare('SELECT id FROM peminjaman WHERE buku_id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            jsonResponse(409, 'error', 'Buku masih memiliki data peminjaman.', null);
        }
        $stmt=$conn->prepare('DELETE FROM buku WHERE id=?');
        $stmt->bind_param('i',$id);
        $stmt->execute();
        jsonResponse(200,'success','Buku berhasil dihapus',['id'=>$id]);
    }
    header('Allow: GET, POST, PUT, DELETE'); jsonResponse(405,'error','Method tidak diizinkan.',null);
} catch (Throwable $e) { handleApiError($e); }

function validNonNegativeInt($value): bool {
    return (is_int($value) || is_string($value))
        && filter_var($value, FILTER_VALIDATE_INT) !== false
        && (int)$value >= 0;
}

function validYear($value): bool {
    return (is_int($value) || is_string($value))
        && filter_var($value, FILTER_VALIDATE_INT) !== false
        && (int)$value >= 1000
        && (int)$value <= 2155;
}
