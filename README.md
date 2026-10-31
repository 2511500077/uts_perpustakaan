<<<<<<< HEAD
=======
<<<<<<< HEAD
<<<<<<< HEAD

# UTS Database Client-Server — API Perpustakaan

## Tema

API Perpustakaan sederhana menggunakan PHP Native, MySQL, dan `mysqli` prepared statements.

## Anggota kelompok

Panji (2511500074)
Afdal (2511500076)
Julio Putrawan (2511500076)
Ade Putra (2511500078)

=======
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
# UTS Database Client-Server — API Perpustakaan

## Tema
API Perpustakaan sederhana menggunakan PHP Native, MySQL, dan `mysqli` prepared statements.

## Anggota kelompok
Lengkapi sebelum dikumpulkan:
<<<<<<< HEAD
=======
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
- Anggota 1 — NIM: ..., Nama: ..., Tugas: database/ERD
- Anggota 2 — NIM: ..., Nama: ..., Tugas: endpoint anggota
- Anggota 3 — NIM: ..., Nama: ..., Tugas: endpoint buku
- Anggota 4 (jika ada) — NIM: ..., Nama: ..., Tugas: endpoint peminjaman/testing

## Struktur
<<<<<<< HEAD
=======
<<<<<<< HEAD

=======
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
- `api/anggota.php` — CRUD anggota
- `api/buku.php` — CRUD buku
- `api/peminjaman.php` — CRUD peminjaman; GET menggunakan JOIN untuk nama anggota dan judul buku
- `helpers/response.php` — response JSON konsisten dan validasi dasar
- `config.php` — koneksi database
- `database/schema.sql` — database, tabel, FK, dan seed data
- `thunderclient/UTS.Thunderclient_collection.json` — koleksi request awal
- `screenshot/` — simpan screenshot hasil pengujian nyata di sini
- `link-video.txt` — isi tautan rekaman Zoom setelah diunggah

## ERD
<<<<<<< HEAD
=======
<<<<<<< HEAD

