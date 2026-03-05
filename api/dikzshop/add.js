const { readData, writeData } = require('../_data');

module.exports = (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');

  const mail = (req.query.mail || req.body?.mail || '').trim().toLowerCase();

  if (!mail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
    return res.status(400).json({ status: '400', msg: 'Email tidak valid' });
  }

  const data   = readData();
  const exists = data.some(item => item.email?.toLowerCase() === mail);
  if (exists) return res.json({ status: '200', msg: 'Email sudah ada' });

  data.unshift({ email: mail, tanggal: new Date().toISOString(), source: 'DikzShop-Jasteb' });
  writeData(data);
  res.json({ status: '200', msg: 'Email berhasil ditambahkan' });
};
