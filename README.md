# 📦 DikzShop v2 — Panduan Deploy

## 🗂 Struktur File
```
dikzshop/
├── public/
│   └── index.html       ← Frontend (React, no build needed)
├── dikzshop/
│   ├── data.json        ← Database email pembeli (auto-generated)
│   └── config.json      ← Konfigurasi nama & email (auto-generated)
├── server.js            ← Backend Node.js (Express)
├── package.json
├── .env.example         ← Template env variables
├── vercel.json          ← Config Vercel
├── Dockerfile           ← Config Docker
└── Procfile             ← Config Heroku
```

---

## 🚀 Cara Deploy di Berbagai Platform

### 1. 🖥️ VPS / Server Sendiri
```bash
git clone <repo-kamu>
cd dikzshop
npm install
cp .env.example .env   # lalu isi nilainya
node server.js
```
> Atau pakai PM2 agar tetap jalan: `pm2 start server.js --name dikzshop`

---

### 2. ▲ Vercel (Gratis!)
1. Push ke GitHub
2. Buka [vercel.com](https://vercel.com) → Import Project
3. Di **Environment Variables**, tambahkan:
   - `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`
   - `SHOP_NAME`, `SENDER_EMAIL`
4. Deploy!

> ⚠️ Catatan Vercel: Karena serverless, file `data.json` tidak persisten.
> Gunakan database eksternal (PlanetScale, Supabase, dll.) untuk production.

---

### 3. 🚂 Railway (Gratis dengan batas)
1. Push ke GitHub
2. Buka [railway.app](https://railway.app) → New Project → Deploy from GitHub
3. Tambah environment variables di Settings → Variables
4. Deploy otomatis!

---

### 4. 🎨 Render (Gratis)
1. Push ke GitHub  
2. Buka [render.com](https://render.com) → New → Web Service
3. Pilih repo, set:
   - **Build Command**: `npm install`
   - **Start Command**: `node server.js`
4. Tambah env vars → Deploy!

---

### 5. 🐳 Docker
```bash
docker build -t dikzshop .
docker run -p 3000:3000 \
  -e SMTP_HOST=smtp.gmail.com \
  -e SMTP_USER=kamu@gmail.com \
  -e SMTP_PASS=app_password \
  dikzshop
```

---

### 6. ☁️ Heroku
```bash
heroku create nama-app-kamu
heroku config:set SMTP_HOST=smtp.gmail.com
heroku config:set SMTP_USER=kamu@gmail.com
heroku config:set SMTP_PASS=app_password
git push heroku main
```

---

### 7. 🐘 PHP Hosting (cPanel, Niagahoster, dll.) — Cara Lama
Kamu tetap bisa pakai file PHP original:
```
upload semua isi folder /php-hosting/ ke public_html
chmod 755 dikzshop/
```
> Pastikan fungsi `mail()` aktif, atau pakai PHPMailer + SMTP.

---

## ⚙️ Konfigurasi Email (SMTP)

Edit file `.env`:

```env
# Opsi 1: Gmail App Password
GMAIL_USER=kamu@gmail.com
GMAIL_PASS=xxxx xxxx xxxx xxxx   # dari myaccount.google.com/apppasswords

# Opsi 2: Brevo (gratis 300 email/hari)
SMTP_HOST=smtp-relay.brevo.com
SMTP_PORT=587
SMTP_USER=kamu@email.com
SMTP_PASS=brevo_api_key
```

---

## 🔗 API Endpoints (sama persis dengan versi PHP)

| Method | URL | Fungsi |
|--------|-----|--------|
| `GET` | `/dikzshop/add.php?mail=email@x.com` | Tambah email pembeli |
| `GET` | `/dikzshop/delete.php?mail=email@x.com` | Hapus by email |
| `GET` | `/dikzshop/delete.php?keys=0` | Hapus by index |
| `GET` | `/dikzshop/ganti.php?nick=Nama&sender=email` | Ganti identitas |
| `POST` | `/apiii.php` | Kirim email blast |

---

## 📝 Catatan

- Data email (`data.json`) auto-dibuat saat pertama jalan
- Di Vercel, data tidak persisten karena serverless — cocok untuk demo/testing
- Untuk production skala besar, hubungkan ke database (MongoDB, Supabase, dll.)