=======
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
mermaid
erDiagram
    ANGGOTA ||--o{ PEMINJAMAN : melakukan
    BUKU ||--o{ PEMINJAMAN : dipinjam
    ANGGOTA {
      int id PK
      varchar no_anggota UK
      varchar nama
      varchar email UK
      varchar telepon
    }
    BUKU {
      int id PK
      varchar kode_buku UK
      varchar judul
      varchar pengarang
      year tahun_terbit
      int stok
    }
    PEMINJAMAN {
      int id PK
      int anggota_id FK
      int buku_id FK
      date tanggal_pinjam
      date tanggal_kembali
      enum status
    }
```

## Cara menjalankan di Laragon
<<<<<<< HEAD
=======
<<<<<<< HEAD

=======
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
1. Ekstrak folder `uts-perpustakaan` ke `C:\\laragon\\www\\uts-perpustakaan`.
2. Jalankan Laragon, aktifkan Apache dan MySQL.
3. Buka phpMyAdmin, import `database/schema.sql`. Script membuat database `uts_perpustakaan`.
4. Pastikan `config.php` cocok dengan kredensial MySQL lokal. Default Laragon biasanya user `root` dan password kosong.
5. Uji URL berikut di browser/Thunder Client:
   - `http://localhost/uts-perpustakaan/api/anggota.php`
   - `http://localhost/uts-perpustakaan/api/buku.php`
   - `http://localhost/uts-perpustakaan/api/peminjaman.php`

## Kontrak endpoint
<<<<<<< HEAD
=======
<<<<<<< HEAD

Semua respons berbentuk `{ "status": "...", "message": "...", "data": ... }`.
Untuk POST dan PUT, pilih Body → JSON.

| Endpoint                 | Method | URL                                               | Body utama                                                                                                        |
| ------------------------ | ------ | ------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------- |
| Anggota                  | GET    | `/api/anggota.php`                                | —                                                                                                                 |
| Anggota detail           | GET    | `/api/anggota.php?id=1`                           | —                                                                                                                 |
| Anggota cari/paginasi    | GET    | `/api/anggota.php?search=andi&page=1&limit=10`    | —                                                                                                                 |
| Anggota                  | POST   | `/api/anggota.php`                                | `{"no_anggota":"AGT-2026-006","nama":"Dewi Lestari","email":"dewi.lestari@example.com","telepon":"081234560006"}` |
| Anggota                  | PUT    | `/api/anggota.php?id=1`                           | seluruh field anggota                                                                                             |
| Anggota                  | DELETE | `/api/anggota.php?id=1`                           | —                                                                                                                 |
| Buku                     | GET    | `/api/buku.php`                                   | —                                                                                                                 |
| Buku detail              | GET    | `/api/buku.php?id=1`                              | —                                                                                                                 |
| Buku cari/paginasi       | GET    | `/api/buku.php?search=bumi&page=1&limit=10`       | —                                                                                                                 |
| Buku                     | POST   | `/api/buku.php`                                   | `{"kode_buku":"BK-006","judul":"Atomic Habits","pengarang":"James Clear","tahun_terbit":2018,"stok":5}`           |
| Buku                     | PUT    | `/api/buku.php?id=1`                              | seluruh field buku                                                                                                |
| Buku                     | DELETE | `/api/buku.php?id=6`                              | —                                                                                                                 |
| Peminjaman               | GET    | `/api/peminjaman.php`                             | —                                                                                                                 |
| Peminjaman detail        | GET    | `/api/peminjaman.php?id=1`                        | —                                                                                                                 |
| Peminjaman cari/paginasi | GET    | `/api/peminjaman.php?search=andi&page=1&limit=10` | —                                                                                                                 |
| Peminjaman               | POST   | `/api/peminjaman.php`                             | `{"anggota_id":1,"buku_id":2,"tanggal_pinjam":"2026-10-30","tanggal_kembali":null,"status":"dipinjam"}`           |
| Peminjaman               | PUT    | `/api/peminjaman.php?id=1`                        | seluruh field peminjaman                                                                                          |
| Peminjaman               | DELETE | `/api/peminjaman.php?id=5`                        | —                                                                                                                 |

## Status yang diharapkan

=======
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
Semua respons berbentuk `{ "status": "...", "message": "...", "data": ... }`.
Untuk POST dan PUT, pilih Body → JSON.

| Endpoint | Method | URL | Body utama |
|---|---|---|---|
| Anggota | GET | `/api/anggota.php` | — |
| Anggota detail | GET | `/api/anggota.php?id=1` | — |
| Anggota cari/paginasi | GET | `/api/anggota.php?search=andi&page=1&limit=10` | — |
| Anggota | POST | `/api/anggota.php` | `{"no_anggota":"AGT-2026-006","nama":"Dewi Lestari","email":"dewi.lestari@example.com","telepon":"081234560006"}` |
| Anggota | PUT | `/api/anggota.php?id=1` | seluruh field anggota |
| Anggota | DELETE | `/api/anggota.php?id=1` | — |
| Buku | GET | `/api/buku.php` | — |
| Buku detail | GET | `/api/buku.php?id=1` | — |
| Buku cari/paginasi | GET | `/api/buku.php?search=bumi&page=1&limit=10` | — |
| Buku | POST | `/api/buku.php` | `{"kode_buku":"BK-006","judul":"Atomic Habits","pengarang":"James Clear","tahun_terbit":2018,"stok":5}` |
| Buku | PUT | `/api/buku.php?id=1` | seluruh field buku |
| Buku | DELETE | `/api/buku.php?id=6` | — |
| Peminjaman | GET | `/api/peminjaman.php` | — |
| Peminjaman detail | GET | `/api/peminjaman.php?id=1` | — |
| Peminjaman cari/paginasi | GET | `/api/peminjaman.php?search=andi&page=1&limit=10` | — |
| Peminjaman | POST | `/api/peminjaman.php` | `{"anggota_id":1,"buku_id":2,"tanggal_pinjam":"2026-10-30","tanggal_kembali":null,"status":"dipinjam"}` |
| Peminjaman | PUT | `/api/peminjaman.php?id=1` | seluruh field peminjaman |
| Peminjaman | DELETE | `/api/peminjaman.php?id=5` | — |

## Status yang diharapkan
<<<<<<< HEAD
=======
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
- `200`: GET sukses, PUT sukses, DELETE sukses
- `201`: POST sukses
- `400`: parameter/body/field tidak valid
- `404`: ID atau FK tidak ditemukan
- `409`: kode unik duplikat
- `405`: HTTP method tidak didukung
- `500`: error server generik; detail dicatat dengan `error_log`

## Catatan pengujian sebelum dikumpulkan
<<<<<<< HEAD
=======
<<<<<<< HEAD

=======
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
- Jalankan seluruh request di Thunder Client dan simpan screenshot hasil nyata ke `screenshot/`.
- Pastikan tes 400, 404, 409, 405 serta uji injeksi `?id=1' OR 1=1 --` gagal membocorkan data.
- Export seluruh request Thunder Client sebagai collection JSON setelah menambahkan semua kasus sukses dan gagal.
- Isi nama/NIM/tugas anggota kelompok, tautan repo GitHub publik, dan tautan rekaman Zoom.
<<<<<<< HEAD
- Project ini adalah starter yang disusun untuk spesifikasi tugas; verifikasi di lingkungan Laragon sebelum dikumpulkan.
=======
<<<<<<< HEAD
- # Project ini adalah starter yang disusun untuk spesifikasi tugas; verifikasi di lingkungan Laragon sebelum dikumpulkan.

# uts_perpustakaan

> > > > > > > 527147acf1fb7cf6f3218f52372c47aca8fd8c44
=======
- Project ini adalah starter yang disusun untuk spesifikasi tugas; verifikasi di lingkungan Laragon sebelum dikumpulkan.
>>>>>>> 2d3c7bb (nyambungin repo ke vscode)
.
>>>>>>> b446c545c5af28bb491ef37556449fbf114972bb
