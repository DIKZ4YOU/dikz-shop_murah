# 📦 Panel DikzShop — Panduan Lengkap

## 🗂 Struktur File
```
panel_dikzshop/
├── index.php          ← Panel admin (buka di browser)
├── apiii.php          ← API kirim email blast
└── dikzshop/
    ├── add.php        ← API tambah email pembeli
    ├── delete.php     ← API hapus email (by email atau by index)
    ├── ganti.php      ← API ganti identitas panel
    ├── data.php       ← Config nama & email pengirim
    └── data.json      ← Database email pembeli (otomatis terisi)
```

---

## 🚀 Cara Pasang di Hosting

1. **Upload semua file** ke hosting PHP kamu (cPanel, Niagahoster, IDHostinger, dll)
2. Pastikan folder `dikzshop/` punya permission **write** (chmod 755 atau 777)
3. Buka `https://namadomain.com/index.php` → Panel siap!
4. Ganti URL di file `dikzshop_v2.html`:
   ```js
   const PANEL_ADD_URL = "https://namadomain.com/dikzshop/add.php";
   const PANEL_DEL_URL = "https://namadomain.com/dikzshop/delete.php";
   ```

---

## 🔗 Endpoint API

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/dikzshop/add.php?mail=email@x.com` | Tambah email pembeli |
| `GET` | `/dikzshop/delete.php?mail=email@x.com` | **Hapus by email** ← dipakai auto-delete |
| `GET` | `/dikzshop/delete.php?keys=0` | Hapus by index (panel manual) |
| `GET` | `/dikzshop/ganti.php?nick=Nama&sender=email` | Ganti identitas |
| `POST` | `/apiii.php` | Kirim email blast |

---

## ⚙️ Alur Kerja Sistem

```
Pembeli beli jasteb
       ↓
Isi email di DikzShop → add.php → data.json
       ↓
Waktu jasteb habis (otomatis) atau Admin "Hapus & Akhiri"
       ↓
delete.php?mail=email@x.com → email terhapus dari data.json
```

---

## 📨 Email Blast via Panel

1. Buka `index.php` di browser
2. Isi **Subjek** dan **Isi Pesan** (HTML dibolehkan)
3. Klik **Kirim ke Semua**

Contoh isi pesan HTML:
```html
<h2>Halo Pelanggan DikzShop!</h2>
<p>Terima kasih sudah memesan jasteb kami.</p>
<p>Ada promo spesial hari ini! Kunjungi toko kami.</p>
```

---

## ⚠️ Catatan Penting

- Pastikan fungsi `mail()` aktif di hosting kamu
- Untuk hasil lebih baik, gunakan SMTP (mis. PHPMailer + Gmail SMTP)
- File `data.json` akan terisi otomatis, **jangan dihapus manual**
- Jika panel tidak bisa tulis file, cek permission folder `dikzshop/`
