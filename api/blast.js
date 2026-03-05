const nodemailer = require('nodemailer');
const { readData, readConfig } = require('./_data');


function createTransporter() {
  if (process.env.SMTP_HOST) {
    return nodemailer.createTransporter({
      host:   process.env.SMTP_HOST,
      port:   parseInt(process.env.SMTP_PORT || '587'),
      secure: process.env.SMTP_SECURE === 'true',
      auth:   { user: process.env.SMTP_USER, pass: process.env.SMTP_PASS },
    });
  }
  if (process.env.GMAIL_USER && process.env.GMAIL_PASS) {
    return nodemailer.createTransporter({
      service: 'gmail',
      auth: { user: process.env.GMAIL_USER, pass: process.env.GMAIL_PASS },
    });
  }
  return null;
}

module.exports = async (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');

  if (req.method === 'OPTIONS') return res.status(200).end();

  const subjek = (req.query.subjek || req.body?.subjek || '').trim();
  const pesan  = (req.query.pesan  || req.body?.pesan  || '').trim();

  if (!subjek || !pesan) {
    return res.status(400).json({ status: '400', msg: 'subjek dan pesan wajib diisi' });
  }

  const transporter = createTransporter();
  if (!transporter) {
    return res.status(500).json({
      status: '500',
      msg: 'SMTP belum dikonfigurasi. Set SMTP_HOST / GMAIL_USER di Vercel Environment Variables.',
    });
  }

  const config = readConfig();
  const data   = readData();
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

  res.json({ status: '200', msg: `Email terkirim ke ${count} penerima`, total: data.length, success: count, failed: errors.length });
};
