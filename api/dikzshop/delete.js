const { readData, writeData } = require('../_data');

module.exports = (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');

  let data = readData();

  // Hapus by email
  if (req.query.mail || req.body?.mail) {
    const mail = (req.query.mail || req.body.mail).trim().toLowerCase();
    if (!mail || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
      return res.status(400).json({ status: '400', msg: 'Email tidak valid' });
    }
    const before = data.length;
    data = data.filter(item => item.email?.toLowerCase() !== mail);
    writeData(data);
    return res.json({
      status: '200',
      msg: data.length < before ? `Email ${mail} berhasil dihapus` : 'Email tidak ditemukan',
    });
  }

  // Hapus by index
  if (req.query.keys !== undefined) {
    const idx = parseInt(req.query.keys);
    if (isNaN(idx) || !data[idx]) {
      return res.status(400).json({ status: '400', msg: 'Index tidak valid' });
    }
    data.splice(idx, 1);
    writeData(data);
    return res.json({ status: '200', msg: 'Email berhasil dihapus' });
  }

  res.status(400).json({ status: '400', msg: 'Parameter mail atau keys diperlukan' });
};
