// Di Vercel, filesystem hanya bisa tulis di /tmp
// Data hilang saat cold start — untuk persisten pakai Vercel KV / Supabase
const fs   = require('fs');
const path = require('path');

const DATA_FILE   = '/tmp/dikzshop_data.json';
const CONFIG_FILE = '/tmp/dikzshop_config.json';

function readData() {
  try {
    if (!fs.existsSync(DATA_FILE)) return [];
    return JSON.parse(fs.readFileSync(DATA_FILE, 'utf8')) || [];
  } catch { return []; }
}

function writeData(d) {
  fs.writeFileSync(DATA_FILE, JSON.stringify(d, null, 2));
}

function readConfig() {
  try {
    if (!fs.existsSync(CONFIG_FILE)) return defaultConfig();
    return JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8'));
  } catch { return defaultConfig(); }
}

function writeConfig(c) {
  fs.writeFileSync(CONFIG_FILE, JSON.stringify(c, null, 2));
}

function defaultConfig() {
  return {
    nik:    process.env.SHOP_NAME    || 'DIKZSHOP',
    sender: process.env.SENDER_EMAIL || 'admin@dikzshop.id',
  };
}

module.exports = { readData, writeData, readConfig, writeConfig };
