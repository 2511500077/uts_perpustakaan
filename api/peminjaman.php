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
        $base = 'SELECT p.id, p.tanggal_pinjam, p.tanggal_kembali, p.status, a.no_anggota, a.nama AS nama_anggota, b.kode_buku, b.judul AS judul_buku FROM peminjaman p INNER JOIN anggota a ON p.anggota_id=a.id INNER JOIN buku b ON p.buku_id=b.id';
        if (isset($_GET['id'])) {
            if (!validPositiveInt($_GET['id'])) jsonResponse(400,'error','Parameter id harus bilangan bulat positif.',null);
            $id=(int)$_GET['id']; $stmt=$conn->prepare($base.' WHERE p.id=?'); $stmt->bind_param('i',$id); $stmt->execute();
            $row=$stmt->get_result()->fetch_assoc(); if (!$row) jsonResponse(404,'error','Peminjaman tidak ditemukan.',null);
            jsonResponse(200,'success','Detail peminjaman berhasil diambil.',$row);
        }
        $search=trim($_GET['search'] ?? ''); $page=isset($_GET['page'])?(int)$_GET['page']:1; $limit=isset($_GET['limit'])?(int)$_GET['limit']:10;
        if ($page<1 || $limit<1 || $limit>100) jsonResponse(400,'error','page harus >= 1 dan limit 1–100.',null);
        $offset=($page-1)*$limit; $like='%'.$search.'%';
        $stmt=$conn->prepare($base.' WHERE a.nama LIKE ? OR a.no_anggota LIKE ? OR b.judul LIKE ? OR p.status LIKE ? ORDER BY p.id LIMIT ? OFFSET ?');
        $stmt->bind_param('ssssii',$like,$like,$like,$like,$limit,$offset); $stmt->execute();
        jsonResponse(200,'success','Daftar peminjaman berhasil diambil',['page'=>$page,'limit'=>$limit,'data'=>$stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
    }
    if ($method === 'POST') {
        $data=readJsonBody(); requireFields($data,['anggota_id','buku_id','tanggal_pinjam','status']);
        if (!validPositiveInt($data['anggota_id']) || !validPositiveInt($data['buku_id'])) jsonResponse(400,'error','anggota_id dan buku_id harus bilangan bulat positif.',null);
        if (!in_array($data['status'],['dipinjam','dikembalikan'],true)) jsonResponse(400,'error','status harus dipinjam atau dikembalikan.',null);
        if (!validDate($data['tanggal_pinjam']) || (!empty($data['tanggal_kembali']) && !validDate($data['tanggal_kembali']))) jsonResponse(400,'error','Format tanggal harus YYYY-MM-DD.',null);
        $aid=(int)$data['anggota_id']; $bid=(int)$data['buku_id'];
        $stmt=$conn->prepare('SELECT id FROM anggota WHERE id=?'); $stmt->bind_param('i',$aid); $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) jsonResponse(404,'error','Anggota terkait tidak ditemukan.',null);
        $stmt=$conn->prepare('SELECT id FROM buku WHERE id=?'); $stmt->bind_param('i',$bid); $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) jsonResponse(404,'error','Buku terkait tidak ditemukan.',null);
        $kembali=empty($data['tanggal_kembali'])?null:$data['tanggal_kembali']; $status=$data['status'];
        $stmt=$conn->prepare('INSERT INTO peminjaman (anggota_id,buku_id,tanggal_pinjam,tanggal_kembali,status) VALUES (?,?,?,?,?)');
        $stmt->bind_param('iisss',$aid,$bid,$data['tanggal_pinjam'],$kembali,$status); $stmt->execute();
        jsonResponse(201,'success','Peminjaman berhasil ditambahkan',['id'=>$conn->insert_id]);
    }
    