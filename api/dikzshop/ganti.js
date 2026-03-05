const { writeConfig } = require('../_data');

module.exports = (req, res) => {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Content-Type', 'application/json');

  const nick   = (req.query.nick   || req.body?.nick   || '').trim();
  const sender = (req.query.sender || req.body?.sender || '').trim();

  if (!nick || !sender) {
    return res.status(400).json({ status: '400', msg: 'nick dan sender wajib diisi' });
  }
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(sender)) {
    return res.status(400).json({ status: '400', msg: 'Email sender tidak valid' });
  }

  writeConfig({ nik: nick, sender });
  res.json({ status: '200', msg: 'Data berhasil diperbarui' });
};
