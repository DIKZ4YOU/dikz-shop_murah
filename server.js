require('dotenv').config();
const express    = require('express');
const cors       = require('cors');
const nodemailer = require('nodemailer');
const fs         = require('fs');
const path       = require('path');

const app  = express();
const PORT = process.env.PORT || 3000;

// ─── PATH DATA ────────────────────────────────────────────────────────────────
const DATA_FILE   = path.join(__dirname, 'dikzshop', 'data.json');
const CONFIG_FILE = path.join(__dirname, 'dikzshop', 'config.json');

// ─── INIT FILES ───────────────────────────────────────────────────────────────
function ensureFiles() {
  if (!fs.existsSync(path.dirname(DATA_FILE))) {
    fs.mkdirSync(path.dirname(DATA_FILE), { recursive: true });
  }
  if (!fs.existsSync(DATA_FILE)) fs.writeFileSync(DATA_FILE, '[]');
  if (!fs.existsSync(CONFIG_FILE)) {
    fs.writeFileSync(CONFIG_FILE, JSON.stringify({
      nik:    process.env.SHOP_NAME    || 'DIKZSHOP 🇮🇩',
      sender: process.env.SENDER_EMAIL || 'admin@dikzshop.id'
    }, null, 2));
  }
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function readData() {
  try   { return JSON.parse(fs.readFileSync(DATA_FILE, 'utf8')) || []; }
  catch { return []; }
}
function writeData(d) {
  fs.writeFileSync(DATA_FILE, JSON.stringify(d, null, 2));
}
function readConfig() {
  try   { return JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8')); }
  catch { return { nik: process.env.SHOP_NAME || 'DIKZSHOP', sender: process.env.SENDER_EMAIL || 'admin@dikzshop.id' }; }
}
function writeConfig(c) {
  fs.writeFileSync(CONFIG_FILE, JSON.stringify(c, null, 2));
}

// ─── NODEMAILER TRANSPORTER ───────────────────────────────────────────────────
function createTransporter() {
  // Dukung SMTP via env vars — bisa pakai Gmail, Mailtrap, Brevo, dll.
  if (process.env.SMTP_HOST) {
    return nodemailer.createTransporter({
      host:   process.env.SMTP_HOST,
      port:   parseInt(process.env.SMTP_PORT || '587'),
      secure: process.env.SMTP_SECURE === 'true',
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
      },
    });
  }
  // Fallback: Gmail dengan App Password
  if (process.env.GMAIL_USER && process.env.GMAIL_PASS) {
    return nodemailer.createTransporter({
      service: 'gmail',
      auth: { user: process.env.GMAIL_USER, pass: process.env.GMAIL_PASS },
    });
  }
  return null;
}

// ─── MIDDLEWARE ───────────────────────────────────────────────────────────────
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

// ─── ROUTE: ADD EMAIL ─────────────────────────────────────────────────────────
// GET /dikzshop/add.php?mail=email@contoh.com
app.all('/dikzshop/add.php', (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  const mail = (req.query.mail || req.body.mail || '').trim().toLowerCase();

  if (!mail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
    return res.json({ status: '400', msg: 'Email tidak valid' });
  }

  const data = readData();
  const exists = data.some(item => item.email?.toLowerCase() === mail);
  if (exists) return res.json({ status: '200', msg: 'Email sudah ada' });

  data.unshift({ email: mail, tanggal: new Date().toISOString(), source: 'DikzShop-Jasteb' });
  writeData(data);
  res.json({ status: '200', msg: 'Email berhasil ditambahkan' });
});

// ─── ROUTE: DELETE EMAIL ──────────────────────────────────────────────────────
// GET /dikzshop/delete.php?mail=email@contoh.com
// GET /dikzshop/delete.php?keys=0
app.all('/dikzshop/delete.php', (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  let data = readData();

  // Hapus by email
  if (req.query.mail || req.body.mail) {
    const mail = (req.query.mail || req.body.mail).trim().toLowerCase();
    if (!mail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
      return res.json({ status: '400', msg: 'Email tidak valid' });
    }
    const before = data.length;
    data = data.filter(item => item.email?.toLowerCase() !== mail);
    writeData(data);
    if (data.length < before) return res.json({ status: '200', msg: `Email ${mail} berhasil dihapus` });
    return res.json({ status: '200', msg: 'Email tidak ditemukan' });
  }

  // Hapus by index
  if (req.query.keys !== undefined) {
    const idx = parseInt(req.query.keys);
    if (isNaN(idx) || !data[idx]) return res.json({ status: '400', msg: 'Index tidak valid' });
    data.splice(idx, 1);
    writeData(data);
    return res.json({ status: '200', msg: 'Email berhasil dihapus' });
  }

  res.json({ status: '400', msg: 'Parameter mail atau keys diperlukan' });
});

// ─── ROUTE: GANTI IDENTITAS ───────────────────────────────────────────────────
// GET /dikzshop/ganti.php?nick=Nama&sender=email
app.all('/dikzshop/ganti.php', (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  const nick   = (req.query.nick   || req.body.nick   || '').trim();
  const sender = (req.query.sender || req.body.sender || '').trim();

  if (!nick || !sender) return res.json({ status: '400', msg: 'nick dan sender wajib diisi' });
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(sender)) return res.json({ status: '400', msg: 'Email sender tidak valid' });

  writeConfig({ nik: nick, sender });
  res.json({ status: '200', msg: 'Data berhasil diperbarui' });
});

// ─── ROUTE: AMBIL SEMUA EMAIL ─────────────────────────────────────────────────
// GET /dikzshop/data.php  (baca list email, untuk panel PHP lama)
app.get('/dikzshop/data.json', (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  res.json(readData());
});

// ─── ROUTE: EMAIL BLAST ───────────────────────────────────────────────────────
// POST /apiii.php  body: { subjek, pesan }
app.all('/apiii.php', async (req, res) => {
  res.setHeader('Content-Type', 'application/json');
  const subjek = (req.query.subjek || req.body.subjek || '').trim();
  const pesan  = (req.query.pesan  || req.body.pesan  || '').trim();

  if (!subjek || !pesan) {
    return res.json({ status: '400', msg: 'subjek dan pesan wajib diisi' });
  }

  const config     = readConfig();
  const data       = readData();
  const transporter = createTransporter();

  if (!transporter) {
    return res.json({
      status: '500',
      msg: 'SMTP belum dikonfigurasi. Set SMTP_HOST / GMAIL_USER di environment variables.',
    });
  }

  let count = 0, errors = [];
  for (const item of data) {
    if (!item.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(item.email)) continue;
    try {
      await transporter.sendMail({
        from:    `"${config.nik}" <${config.sender}>`,
        to:      item.email,
        subject: subjek,
        html:    pesan,
      });
      count++;
    } catch (e) {
      errors.push(item.email);
    }
  }

  res.json({
    status:  '200',
    msg:     `Email terkirim ke ${count} penerima`,
    total:   data.length,
    success: count,
    failed:  errors.length,
  });
});

// ─── SERVE FRONTEND ───────────────────────────────────────────────────────────
app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

// ─── START ────────────────────────────────────────────────────────────────────
ensureFiles();
app.listen(PORT, () => {
  console.log(`✅ DikzShop berjalan di http://localhost:${PORT}`);
  console.log(`📧 SMTP: ${process.env.SMTP_HOST || process.env.GMAIL_USER ? 'Terkonfigurasi' : '⚠️  Belum dikonfigurasi (lihat .env.example)'}`);
});

module.exports = app;
