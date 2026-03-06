<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>DikzShop</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
  <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{background:#F4F2FF;font-family:'Poppins',sans-serif}
    #root{max-width:430px;margin:0 auto;min-height:100vh}
    input,button,textarea,select{font-family:'Poppins',sans-serif}
    ::-webkit-scrollbar{width:4px}
    ::-webkit-scrollbar-thumb{background:#C4B5FD;border-radius:4px}
    @keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}
    @keyframes slideDown{from{opacity:0;transform:translateX(-50%) translateY(-14px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    @keyframes slideUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:0.5}}
    @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
  </style>
</head>
<body>
<div id="root"></div>
<script type="text/babel">
const { useState, useEffect, useRef, useCallback, useMemo } = React;

// ─── SVG ICON ENGINE ──────────────────────────────────────────────────────────
const IC = {
  home:    [["path","M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"],["path","M9 22V12h6v10"]],
  bag:     [["path","M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"],["line","x1=3 y1=6 x2=21 y2=6"],["path","M16 10a4 4 0 01-8 0"]],
  msg:     [["path","M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"]],
  users:   [["path","M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"],["circle","cx=9 cy=7 r=4"],["path","M23 21v-2a4 4 0 00-3-3.87"],["path","M16 3.13a4 4 0 010 7.75"]],
  user:    [["path","M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"],["circle","cx=12 cy=7 r=4"]],
  send:    [["line","x1=22 y1=2 x2=11 y2=13"],["polygon","points=22,2,15,22,11,13,2,9,22,2"]],
  copy:    [["rect","x=9 y=9 width=13 height=13 rx=2 ry=2"],["path","M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"]],
  check:   [["polyline","points=20,6,9,17,4,12"]],
  cr:      [["polyline","points=9,18,15,12,9,6"]],
  cl:      [["polyline","points=15,18,9,12,15,6"]],
  plus:    [["line","x1=12 y1=5 x2=12 y2=19"],["line","x1=5 y1=12 x2=19 y2=12"]],
  x:       [["line","x1=18 y1=6 x2=6 y2=18"],["line","x1=6 y1=6 x2=18 y2=18"]],
  bell:    [["path","M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"],["path","M13.73 21a2 2 0 01-3.46 0"]],
  shield:  [["path","M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"]],
  logout:  [["path","M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"],["polyline","points=16,17,21,12,16,7"],["line","x1=21 y1=12 x2=9 y2=12"]],
  zap:     [["polygon","points=13,2,3,14,12,14,11,22,21,10,12,10,13,2"]],
  star:    [["polygon","points=12,2,15.09,8.26,22,9.27,17,14.14,18.18,21.02,12,17.77,5.82,21.02,7,14.14,2,9.27,8.91,8.26,12,2"]],
  gift:    [["polyline","points=20,12,20,22,4,22,4,12"],["rect","x=2 y=7 width=20 height=5"],["line","x1=12 y1=22 x2=12 y2=7"],["path","M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"],["path","M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"]],
  trend:   [["polyline","points=23,6,13.5,15.5,8.5,10.5,1,18"],["polyline","points=17,6,23,6,23,12"]],
  pkg:     [["path","M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"],["polyline","points=3.27,6.96,12,12.01,20.73,6.96"],["line","x1=12 y1=22.08 x2=12 y2=12"]],
  code:    [["polyline","points=16,18,22,12,16,6"],["polyline","points=8,6,2,12,8,18"]],
  monitor: [["rect","x=1 y=3 width=22 height=14 rx=2 ry=2"],["line","x1=8 y1=21 x2=16 y2=21"],["line","x1=12 y1=17 x2=12 y2=21"]],
  unlock:  [["rect","x=3 y=11 width=18 height=11 rx=2 ry=2"],["path","M7 11V7a5 5 0 019.9-1"]],
  eye:     [["path","M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"],["circle","cx=12 cy=12 r=3"]],
  eyeoff:  [["path","M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"],["line","x1=1 y1=1 x2=23 y2=23"]],
  search:  [["circle","cx=11 cy=11 r=8"],["line","x1=21 y1=21 x2=16.65 y2=16.65"]],
  aur:     [["line","x1=7 y1=17 x2=17 y2=7"],["polyline","points=7,7,17,7,17,17"]],
  adl:     [["line","x1=17 y1=7 x2=7 y2=17"],["polyline","points=17,17,7,17,7,7"]],
  wallet:  [["rect","x=1 y=4 width=22 height=16 rx=2 ry=2"],["line","x1=1 y1=10 x2=23 y2=10"]],
  clock:   [["circle","cx=12 cy=12 r=10"],["polyline","points=12,6,12,12,16,14"]],
  bar:     [["line","x1=18 y1=20 x2=18 y2=10"],["line","x1=12 y1=20 x2=12 y2=4"],["line","x1=6 y1=20 x2=6 y2=14"]],
  uc:      [["path","M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"],["circle","cx=8.5 cy=7 r=4"],["polyline","points=17,11,19,13,23,9"]],
  msq:     [["path","M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"]],
  edit:    [["path","M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"],["path","M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"]],
  trash:   [["polyline","points=3,6,5,6,21,6"],["path","M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"],["line","x1=10 y1=11 x2=10 y2=17"],["line","x1=14 y1=11 x2=14 y2=17"]],
  qr:      [["rect","x=3 y=3 width=7 height=7"],["rect","x=14 y=3 width=7 height=7"],["rect","x=14 y=14 width=7 height=7"],["rect","x=3 y=14 width=7 height=7"],["rect","x=5 y=5 width=3 height=3"],["rect","x=16 y=5 width=3 height=3"],["rect","x=16 y=16 width=3 height=3"],["rect","x=5 y=16 width=3 height=3"]],
  ok:      [["path","M22 11.08V12a10 10 0 11-5.93-9.14"],["polyline","points=22,4,12,14.01,9,11.01"]],
  alert:   [["circle","cx=12 cy=12 r=10"],["line","x1=12 y1=8 x2=12 y2=12"],["line","x1=12 y1=16 x2=12.01 y2=16"]],
  info:    [["circle","cx=12 cy=12 r=10"],["line","x1=12 y1=16 x2=12 y2=12"],["line","x1=12 y1=8 x2=12.01 y2=8"]],
  crown:   [["path","M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"]],
  mail:    [["path","M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"],["polyline","points=22,6,12,13,2,6"]],
  timer:   [["circle","cx=12 cy=12 r=10"],["polyline","points=12,6,12,12,16,14"]],
  warn:    [["path","M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"],["line","x1=12 y1=9 x2=12 y2=13"],["line","x1=12 y1=17 x2=12.01 y2=17"]],
  refresh: [["polyline","points=23,4,23,10,17,10"],["polyline","points=1,20,1,14,7,14"],["path","M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"]],
  list:    [["line","x1=8 y1=6 x2=21 y2=6"],["line","x1=8 y1=12 x2=21 y2=12"],["line","x1=8 y1=18 x2=21 y2=18"],["line","x1=3 y1=6 x2=3.01 y2=6"],["line","x1=3 y1=12 x2=3.01 y2=12"],["line","x1=3 y1=18 x2=3.01 y2=18"]],
  phone:   [["path","M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.86 19.79 19.79 0 01.21 1.28 2 2 0 012.22 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.91-.91a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"]],
};

function Ico({ n, size=20, color="currentColor", fill="none", sw=2, style={} }) {
  const els = IC[n] || [];
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill={fill} stroke={color}
      strokeWidth={sw} strokeLinecap="round" strokeLinejoin="round"
      style={{display:"inline-block",verticalAlign:"middle",flexShrink:0,...style}}>
      {els.map(([tag,attrs],i) => {
        const p = {};
        String(attrs).split(" ").forEach(a => {
          const [k,...v] = a.split("="); if(k) p[k] = v.join("=").replace(/^"/,"").replace(/"$/,"");
        });
        if(tag==="path")     return <path key={i} d={p.d}/>;
        if(tag==="line")     return <line key={i} x1={p.x1} y1={p.y1} x2={p.x2} y2={p.y2}/>;
        if(tag==="circle")   return <circle key={i} cx={p.cx} cy={p.cy} r={p.r}/>;
        if(tag==="rect")     return <rect key={i} x={p.x} y={p.y} width={p.width} height={p.height} rx={p.rx||0} ry={p.ry||0}/>;
        if(tag==="polyline") return <polyline key={i} points={p.points}/>;
        if(tag==="polygon")  return <polygon key={i} points={p.points}/>;
        return null;
      })}
    </svg>
  );
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
const rp  = n => "Rp " + Number(n).toLocaleString("id-ID");
const gid = () => Math.random().toString(36).slice(2,10);
const today = () => new Date().toISOString().slice(0,10);
const now   = () => new Date().toLocaleTimeString("id-ID",{hour:"2-digit",minute:"2-digit"});

function msToHuman(ms) {
  if (ms <= 0) return "Kedaluwarsa";
  const totalMin = Math.floor(ms / 60000);
  if (totalMin < 60) return `${totalMin} menit`;
  const h = Math.floor(totalMin / 60);
  const m = totalMin % 60;
  if (m === 0) return `${h} jam`;
  return `${h}j ${m}m`;
}

function expiryStr(ts) {
  const d = new Date(ts);
  return d.toLocaleString("id-ID",{day:"2-digit",month:"short",hour:"2-digit",minute:"2-digit"});
}

// ─── PANEL CONFIG ──────────────────────────────────────────────────────────────
const PANEL_ADD_URL = "https://GANTI_URL_HOSTINGMU/dikzshop/add.php";
const PANEL_DEL_URL = "https://GANTI_URL_HOSTINGMU/dikzshop/delete.php";

// ─── DURASI OPTIONS ───────────────────────────────────────────────────────────
const DURASI_JAM = [
  { label:"1 Jam",  unit:"jam",   value:1,   minutes:60   },
  { label:"2 Jam",  unit:"jam",   value:2,   minutes:120  },
  { label:"3 Jam",  unit:"jam",   value:3,   minutes:180  },
  { label:"6 Jam",  unit:"jam",   value:6,   minutes:360  },
  { label:"12 Jam", unit:"jam",   value:12,  minutes:720  },
  { label:"24 Jam", unit:"jam",   value:24,  minutes:1440 },
];

const DURASI_MENIT = [
  { label:"15 Mnt", unit:"menit", value:15,  minutes:15  },
  { label:"30 Mnt", unit:"menit", value:30,  minutes:30  },
  { label:"45 Mnt", unit:"menit", value:45,  minutes:45  },
  { label:"60 Mnt", unit:"menit", value:60,  minutes:60  },
  { label:"90 Mnt", unit:"menit", value:90,  minutes:90  },
  { label:"2 Jam",  unit:"menit", value:120, minutes:120 },
];

const getJastebPrice = (product, durasi) => {
  if (!product || !durasi) return 0;
  return Math.max(500, Math.round(product.pricePerHour * durasi.minutes / 60));
};

// ─── INITIAL DATA ─────────────────────────────────────────────────────────────
const IMG = {
  1:"https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?w=400&q=80",
  2:"https://images.unsplash.com/photo-1614064641938-3bbee52942c7?w=400&q=80",
  3:"https://images.unsplash.com/photo-1611746872915-64382b5c76da?w=400&q=80",
  4:"https://images.unsplash.com/photo-1611162617213-7d7a39e9b1d7?w=400&q=80",
  5:"https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&q=80",
  6:"https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&q=80",
  7:"https://images.unsplash.com/photo-1563986768609-322da13575f3?w=400&q=80",
  8:"https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400&q=80",
};

const INIT_PRODUCTS = {
  script:[
    {id:1,name:"Script Auto Order Bot",price:85000,desc:"Bot otomatis untuk auto order berbagai platform e-commerce dengan fitur lengkap.",stock:50,sales:234,rating:4.8},
    {id:2,name:"Script Checker Akun Premium",price:45000,desc:"Cek validitas akun premium Netflix, Spotify, Disney+ secara otomatis.",stock:100,sales:567,rating:4.9},
    {id:3,name:"Script WA Blast Pro",price:120000,desc:"Kirim pesan massal WhatsApp dengan delay & anti-banned, support multi-device.",stock:30,sales:89,rating:4.7},
    {id:4,name:"Script Auto Follow IG",price:65000,desc:"Auto follow/unfollow Instagram secara otomatis, bypass ratelimit.",stock:75,sales:312,rating:4.6},
  ],
  panel:[
    {id:5,name:"Panel SMM Reseller",price:250000,desc:"Panel lengkap untuk jualan jasa sosmed (followers, likes, views) siap pakai.",stock:20,sales:45,rating:4.9},
    {id:6,name:"Panel Inject Kuota",price:180000,desc:"Panel inject kuota all operator Indonesia, support Telkomsel, XL, Indosat.",stock:15,sales:78,rating:4.8},
    {id:7,name:"Panel PPOB Lengkap",price:350000,desc:"Panel pembayaran tagihan listrik, air, BPJS & top up game semua ada.",stock:10,sales:23,rating:4.7},
    {id:8,name:"Panel Gaming Top Up",price:200000,desc:"Panel top up game Mobile Legends, Free Fire, PUBG, dll dengan margin cuan.",stock:25,sales:156,rating:4.8},
  ],
  jasteb:[
    {id:9, name:"Jasteb Shopee Thailand",  pricePerHour:5000, desc:"Jasa titip beli dari Shopee Thailand. Bayar per jam atau per menit, fleksibel!", stock:999,sales:1204,rating:4.9},
    {id:10,name:"Jasteb Taobao China",     pricePerHour:4000, desc:"Jasa titip beli dari Taobao China. Bayar per jam atau per menit, fleksibel!",  stock:999,sales:892,rating:4.8},
    {id:11,name:"Jasteb Amazon US",        pricePerHour:8000, desc:"Jasa titip beli dari Amazon Amerika. Bayar per jam atau per menit, fleksibel!", stock:999,sales:445,rating:4.7},
    {id:12,name:"Jasteb Lazada Malaysia",  pricePerHour:6000, desc:"Jasa titip beli dari Lazada Malaysia. Bayar per jam atau per menit, fleksibel!",stock:999,sales:334,rating:4.8},
  ],
  unchek:[
    {id:13,name:"Unchek Netflix",          price:5000, desc:"Uncheck / unlock akun Netflix, langsung aktif. Garansi 1x24 jam.",       stock:200,sales:2341,rating:4.9},
    {id:14,name:"Unchek Spotify Premium",  price:4000, desc:"Uncheck / unlock akun Spotify Premium. Garansi 1x24 jam.",               stock:300,sales:1876,rating:4.8},
    {id:15,name:"Unchek Disney+ Hotstar",  price:6000, desc:"Uncheck / unlock akun Disney+ Hotstar. Garansi 1x24 jam.",              stock:150,sales:987,rating:4.7},
    {id:16,name:"Unchek YouTube Premium",  price:5500, desc:"Uncheck / unlock akun YouTube Premium. Garansi 1x24 jam.",              stock:180,sales:1234,rating:4.9},
  ],
};

const INIT_USERS = [
  {id:"u1",name:"Admin Dikz",email:"admin@dikzshop.id",password:"admin123",balance:5000000,role:"admin",referralCode:"ADMIN01",referredBy:null,avatar:"A",joinDate:"2024-01-01"},
  {id:"u2",name:"Budi Santoso",email:"budi@gmail.com",password:"budi123",balance:250000,role:"user",referralCode:"BUDI22",referredBy:null,avatar:"B",joinDate:"2024-06-15"},
];

const INIT_TXS = [
  {id:"t1",userId:"u2",type:"topup",amount:100000,desc:"Top Up via QRIS",date:"2025-03-01",status:"success"},
  {id:"t2",userId:"u2",type:"purchase",amount:-45000,desc:"Beli Script Checker Akun Premium",date:"2025-03-02",status:"success"},
];

// Email pool hanya untuk admin — tidak ditampilkan ke pembeli
const INIT_EMAIL_POOL = [
  {id:"e1",email:"shopee.th001@gmail.com",note:"Akun Shopee Thailand 1"},
  {id:"e2",email:"shopee.th002@gmail.com",note:"Akun Shopee Thailand 2"},
  {id:"e3",email:"taobao.cn001@gmail.com",note:"Akun Taobao China 1"},
  {id:"e4",email:"amazon.us001@gmail.com",note:"Akun Amazon US 1"},
  {id:"e5",email:"lazada.my001@gmail.com",note:"Akun Lazada Malaysia 1"},
];

// ─── ROOT APP ─────────────────────────────────────────────────────────────────
function App() {
  const [users,        setUsers]        = useState(INIT_USERS);
  const [products,     setProducts]     = useState(INIT_PRODUCTS);
  const [txs,          setTxs]          = useState(INIT_TXS);
  const [emailPool,    setEmailPool]    = useState(INIT_EMAIL_POOL);
  const [topupReqs,    setTopupReqs]    = useState([]);
  const [jastebOrders, setJastebOrders] = useState([]);
  const [chats,        setChats]        = useState([{id:"c1",userId:"u2",userName:"Budi Santoso",messages:[{from:"ai",text:"Halo Budi! Selamat datang di DikzShop! Ada yang bisa saya bantu?",time:"10:00"}],lastMsg:"Halo!",unread:0}]);
  const [currentUser,  setCurrentUser]  = useState(null);
  const [page,         setPage]         = useState("auth");
  const [adminTab,     setAdminTab]     = useState("dashboard");
  const [notif,        setNotif]        = useState(null);
  const [modal,        setModal]        = useState(null);

  const notify = useCallback((msg, type="success") => {
    setNotif({msg,type,id:gid()});
    setTimeout(() => setNotif(null), 3500);
  }, []);

  // Auto-expire jasteb orders & hapus email pembeli dari panel
  useEffect(() => {
    const t = setInterval(() => {
      const now = Date.now();
      setJastebOrders(prev => prev.map(o => {
        if (o.status === "active" && o.expiresAt <= now) {
          // Hapus email pembeli dari panel otomatis
          fetch(`${PANEL_DEL_URL}?mail=${encodeURIComponent(o.buyerEmail)}`, {method:"GET", mode:"no-cors"}).catch(()=>{});
          return {...o, status:"expired"};
        }
        return o;
      }));
    }, 30000);
    return () => clearInterval(t);
  }, []);

  const updateUser = u => {
    setUsers(prev => prev.map(x => x.id===u.id ? u : x));
    if (currentUser?.id === u.id) setCurrentUser(u);
  };
  const addTx = tx => setTxs(prev => [tx, ...prev]);

  if (!currentUser) return (
    <AuthPage users={users} setUsers={setUsers}
      setCurrentUser={u => { setCurrentUser(u); setPage(u.role==="admin"?"admin":"home"); }}
      notify={notify} notif={notif}/>
  );

  if (currentUser.role==="admin" && page==="admin") return (
    <AdminPanel
      users={users} setUsers={setUsers} products={products} setProducts={setProducts}
      txs={txs} setTxs={setTxs} emailPool={emailPool} setEmailPool={setEmailPool}
      topupReqs={topupReqs} setTopupReqs={setTopupReqs}
      jastebOrders={jastebOrders} setJastebOrders={setJastebOrders}
      chats={chats} setChats={setChats}
      currentUser={currentUser} adminTab={adminTab} setAdminTab={setAdminTab}
      setPage={setPage} notify={notify} notif={notif}
      updateUser={updateUser} addTx={addTx}/>
  );

  return (
    <UserApp
      currentUser={currentUser} users={users} setUsers={setUsers}
      products={products} txs={txs.filter(t=>t.userId===currentUser.id)}
      jastebOrders={jastebOrders.filter(o=>o.userId===currentUser.id)}
      setJastebOrders={setJastebOrders}
      topupReqs={topupReqs} setTopupReqs={setTopupReqs}
      chats={chats} setChats={setChats}
      page={page} setPage={setPage}
      notify={notify} notif={notif}
      updateUser={updateUser} addTx={addTx}
      modal={modal} setModal={setModal}
      setCurrentUser={setCurrentUser}/>
  );
}

// ─── NOTIF ────────────────────────────────────────────────────────────────────
function Notif({msg, type, id}) {
  const cfg = {
    success:{bg:"#ECFDF5",border:"#A7F3D0",color:"#059669"},
    error:  {bg:"#FEF2F2",border:"#FCA5A5",color:"#DC2626"},
    info:   {bg:"#EFF6FF",border:"#BAE6FD",color:"#3B82F6"},
  }[type] || {bg:"#EFF6FF",border:"#BAE6FD",color:"#3B82F6"};
  return (
    <div key={id} style={{position:"fixed",top:16,left:"50%",transform:"translateX(-50%)",zIndex:9999,background:cfg.bg,border:`1.5px solid ${cfg.border}`,borderRadius:16,padding:"12px 18px",boxShadow:"0 8px 32px rgba(0,0,0,0.15)",display:"flex",alignItems:"center",gap:10,maxWidth:360,minWidth:260,animation:"slideDown 0.3s ease",boxSizing:"border-box"}}>
      <Ico n={type==="error"?"alert":"ok"} size={18} color={cfg.color}/>
      <span style={{fontSize:13,fontWeight:600,color:cfg.color,flex:1}}>{msg}</span>
    </div>
  );
}

// ─── FIELD ────────────────────────────────────────────────────────────────────
function Field({label, type="text", value, onChange, placeholder, icon, onIcon, autoFocus, disabled}) {
  const [focused, setFocused] = useState(false);
  return (
    <div>
      {label && <label style={{display:"block",fontSize:12,fontWeight:700,color:"#374151",marginBottom:6}}>{label}</label>}
      <div style={{position:"relative"}}>
        <input type={type} value={value} onChange={e=>onChange(e.target.value)}
          placeholder={placeholder} autoFocus={autoFocus} disabled={disabled}
          onFocus={()=>setFocused(true)} onBlur={()=>setFocused(false)}
          style={{width:"100%",padding:"12px 16px",paddingRight:icon?"44px":"16px",borderRadius:13,border:`1.5px solid ${focused?"#7C3AED":"#E5E7EB"}`,fontSize:14,outline:"none",boxSizing:"border-box",background:disabled?"#F9FAFB":"#FAFAFA",transition:"border-color 0.15s",color:"#111827"}}/>
        {icon && <button type="button" onClick={onIcon} style={{position:"absolute",right:12,top:"50%",transform:"translateY(-50%)",background:"none",border:"none",cursor:"pointer",color:"#9CA3AF",display:"flex"}}>{icon}</button>}
      </div>
    </div>
  );
}

// ─── BTN ──────────────────────────────────────────────────────────────────────
function Btn({children, onClick, loading, color="#7C3AED", disabled}) {
  const isDisabled = loading || disabled;
  return (
    <button onClick={onClick} disabled={isDisabled}
      style={{width:"100%",padding:14,borderRadius:14,border:"none",background:isDisabled?`${color}55`:`linear-gradient(135deg,${color},${color}cc)`,color:"white",fontSize:15,fontWeight:800,cursor:isDisabled?"not-allowed":"pointer",boxShadow:isDisabled?"none":`0 4px 16px ${color}55`,transition:"all 0.2s"}}>
      {loading?"Memproses...":children}
    </button>
  );
}

// ─── TX ITEM ──────────────────────────────────────────────────────────────────
function TxItem({tx, sub}) {
  const cfg = {
    topup:    {n:"adl",   color:"#059669",bg:"#ECFDF5"},
    purchase: {n:"bag",   color:"#DC2626",bg:"#FEF2F2"},
    transfer: {n:"aur",   color:"#7C3AED",bg:"#F5F3FF"},
  }[tx.type] || {n:"adl",color:"#059669",bg:"#ECFDF5"};
  return (
    <div style={{background:"white",borderRadius:16,padding:"13px 15px",marginBottom:8,display:"flex",alignItems:"center",gap:12,boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
      <div style={{width:42,height:42,borderRadius:12,background:cfg.bg,display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
        <Ico n={cfg.n} size={20} color={cfg.color}/>
      </div>
      <div style={{flex:1,minWidth:0}}>
        <p style={{margin:0,fontWeight:600,fontSize:13,color:"#111827",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{tx.desc}</p>
        <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{tx.date}{sub?` • ${sub}`:""}</p>
      </div>
      <span style={{fontSize:14,fontWeight:800,color:tx.amount>0?"#059669":"#DC2626",flexShrink:0}}>
        {tx.amount>0?"+":""}{rp(Math.abs(tx.amount))}
      </span>
    </div>
  );
}

// ─── AUTH ─────────────────────────────────────────────────────────────────────
function AuthPage({users, setUsers, setCurrentUser, notify, notif}) {
  const [mode,   setMode]   = useState("login");
  const [f,      setF]      = useState({name:"",email:"",password:"",confirm:"",ref:""});
  const [showPw, setShowPw] = useState(false);
  const [loading,setLoading]= useState(false);
  const set = (k,v) => setF(p=>({...p,[k]:v}));

  const login = () => {
    if (!f.email || !f.password) return notify("Email dan password wajib diisi!", "error");
    setLoading(true);
    setTimeout(() => {
      const u = users.find(u => u.email.toLowerCase()===f.email.toLowerCase() && u.password===f.password);
      if (u) { setCurrentUser(u); notify("Selamat datang, "+u.name+"!"); }
      else notify("Email atau password salah!", "error");
      setLoading(false);
    }, 700);
  };

  const register = () => {
    if (!f.name||!f.email||!f.password) return notify("Semua field wajib diisi!", "error");
    if (f.password !== f.confirm) return notify("Password tidak cocok!", "error");
    if (f.password.length < 6) return notify("Password minimal 6 karakter!", "error");
    if (users.find(u=>u.email.toLowerCase()===f.email.toLowerCase())) return notify("Email sudah terdaftar!", "error");
    setLoading(true);
    setTimeout(() => {
      const referrer = f.ref ? users.find(u=>u.referralCode===f.ref.toUpperCase()) : null;
      const nu = {id:gid(),name:f.name,email:f.email,password:f.password,balance:referrer?10000:0,role:"user",referralCode:(f.name.slice(0,4).toUpperCase().replace(/\s/g,""))+Math.floor(10+Math.random()*89),referredBy:referrer?.id||null,avatar:f.name[0].toUpperCase(),joinDate:today()};
      let updated = [...users, nu];
      if (referrer) { updated = updated.map(u=>u.id===referrer.id?{...u,balance:u.balance+25000}:u); }
      setUsers(updated);
      setCurrentUser(nu);
      notify(referrer?"Registrasi berhasil! Bonus referral Rp 10.000 didapat!":"Registrasi berhasil! Selamat datang di DikzShop!");
      setLoading(false);
    }, 900);
  };

  return (
    <div style={{minHeight:"100vh",background:"linear-gradient(135deg,#2D1B69 0%,#553C9A 45%,#7C3AED 75%,#9F7AEA 100%)",display:"flex",alignItems:"center",justifyContent:"center",padding:20}}>
      {notif && <Notif {...notif}/>}
      <div style={{width:"100%",maxWidth:390}}>
        <div style={{textAlign:"center",marginBottom:28}}>
          <div style={{width:72,height:72,background:"white",borderRadius:24,display:"flex",alignItems:"center",justifyContent:"center",margin:"0 auto 14px",boxShadow:"0 12px 40px rgba(0,0,0,0.25)"}}>
            <Ico n="zap" size={36} color="#7C3AED" sw={2.5}/>
          </div>
          <h1 style={{color:"white",fontSize:28,fontWeight:900,margin:0,letterSpacing:"-0.5px"}}>DikzShop</h1>
          <p style={{color:"rgba(255,255,255,0.65)",fontSize:13,margin:"6px 0 0"}}>Platform Jasteb & Digital Terpercaya</p>
        </div>
        <div style={{background:"white",borderRadius:26,padding:28,boxShadow:"0 24px 64px rgba(0,0,0,0.3)"}}>
          <div style={{display:"flex",background:"#F5F3FF",borderRadius:14,padding:4,marginBottom:24}}>
            {["login","register"].map(m => (
              <button key={m} onClick={()=>setMode(m)} style={{flex:1,padding:"10px",borderRadius:11,border:"none",cursor:"pointer",fontWeight:700,fontSize:13,background:mode===m?"#7C3AED":"transparent",color:mode===m?"white":"#6B7280",transition:"all 0.2s"}}>
                {m==="login"?"Masuk":"Daftar"}
              </button>
            ))}
          </div>
          {mode==="login" ? (
            <div style={{display:"flex",flexDirection:"column",gap:14}}>
              <Field label="Email" type="email" value={f.email} onChange={v=>set("email",v)} placeholder="email@kamu.com"/>
              <Field label="Password" type={showPw?"text":"password"} value={f.password} onChange={v=>set("password",v)} placeholder="••••••••" icon={<Ico n={showPw?"eyeoff":"eye"} size={15}/>} onIcon={()=>setShowPw(!showPw)}/>
              <Btn onClick={login} loading={loading}>Masuk Sekarang</Btn>
            </div>
          ) : (
            <div style={{display:"flex",flexDirection:"column",gap:12}}>
              <Field label="Nama Lengkap" value={f.name} onChange={v=>set("name",v)} placeholder="Nama lengkap kamu"/>
              <Field label="Email" type="email" value={f.email} onChange={v=>set("email",v)} placeholder="email@kamu.com"/>
              <Field label="Password" type={showPw?"text":"password"} value={f.password} onChange={v=>set("password",v)} placeholder="Min. 6 karakter" icon={<Ico n={showPw?"eyeoff":"eye"} size={15}/>} onIcon={()=>setShowPw(!showPw)}/>
              <Field label="Konfirmasi Password" type="password" value={f.confirm} onChange={v=>set("confirm",v)} placeholder="Ulangi password"/>
              <Field label="Kode Referral (Opsional)" value={f.ref} onChange={v=>set("ref",v)} placeholder="Contoh: BUDI22"/>
              <Btn onClick={register} loading={loading}>Daftar Sekarang</Btn>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

// ─── USER APP SHELL ───────────────────────────────────────────────────────────
function UserApp({currentUser,users,setUsers,products,txs,jastebOrders,setJastebOrders,topupReqs,setTopupReqs,chats,setChats,page,setPage,notify,notif,updateUser,addTx,modal,setModal,setCurrentUser}) {
  const nav = [
    {id:"home",   n:"home",   label:"Beranda"},
    {id:"store",  n:"bag",    label:"Toko"},
    {id:"wallet", n:"wallet", label:"Dompet"},
    {id:"chat",   n:"msg",    label:"Chat"},
    {id:"profile",n:"user",   label:"Profil"},
  ];
  const pendingTopup = topupReqs.filter(r=>r.userId===currentUser.id&&r.status==="pending").length;

  return (
    <div style={{maxWidth:430,margin:"0 auto",minHeight:"100vh",background:"#F4F2FF",position:"relative",paddingBottom:80}}>
      {notif && <Notif {...notif}/>}

      {/* Modals */}
      {modal?.type==="payment"         && <PaymentModal  modal={modal} setModal={setModal} currentUser={currentUser} updateUser={updateUser} addTx={addTx} setTopupReqs={setTopupReqs} notify={notify}/>}
      {modal?.type==="transfer"     && <TransferModal setModal={setModal} users={users} currentUser={currentUser} updateUser={updateUser} addTx={addTx} notify={notify} setUsers={setUsers}/>}
      {modal?.type==="jasteb_result"&& <JastebResultModal modal={modal} setModal={setModal}/>}

      {page==="home"    && <HomePage    currentUser={currentUser} txs={txs} setPage={setPage} setModal={setModal} topupReqs={topupReqs} notify={notify} jastebOrders={jastebOrders}/>}
      {page==="store"   && <StorePage   products={products} currentUser={currentUser} updateUser={updateUser} addTx={addTx} setJastebOrders={setJastebOrders} notify={notify} setModal={setModal}/>}
      {page==="wallet"  && <WalletPage  currentUser={currentUser} txs={txs} setModal={setModal} topupReqs={topupReqs}/>}
      {page==="chat"    && <ChatPage    currentUser={currentUser} chats={chats} setChats={setChats}/>}
      {page==="event"   && <EventPage   currentUser={currentUser} users={users} notify={notify}/>}
      {page==="profile" && <ProfilePage currentUser={currentUser} setCurrentUser={setCurrentUser} setPage={setPage} notify={notify}/>}

      <div style={{position:"fixed",bottom:0,left:"50%",transform:"translateX(-50%)",width:"100%",maxWidth:430,background:"white",borderTop:"1px solid #EDE9FE",display:"flex",zIndex:100,boxShadow:"0 -4px 20px rgba(124,58,237,0.08)"}}>
        {nav.map(item => {
          const active = page===item.id;
          const badge  = item.id==="wallet" && pendingTopup>0;
          return (
            <button key={item.id} onClick={()=>setPage(item.id)} style={{flex:1,display:"flex",flexDirection:"column",alignItems:"center",padding:"10px 0 8px",border:"none",background:"transparent",cursor:"pointer",gap:2,position:"relative"}}>
              <Ico n={item.n} size={22} color={active?"#7C3AED":"#9CA3AF"} sw={active?2.5:1.8}/>
              <span style={{fontSize:10,fontWeight:active?700:400,color:active?"#7C3AED":"#9CA3AF"}}>{item.label}</span>
              {active && <div style={{width:4,height:4,borderRadius:"50%",background:"#7C3AED"}}/>}
              {badge  && <div style={{position:"absolute",top:8,right:"calc(50% - 14px)",width:16,height:16,borderRadius:"50%",background:"#DC2626",display:"flex",alignItems:"center",justifyContent:"center"}}><span style={{fontSize:9,fontWeight:700,color:"white"}}>{pendingTopup}</span></div>}
            </button>
          );
        })}
      </div>
    </div>
  );
}

// ─── HOME PAGE ────────────────────────────────────────────────────────────────
function HomePage({currentUser,txs,setPage,setModal,topupReqs,notify,jastebOrders}) {
  const [showBal,    setShowBal]    = useState(true);
  const [bannerIdx,  setBannerIdx]  = useState(0);
  const pendingTopup  = topupReqs.filter(r=>r.userId===currentUser.id&&r.status==="pending");
  const activeJasteb  = jastebOrders.filter(o=>o.status==="active");

  const banners = [
    {bg:"linear-gradient(135deg,#7C3AED,#4C1D95)",title:"Jasteb Per Jam/Menit",sub:"Bayar sesuai durasi, lebih hemat!",n:"timer",action:()=>setPage("store")},
    {bg:"linear-gradient(135deg,#059669,#065F46)",title:"Bonus Referral",sub:"Ajak teman, dapat Rp 25.000!",n:"gift",action:()=>setPage("event")},
    {bg:"linear-gradient(135deg,#DC2626,#7F1D1D)",title:"Panel SMM Murah",sub:"Harga terbaik bulan ini",n:"trend",action:()=>setPage("store")},
  ];
  useEffect(() => { const t = setInterval(()=>setBannerIdx(p=>(p+1)%banners.length),3500); return ()=>clearInterval(t); },[]);

  const quick = [
    {n:"plus",   label:"Top Up",  color:"#7C3AED",bg:"#F5F3FF",action:()=>setModal({type:"payment"})},
    {n:"aur",    label:"Transfer",color:"#059669",bg:"#ECFDF5",action:()=>setModal({type:"transfer"})},
    {n:"gift",   label:"Event",   color:"#DC2626",bg:"#FEF2F2",action:()=>setPage("event")},
    {n:"clock",  label:"Riwayat", color:"#D97706",bg:"#FFFBEB",action:()=>setPage("wallet")},
  ];

  return (
    <div>
      <div style={{background:"linear-gradient(160deg,#2D1B69 0%,#553C9A 40%,#7C3AED 100%)",padding:"48px 20px 80px",borderRadius:"0 0 36px 36px"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:20}}>
          <div>
            <p style={{color:"rgba(255,255,255,0.6)",fontSize:12,margin:0}}>Selamat datang,</p>
            <h2 style={{color:"white",fontSize:18,fontWeight:800,margin:"2px 0 0"}}>{currentUser.name}</h2>
          </div>
          <div style={{display:"flex",gap:10,alignItems:"center"}}>
            <div style={{width:38,height:38,background:"rgba(255,255,255,0.15)",borderRadius:12,display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="bell" size={18} color="white"/>
            </div>
            <div style={{width:38,height:38,background:"white",borderRadius:12,display:"flex",alignItems:"center",justifyContent:"center"}}>
              <span style={{fontSize:15,fontWeight:800,color:"#7C3AED"}}>{currentUser.avatar}</span>
            </div>
          </div>
        </div>

        <div style={{background:"rgba(255,255,255,0.12)",backdropFilter:"blur(16px)",borderRadius:22,padding:20,border:"1px solid rgba(255,255,255,0.2)"}}>
          <div style={{display:"flex",justifyContent:"space-between",alignItems:"center"}}>
            <p style={{color:"rgba(255,255,255,0.7)",fontSize:12,margin:0}}>Saldo DikzShop</p>
            <button onClick={()=>setShowBal(!showBal)} style={{background:"none",border:"none",cursor:"pointer",color:"rgba(255,255,255,0.7)",display:"flex"}}>
              <Ico n={showBal?"eye":"eyeoff"} size={16} color="rgba(255,255,255,0.7)"/>
            </button>
          </div>
          <h1 style={{color:"white",fontSize:30,fontWeight:900,margin:"8px 0 12px",letterSpacing:"-1px"}}>
            {showBal ? rp(currentUser.balance) : "Rp ••••••"}
          </h1>
          <div style={{display:"flex",alignItems:"center",gap:8}}>
            <div style={{background:"rgba(255,255,255,0.15)",borderRadius:8,padding:"4px 12px"}}>
              <span style={{color:"rgba(255,255,255,0.9)",fontSize:11,fontWeight:600}}>Kode: {currentUser.referralCode}</span>
            </div>
            {activeJasteb.length > 0 && (
              <div style={{background:"rgba(16,185,129,0.25)",borderRadius:8,padding:"4px 10px",display:"flex",alignItems:"center",gap:4}}>
                <div style={{width:6,height:6,borderRadius:"50%",background:"#10B981",animation:"pulse 1.5s infinite"}}/>
                <span style={{color:"#A7F3D0",fontSize:11,fontWeight:600}}>{activeJasteb.length} jasteb aktif</span>
              </div>
            )}
          </div>
        </div>
      </div>

      <div style={{margin:"-44px 16px 0",background:"white",borderRadius:22,padding:"18px 12px",boxShadow:"0 8px 32px rgba(124,58,237,0.12)",display:"flex",gap:4}}>
        {quick.map((a,i) => (
          <button key={i} onClick={a.action} style={{flex:1,display:"flex",flexDirection:"column",alignItems:"center",gap:8,background:"none",border:"none",cursor:"pointer"}}>
            <div style={{width:50,height:50,borderRadius:16,background:a.bg,display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n={a.n} size={22} color={a.color}/>
            </div>
            <span style={{fontSize:11,fontWeight:600,color:"#374151"}}>{a.label}</span>
          </button>
        ))}
      </div>

      {pendingTopup.length > 0 && (
        <div onClick={()=>setPage("wallet")} style={{margin:"16px 16px 0",background:"linear-gradient(135deg,#FEF3C7,#FDE68A)",borderRadius:16,padding:"14px 18px",display:"flex",alignItems:"center",gap:12,cursor:"pointer",border:"1px solid #F59E0B40"}}>
          <div style={{width:38,height:38,borderRadius:12,background:"#F59E0B",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="clock" size={18} color="white"/>
          </div>
          <div style={{flex:1}}>
            <p style={{margin:0,fontWeight:700,fontSize:13,color:"#92400E"}}>{pendingTopup.length} Top Up Menunggu Konfirmasi</p>
            <p style={{margin:"2px 0 0",fontSize:11,color:"#B45309"}}>Total: {rp(pendingTopup.reduce((s,r)=>s+r.amount,0))}</p>
          </div>
          <Ico n="cr" size={16} color="#B45309"/>
        </div>
      )}

      <div style={{margin:"16px 16px 0"}}>
        <div style={{borderRadius:18,overflow:"hidden",height:116,position:"relative",cursor:"pointer"}} onClick={banners[bannerIdx].action}>
          {banners.map((b,i) => (
            <div key={i} style={{position:"absolute",inset:0,background:b.bg,display:"flex",alignItems:"center",padding:"20px 24px",transition:"opacity 0.5s",opacity:i===bannerIdx?1:0,pointerEvents:i===bannerIdx?"auto":"none"}}>
              <div style={{flex:1}}>
                <h3 style={{color:"white",margin:"0 0 4px",fontSize:16,fontWeight:800}}>{b.title}</h3>
                <p style={{color:"rgba(255,255,255,0.85)",margin:0,fontSize:13}}>{b.sub}</p>
              </div>
              <div style={{width:52,height:52,background:"rgba(255,255,255,0.15)",borderRadius:16,display:"flex",alignItems:"center",justifyContent:"center"}}>
                <Ico n={b.n} size={26} color="white"/>
              </div>
            </div>
          ))}
          <div style={{position:"absolute",bottom:10,left:"50%",transform:"translateX(-50%)",display:"flex",gap:5}}>
            {banners.map((_,i) => <div key={i} style={{width:i===bannerIdx?18:6,height:6,borderRadius:3,background:"rgba(255,255,255,0.7)",transition:"width 0.3s"}}/>)}
          </div>
        </div>
      </div>

      <div style={{margin:"20px 16px 0"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:14}}>
          <h3 style={{margin:0,fontSize:15,fontWeight:800,color:"#111827"}}>Kategori Produk</h3>
          <button onClick={()=>setPage("store")} style={{background:"none",border:"none",cursor:"pointer",color:"#7C3AED",fontSize:12,fontWeight:700}}>Lihat Semua</button>
        </div>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:12}}>
          {[
            {n:"code",   label:"Script", sub:"Auto & Bot",   color:"#7C3AED",bg:"#F5F3FF"},
            {n:"monitor",label:"Panel",  sub:"SMM & PPOB",   color:"#059669",bg:"#ECFDF5"},
            {n:"pkg",    label:"Jasteb", sub:"Per Jam/Menit",color:"#DC2626",bg:"#FEF2F2"},
            {n:"unlock", label:"Unchek", sub:"Unlock Akun",  color:"#D97706",bg:"#FFFBEB"},
          ].map((c,i) => (
            <button key={i} onClick={()=>setPage("store")} style={{background:"white",border:`1.5px solid ${c.color}18`,borderRadius:18,padding:16,display:"flex",alignItems:"center",gap:12,cursor:"pointer",boxShadow:"0 2px 12px rgba(0,0,0,0.04)"}}>
              <div style={{width:46,height:46,borderRadius:14,background:c.bg,display:"flex",alignItems:"center",justifyContent:"center"}}>
                <Ico n={c.n} size={22} color={c.color}/>
              </div>
              <div style={{textAlign:"left"}}>
                <p style={{margin:0,fontSize:14,fontWeight:700,color:"#111827"}}>{c.label}</p>
                <p style={{margin:0,fontSize:11,color:"#9CA3AF"}}>{c.sub}</p>
              </div>
            </button>
          ))}
        </div>
      </div>

      <div style={{margin:"20px 16px 0"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:12}}>
          <h3 style={{margin:0,fontSize:15,fontWeight:800,color:"#111827"}}>Transaksi Terbaru</h3>
          <button onClick={()=>setPage("wallet")} style={{background:"none",border:"none",cursor:"pointer",color:"#7C3AED",fontSize:12,fontWeight:700}}>Lihat Semua</button>
        </div>
        {txs.length===0
          ? <div style={{textAlign:"center",padding:"32px 0",color:"#9CA3AF",fontSize:13}}>Belum ada transaksi</div>
          : txs.slice(0,3).map(tx=><TxItem key={tx.id} tx={tx}/>)
        }
      </div>
      <div style={{height:16}}/>
    </div>
  );
}

// ─── JASTEB BUY MODAL (per jam / per menit) ────────────────────────────────────
function JastebBuyModal({product, currentUser, updateUser, addTx, notify, setModal, setJastebOrders, onClose}) {
  const [unitMode,       setUnitMode]       = useState("jam");
  const [selectedDurasi, setSelectedDurasi] = useState(DURASI_JAM[0]);
  const [buyerEmail,     setBuyerEmail]     = useState(currentUser.email || "");
  const [loading,        setLoading]        = useState(false);

  const durList = unitMode === "jam" ? DURASI_JAM : DURASI_MENIT;
  const price   = getJastebPrice(product, selectedDurasi);
  const sisa    = currentUser.balance - price;

  const handleUnitSwitch = (unit) => {
    setUnitMode(unit);
    setSelectedDurasi(unit === "jam" ? DURASI_JAM[0] : DURASI_MENIT[0]);
  };

  const handleBuy = async () => {
    if (!buyerEmail || !buyerEmail.includes("@") || !buyerEmail.includes(".")) {
      notify("Masukkan email yang valid!", "error"); return;
    }
    if (currentUser.balance < price) {
      notify("Saldo tidak cukup! Silakan top up.", "error"); return;
    }
    setLoading(true);

    const expiresAt = Date.now() + selectedDurasi.minutes * 60000;
    const orderId   = gid();

    // Kirim email pembeli ke panel
    try {
      await fetch(`${PANEL_ADD_URL}?mail=${encodeURIComponent(buyerEmail)}`, {method:"GET", mode:"no-cors"});
    } catch(e) { console.warn("Panel offline:", e); }

    // Potong saldo
    updateUser({...currentUser, balance: currentUser.balance - price});

    // Catat transaksi
    addTx({id:gid(), userId:currentUser.id, type:"purchase", amount:-price, desc:`Jasteb ${product.name} — ${selectedDurasi.label}`, date:today(), status:"success"});

    // Simpan order jasteb
    const order = {
      id:          orderId,
      userId:      currentUser.id,
      userName:    currentUser.name,
      productId:   product.id,
      productName: product.name,
      durasi:      selectedDurasi,
      price,
      buyerEmail,
      expiresAt,
      createdAt:   Date.now(),
      status:      "active"
    };
    setJastebOrders(prev => [order, ...prev]);

    setLoading(false);
    onClose();
    setModal({type:"jasteb_result", data:{order, product}});
  };

  return (
    <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.6)",display:"flex",alignItems:"flex-end",justifyContent:"center",zIndex:200}}>
      <div style={{background:"white",borderRadius:"28px 28px 0 0",width:"100%",maxWidth:430,padding:24,maxHeight:"92vh",overflowY:"auto",animation:"slideUp 0.25s ease"}}>
        {/* Header */}
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:18}}>
          <div style={{display:"flex",alignItems:"center",gap:10}}>
            <div style={{width:44,height:44,borderRadius:14,background:"linear-gradient(135deg,#7C3AED,#5B21B6)",display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="pkg" size={22} color="white"/>
            </div>
            <div>
              <h3 style={{margin:0,fontSize:16,fontWeight:800,color:"#111827"}}>{product.name}</h3>
              <p style={{margin:"2px 0 0",fontSize:12,color:"#7C3AED",fontWeight:600}}>{rp(product.pricePerHour)}/jam</p>
            </div>
          </div>
          <button onClick={onClose} style={{width:34,height:34,borderRadius:"50%",background:"#F3F4F6",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="x" size={15}/>
          </button>
        </div>

        {/* Toggle Per Jam / Per Menit */}
        <p style={{margin:"0 0 10px",fontSize:13,fontWeight:700,color:"#374151"}}>Pilih Satuan Waktu:</p>
        <div style={{display:"flex",background:"#F5F3FF",borderRadius:14,padding:4,marginBottom:16}}>
          <button onClick={()=>handleUnitSwitch("jam")}
              style={{flex:1,padding:"11px 8px",borderRadius:11,border:"none",cursor:"pointer",fontWeight:700,fontSize:13,background:unitMode==="jam"?"#7C3AED":"transparent",color:unitMode==="jam"?"white":"#6B7280",transition:"all 0.2s",display:"flex",alignItems:"center",justifyContent:"center",gap:5}}>
              <Ico n="clock" size={14} color={unitMode==="jam"?"white":"#6B7280"}/> Per Jam
            </button>
            <button onClick={()=>handleUnitSwitch("menit")}
              style={{flex:1,padding:"11px 8px",borderRadius:11,border:"none",cursor:"pointer",fontWeight:700,fontSize:13,background:unitMode==="menit"?"#7C3AED":"transparent",color:unitMode==="menit"?"white":"#6B7280",transition:"all 0.2s",display:"flex",alignItems:"center",justifyContent:"center",gap:5}}>
              <Ico n="timer" size={14} color={unitMode==="menit"?"white":"#6B7280"}/> Per Menit
            </button>
        </div>

        {/* Duration grid */}
        <p style={{margin:"0 0 10px",fontSize:13,fontWeight:700,color:"#374151"}}>Pilih Durasi:</p>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:8,marginBottom:16}}>
          {durList.map(d => {
            const p = getJastebPrice(product, d);
            const sel = selectedDurasi.value===d.value && selectedDurasi.unit===d.unit;
            return (
              <button key={`${d.unit}-${d.value}`} onClick={()=>setSelectedDurasi(d)}
                style={{padding:"11px 6px",borderRadius:12,border:`2px solid ${sel?"#7C3AED":"#E5E7EB"}`,background:sel?"#F5F3FF":"white",cursor:"pointer",display:"flex",flexDirection:"column",alignItems:"center",gap:2,transition:"all 0.15s"}}>
                <span style={{fontSize:12,fontWeight:700,color:sel?"#7C3AED":"#374151"}}>{d.label}</span>
                <span style={{fontSize:11,color:sel?"#5B21B6":"#9CA3AF",fontWeight:600}}>{rp(p)}</span>
              </button>
            );
          })}
        </div>

        {/* Summary */}
        <div style={{background:"#F9FAFB",borderRadius:14,padding:"14px 16px",marginBottom:16}}>
          <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Durasi</span><span style={{fontWeight:700,color:"#111827"}}>{selectedDurasi.label}</span></div>
          <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Tarif</span><span style={{fontWeight:600,color:"#111827"}}>{rp(product.pricePerHour)}/jam</span></div>
          <div style={{borderTop:"1px solid #E5E7EB",marginTop:8,paddingTop:8,display:"flex",justifyContent:"space-between"}}>
            <span style={{fontSize:14,fontWeight:700,color:"#111827"}}>Total Bayar</span>
            <span style={{fontSize:17,fontWeight:900,color:"#7C3AED"}}>{rp(price)}</span>
          </div>
        </div>

        <div style={{display:"flex",justifyContent:"space-between",marginBottom:4,fontSize:13,color:"#6B7280"}}><span>Saldo kamu</span><span style={{fontWeight:600,color:"#111827"}}>{rp(currentUser.balance)}</span></div>
        <div style={{display:"flex",justifyContent:"space-between",marginBottom:16,fontSize:13,color:"#6B7280"}}>
          <span>Saldo setelah beli</span>
          <span style={{fontWeight:700,color:sisa<0?"#DC2626":"#059669"}}>{rp(sisa)}</span>
        </div>

        {/* Email pembeli */}
        <div style={{marginBottom:18}}>
          <Field label="Email Kamu (untuk konfirmasi pesanan) *"
            type="email" value={buyerEmail} onChange={setBuyerEmail}
            placeholder="email@kamu.com"/>
          <p style={{margin:"6px 0 0",fontSize:11,color:"#9CA3AF"}}>Admin akan menghubungi kamu melalui email ini setelah pesanan diterima.</p>
        </div>

        {sisa < 0 && (
          <div style={{background:"#FEF2F2",borderRadius:12,padding:"10px 14px",marginBottom:14,display:"flex",gap:8,alignItems:"center"}}>
            <Ico n="alert" size={14} color="#DC2626"/>
            <p style={{margin:0,fontSize:12,color:"#DC2626",fontWeight:600}}>Saldo tidak cukup. Silakan top up terlebih dahulu.</p>
          </div>
        )}

        <div style={{display:"flex",gap:12}}>
          <button onClick={onClose} style={{flex:1,padding:14,borderRadius:14,border:"2px solid #E5E7EB",background:"white",cursor:"pointer",fontWeight:700,fontSize:14}}>Batal</button>
          <button onClick={handleBuy} disabled={loading || sisa < 0}
            style={{flex:2,padding:14,borderRadius:14,border:"none",background:loading||sisa<0?"#E5E7EB":"linear-gradient(135deg,#7C3AED,#5B21B6)",color:loading||sisa<0?"#9CA3AF":"white",cursor:loading||sisa<0?"not-allowed":"pointer",fontWeight:800,fontSize:14,transition:"all 0.2s"}}>
            {loading?"Memproses...":"Konfirmasi Beli"}
          </button>
        </div>
      </div>
    </div>
  );
}

// ─── JASTEB RESULT MODAL ──────────────────────────────────────────────────────
function JastebResultModal({modal, setModal}) {
  const {order} = modal.data;
  const [remaining, setRemaining] = useState(order.expiresAt - Date.now());

  useEffect(() => {
    const t = setInterval(() => setRemaining(order.expiresAt - Date.now()), 30000);
    return () => clearInterval(t);
  }, [order.expiresAt]);

  return (
    <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.65)",display:"flex",alignItems:"center",justifyContent:"center",zIndex:300,padding:20}}>
      <div style={{background:"white",borderRadius:24,width:"100%",maxWidth:370,padding:28,textAlign:"center",animation:"fadeIn 0.22s ease"}}>
        <div style={{width:68,height:68,borderRadius:"50%",background:"linear-gradient(135deg,#ECFDF5,#D1FAE5)",display:"flex",alignItems:"center",justifyContent:"center",margin:"0 auto 16px",boxShadow:"0 4px 20px rgba(5,150,105,0.2)"}}>
          <Ico n="ok" size={34} color="#059669"/>
        </div>
        <h3 style={{margin:"0 0 4px",fontSize:19,fontWeight:800,color:"#111827"}}>Pesanan Diterima!</h3>
        <p style={{margin:"0 0 2px",fontSize:13,color:"#6B7280"}}>{order.productName}</p>
        <p style={{margin:"0 0 20px",fontSize:15,fontWeight:800,color:"#7C3AED"}}>{rp(order.price)} • {order.durasi.label}</p>

        {/* Info pesanan */}
        <div style={{background:"#F5F3FF",borderRadius:18,padding:16,marginBottom:14,textAlign:"left"}}>
          <p style={{margin:"0 0 10px",fontSize:12,fontWeight:700,color:"#6B7280",letterSpacing:"0.5px"}}>DETAIL PESANAN</p>
          <div style={{display:"flex",justifyContent:"space-between",marginBottom:6,fontSize:13}}>
            <span style={{color:"#6B7280"}}>Order ID</span>
            <span style={{fontWeight:700,color:"#111827",fontFamily:"monospace"}}>{order.id.toUpperCase()}</span>
          </div>
          <div style={{display:"flex",justifyContent:"space-between",marginBottom:6,fontSize:13}}>
            <span style={{color:"#6B7280"}}>Durasi</span>
            <span style={{fontWeight:700,color:"#111827"}}>{order.durasi.label}</span>
          </div>
          <div style={{display:"flex",justifyContent:"space-between",fontSize:13}}>
            <span style={{color:"#6B7280"}}>Berakhir</span>
            <span style={{fontWeight:700,color:"#111827"}}>{expiryStr(order.expiresAt)}</span>
          </div>
        </div>

        {/* Timer */}
        <div style={{display:"flex",alignItems:"center",gap:8,background:"#FEF3C7",borderRadius:12,padding:"12px 16px",marginBottom:14,textAlign:"left"}}>
          <Ico n="timer" size={16} color="#D97706" style={{flexShrink:0}}/>
          <div>
            <p style={{margin:0,fontSize:12,color:"#92400E",fontWeight:600}}>Sisa Waktu: <strong>{msToHuman(remaining)}</strong></p>
            <p style={{margin:"2px 0 0",fontSize:11,color:"#B45309"}}>Email kamu otomatis dihapus dari sistem saat waktu habis</p>
          </div>
        </div>

        {/* Email konfirmasi */}
        <div style={{display:"flex",alignItems:"flex-start",gap:8,background:"#ECFDF5",borderRadius:12,padding:"12px 16px",marginBottom:20,textAlign:"left",border:"1px solid #A7F3D0"}}>
          <Ico n="mail" size={16} color="#059669" style={{flexShrink:0,marginTop:1}}/>
          <p style={{margin:0,fontSize:12,color:"#065F46",fontWeight:600,lineHeight:1.5}}>
            Konfirmasi pesanan dikirim ke <strong>{order.buyerEmail}</strong>.<br/>
            Admin akan menghubungi kamu segera! ✓
          </p>
        </div>

        <button onClick={()=>setModal(null)} style={{width:"100%",background:"linear-gradient(135deg,#7C3AED,#5B21B6)",border:"none",borderRadius:14,padding:14,color:"white",fontWeight:800,fontSize:15,cursor:"pointer",boxShadow:"0 4px 16px rgba(124,58,237,0.3)"}}>
          Selesai
        </button>
      </div>
    </div>
  );
}

// ─── STORE PAGE ───────────────────────────────────────────────────────────────
function StorePage({products,currentUser,updateUser,addTx,setJastebOrders,notify,setModal}) {
  const [tab,           setTab]           = useState("script");
  const [search,        setSearch]        = useState("");
  const [buyingProduct, setBuyingProduct] = useState(null);
  const [jastebProduct, setJastebProduct] = useState(null);

  const tabs = [
    {id:"script",label:"Script", n:"code"},
    {id:"panel", label:"Panel",  n:"monitor"},
    {id:"jasteb",label:"Jasteb", n:"pkg"},
    {id:"unchek",label:"Unchek", n:"unlock"},
  ];

  const isJasteb = p => p && p.pricePerHour !== undefined;

  const handleBuy = product => {
    if (isJasteb(product)) {
      setJastebProduct(product);
    } else {
      if (currentUser.balance < product.price) { notify("Saldo tidak cukup! Silakan top up.","error"); return; }
      setBuyingProduct(product);
    }
  };

  const confirmBuyRegular = () => {
    const p = buyingProduct;
    updateUser({...currentUser, balance: currentUser.balance - p.price});
    addTx({id:gid(),userId:currentUser.id,type:"purchase",amount:-p.price,desc:"Beli "+p.name,date:today(),status:"success"});
    setBuyingProduct(null);
    notify("Pembelian berhasil!");
  };

  const filtered = (products[tab]||[]).filter(p=>p.name.toLowerCase().includes(search.toLowerCase()));

  return (
    <div>
      {/* Modal konfirmasi produk reguler */}
      {buyingProduct && !isJasteb(buyingProduct) && (
        <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.55)",display:"flex",alignItems:"flex-end",justifyContent:"center",zIndex:200}}>
          <div style={{background:"white",borderRadius:"28px 28px 0 0",width:"100%",maxWidth:430,padding:24,animation:"slideUp 0.25s ease"}}>
            <h3 style={{margin:"0 0 16px",fontSize:18,fontWeight:800}}>Konfirmasi Pembelian</h3>
            <div style={{background:"#F5F3FF",borderRadius:16,padding:16,marginBottom:16}}>
              <p style={{margin:"0 0 4px",fontWeight:700,color:"#111827",fontSize:15}}>{buyingProduct.name}</p>
              <p style={{margin:0,color:"#7C3AED",fontWeight:900,fontSize:20}}>{rp(buyingProduct.price)}</p>
            </div>
            <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Saldo kamu</span><span style={{fontWeight:600,color:"#111827"}}>{rp(currentUser.balance)}</span></div>
            <div style={{display:"flex",justifyContent:"space-between",marginBottom:18,fontSize:13,color:"#6B7280"}}><span>Saldo setelah beli</span><span style={{fontWeight:700,color:currentUser.balance-buyingProduct.price<0?"#DC2626":"#059669"}}>{rp(currentUser.balance-buyingProduct.price)}</span></div>
            <div style={{display:"flex",gap:12}}>
              <button onClick={()=>setBuyingProduct(null)} style={{flex:1,padding:14,borderRadius:14,border:"2px solid #E5E7EB",background:"white",cursor:"pointer",fontWeight:700,fontSize:14}}>Batal</button>
              <button onClick={confirmBuyRegular} style={{flex:2,padding:14,borderRadius:14,border:"none",background:"linear-gradient(135deg,#7C3AED,#5B21B6)",color:"white",cursor:"pointer",fontWeight:800,fontSize:14}}>Beli Sekarang</button>
            </div>
          </div>
        </div>
      )}

      {/* Jasteb buy modal */}
      {jastebProduct && (
        <JastebBuyModal
          product={jastebProduct}
          currentUser={currentUser}
          updateUser={updateUser}
          addTx={addTx}
          notify={notify}
          setModal={setModal}
          setJastebOrders={setJastebOrders}
          onClose={()=>setJastebProduct(null)}
        />
      )}

      <div style={{background:"linear-gradient(160deg,#2D1B69,#7C3AED)",padding:"48px 16px 20px"}}>
        <h2 style={{color:"white",fontSize:20,fontWeight:800,margin:"0 0 14px"}}>Toko Digital</h2>
        <div style={{position:"relative"}}>
          <div style={{position:"absolute",left:14,top:"50%",transform:"translateY(-50%)"}}>
            <Ico n="search" size={16} color="#9CA3AF"/>
          </div>
          <input value={search} onChange={e=>setSearch(e.target.value)} placeholder="Cari produk..."
            style={{width:"100%",padding:"12px 12px 12px 42px",borderRadius:14,border:"none",fontSize:14,boxSizing:"border-box",background:"white",outline:"none"}}/>
        </div>
      </div>

      <div style={{background:"white",display:"flex",overflowX:"auto",borderBottom:"1px solid #F3F4F6",boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
        {tabs.map(t => {
          const active = tab===t.id;
          return (
            <button key={t.id} onClick={()=>setTab(t.id)} style={{display:"flex",alignItems:"center",gap:6,padding:"14px 18px",border:"none",background:"none",cursor:"pointer",borderBottom:active?"3px solid #7C3AED":"3px solid transparent",color:active?"#7C3AED":"#6B7280",fontWeight:active?700:500,fontSize:13,whiteSpace:"nowrap",transition:"all 0.15s"}}>
              <Ico n={t.n} size={14} color={active?"#7C3AED":"#6B7280"}/>{t.label}
            </button>
          );
        })}
      </div>

      <div style={{padding:16}}>
        {filtered.length===0
          ? <div style={{textAlign:"center",padding:"48px 0",color:"#9CA3AF"}}>
              <Ico n="search" size={40} color="#D1D5DB"/>
              <p style={{margin:"12px 0 0",fontSize:14,fontWeight:600}}>Produk tidak ditemukan</p>
            </div>
          : filtered.map(p =>
              tab==="jasteb"
                ? <JastebCard key={p.id} product={p} onBuy={handleBuy}/>
                : tab==="script"||tab==="panel"
                ? <ImageCard  key={p.id} product={p} onBuy={handleBuy}/>
                : <SimpleCard key={p.id} product={p} onBuy={handleBuy}/>
            )
        }
      </div>
    </div>
  );
}

// ─── PRODUCT CARDS ────────────────────────────────────────────────────────────
function ImageCard({product, onBuy}) {
  const img = IMG[product.id];
  return (
    <div style={{background:"white",borderRadius:20,marginBottom:14,boxShadow:"0 2px 16px rgba(0,0,0,0.06)",overflow:"hidden"}}>
      {img && (
        <div style={{width:"100%",height:160,overflow:"hidden",position:"relative"}}>
          <img src={img} alt={product.name} style={{width:"100%",height:"100%",objectFit:"cover"}} onError={e=>{e.target.style.display="none"}}/>
          <div style={{position:"absolute",top:10,right:10,background:"rgba(0,0,0,0.5)",backdropFilter:"blur(4px)",borderRadius:10,padding:"4px 10px",display:"flex",alignItems:"center",gap:4}}>
            <Ico n="star" size={11} color="#F59E0B" fill="#F59E0B"/>
            <span style={{fontSize:12,fontWeight:700,color:"white"}}>{product.rating}</span>
          </div>
        </div>
      )}
      <div style={{padding:"14px 16px"}}>
        <h4 style={{margin:"0 0 6px",fontSize:15,fontWeight:800,color:"#111827"}}>{product.name}</h4>
        <p style={{margin:"0 0 12px",fontSize:12,color:"#6B7280",lineHeight:1.5}}>{product.desc}</p>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center"}}>
          <div>
            <p style={{margin:0,fontSize:18,fontWeight:900,color:"#7C3AED"}}>{rp(product.price)}</p>
            <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{product.sales.toLocaleString()} terjual</p>
          </div>
          <button onClick={()=>onBuy(product)} style={{background:"linear-gradient(135deg,#7C3AED,#5B21B6)",color:"white",border:"none",borderRadius:12,padding:"10px 22px",fontWeight:700,fontSize:13,cursor:"pointer",boxShadow:"0 4px 12px rgba(124,58,237,0.3)"}}>
            Beli
          </button>
        </div>
      </div>
    </div>
  );
}

function JastebCard({product, onBuy}) {
  const perMenit = Math.round(product.pricePerHour / 60);
  return (
    <div style={{background:"white",borderRadius:20,padding:16,marginBottom:14,boxShadow:"0 2px 16px rgba(0,0,0,0.06)",border:"1.5px solid #EDE9FE"}}>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:8}}>
        <h4 style={{margin:0,fontSize:15,fontWeight:800,color:"#111827",flex:1,paddingRight:8}}>{product.name}</h4>
        <div style={{display:"flex",alignItems:"center",gap:3,flexShrink:0}}>
          <Ico n="star" size={11} color="#F59E0B" fill="#F59E0B"/>
          <span style={{fontSize:12,fontWeight:700,color:"#111827"}}>{product.rating}</span>
        </div>
      </div>
      <p style={{margin:"0 0 12px",fontSize:12,color:"#6B7280",lineHeight:1.5}}>{product.desc}</p>

      {/* Tarif badge */}
      <div style={{display:"flex",gap:8,marginBottom:12,flexWrap:"wrap"}}>
        <div style={{background:"#F5F3FF",borderRadius:10,padding:"5px 12px",display:"flex",alignItems:"center",gap:5}}>
          <Ico n="timer" size={12} color="#7C3AED"/>
          <span style={{fontSize:11,fontWeight:700,color:"#7C3AED"}}>{rp(product.pricePerHour)}/jam</span>
        </div>
        <div style={{background:"#EFF6FF",borderRadius:10,padding:"5px 12px",display:"flex",alignItems:"center",gap:5}}>
          <Ico n="clock" size={12} color="#3B82F6"/>
          <span style={{fontSize:11,fontWeight:700,color:"#3B82F6"}}>{rp(perMenit)}/menit</span>
        </div>
      </div>

      <div style={{display:"flex",alignItems:"center",gap:6,marginBottom:14,background:"#F0FDF4",borderRadius:10,padding:"7px 12px"}}>
        <Ico n="ok" size={12} color="#059669"/>
        <span style={{fontSize:11,fontWeight:600,color:"#059669"}}>Bayar per jam atau per menit — fleksibel!</span>
      </div>

      <div style={{display:"flex",justifyContent:"space-between",alignItems:"center"}}>
        <div>
          <p style={{margin:0,fontSize:17,fontWeight:900,color:"#7C3AED"}}>Mulai {rp(perMenit * 15)}</p>
          <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{product.sales.toLocaleString()} terjual • pilih durasi</p>
        </div>
        <button onClick={()=>onBuy(product)} style={{background:"linear-gradient(135deg,#7C3AED,#5B21B6)",color:"white",border:"none",borderRadius:12,padding:"10px 20px",fontWeight:700,fontSize:13,cursor:"pointer",boxShadow:"0 4px 12px rgba(124,58,237,0.3)"}}>
          Beli
        </button>
      </div>
    </div>
  );
}

function SimpleCard({product, onBuy}) {
  return (
    <div style={{background:"white",borderRadius:18,padding:16,marginBottom:12,boxShadow:"0 2px 12px rgba(0,0,0,0.05)"}}>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:8}}>
        <h4 style={{margin:0,fontSize:15,fontWeight:800,color:"#111827",flex:1,paddingRight:8}}>{product.name}</h4>
        <div style={{display:"flex",alignItems:"center",gap:3,flexShrink:0}}>
          <Ico n="star" size={11} color="#F59E0B" fill="#F59E0B"/>
          <span style={{fontSize:12,fontWeight:700,color:"#111827"}}>{product.rating}</span>
        </div>
      </div>
      <p style={{margin:"0 0 12px",fontSize:12,color:"#6B7280",lineHeight:1.5}}>{product.desc}</p>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"center"}}>
        <div>
          <p style={{margin:0,fontSize:17,fontWeight:900,color:"#7C3AED"}}>{rp(product.price)}</p>
          <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{product.sales.toLocaleString()} terjual</p>
        </div>
        <button onClick={()=>onBuy(product)} style={{background:"linear-gradient(135deg,#7C3AED,#5B21B6)",color:"white",border:"none",borderRadius:12,padding:"10px 20px",fontWeight:700,fontSize:13,cursor:"pointer",boxShadow:"0 4px 12px rgba(124,58,237,0.3)"}}>
          Beli
        </button>
      </div>
    </div>
  );
}

// ─── WALLET PAGE ──────────────────────────────────────────────────────────────
function WalletPage({currentUser, txs, setModal, topupReqs}) {
  const myPending = topupReqs.filter(r=>r.userId===currentUser.id&&r.status==="pending");
  const myDone    = topupReqs.filter(r=>r.userId===currentUser.id&&r.status!=="pending");
  const [tab,setTab] = useState("tx");

  return (
    <div>
      <div style={{background:"linear-gradient(160deg,#2D1B69,#7C3AED)",padding:"48px 16px 30px"}}>
        <h2 style={{color:"white",fontSize:20,fontWeight:800,margin:"0 0 4px"}}>Dompet</h2>
        <p style={{color:"rgba(255,255,255,0.7)",fontSize:13,margin:0}}>Saldo: {rp(currentUser.balance)}</p>
      </div>
      <div style={{padding:"16px 16px 0"}}>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:12,marginBottom:16}}>
          <button onClick={()=>setModal({type:"payment"})} style={{background:"white",border:"2px solid #EDE9FE",borderRadius:18,padding:"18px 14px",display:"flex",flexDirection:"column",alignItems:"center",gap:10,cursor:"pointer",boxShadow:"0 2px 12px rgba(0,0,0,0.04)"}}>
            <div style={{width:46,height:46,borderRadius:14,background:"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="qr" size={22} color="#7C3AED"/>
            </div>
            <div style={{textAlign:"center"}}>
              <p style={{margin:0,fontSize:14,fontWeight:700,color:"#111827"}}>Top Up</p>
              <p style={{margin:"2px 0 0",fontSize:11,color:"#7C3AED",fontWeight:600}}>via QRIS</p>
            </div>
          </button>
          <button onClick={()=>setModal({type:"transfer"})} style={{background:"linear-gradient(135deg,#7C3AED,#5B21B6)",border:"none",borderRadius:18,padding:"18px 14px",display:"flex",flexDirection:"column",alignItems:"center",gap:10,cursor:"pointer",boxShadow:"0 4px 16px rgba(124,58,237,0.25)"}}>
            <div style={{width:46,height:46,borderRadius:14,background:"rgba(255,255,255,0.2)",display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="aur" size={22} color="white"/>
            </div>
            <div style={{textAlign:"center"}}>
              <p style={{margin:0,fontSize:14,fontWeight:700,color:"white"}}>Transfer</p>
              <p style={{margin:"2px 0 0",fontSize:11,color:"rgba(255,255,255,0.8)"}}>Antar User</p>
            </div>
          </button>
        </div>

        {/* Tabs */}
        <div style={{display:"flex",background:"#F5F3FF",borderRadius:14,padding:4,marginBottom:16}}>
          {[{id:"tx",label:"Transaksi"},{id:"topup",label:`Top Up (${myPending.length} pending)`}].map(t => (
            <button key={t.id} onClick={()=>setTab(t.id)} style={{flex:1,padding:"10px",borderRadius:11,border:"none",cursor:"pointer",fontWeight:700,fontSize:12,background:tab===t.id?"#7C3AED":"transparent",color:tab===t.id?"white":"#6B7280"}}>
              {t.label}
            </button>
          ))}
        </div>

        {tab==="tx" && (
          txs.length===0
          ? <div style={{textAlign:"center",padding:"32px 0",color:"#9CA3AF",fontSize:13}}>Belum ada transaksi</div>
          : txs.map(tx=><TxItem key={tx.id} tx={tx}/>)
        )}

        {tab==="topup" && (
          <>
            {myPending.length > 0 && (
              <>
                <h4 style={{margin:"0 0 10px",fontSize:14,fontWeight:700,color:"#D97706"}}>Menunggu Konfirmasi</h4>
                {myPending.map(r => (
                  <div key={r.id} style={{background:"#FFFBEB",border:"1.5px solid #FDE68A",borderRadius:16,padding:"13px 15px",marginBottom:10,display:"flex",alignItems:"center",gap:12}}>
                    <div style={{width:40,height:40,borderRadius:12,background:"#FEF3C7",display:"flex",alignItems:"center",justifyContent:"center"}}><Ico n="clock" size={20} color="#D97706"/></div>
                    <div style={{flex:1}}>
                      <p style={{margin:0,fontWeight:700,fontSize:13,color:"#111827"}}>Top Up {rp(r.amount)}</p>
                      <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{r.date}</p>
                    </div>
                    <span style={{background:"#FEF3C7",borderRadius:8,padding:"4px 10px",fontSize:11,fontWeight:700,color:"#D97706"}}>PENDING</span>
                  </div>
                ))}
              </>
            )}
            {myDone.length > 0 && (
              <>
                <h4 style={{margin:"12px 0 10px",fontSize:14,fontWeight:700,color:"#374151"}}>Riwayat Top Up</h4>
                {myDone.map(r => (
                  <div key={r.id} style={{background:"white",borderRadius:14,padding:"13px 15px",marginBottom:8,display:"flex",alignItems:"center",gap:12,boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
                    <div style={{width:38,height:38,borderRadius:12,background:r.status==="confirmed"?"#ECFDF5":"#FEF2F2",display:"flex",alignItems:"center",justifyContent:"center"}}>
                      <Ico n={r.status==="confirmed"?"ok":"x"} size={18} color={r.status==="confirmed"?"#059669":"#DC2626"}/>
                    </div>
                    <div style={{flex:1}}>
                      <p style={{margin:0,fontWeight:700,fontSize:13}}>{rp(r.amount)}</p>
                      <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{r.date}</p>
                    </div>
                    <span style={{fontSize:11,fontWeight:700,color:r.status==="confirmed"?"#059669":"#DC2626"}}>{r.status==="confirmed"?"Berhasil":"Ditolak"}</span>
                  </div>
                ))}
              </>
            )}
            {myPending.length===0 && myDone.length===0 && (
              <div style={{textAlign:"center",padding:"32px 0",color:"#9CA3AF",fontSize:13}}>Belum ada riwayat top up</div>
            )}
          </>
        )}
      </div>
    </div>
  );
}

// ─── CHAT PAGE ────────────────────────────────────────────────────────────────
function ChatPage({currentUser, chats, setChats}) {
  const [input,   setInput]   = useState("");
  const [loading, setLoading] = useState(false);
  const chatRef = useRef(null);
  const myChat  = chats.find(c=>c.userId===currentUser.id);

  useEffect(() => {
    if (!myChat) {
      setChats(prev => [...prev, {id:gid(),userId:currentUser.id,userName:currentUser.name,messages:[{from:"ai",text:`Halo ${currentUser.name}! Selamat datang di DikzShop!\n\nSaya siap bantu kamu seputar produk, pembayaran, dan layanan kami. Silakan tanyakan apa saja!`,time:now()}],lastMsg:"Halo!",unread:0}]);
    }
  }, []);

  useEffect(() => {
    if(chatRef.current) chatRef.current.scrollTop=chatRef.current.scrollHeight;
  }, [myChat?.messages, loading]);

  const send = async () => {
    if (!input.trim()||loading) return;
    const txt = input.trim();
    setInput(""); setLoading(true);
    setChats(prev=>prev.map(c=>c.userId===currentUser.id?{...c,messages:[...c.messages,{from:"user",text:txt,time:now()}],lastMsg:txt}:c));
    try {
      const history = (myChat?.messages||[]).slice(-8).map(m=>({role:m.from==="user"?"user":"assistant",content:m.text}));
      const res = await fetch("https://api.anthropic.com/v1/messages",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({model:"claude-sonnet-4-20250514",max_tokens:600,system:"Kamu adalah asisten customer service DikzShop yang ramah dan helpful. DikzShop adalah platform jasteb & digital terpercaya Indonesia. Produk: Script (bot automation), Panel (SMM & PPOB), Jasteb (jasa titip per jam/menit — pilih durasi & masukkan email kamu), Unchek (unlock akun). Pembayaran via QRIS, saldo bisa ditransfer antar user. Jawab dalam bahasa Indonesia yang ramah dan singkat.",messages:[...history,{role:"user",content:txt}]})});
      const d = await res.json();
      const reply = d.content?.[0]?.text || "Maaf ada gangguan. Silakan coba lagi.";
      setChats(prev=>prev.map(c=>c.userId===currentUser.id?{...c,messages:[...c.messages,{from:"ai",text:reply,time:now()}],lastMsg:reply.slice(0,50)}:c));
    } catch {
      setChats(prev=>prev.map(c=>c.userId===currentUser.id?{...c,messages:[...c.messages,{from:"ai",text:"Maaf ada gangguan koneksi. Coba lagi beberapa saat.",time:now()}]}:c));
    }
    setLoading(false);
  };

  const currentChat = chats.find(c=>c.userId===currentUser.id);
  return (
    <div style={{display:"flex",flexDirection:"column",height:"calc(100vh - 80px)"}}>
      <div style={{background:"linear-gradient(160deg,#2D1B69,#7C3AED)",padding:"48px 16px 18px"}}>
        <div style={{display:"flex",alignItems:"center",gap:12}}>
          <div style={{width:44,height:44,borderRadius:"50%",background:"white",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="zap" size={22} color="#7C3AED"/>
          </div>
          <div>
            <h2 style={{color:"white",fontSize:16,fontWeight:800,margin:0}}>DikzShop Support</h2>
            <div style={{display:"flex",alignItems:"center",gap:6}}>
              <div style={{width:7,height:7,borderRadius:"50%",background:"#10B981",animation:"pulse 1.5s infinite"}}/>
              <span style={{color:"rgba(255,255,255,0.75)",fontSize:12}}>Online • AI Powered</span>
            </div>
          </div>
        </div>
      </div>

      <div ref={chatRef} style={{flex:1,overflowY:"auto",padding:"14px 16px",display:"flex",flexDirection:"column",gap:12,background:"#F4F2FF"}}>
        {currentChat?.messages.map((msg,i) => (
          <div key={i} style={{display:"flex",justifyContent:msg.from==="user"?"flex-end":"flex-start",gap:8,alignItems:"flex-end"}}>
            {msg.from!=="user" && (
              <div style={{width:30,height:30,borderRadius:"50%",background:"#7C3AED",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                <Ico n="zap" size={13} color="white"/>
              </div>
            )}
            <div>
              <div style={{background:msg.from==="user"?"linear-gradient(135deg,#7C3AED,#5B21B6)":"white",color:msg.from==="user"?"white":"#111827",padding:"11px 15px",borderRadius:msg.from==="user"?"18px 18px 4px 18px":"18px 18px 18px 4px",maxWidth:270,fontSize:13,lineHeight:1.55,boxShadow:"0 2px 10px rgba(0,0,0,0.07)",wordBreak:"break-word",whiteSpace:"pre-line"}}>
                {msg.text}
              </div>
              <p style={{margin:"3px 0 0",fontSize:10,color:"#9CA3AF",textAlign:msg.from==="user"?"right":"left"}}>{msg.time}</p>
            </div>
          </div>
        ))}
        {loading && (
          <div style={{display:"flex",gap:8,alignItems:"flex-end"}}>
            <div style={{width:30,height:30,borderRadius:"50%",background:"#7C3AED",display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="zap" size={13} color="white"/>
            </div>
            <div style={{background:"white",padding:"13px 16px",borderRadius:"18px 18px 18px 4px",boxShadow:"0 2px 10px rgba(0,0,0,0.07)"}}>
              <div style={{display:"flex",gap:4}}>
                {[0,1,2].map(i=><div key={i} style={{width:6,height:6,borderRadius:"50%",background:"#7C3AED",animation:`bounce 1s infinite ${i*0.18}s`}}/>)}
              </div>
            </div>
          </div>
        )}
      </div>

      <div style={{background:"white",padding:"10px 14px",borderTop:"1px solid #F3F4F6",display:"flex",gap:8,alignItems:"center"}}>
        <input value={input} onChange={e=>setInput(e.target.value)} onKeyDown={e=>e.key==="Enter"&&!e.shiftKey&&send()} placeholder="Ketik pesan..."
          style={{flex:1,padding:"11px 16px",borderRadius:24,border:"1.5px solid #EDE9FE",background:"#F9FAFB",fontSize:14,outline:"none"}}/>
        <button onClick={send} disabled={loading||!input.trim()} style={{width:44,height:44,borderRadius:"50%",background:"linear-gradient(135deg,#7C3AED,#5B21B6)",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",opacity:loading||!input.trim()?0.5:1}}>
          <Ico n="send" size={18} color="white"/>
        </button>
      </div>
    </div>
  );
}

// ─── EVENT PAGE ───────────────────────────────────────────────────────────────
function EventPage({currentUser, users, notify}) {
  const [copied,  setCopied]  = useState(false);
  const refs     = users.filter(u=>u.referredBy===currentUser.id);
  const earned   = refs.length * 25000;
  const milestones = [{n:1,reward:25000},{n:3,reward:100000},{n:5,reward:200000},{n:10,reward:500000}];

  const copy = () => {
    navigator.clipboard?.writeText(currentUser.referralCode).catch(()=>{});
    setCopied(true); setTimeout(()=>setCopied(false),2000);
    notify("Kode referral disalin!");
  };

  return (
    <div>
      <div style={{background:"linear-gradient(160deg,#7F1D1D,#DC2626,#7C3AED)",padding:"48px 16px 30px"}}>
        <h2 style={{color:"white",fontSize:20,fontWeight:800,margin:"0 0 4px"}}>Event Undang Teman</h2>
        <p style={{color:"rgba(255,255,255,0.75)",fontSize:13,margin:0}}>Ajak teman, raih hadiah berlimpah!</p>
      </div>
      <div style={{padding:16}}>
        <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:12,marginBottom:18}}>
          {[{n:"users",val:refs.length,label:"Teman Diajak",color:"#7C3AED"},{n:"wallet",val:rp(earned),label:"Bonus Didapat",color:"#059669"}].map((s,i) => (
            <div key={i} style={{background:"white",borderRadius:18,padding:18,textAlign:"center",boxShadow:"0 2px 12px rgba(0,0,0,0.05)"}}>
              <div style={{width:44,height:44,borderRadius:14,background:s.color+"15",display:"flex",alignItems:"center",justifyContent:"center",margin:"0 auto 10px"}}>
                <Ico n={s.n} size={22} color={s.color}/>
              </div>
              <p style={{margin:0,fontSize:20,fontWeight:900,color:"#111827"}}>{s.val}</p>
              <p style={{margin:"4px 0 0",fontSize:11,color:"#6B7280"}}>{s.label}</p>
            </div>
          ))}
        </div>

        <div style={{background:"linear-gradient(135deg,#7C3AED,#4C1D95)",borderRadius:20,padding:20,marginBottom:18}}>
          <p style={{color:"rgba(255,255,255,0.7)",fontSize:11,margin:"0 0 8px",fontWeight:600,letterSpacing:"1px"}}>KODE REFERRAL KAMU</p>
          <div style={{display:"flex",alignItems:"center",gap:12}}>
            <h2 style={{color:"white",fontSize:26,fontWeight:900,margin:0,letterSpacing:4}}>{currentUser.referralCode}</h2>
            <button onClick={copy} style={{background:"rgba(255,255,255,0.2)",border:"1px solid rgba(255,255,255,0.3)",borderRadius:10,padding:"8px 14px",color:"white",cursor:"pointer",display:"flex",alignItems:"center",gap:6,fontSize:12,fontWeight:700}}>
              <Ico n={copied?"check":"copy"} size={13} color="white"/>{copied?"Disalin!":"Salin"}
            </button>
          </div>
          <p style={{color:"rgba(255,255,255,0.7)",fontSize:12,margin:"12px 0 0"}}>Teman dapat +Rp 10.000 • Kamu dapat +Rp 25.000 per teman</p>
        </div>

        <h3 style={{margin:"0 0 14px",fontSize:15,fontWeight:800,color:"#111827"}}>Target & Hadiah</h3>
        {milestones.map((m,i) => {
          const done = refs.length >= m.n;
          return (
            <div key={i} style={{background:"white",borderRadius:16,padding:"14px 16px",display:"flex",alignItems:"center",gap:14,marginBottom:10,border:`1.5px solid ${done?"#7C3AED":"#F3F4F6"}`,boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
              <div style={{width:42,height:42,borderRadius:"50%",background:done?"#7C3AED":"#F3F4F6",display:"flex",alignItems:"center",justifyContent:"center"}}>
                <Ico n={done?"ok":"users"} size={20} color={done?"white":"#9CA3AF"}/>
              </div>
              <div style={{flex:1}}>
                <p style={{margin:0,fontWeight:700,color:"#111827",fontSize:14}}>Ajak {m.n} Teman</p>
                <p style={{margin:"2px 0 0",color:"#059669",fontSize:13,fontWeight:700}}>+{rp(m.reward)}</p>
              </div>
              <div style={{background:done?"#F0FDF4":"#F9FAFB",borderRadius:8,padding:"4px 10px"}}>
                <span style={{fontSize:11,fontWeight:700,color:done?"#059669":"#9CA3AF"}}>{done?"Selesai!":`${refs.length}/${m.n}`}</span>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}

// ─── PROFILE PAGE ─────────────────────────────────────────────────────────────
function ProfilePage({currentUser, setCurrentUser, setPage, notify}) {
  const menus = [
    {n:"wallet", label:"Riwayat & Dompet",   action:()=>setPage("wallet")},
    {n:"users",  label:"Undang Teman",         action:()=>setPage("event")},
    {n:"shield", label:"Keamanan Akun",        action:()=>notify("Fitur segera hadir!")},
    {n:"bell",   label:"Notifikasi",           action:()=>notify("Fitur segera hadir!")},
    {n:"info",   label:"Tentang DikzShop",     action:()=>notify("DikzShop v4.0 — Platform Jasteb & Digital")},
  ];
  return (
    <div>
      <div style={{background:"linear-gradient(160deg,#2D1B69,#7C3AED)",padding:"48px 16px 40px"}}>
        <div style={{display:"flex",alignItems:"center",gap:16}}>
          <div style={{width:68,height:68,borderRadius:22,background:"white",display:"flex",alignItems:"center",justifyContent:"center",boxShadow:"0 4px 20px rgba(0,0,0,0.15)"}}>
            <span style={{fontSize:26,fontWeight:900,color:"#7C3AED"}}>{currentUser.avatar}</span>
          </div>
          <div>
            <h2 style={{color:"white",fontSize:20,fontWeight:800,margin:0}}>{currentUser.name}</h2>
            <p style={{color:"rgba(255,255,255,0.7)",fontSize:12,margin:"4px 0 0"}}>{currentUser.email}</p>
            <div style={{display:"inline-flex",alignItems:"center",gap:4,background:"rgba(255,255,255,0.15)",borderRadius:8,padding:"3px 10px",marginTop:6}}>
              <Ico n="star" size={11} color="#F59E0B" fill="#F59E0B"/>
              <span style={{fontSize:11,color:"white",fontWeight:600}}>Member Aktif</span>
            </div>
          </div>
        </div>
      </div>
      <div style={{padding:16}}>
        <div style={{background:"white",borderRadius:20,padding:"18px 20px",marginBottom:14,boxShadow:"0 2px 12px rgba(0,0,0,0.05)"}}>
          <p style={{margin:"0 0 4px",fontSize:12,color:"#6B7280"}}>Total Saldo</p>
          <p style={{margin:0,fontSize:26,fontWeight:900,color:"#111827"}}>{rp(currentUser.balance)}</p>
        </div>

        {currentUser.role==="admin" && (
          <button onClick={()=>setPage("admin")} style={{width:"100%",background:"linear-gradient(135deg,#D97706,#B45309)",border:"none",borderRadius:18,padding:"16px 20px",display:"flex",alignItems:"center",gap:12,cursor:"pointer",marginBottom:12,boxShadow:"0 4px 16px rgba(217,119,6,0.25)"}}>
            <div style={{width:40,height:40,borderRadius:12,background:"rgba(255,255,255,0.2)",display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="crown" size={20} color="white"/>
            </div>
            <span style={{color:"white",fontWeight:800,fontSize:15,flex:1,textAlign:"left"}}>Buka Admin Panel</span>
            <Ico n="cr" size={18} color="white"/>
          </button>
        )}

        <div style={{background:"white",borderRadius:20,overflow:"hidden",boxShadow:"0 2px 12px rgba(0,0,0,0.05)",marginBottom:12}}>
          {menus.map((m,i) => (
            <button key={i} onClick={m.action} style={{width:"100%",display:"flex",alignItems:"center",gap:14,padding:"16px 18px",border:"none",background:"white",cursor:"pointer",borderBottom:i<menus.length-1?"1px solid #F9FAFB":"none"}}>
              <div style={{width:36,height:36,borderRadius:10,background:"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center"}}>
                <Ico n={m.n} size={17} color="#7C3AED"/>
              </div>
              <span style={{flex:1,fontSize:14,fontWeight:500,color:"#111827",textAlign:"left"}}>{m.label}</span>
              <Ico n="cr" size={15} color="#D1D5DB"/>
            </button>
          ))}
        </div>

        <button onClick={()=>setCurrentUser(null)} style={{width:"100%",background:"white",border:"1.5px solid #FEE2E2",borderRadius:18,padding:"16px 20px",display:"flex",alignItems:"center",gap:12,cursor:"pointer"}}>
          <div style={{width:36,height:36,borderRadius:10,background:"#FEF2F2",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="logout" size={17} color="#DC2626"/>
          </div>
          <span style={{color:"#DC2626",fontWeight:700,fontSize:15}}>Keluar</span>
        </button>
      </div>
    </div>
  );
}

// ─── PAYMENT MODAL (SUPER ADVANCED) ──────────────────────────────────────────
const PAYMENT_METHODS = [
  {
    id:"qris", label:"QRIS", sub:"Semua e-wallet & m-banking",
    color:"#7C3AED", bg:"#F5F3FF",
    icon:"qr", fee:0, feeLabel:"Gratis",
    supports:["GoPay","OVO","DANA","ShopeePay","LinkAja","Jenius","BCA","Mandiri","BNI","BRI","BSI"]
  },
  {
    id:"bca",   label:"Transfer BCA",   sub:"Virtual Account BCA",   color:"#1E40AF",bg:"#EFF6FF",icon:"wallet",fee:0,  feeLabel:"Gratis",    bankCode:"014",prefix:"70017"
  },
  {
    id:"mandiri",label:"Transfer Mandiri",sub:"Virtual Account Mandiri",color:"#1D4ED8",bg:"#EFF6FF",icon:"wallet",fee:0,feeLabel:"Gratis",    bankCode:"008",prefix:"88890"
  },
  {
    id:"bni",   label:"Transfer BNI",   sub:"Virtual Account BNI",   color:"#F97316",bg:"#FFF7ED",icon:"wallet",fee:0,  feeLabel:"Gratis",    bankCode:"009",prefix:"8808"
  },
  {
    id:"bri",   label:"Transfer BRI",   sub:"Virtual Account BRI",   color:"#15803D",bg:"#F0FDF4",icon:"wallet",fee:0,  feeLabel:"Gratis",    bankCode:"002",prefix:"26300"
  },
  {
    id:"gopay", label:"GoPay",          sub:"Transfer ke no. GoPay", color:"#00AA13",bg:"#F0FDF4",icon:"zap",   fee:0,  feeLabel:"Gratis",    phone:"0821-xxxx-xxxx"
  },
  {
    id:"ovo",   label:"OVO",            sub:"Transfer ke no. OVO",   color:"#4C1D95",bg:"#F5F3FF",icon:"zap",   fee:1000,feeLabel:"Rp 1.000", phone:"0812-xxxx-xxxx"
  },
  {
    id:"dana",  label:"DANA",           sub:"Transfer ke no. DANA",  color:"#1D4ED8",bg:"#EFF6FF",icon:"zap",   fee:0,  feeLabel:"Gratis",    phone:"0813-xxxx-xxxx"
  },
  {
    id:"shopeepay",label:"ShopeePay",   sub:"Transfer ke no. HP",    color:"#EA580C",bg:"#FFF7ED",icon:"zap",   fee:0,  feeLabel:"Gratis",    phone:"0878-xxxx-xxxx"
  },
  {
    id:"pulsa", label:"Pulsa / Voucher",sub:"Transfer pulsa ke admin",color:"#B45309",bg:"#FFFBEB",icon:"phone",fee:5000,feeLabel:"Rp 5.000",  phone:"0821-1234-5678"
  },
];

function genVA(method, amount) {
  const seed = (amount % 9000) + 1000;
  if (method.prefix) return method.prefix + seed + "00";
  return "";
}

function QRPattern({seed=42, size=180}) {
  const cells = 13;
  const cell = Math.floor(size/cells);
  return (
    <div style={{display:"grid",gridTemplateColumns:`repeat(${cells},${cell}px)`,gap:0,background:"white",padding:6,borderRadius:8}}>
      {Array.from({length:cells*cells},(_,i)=>{
        const r=Math.floor(i/cells),c=i%cells;
        const cornerTL=(r<4&&c<4);
        const cornerTR=(r<4&&c>=cells-4);
        const cornerBL=(r>=cells-4&&c<4);
        const isCornerBorder=((r===0||r===3)&&c<4)||((c===0||c===3)&&r<4)||
                             ((r===0||r===3)&&c>=cells-4)||((c===cells-1||c===cells-4)&&r<4)||
                             ((r===cells-1||r===cells-4)&&c<4)||((c===0||c===3)&&r>=cells-4);
        const isCornerFill=(r>=1&&r<=2&&c>=1&&c<=2)||(r>=1&&r<=2&&c>=cells-3&&c<=cells-2)||(r>=cells-3&&r<=cells-2&&c>=1&&c<=2);
        const dark=isCornerBorder||isCornerFill||(!cornerTL&&!cornerTR&&!cornerBL&&((r+c+seed)%3===0||(r*c+seed)%5===0));
        return <div key={i} style={{width:cell,height:cell,background:dark?"#111827":"white"}}/>;
      })}
    </div>
  );
}

function CountdownTimer({expiresAt, onExpired}) {
  const [sisa, setSisa] = useState(expiresAt - Date.now());
  useEffect(() => {
    const t = setInterval(() => {
      const r = expiresAt - Date.now();
      setSisa(r);
      if (r <= 0) { clearInterval(t); onExpired && onExpired(); }
    }, 1000);
    return () => clearInterval(t);
  }, [expiresAt]);
  const mins = Math.max(0, Math.floor(sisa/60000));
  const secs = Math.max(0, Math.floor((sisa%60000)/1000));
  const pct  = Math.max(0, sisa/(15*60000));
  const color = pct > 0.4 ? "#059669" : pct > 0.15 ? "#D97706" : "#DC2626";
  return (
    <div style={{background:"#F9FAFB",borderRadius:14,padding:"12px 16px",marginBottom:14}}>
      <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:8}}>
        <div style={{display:"flex",alignItems:"center",gap:6}}>
          <Ico n="clock" size={14} color={color}/>
          <span style={{fontSize:12,fontWeight:700,color:"#374151"}}>Batas Waktu Pembayaran</span>
        </div>
        <span style={{fontSize:18,fontWeight:900,color,fontFamily:"monospace"}}>
          {String(mins).padStart(2,"0")}:{String(secs).padStart(2,"0")}
        </span>
      </div>
      <div style={{height:6,background:"#E5E7EB",borderRadius:3,overflow:"hidden"}}>
        <div style={{height:"100%",width:`${pct*100}%`,background:`linear-gradient(90deg,${color},${color}aa)`,borderRadius:3,transition:"width 1s linear"}}/>
      </div>
      {sisa <= 0 && <p style={{margin:"6px 0 0",fontSize:11,color:"#DC2626",fontWeight:700}}>Waktu habis! Silakan buat transaksi baru.</p>}
    </div>
  );
}

function PaymentModal({setModal, currentUser, updateUser, addTx, setTopupReqs, notify}) {
  const AMTS = [10000,25000,50000,100000,200000,500000];
  const [step,        setStep]       = useState(1); // 1=amount, 2=method, 3=pay, 4=proof, 5=done
  const [amount,      setAmount]     = useState(50000);
  const [custom,      setCustom]     = useState("");
  const [method,      setMethod]     = useState(null);
  const [copied,      setCopied]     = useState(null);
  const [expired,     setExpired]    = useState(false);
  const [expiresAt,   setExpiresAt]  = useState(null);
  const [checking,    setChecking]   = useState(false);
  const [proofFile,   setProofFile]  = useState(null);
  const [proofPreview,setProofPreview]= useState(null);
  const [submitting,  setSubmitting] = useState(false);
  const [methodCat,   setMethodCat]  = useState("all"); // all|bank|ewallet|other
  const fileRef = useRef(null);

  const final = custom ? Number(custom) : amount;
  const totalBayar = method ? final + (method.fee||0) : final;
  const vaNumber   = method && method.prefix ? genVA(method, final) : null;

  const STEP_LABELS = ["Nominal","Metode","Bayar","Bukti","Selesai"];

  const copyText = (text, key) => {
    navigator.clipboard?.writeText(text).catch(()=>{});
    setCopied(key);
    setTimeout(()=>setCopied(null),2500);
    notify("Disalin!");
  };

  const goToPay = (m) => {
    setMethod(m);
    setExpiresAt(Date.now() + 15*60*1000);
    setExpired(false);
    setStep(3);
  };

  const handleProofFile = (e) => {
    const f = e.target.files?.[0];
    if (!f) return;
    if (f.size > 5*1024*1024) { notify("Ukuran file max 5MB!", "error"); return; }
    setProofFile(f);
    const reader = new FileReader();
    reader.onload = ev => setProofPreview(ev.target.result);
    reader.readAsDataURL(f);
  };

  const simulateCheck = () => {
    setChecking(true);
    setTimeout(() => {
      setChecking(false);
      notify("Pembayaran belum terdeteksi. Pastikan sudah transfer sesuai nominal.", "info");
    }, 2500);
  };

  const submitWithProof = () => {
    if (final < 10000) return notify("Minimal top up Rp 10.000!", "error");
    setSubmitting(true);
    setTimeout(() => {
      const req = {
        id:gid(), userId:currentUser.id, userName:currentUser.name,
        amount:final, date:today(), status:"pending", createdAt:Date.now(),
        method: method?.label || "QRIS",
        methodId: method?.id || "qris",
        vaNumber, proof: proofPreview ? "uploaded" : null,
      };
      setTopupReqs(prev=>[req,...prev]);
      setStep(5);
      setSubmitting(false);
    }, 1500);
  };

  const cats = [
    {id:"all",label:"Semua"},
    {id:"qris",label:"QRIS"},
    {id:"bank",label:"Bank"},
    {id:"ewallet",label:"E-Wallet"},
    {id:"other",label:"Lainnya"},
  ];
  const catFilter = m => {
    if (methodCat==="all") return true;
    if (methodCat==="qris") return m.id==="qris";
    if (methodCat==="bank") return ["bca","mandiri","bni","bri"].includes(m.id);
    if (methodCat==="ewallet") return ["gopay","ovo","dana","shopeepay"].includes(m.id);
    if (methodCat==="other") return m.id==="pulsa";
    return true;
  };

  const bankColor = {bca:"#1E40AF",mandiri:"#1D4ED8",bni:"#F97316",bri:"#15803D",gopay:"#00AA13",ovo:"#4C1D95",dana:"#1D4ED8",shopeepay:"#EA580C",qris:"#7C3AED",pulsa:"#B45309"};

  return (
    <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.65)",display:"flex",alignItems:"flex-end",justifyContent:"center",zIndex:300}}>
      <div style={{background:"white",borderRadius:"28px 28px 0 0",width:"100%",maxWidth:430,maxHeight:"94vh",overflowY:"auto",animation:"slideUp 0.25s ease"}}>
        {/* Header */}
        <div style={{background:"linear-gradient(135deg,#7C3AED,#5B21B6)",padding:"20px 20px 16px",borderRadius:"28px 28px 0 0",position:"sticky",top:0,zIndex:10}}>
          <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:14}}>
            <div>
              <h3 style={{margin:0,fontSize:18,fontWeight:800,color:"white"}}>Top Up Saldo</h3>
              <p style={{margin:"2px 0 0",fontSize:12,color:"rgba(255,255,255,0.7)"}}>
                {step===1?"Pilih nominal":step===2?"Pilih metode pembayaran":step===3?"Selesaikan pembayaran":step===4?"Upload bukti pembayaran":"Transaksi berhasil diajukan"}
              </p>
            </div>
            <button onClick={()=>setModal(null)} style={{width:34,height:34,borderRadius:"50%",background:"rgba(255,255,255,0.15)",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
              <Ico n="x" size={16} color="white"/>
            </button>
          </div>
          {/* Step indicator */}
          <div style={{display:"flex",alignItems:"center",gap:0}}>
            {STEP_LABELS.map((lbl,i) => {
              const idx = i+1;
              const done = step > idx;
              const active = step === idx;
              return (
                <React.Fragment key={i}>
                  <div style={{display:"flex",flexDirection:"column",alignItems:"center",gap:3}}>
                    <div style={{width:24,height:24,borderRadius:"50%",background:done?"#10B981":active?"white":"rgba(255,255,255,0.25)",display:"flex",alignItems:"center",justifyContent:"center",transition:"all 0.3s"}}>
                      {done
                        ? <Ico n="check" size={12} color="white"/>
                        : <span style={{fontSize:10,fontWeight:800,color:active?"#7C3AED":"rgba(255,255,255,0.5)"}}>{idx}</span>
                      }
                    </div>
                    <span style={{fontSize:8,color:active?"white":done?"#A7F3D0":"rgba(255,255,255,0.4)",fontWeight:active?700:400}}>{lbl}</span>
                  </div>
                  {i<STEP_LABELS.length-1 && <div style={{flex:1,height:2,background:done?"#10B981":"rgba(255,255,255,0.2)",margin:"0 4px 14px",transition:"background 0.3s"}}/>}
                </React.Fragment>
              );
            })}
          </div>
        </div>

        <div style={{padding:"20px 20px 28px"}}>

          {/* STEP 1: AMOUNT */}
          {step===1 && (
            <>
              <p style={{margin:"0 0 12px",fontSize:13,fontWeight:700,color:"#374151"}}>Pilih Nominal Top Up:</p>
              <div style={{display:"grid",gridTemplateColumns:"1fr 1fr 1fr",gap:8,marginBottom:16}}>
                {AMTS.map(a => {
                  const sel = amount===a && !custom;
                  return (
                    <button key={a} onClick={()=>{setAmount(a);setCustom("");}}
                      style={{padding:"13px 4px",borderRadius:14,border:`2px solid ${sel?"#7C3AED":"#E5E7EB"}`,background:sel?"linear-gradient(135deg,#7C3AED,#5B21B6)":"white",color:sel?"white":"#374151",fontWeight:sel?800:500,fontSize:12,cursor:"pointer",transition:"all 0.15s"}}>
                      {rp(a)}
                    </button>
                  );
                })}
              </div>
              <div style={{marginBottom:16}}>
                <Field label="Atau masukkan nominal lain" type="number" value={custom} onChange={v=>{setCustom(v);}} placeholder="Min. Rp 10.000"/>
              </div>
              <div style={{background:"linear-gradient(135deg,#F5F3FF,#EDE9FE)",borderRadius:16,padding:"16px 18px",marginBottom:18,display:"flex",justifyContent:"space-between",alignItems:"center",border:"1.5px solid #DDD6FE"}}>
                <div>
                  <p style={{margin:0,fontSize:12,color:"#6B7280"}}>Total Top Up</p>
                  <p style={{margin:"2px 0 0",fontSize:26,fontWeight:900,color:"#7C3AED"}}>{rp(final)}</p>
                </div>
                <div style={{width:50,height:50,borderRadius:16,background:"white",display:"flex",alignItems:"center",justifyContent:"center",boxShadow:"0 4px 12px rgba(124,58,237,0.2)"}}>
                  <Ico n="wallet" size={24} color="#7C3AED"/>
                </div>
              </div>
              <Btn onClick={()=>{ if(final<10000) return notify("Minimal top up Rp 10.000!","error"); setStep(2); }} color="#7C3AED">
                Pilih Metode Pembayaran
              </Btn>
            </>
          )}

          {/* STEP 2: METHOD */}
          {step===2 && (
            <>
              <div style={{background:"#F5F3FF",borderRadius:12,padding:"10px 14px",marginBottom:14,display:"flex",justifyContent:"space-between",alignItems:"center"}}>
                <span style={{fontSize:13,color:"#6B7280"}}>Nominal Top Up</span>
                <span style={{fontSize:15,fontWeight:900,color:"#7C3AED"}}>{rp(final)}</span>
              </div>
              {/* Category filter */}
              <div style={{display:"flex",gap:6,marginBottom:14,overflowX:"auto",paddingBottom:2}}>
                {cats.map(c=>(
                  <button key={c.id} onClick={()=>setMethodCat(c.id)}
                    style={{padding:"6px 14px",borderRadius:20,border:`1.5px solid ${methodCat===c.id?"#7C3AED":"#E5E7EB"}`,background:methodCat===c.id?"#7C3AED":"white",color:methodCat===c.id?"white":"#6B7280",fontWeight:methodCat===c.id?700:500,fontSize:12,cursor:"pointer",whiteSpace:"nowrap",flexShrink:0}}>
                    {c.label}
                  </button>
                ))}
              </div>
              <div style={{display:"flex",flexDirection:"column",gap:10}}>
                {PAYMENT_METHODS.filter(catFilter).map(m => (
                  <button key={m.id} onClick={()=>goToPay(m)}
                    style={{display:"flex",alignItems:"center",gap:14,padding:"14px 16px",background:"white",borderRadius:16,border:"1.5px solid #F3F4F6",cursor:"pointer",boxShadow:"0 2px 8px rgba(0,0,0,0.04)",textAlign:"left",transition:"all 0.15s"}}>
                    <div style={{width:46,height:46,borderRadius:14,background:m.bg,display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0,border:`1.5px solid ${m.color}22`}}>
                      <Ico n={m.icon} size={22} color={m.color}/>
                    </div>
                    <div style={{flex:1}}>
                      <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827"}}>{m.label}</p>
                      <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{m.sub}</p>
                      {m.supports && <div style={{display:"flex",gap:4,marginTop:5,flexWrap:"wrap"}}>
                        {m.supports.slice(0,5).map(s=><span key={s} style={{fontSize:9,background:"#F3F4F6",borderRadius:4,padding:"2px 6px",color:"#374151",fontWeight:600}}>{s}</span>)}
                        {m.supports.length>5 && <span style={{fontSize:9,color:"#9CA3AF"}}>+{m.supports.length-5}</span>}
                      </div>}
                    </div>
                    <div style={{display:"flex",flexDirection:"column",alignItems:"flex-end",gap:4,flexShrink:0}}>
                      <span style={{fontSize:11,fontWeight:700,color:m.fee===0?"#059669":"#D97706"}}>
                        {m.feeLabel}
                      </span>
                      <Ico n="cr" size={14} color="#D1D5DB"/>
                    </div>
                  </button>
                ))}
              </div>
              <button onClick={()=>setStep(1)} style={{width:"100%",background:"none",border:"none",color:"#6B7280",fontSize:13,cursor:"pointer",marginTop:14,padding:8}}>Kembali</button>
            </>
          )}

          {/* STEP 3: PAY */}
          {step===3 && method && (
            <>
              {/* Method badge */}
              <div style={{display:"flex",alignItems:"center",gap:10,padding:"12px 14px",background:method.bg,borderRadius:14,marginBottom:14,border:`1.5px solid ${method.color}33`}}>
                <div style={{width:36,height:36,borderRadius:10,background:"white",display:"flex",alignItems:"center",justifyContent:"center"}}>
                  <Ico n={method.icon} size={18} color={method.color}/>
                </div>
                <div style={{flex:1}}>
                  <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827"}}>{method.label}</p>
                  <p style={{margin:"1px 0 0",fontSize:11,color:"#6B7280"}}>{method.sub}</p>
                </div>
                <div style={{textAlign:"right"}}>
                  <p style={{margin:0,fontSize:12,color:"#6B7280"}}>Total Bayar</p>
                  <p style={{margin:"1px 0 0",fontSize:16,fontWeight:900,color:method.color}}>{rp(totalBayar)}</p>
                </div>
              </div>

              <CountdownTimer expiresAt={expiresAt} onExpired={()=>setExpired(true)}/>

              {expired && (
                <div style={{background:"#FEF2F2",borderRadius:12,padding:"12px 14px",marginBottom:14,textAlign:"center"}}>
                  <p style={{margin:"0 0 8px",fontWeight:700,color:"#DC2626",fontSize:14}}>Waktu pembayaran habis!</p>
                  <button onClick={()=>{setStep(2);setExpired(false);}} style={{background:"#DC2626",border:"none",borderRadius:10,padding:"8px 20px",color:"white",fontWeight:700,cursor:"pointer",fontSize:13}}>Ganti Metode</button>
                </div>
              )}

              {!expired && (
                <>
                  {/* QRIS */}
                  {method.id==="qris" && (
                    <div style={{textAlign:"center",marginBottom:16}}>
                      <p style={{margin:"0 0 12px",fontSize:13,color:"#6B7280"}}>Scan QR Code di bawah ini</p>
                      <div style={{display:"inline-block",padding:12,border:`3px solid ${method.color}`,borderRadius:20,background:"white",marginBottom:12,boxShadow:"0 8px 32px rgba(124,58,237,0.15)"}}>
                        <QRPattern seed={final % 97} size={180}/>
                      </div>
                      <div style={{background:"linear-gradient(135deg,#F5F3FF,#EDE9FE)",borderRadius:14,padding:"14px 18px",marginBottom:12,display:"inline-block",minWidth:200}}>
                        <p style={{margin:0,fontSize:11,color:"#6B7280"}}>Total Pembayaran</p>
                        <p style={{margin:"4px 0 0",fontSize:26,fontWeight:900,color:"#7C3AED"}}>{rp(totalBayar)}</p>
                        <p style={{margin:"4px 0 0",fontSize:10,color:"#9CA3AF"}}>Ref: DIKZ-{Math.abs(final*17+3).toString(16).toUpperCase().slice(0,8)}</p>
                      </div>
                      <p style={{margin:"0 0 10px",fontSize:12,color:"#6B7280"}}>Didukung oleh:</p>
                      <div style={{display:"flex",gap:5,justifyContent:"center",flexWrap:"wrap",marginBottom:8}}>
                        {["GoPay","OVO","DANA","ShopeePay","BCA","Mandiri","BNI","BRI","LinkAja","BSI"].map(w=>(
                          <span key={w} style={{fontSize:10,background:"#F3F4F6",borderRadius:6,padding:"3px 8px",color:"#374151",fontWeight:600}}>{w}</span>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* VIRTUAL ACCOUNT (Bank Transfer) */}
                  {["bca","mandiri","bni","bri"].includes(method.id) && vaNumber && (
                    <div style={{marginBottom:16}}>
                      <p style={{margin:"0 0 12px",fontSize:13,color:"#6B7280",fontWeight:600}}>Detail Virtual Account:</p>
                      {/* Bank name */}
                      <div style={{background:method.bg,borderRadius:14,padding:"14px 16px",marginBottom:10,border:`1.5px solid ${method.color}33`}}>
                        <p style={{margin:"0 0 4px",fontSize:11,color:"#6B7280",fontWeight:600}}>Bank Tujuan</p>
                        <p style={{margin:0,fontSize:16,fontWeight:900,color:method.color}}>{method.label.replace("Transfer ","")}</p>
                      </div>
                      {/* VA Number */}
                      <div style={{background:"#F9FAFB",borderRadius:14,padding:"14px 16px",marginBottom:10,border:"1.5px solid #E5E7EB"}}>
                        <p style={{margin:"0 0 4px",fontSize:11,color:"#6B7280",fontWeight:600}}>Nomor Virtual Account</p>
                        <div style={{display:"flex",alignItems:"center",gap:10}}>
                          <p style={{margin:0,fontSize:20,fontWeight:900,color:"#111827",fontFamily:"monospace",flex:1,letterSpacing:2}}>
                            {vaNumber.match(/.{1,4}/g)?.join(" ")}
                          </p>
                          <button onClick={()=>copyText(vaNumber,"va")}
                            style={{width:38,height:38,borderRadius:10,background:copied==="va"?"#ECFDF5":"#F5F3FF",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                            <Ico n={copied==="va"?"check":"copy"} size={16} color={copied==="va"?"#059669":"#7C3AED"}/>
                          </button>
                        </div>
                      </div>
                      {/* Amount */}
                      <div style={{background:"#F9FAFB",borderRadius:14,padding:"14px 16px",marginBottom:10,border:"1.5px solid #E5E7EB"}}>
                        <p style={{margin:"0 0 4px",fontSize:11,color:"#6B7280",fontWeight:600}}>Jumlah Transfer (harus tepat)</p>
                        <div style={{display:"flex",alignItems:"center",gap:10}}>
                          <p style={{margin:0,fontSize:20,fontWeight:900,color:method.color,flex:1}}>{rp(totalBayar)}</p>
                          <button onClick={()=>copyText(String(totalBayar),"amt")}
                            style={{width:38,height:38,borderRadius:10,background:copied==="amt"?"#ECFDF5":"#F5F3FF",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                            <Ico n={copied==="amt"?"check":"copy"} size={16} color={copied==="amt"?"#059669":"#7C3AED"}/>
                          </button>
                        </div>
                      </div>
                      {/* Steps */}
                      <div style={{background:method.bg,borderRadius:14,padding:"13px 16px",marginBottom:12,border:`1.5px solid ${method.color}22`}}>
                        <p style={{margin:"0 0 8px",fontSize:12,fontWeight:700,color:method.color}}>Cara Transfer {method.label.replace("Transfer ","")}:</p>
                        {["Buka aplikasi m-Banking atau ATM",`Pilih menu Transfer > Virtual Account`,`Masukkan nomor VA: ${vaNumber}`,"Masukkan nominal tepat: "+rp(totalBayar),"Konfirmasi dan selesaikan pembayaran"].map((s,i)=>(
                          <div key={i} style={{display:"flex",gap:8,marginBottom:5,alignItems:"flex-start"}}>
                            <div style={{width:18,height:18,borderRadius:"50%",background:method.color,display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0,marginTop:1}}>
                              <span style={{fontSize:9,fontWeight:800,color:"white"}}>{i+1}</span>
                            </div>
                            <span style={{fontSize:11,color:"#374151",lineHeight:1.5}}>{s}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* E-WALLET */}
                  {["gopay","ovo","dana","shopeepay"].includes(method.id) && (
                    <div style={{marginBottom:16}}>
                      <div style={{background:"#F9FAFB",borderRadius:14,padding:"14px 16px",marginBottom:10,border:"1.5px solid #E5E7EB"}}>
                        <p style={{margin:"0 0 4px",fontSize:11,color:"#6B7280",fontWeight:600}}>Nomor {method.label} Tujuan</p>
                        <div style={{display:"flex",alignItems:"center",gap:10}}>
                          <p style={{margin:0,fontSize:20,fontWeight:900,color:"#111827",fontFamily:"monospace",flex:1,letterSpacing:1}}>
                            {method.phone}
                          </p>
                          <button onClick={()=>copyText(method.phone,"phone")}
                            style={{width:38,height:38,borderRadius:10,background:copied==="phone"?"#ECFDF5":"#F5F3FF",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                            <Ico n={copied==="phone"?"check":"copy"} size={16} color={copied==="phone"?"#059669":"#7C3AED"}/>
                          </button>
                        </div>
                      </div>
                      <div style={{background:"#F9FAFB",borderRadius:14,padding:"14px 16px",marginBottom:10,border:"1.5px solid #E5E7EB"}}>
                        <p style={{margin:"0 0 4px",fontSize:11,color:"#6B7280",fontWeight:600}}>Jumlah Transfer</p>
                        <div style={{display:"flex",alignItems:"center",gap:10}}>
                          <p style={{margin:0,fontSize:20,fontWeight:900,color:method.color,flex:1}}>{rp(totalBayar)}</p>
                          <button onClick={()=>copyText(String(totalBayar),"amtew")}
                            style={{width:38,height:38,borderRadius:10,background:copied==="amtew"?"#ECFDF5":"#F5F3FF",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                            <Ico n={copied==="amtew"?"check":"copy"} size={16} color={copied==="amtew"?"#059669":"#7C3AED"}/>
                          </button>
                        </div>
                      </div>
                      {method.fee > 0 && (
                        <div style={{background:"#FEF3C7",borderRadius:10,padding:"10px 12px",marginBottom:10,display:"flex",gap:6,alignItems:"center"}}>
                          <Ico n="info" size={13} color="#D97706"/>
                          <span style={{fontSize:11,color:"#92400E",fontWeight:600}}>Biaya transfer {rp(method.fee)} sudah termasuk dalam total.</span>
                        </div>
                      )}
                      <div style={{background:method.bg,borderRadius:14,padding:"13px 16px",marginBottom:12,border:`1.5px solid ${method.color}22`}}>
                        <p style={{margin:"0 0 8px",fontSize:12,fontWeight:700,color:method.color}}>Cara Transfer {method.label}:</p>
                        {[`Buka aplikasi ${method.label}`,`Pilih menu Kirim / Transfer`,`Masukkan nomor: ${method.phone}`,"Masukkan nominal: "+rp(totalBayar),"Screenshot bukti transfer","Klik 'Sudah Bayar' di bawah"].map((s,i)=>(
                          <div key={i} style={{display:"flex",gap:8,marginBottom:5,alignItems:"flex-start"}}>
                            <div style={{width:18,height:18,borderRadius:"50%",background:method.color,display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0,marginTop:1}}>
                              <span style={{fontSize:9,fontWeight:800,color:"white"}}>{i+1}</span>
                            </div>
                            <span style={{fontSize:11,color:"#374151",lineHeight:1.5}}>{s}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* PULSA */}
                  {method.id==="pulsa" && (
                    <div style={{marginBottom:16}}>
                      <div style={{background:"#FFFBEB",borderRadius:14,padding:"14px 16px",marginBottom:10,border:"1.5px solid #FDE68A"}}>
                        <p style={{margin:"0 0 4px",fontSize:11,color:"#6B7280",fontWeight:600}}>Nomor Tujuan Admin</p>
                        <div style={{display:"flex",alignItems:"center",gap:10}}>
                          <p style={{margin:0,fontSize:20,fontWeight:900,color:"#B45309",fontFamily:"monospace",flex:1}}>{method.phone}</p>
                          <button onClick={()=>copyText(method.phone,"pulsa")}
                            style={{width:38,height:38,borderRadius:10,background:copied==="pulsa"?"#ECFDF5":"#FFFBEB",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                            <Ico n={copied==="pulsa"?"check":"copy"} size={16} color={copied==="pulsa"?"#059669":"#B45309"}/>
                          </button>
                        </div>
                      </div>
                      <div style={{background:"#FFFBEB",borderRadius:14,padding:"13px 16px",marginBottom:12,border:"1.5px solid #FDE68A"}}>
                        <p style={{margin:"0 0 6px",fontSize:12,fontWeight:700,color:"#B45309"}}>Petunjuk Transfer Pulsa:</p>
                        {["Pastikan pulsa kamu mencukupi","Kirim pulsa ke nomor di atas","Nominal: "+rp(final)+" (di luar biaya "+rp(method.fee)+" untuk total "+rp(totalBayar)+")", "Screenshot bukti pengiriman","Klik Sudah Bayar & upload bukti"].map((s,i)=>(
                          <div key={i} style={{display:"flex",gap:8,marginBottom:5,alignItems:"flex-start"}}>
                            <div style={{width:18,height:18,borderRadius:"50%",background:"#B45309",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0,marginTop:1}}>
                              <span style={{fontSize:9,fontWeight:800,color:"white"}}>{i+1}</span>
                            </div>
                            <span style={{fontSize:11,color:"#374151",lineHeight:1.5}}>{s}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                  )}

                  {/* Fee breakdown */}
                  {method.fee > 0 && (
                    <div style={{background:"#F9FAFB",borderRadius:12,padding:"12px 14px",marginBottom:14}}>
                      <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Top Up</span><span style={{fontWeight:600}}>{rp(final)}</span></div>
                      <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Biaya Admin</span><span style={{fontWeight:600,color:"#D97706"}}>{rp(method.fee)}</span></div>
                      <div style={{borderTop:"1px solid #E5E7EB",paddingTop:8,display:"flex",justifyContent:"space-between"}}><span style={{fontSize:14,fontWeight:700}}>Total Bayar</span><span style={{fontSize:16,fontWeight:900,color:"#7C3AED"}}>{rp(totalBayar)}</span></div>
                    </div>
                  )}

                  {/* Cek status button */}
                  <button onClick={simulateCheck} disabled={checking}
                    style={{width:"100%",padding:"12px",borderRadius:12,border:`2px solid ${method.color}`,background:"white",color:method.color,fontWeight:700,fontSize:13,cursor:checking?"not-allowed":"pointer",display:"flex",alignItems:"center",justifyContent:"center",gap:8,marginBottom:10,opacity:checking?0.7:1}}>
                    <Ico n={checking?"refresh":"search"} size={15} color={method.color} style={{animation:checking?"spin 1s linear infinite":""}}/>
                    {checking?"Mengecek pembayaran...":"Cek Status Pembayaran"}
                  </button>

                  <Btn onClick={()=>setStep(4)} color={method.color}>Sudah Bayar — Upload Bukti</Btn>
                  <button onClick={()=>setStep(2)} style={{width:"100%",background:"none",border:"none",color:"#6B7280",fontSize:12,cursor:"pointer",marginTop:8,padding:6}}>Ganti Metode Pembayaran</button>
                </>
              )}
            </>
          )}

          {/* STEP 4: PROOF UPLOAD */}
          {step===4 && (
            <>
              <div style={{background:"#F5F3FF",borderRadius:12,padding:"12px 14px",marginBottom:14,display:"flex",justifyContent:"space-between",alignItems:"center"}}>
                <span style={{fontSize:13,color:"#6B7280"}}>Metode: <strong style={{color:"#7C3AED"}}>{method?.label}</strong></span>
                <span style={{fontSize:13,fontWeight:800,color:"#7C3AED"}}>{rp(totalBayar)}</span>
              </div>

              <p style={{margin:"0 0 12px",fontSize:13,fontWeight:700,color:"#374151"}}>Upload Bukti Pembayaran:</p>

              <div onClick={()=>fileRef.current?.click()}
                style={{border:`2px dashed ${proofPreview?"#059669":"#DDD6FE"}`,borderRadius:18,padding:"24px 16px",textAlign:"center",cursor:"pointer",background:proofPreview?"#F0FDF4":"#FAFAFF",marginBottom:14,transition:"all 0.2s"}}>
                {proofPreview ? (
                  <div>
                    <img src={proofPreview} alt="Bukti" style={{maxWidth:"100%",maxHeight:200,borderRadius:12,objectFit:"contain",marginBottom:8}}/>
                    <p style={{margin:0,fontSize:12,color:"#059669",fontWeight:700}}>Bukti berhasil diupload</p>
                    <p style={{margin:"4px 0 0",fontSize:11,color:"#6B7280"}}>Klik untuk ganti foto</p>
                  </div>
                ) : (
                  <>
                    <div style={{width:56,height:56,borderRadius:18,background:"#EDE9FE",display:"flex",alignItems:"center",justifyContent:"center",margin:"0 auto 12px"}}>
                      <Ico n="plus" size={26} color="#7C3AED"/>
                    </div>
                    <p style={{margin:"0 0 4px",fontSize:14,fontWeight:700,color:"#374151"}}>Klik untuk upload foto bukti</p>
                    <p style={{margin:0,fontSize:11,color:"#9CA3AF"}}>JPG, PNG, maksimal 5MB</p>
                  </>
                )}
              </div>
              <input ref={fileRef} type="file" accept="image/*" onChange={handleProofFile} style={{display:"none"}}/>

              <div style={{background:"#EFF6FF",borderRadius:12,padding:"12px 14px",marginBottom:16,display:"flex",gap:8,alignItems:"flex-start"}}>
                <Ico n="info" size={14} color="#3B82F6" style={{flexShrink:0,marginTop:1}}/>
                <p style={{margin:0,fontSize:12,color:"#1E40AF",lineHeight:1.5}}>Upload bukti transfer/screenshot pembayaran. Admin akan memverifikasi dan mengkonfirmasi saldo kamu dalam 5 menit.</p>
              </div>

              <Btn onClick={submitWithProof} loading={submitting} color={method?.color||"#7C3AED"}>
                {submitting?"Mengajukan...":"Ajukan Top Up Sekarang"}
              </Btn>
              <button onClick={()=>setStep(3)} style={{width:"100%",background:"none",border:"none",color:"#6B7280",fontSize:12,cursor:"pointer",marginTop:8,padding:6}}>Kembali</button>
            </>
          )}

          {/* STEP 5: SUCCESS */}
          {step===5 && (
            <div style={{textAlign:"center",paddingTop:8}}>
              <div style={{width:80,height:80,borderRadius:"50%",background:"linear-gradient(135deg,#ECFDF5,#D1FAE5)",display:"flex",alignItems:"center",justifyContent:"center",margin:"0 auto 18px",boxShadow:"0 8px 32px rgba(5,150,105,0.2)"}}>
                <Ico n="ok" size={40} color="#059669"/>
              </div>
              <h3 style={{margin:"0 0 6px",fontSize:20,fontWeight:900,color:"#111827"}}>Top Up Diajukan!</h3>
              <p style={{margin:"0 0 4px",fontSize:14,color:"#6B7280"}}>Metode: <strong>{method?.label}</strong></p>
              <p style={{margin:"0 0 20px",fontSize:18,fontWeight:900,color:"#7C3AED"}}>{rp(final)}</p>
              <div style={{background:"#F9FAFB",borderRadius:16,padding:"16px",marginBottom:18,textAlign:"left"}}>
                <div style={{display:"flex",justifyContent:"space-between",marginBottom:8,fontSize:13}}><span style={{color:"#6B7280"}}>Status</span><span style={{fontWeight:700,color:"#D97706",background:"#FFFBEB",padding:"2px 10px",borderRadius:20}}>Menunggu Verifikasi</span></div>
                <div style={{display:"flex",justifyContent:"space-between",marginBottom:8,fontSize:13}}><span style={{color:"#6B7280"}}>Nominal</span><span style={{fontWeight:700}}>{rp(final)}</span></div>
                <div style={{display:"flex",justifyContent:"space-between",marginBottom:8,fontSize:13}}><span style={{color:"#6B7280"}}>Metode</span><span style={{fontWeight:700}}>{method?.label}</span></div>
                <div style={{display:"flex",justifyContent:"space-between",fontSize:13}}><span style={{color:"#6B7280"}}>Bukti</span><span style={{fontWeight:700,color:proofPreview?"#059669":"#9CA3AF"}}>{proofPreview?"Sudah diupload":"Tanpa bukti"}</span></div>
              </div>
              <div style={{display:"flex",alignItems:"center",gap:8,background:"#EFF6FF",borderRadius:12,padding:"12px 14px",marginBottom:18}}>
                <Ico n="bell" size={14} color="#3B82F6"/>
                <p style={{margin:0,fontSize:12,color:"#1E40AF",lineHeight:1.5}}>Saldo akan aktif setelah admin verifikasi. Biasanya proses dalam <strong>5-10 menit</strong>.</p>
              </div>
              <Btn onClick={()=>setModal(null)} color="#7C3AED">Selesai</Btn>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

// ─── TRANSFER MODAL ───────────────────────────────────────────────────────────
function TransferModal({setModal, users, currentUser, updateUser, addTx, notify, setUsers}) {
  const [search,    setSearch]    = useState("");
  const [recipient, setRecipient] = useState(null);
  const [amount,    setAmount]    = useState("");
  const [note,      setNote]      = useState("");
  const [step,      setStep]      = useState(1);
  const [processing,setProcessing]= useState(false);

  const others   = users.filter(u=>u.id!==currentUser.id&&u.role==="user");
  const filtered = others.filter(u=>u.name.toLowerCase().includes(search.toLowerCase())||u.email.toLowerCase().includes(search.toLowerCase())||u.referralCode.toLowerCase().includes(search.toLowerCase()));

  const doTransfer = () => {
    const amt = Number(amount);
    if (!amt||amt<1000) return notify("Minimal transfer Rp 1.000!","error");
    if (amt>currentUser.balance) return notify("Saldo tidak cukup!","error");
    setProcessing(true);
    setTimeout(() => {
      updateUser({...currentUser,balance:currentUser.balance-amt});
      setUsers(prev=>prev.map(u=>u.id===recipient.id?{...u,balance:u.balance+amt}:u));
      addTx({id:gid(),userId:currentUser.id,type:"transfer",amount:-amt,desc:`Transfer ke ${recipient.name}${note?" — "+note:""}`,date:today(),status:"success"});
      notify(`Transfer ke ${recipient.name} berhasil! ✓`);
      setModal(null);
    }, 1200);
  };

  return (
    <div style={{position:"fixed",inset:0,background:"rgba(0,0,0,0.6)",display:"flex",alignItems:"flex-end",justifyContent:"center",zIndex:300}}>
      <div style={{background:"white",borderRadius:"28px 28px 0 0",width:"100%",maxWidth:430,padding:24,maxHeight:"85vh",overflowY:"auto",animation:"slideUp 0.25s ease"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:20}}>
          <h3 style={{margin:0,fontSize:18,fontWeight:800}}>Transfer Saldo</h3>
          <button onClick={()=>setModal(null)} style={{width:32,height:32,borderRadius:"50%",background:"#F3F4F6",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="x" size={15}/>
          </button>
        </div>

        {step===1 && (
          <>
            <Field label="Cari penerima (nama / email / kode referral)" value={search} onChange={setSearch} placeholder="Ketik untuk mencari..."/>
            <div style={{marginTop:14,display:"flex",flexDirection:"column",gap:8}}>
              {filtered.slice(0,6).map(u => (
                <button key={u.id} onClick={()=>{setRecipient(u);setStep(2);}} style={{display:"flex",alignItems:"center",gap:12,padding:"13px 15px",background:"#F9FAFB",borderRadius:14,border:"1.5px solid #F3F4F6",cursor:"pointer",textAlign:"left"}}>
                  <div style={{width:40,height:40,borderRadius:"50%",background:"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center"}}>
                    <span style={{fontWeight:800,color:"#7C3AED"}}>{u.avatar}</span>
                  </div>
                  <div>
                    <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827"}}>{u.name}</p>
                    <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{u.email} • {u.referralCode}</p>
                  </div>
                </button>
              ))}
              {filtered.length===0 && <p style={{textAlign:"center",color:"#9CA3AF",fontSize:13,padding:"20px 0"}}>User tidak ditemukan</p>}
            </div>
          </>
        )}

        {step===2 && recipient && (
          <>
            <div style={{background:"#F5F3FF",borderRadius:16,padding:"14px 16px",marginBottom:18,display:"flex",alignItems:"center",gap:12}}>
              <div style={{width:44,height:44,borderRadius:"50%",background:"#7C3AED",display:"flex",alignItems:"center",justifyContent:"center"}}>
                <span style={{fontWeight:900,color:"white",fontSize:18}}>{recipient.avatar}</span>
              </div>
              <div>
                <p style={{margin:0,fontWeight:800,color:"#111827"}}>{recipient.name}</p>
                <p style={{margin:"2px 0 0",fontSize:12,color:"#6B7280"}}>{recipient.email}</p>
              </div>
            </div>
            <div style={{display:"flex",flexDirection:"column",gap:12,marginBottom:16}}>
              <Field label={`Nominal (Saldo kamu: ${rp(currentUser.balance)})`} type="number" value={amount} onChange={setAmount} placeholder="Minimal Rp 1.000"/>
              <Field label="Catatan (Opsional)" value={note} onChange={setNote} placeholder="Contoh: Bayar hutang makan"/>
            </div>
            {amount && Number(amount)>0 && (
              <div style={{background:"#F8F7FF",borderRadius:12,padding:"13px 16px",marginBottom:16}}>
                <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Transfer ke</span><span style={{fontWeight:700,color:"#111827"}}>{recipient.name}</span></div>
                <div style={{display:"flex",justifyContent:"space-between",marginBottom:5,fontSize:13,color:"#6B7280"}}><span>Jumlah</span><span style={{fontWeight:700,color:"#111827"}}>{rp(Number(amount))}</span></div>
                <div style={{display:"flex",justifyContent:"space-between",borderTop:"1px solid #E9D5FF",paddingTop:8}}>
                  <span style={{fontSize:13,color:"#6B7280"}}>Sisa saldo</span>
                  <span style={{fontSize:13,fontWeight:800,color:currentUser.balance-Number(amount)<0?"#DC2626":"#059669"}}>{rp(currentUser.balance-Number(amount))}</span>
                </div>
              </div>
            )}
            <div style={{display:"flex",gap:10}}>
              <button onClick={()=>setStep(1)} style={{flex:1,padding:14,borderRadius:14,border:"2px solid #E5E7EB",background:"white",cursor:"pointer",fontWeight:700,fontSize:14}}>Kembali</button>
              <button onClick={doTransfer} disabled={processing} style={{flex:2,padding:14,borderRadius:14,border:"none",background:"linear-gradient(135deg,#7C3AED,#5B21B6)",color:"white",cursor:"pointer",fontWeight:800,fontSize:14,opacity:processing?0.7:1}}>
                {processing?"Memproses...":"Transfer Sekarang"}
              </button>
            </div>
          </>
        )}
      </div>
    </div>
  );
}

// ─── ADMIN PANEL ──────────────────────────────────────────────────────────────
function AdminPanel({users,setUsers,products,setProducts,txs,setTxs,emailPool,setEmailPool,topupReqs,setTopupReqs,jastebOrders,setJastebOrders,chats,setChats,currentUser,adminTab,setAdminTab,setPage,notify,notif,updateUser,addTx}) {
  const tabs = [
    {id:"dashboard",n:"bar",   label:"Dashboard"},
    {id:"topup",    n:"qr",    label:"Top Up"},
    {id:"pesanan",  n:"timer", label:"Pesanan"},
    {id:"products", n:"pkg",   label:"Produk"},
    {id:"emailpool",n:"mail",  label:"Email Pool"},
    {id:"users",    n:"uc",    label:"Users"},
    {id:"chats",    n:"msq",   label:"Chat"},
  ];
  const pending      = topupReqs.filter(r=>r.status==="pending").length;
  const activeOrders = jastebOrders.filter(o=>o.status==="active").length;

  return (
    <div style={{maxWidth:430,margin:"0 auto",minHeight:"100vh",background:"#F0F0F5",paddingBottom:80}}>
      {notif && <Notif {...notif}/>}
      <div style={{background:"linear-gradient(160deg,#111827,#1F2937,#374151)",padding:"48px 16px 20px"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center"}}>
          <div>
            <p style={{color:"rgba(255,255,255,0.5)",fontSize:11,margin:0,letterSpacing:"1px"}}>ADMIN PANEL</p>
            <h2 style={{color:"white",fontSize:18,fontWeight:800,margin:"2px 0 0"}}>DikzShop Admin</h2>
          </div>
          <button onClick={()=>setPage("home")} style={{background:"rgba(255,255,255,0.1)",border:"1px solid rgba(255,255,255,0.2)",borderRadius:10,padding:"8px 14px",color:"white",cursor:"pointer",fontSize:12,fontWeight:600,display:"flex",alignItems:"center",gap:5}}>
            <Ico n="cl" size={13} color="white"/>Kembali
          </button>
        </div>
      </div>

      {adminTab==="dashboard" && <AdminDashboard users={users} products={products} txs={txs} topupReqs={topupReqs} jastebOrders={jastebOrders}/>}
      {adminTab==="topup"     && <AdminTopup topupReqs={topupReqs} setTopupReqs={setTopupReqs} users={users} updateUser={updateUser} addTx={addTx} notify={notify}/>}
      {adminTab==="pesanan"   && <AdminJastebOrders jastebOrders={jastebOrders} setJastebOrders={setJastebOrders} notify={notify}/>}
      {adminTab==="products"  && <AdminProducts products={products} setProducts={setProducts} notify={notify}/>}
      {adminTab==="emailpool" && <AdminEmailPool emailPool={emailPool} setEmailPool={setEmailPool} notify={notify}/>}
      {adminTab==="users"     && <AdminUsers users={users}/>}
      {adminTab==="chats"     && <AdminChats chats={chats} setChats={setChats}/>}

      <div style={{position:"fixed",bottom:0,left:"50%",transform:"translateX(-50%)",width:"100%",maxWidth:430,background:"#111827",display:"flex",zIndex:100,overflowX:"auto"}}>
        {tabs.map(item => {
          const active = adminTab===item.id;
          const badge  = (item.id==="topup" && pending>0) || (item.id==="pesanan" && activeOrders>0);
          const bCount = item.id==="topup" ? pending : activeOrders;
          return (
            <button key={item.id} onClick={()=>setAdminTab(item.id)} style={{flex:1,display:"flex",flexDirection:"column",alignItems:"center",padding:"11px 4px 8px",border:"none",background:"transparent",cursor:"pointer",gap:3,minWidth:52,position:"relative"}}>
              <Ico n={item.n} size={18} color={active?"#F59E0B":"#6B7280"} sw={active?2.5:1.8}/>
              <span style={{fontSize:9,fontWeight:active?700:400,color:active?"#F59E0B":"#6B7280",textAlign:"center"}}>{item.label}</span>
              {badge && bCount>0 && <div style={{position:"absolute",top:7,right:"calc(50% - 16px)",width:15,height:15,borderRadius:"50%",background:"#DC2626",display:"flex",alignItems:"center",justifyContent:"center"}}><span style={{fontSize:8,fontWeight:700,color:"white"}}>{bCount}</span></div>}
            </button>
          );
        })}
      </div>
    </div>
  );
}

// ─── ADMIN DASHBOARD ──────────────────────────────────────────────────────────
function AdminDashboard({users,products,txs,topupReqs,jastebOrders}) {
  const revenue      = txs.filter(t=>t.type==="purchase").reduce((s,t)=>s+Math.abs(t.amount),0);
  const totalP       = Object.values(products).flat().length;
  const pending      = topupReqs.filter(r=>r.status==="pending").length;
  const activeOrders = jastebOrders.filter(o=>o.status==="active").length;

  const stats = [
    {label:"Total Users",    val:users.filter(u=>u.role==="user").length, n:"users",  color:"#7C3AED"},
    {label:"Total Produk",   val:totalP,                                  n:"pkg",    color:"#059669"},
    {label:"Revenue",        val:rp(revenue),                             n:"trend",  color:"#DC2626"},
    {label:"Jasteb Aktif",   val:activeOrders,                            n:"timer",  color:"#D97706"},
  ];

  return (
    <div style={{padding:16}}>
      <div style={{display:"grid",gridTemplateColumns:"1fr 1fr",gap:12,marginBottom:18}}>
        {stats.map((s,i) => (
          <div key={i} style={{background:"white",borderRadius:16,padding:16,boxShadow:"0 2px 8px rgba(0,0,0,0.05)"}}>
            <div style={{width:38,height:38,borderRadius:12,background:s.color+"15",display:"flex",alignItems:"center",justifyContent:"center",marginBottom:10}}>
              <Ico n={s.n} size={18} color={s.color}/>
            </div>
            <p style={{margin:0,fontSize:20,fontWeight:900,color:"#111827"}}>{s.val}</p>
            <p style={{margin:"3px 0 0",fontSize:11,color:"#6B7280"}}>{s.label}</p>
          </div>
        ))}
      </div>

      {pending>0 && (
        <div style={{background:"#FFFBEB",border:"1.5px solid #FDE68A",borderRadius:16,padding:"14px 16px",marginBottom:16,display:"flex",alignItems:"center",gap:12}}>
          <div style={{width:38,height:38,borderRadius:12,background:"#FEF3C7",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="clock" size={18} color="#D97706"/>
          </div>
          <div style={{flex:1}}>
            <p style={{margin:0,fontWeight:700,fontSize:13,color:"#92400E"}}>{pending} Top Up Menunggu Konfirmasi</p>
            <p style={{margin:"2px 0 0",fontSize:11,color:"#B45309"}}>Total: {rp(topupReqs.filter(r=>r.status==="pending").reduce((s,r)=>s+r.amount,0))}</p>
          </div>
        </div>
      )}

      <h3 style={{margin:"0 0 12px",fontSize:15,fontWeight:800,color:"#111827"}}>Transaksi Terbaru</h3>
      {txs.length===0
        ? <div style={{textAlign:"center",padding:"32px 0",color:"#9CA3AF",fontSize:13}}>Belum ada transaksi</div>
        : txs.slice(0,5).map(tx => {
            const u = users.find(u=>u.id===tx.userId);
            return <TxItem key={tx.id} tx={tx} sub={u?.name}/>;
          })
      }
    </div>
  );
}

// ─── ADMIN TOPUP ──────────────────────────────────────────────────────────────
function AdminTopup({topupReqs,setTopupReqs,users,updateUser,addTx,notify}) {
  const [tab, setTab] = useState("pending");
  const pending = topupReqs.filter(r=>r.status==="pending");
  const done    = topupReqs.filter(r=>r.status!=="pending");

  const confirm = req => {
    const user = users.find(u=>u.id===req.userId);
    if (!user) return;
    updateUser({...user, balance:user.balance+req.amount});
    addTx({id:gid(),userId:req.userId,type:"topup",amount:req.amount,desc:"Top Up via QRIS (dikonfirmasi admin)",date:today(),status:"success"});
    setTopupReqs(prev=>prev.map(r=>r.id===req.id?{...r,status:"confirmed"}:r));
    notify(`Top up ${rp(req.amount)} untuk ${req.userName} dikonfirmasi! ✓`);
  };

  const reject = req => {
    setTopupReqs(prev=>prev.map(r=>r.id===req.id?{...r,status:"rejected"}:r));
    notify(`Top up ${req.userName} ditolak.`,"error");
  };

  return (
    <div>
      <div style={{display:"flex",background:"white",borderBottom:"1px solid #F3F4F6"}}>
        {[{id:"pending",label:`Menunggu (${pending.length})`},{id:"done",label:`Riwayat (${done.length})`}].map(t => (
          <button key={t.id} onClick={()=>setTab(t.id)} style={{flex:1,padding:14,border:"none",background:"none",cursor:"pointer",fontWeight:tab===t.id?700:400,color:tab===t.id?"#F59E0B":"#6B7280",borderBottom:tab===t.id?"3px solid #F59E0B":"3px solid transparent",fontSize:13}}>
            {t.label}
          </button>
        ))}
      </div>
      <div style={{padding:16}}>
        {tab==="pending" && (
          pending.length===0
          ? <div style={{textAlign:"center",padding:"48px 0",color:"#9CA3AF"}}>
              <Ico n="ok" size={40} color="#D1D5DB"/>
              <p style={{margin:"12px 0 0",fontSize:14,fontWeight:600}}>Tidak ada top up pending</p>
            </div>
          : pending.map(r => (
            <div key={r.id} style={{background:"white",borderRadius:18,padding:16,marginBottom:12,boxShadow:"0 2px 12px rgba(0,0,0,0.06)",border:"1.5px solid #FDE68A"}}>
              <div style={{display:"flex",alignItems:"center",gap:12,marginBottom:12}}>
                <div style={{width:44,height:44,borderRadius:12,background:"#FEF3C7",display:"flex",alignItems:"center",justifyContent:"center"}}>
                  <span style={{fontSize:17,fontWeight:800,color:"#D97706"}}>{r.userName[0]}</span>
                </div>
                <div style={{flex:1}}>
                  <p style={{margin:0,fontWeight:800,fontSize:14,color:"#111827"}}>{r.userName}</p>
                  <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{r.date}</p>
                </div>
                <p style={{margin:0,fontSize:18,fontWeight:900,color:"#059669"}}>+{rp(r.amount)}</p>
              </div>
              <div style={{display:"flex",gap:8}}>
                <button onClick={()=>reject(r)} style={{flex:1,padding:10,borderRadius:12,border:"1.5px solid #FCA5A5",background:"white",color:"#DC2626",fontWeight:700,cursor:"pointer",fontSize:13}}>Tolak</button>
                <button onClick={()=>confirm(r)} style={{flex:2,padding:10,borderRadius:12,border:"none",background:"linear-gradient(135deg,#059669,#065F46)",color:"white",fontWeight:700,cursor:"pointer",fontSize:13}}>✓ Konfirmasi Top Up</button>
              </div>
            </div>
          ))
        )}

        {tab==="done" && (
          done.length===0
          ? <div style={{textAlign:"center",padding:"48px 0",color:"#9CA3AF",fontSize:13}}>Belum ada riwayat</div>
          : done.map(r => (
            <div key={r.id} style={{background:"white",borderRadius:14,padding:"13px 15px",marginBottom:8,display:"flex",alignItems:"center",gap:12,boxShadow:"0 2px 6px rgba(0,0,0,0.04)"}}>
              <div style={{width:38,height:38,borderRadius:12,background:r.status==="confirmed"?"#ECFDF5":"#FEF2F2",display:"flex",alignItems:"center",justifyContent:"center"}}>
                <Ico n={r.status==="confirmed"?"ok":"x"} size={18} color={r.status==="confirmed"?"#059669":"#DC2626"}/>
              </div>
              <div style={{flex:1}}>
                <p style={{margin:0,fontWeight:700,fontSize:13,color:"#111827"}}>{r.userName} — {rp(r.amount)}</p>
                <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{r.date}</p>
              </div>
              <span style={{fontSize:11,fontWeight:700,color:r.status==="confirmed"?"#059669":"#DC2626"}}>{r.status==="confirmed"?"Dikonfirmasi":"Ditolak"}</span>
            </div>
          ))
        )}
      </div>
    </div>
  );
}

// ─── ADMIN JASTEB ORDERS (BARU) ───────────────────────────────────────────────
function AdminJastebOrders({jastebOrders, setJastebOrders, notify}) {
  const [tab, setTab] = useState("active");
  const [tick, setTick] = useState(0);

  useEffect(() => {
    const t = setInterval(() => setTick(p=>p+1), 30000);
    return () => clearInterval(t);
  }, []);

  const active  = jastebOrders.filter(o=>o.status==="active");
  const expired = jastebOrders.filter(o=>o.status==="expired");

  const forceExpire = (order) => {
    fetch(`${PANEL_DEL_URL}?mail=${encodeURIComponent(order.buyerEmail)}`, {method:"GET", mode:"no-cors"}).catch(()=>{});
    setJastebOrders(prev=>prev.map(o=>o.id===order.id?{...o,status:"expired"}:o));
    notify(`Order ${order.productName} — ${order.buyerEmail} dihapus.`);
  };

  const [copiedId, setCopiedId] = useState(null);
  const copyEmail = (email, id) => {
    navigator.clipboard?.writeText(email).catch(()=>{});
    setCopiedId(id); setTimeout(()=>setCopiedId(null), 2000);
    notify("Email disalin!");
  };

  return (
    <div>
      {/* Summary */}
      <div style={{background:"white",padding:"14px 16px",borderBottom:"1px solid #F3F4F6",display:"flex",gap:12}}>
        <div style={{flex:1,background:"#F0FDF4",borderRadius:12,padding:"12px 14px",textAlign:"center"}}>
          <p style={{margin:0,fontSize:22,fontWeight:900,color:"#059669"}}>{active.length}</p>
          <p style={{margin:"3px 0 0",fontSize:11,color:"#6B7280"}}>Aktif Sekarang</p>
        </div>
        <div style={{flex:1,background:"#F9FAFB",borderRadius:12,padding:"12px 14px",textAlign:"center"}}>
          <p style={{margin:0,fontSize:22,fontWeight:900,color:"#6B7280"}}>{expired.length}</p>
          <p style={{margin:"3px 0 0",fontSize:11,color:"#9CA3AF"}}>Sudah Berakhir</p>
        </div>
      </div>

      {/* Tabs */}
      <div style={{display:"flex",background:"white",borderBottom:"1px solid #F3F4F6"}}>
        {[{id:"active",label:`Aktif (${active.length})`},{id:"expired",label:`Berakhir (${expired.length})`}].map(t => (
          <button key={t.id} onClick={()=>setTab(t.id)} style={{flex:1,padding:13,border:"none",background:"none",cursor:"pointer",fontWeight:tab===t.id?700:400,color:tab===t.id?"#F59E0B":"#6B7280",borderBottom:tab===t.id?"3px solid #F59E0B":"3px solid transparent",fontSize:13}}>
            {t.label}
          </button>
        ))}
      </div>

      <div style={{padding:"12px 16px"}}>
        {tab==="active" && (
          active.length===0
          ? <div style={{textAlign:"center",padding:"48px 20px",color:"#9CA3AF"}}>
              <Ico n="timer" size={40} color="#D1D5DB"/>
              <p style={{margin:"12px 0 0",fontSize:14,fontWeight:600}}>Tidak ada pesanan aktif</p>
            </div>
          : active.map(o => {
              const remaining = o.expiresAt - Date.now();
              const progress  = Math.max(0, remaining / (o.durasi.minutes * 60000));
              return (
                <div key={o.id} style={{background:"white",borderRadius:18,padding:16,marginBottom:12,boxShadow:"0 2px 12px rgba(0,0,0,0.06)",border:"1.5px solid #EDE9FE"}}>
                  <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start",marginBottom:10}}>
                    <div style={{flex:1}}>
                      <p style={{margin:0,fontWeight:800,fontSize:14,color:"#111827"}}>{o.productName}</p>
                      <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{o.userName} • {o.durasi.label} • {rp(o.price)}</p>
                    </div>
                    <div style={{background:"#ECFDF5",borderRadius:8,padding:"4px 10px",flexShrink:0,marginLeft:8}}>
                      <span style={{fontSize:11,fontWeight:700,color:"#059669"}}>AKTIF</span>
                    </div>
                  </div>

                  {/* Timer bar */}
                  <div style={{background:"#F3F4F6",borderRadius:6,height:6,marginBottom:8,overflow:"hidden"}}>
                    <div style={{height:"100%",width:`${progress*100}%`,background:progress>0.3?"#059669":progress>0.1?"#D97706":"#DC2626",borderRadius:6,transition:"width 0.5s"}}/>
                  </div>
                  <p style={{margin:"0 0 10px",fontSize:12,color:remaining<=0?"#DC2626":"#D97706",fontWeight:600}}>
                    Sisa: {msToHuman(remaining)} | Berakhir: {expiryStr(o.expiresAt)}
                  </p>

                  {/* Email pembeli */}
                  <div style={{background:"#F5F3FF",borderRadius:12,padding:"10px 12px",marginBottom:10,display:"flex",alignItems:"center",gap:8}}>
                    <Ico n="mail" size={14} color="#7C3AED"/>
                    <span style={{flex:1,fontSize:12,fontWeight:700,color:"#7C3AED",wordBreak:"break-all"}}>{o.buyerEmail}</span>
                    <button onClick={()=>copyEmail(o.buyerEmail, o.id)} style={{width:28,height:28,borderRadius:8,background:copiedId===o.id?"#059669":"#7C3AED",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                      <Ico n={copiedId===o.id?"check":"copy"} size={12} color="white"/>
                    </button>
                  </div>

                  <div style={{display:"flex",gap:8}}>
                    <div style={{flex:1,fontSize:11,color:"#9CA3AF",padding:"8px 12px",background:"#F9FAFB",borderRadius:10,fontFamily:"monospace"}}>ID: {o.id.toUpperCase()}</div>
                    <button onClick={()=>forceExpire(o)} style={{padding:"8px 14px",borderRadius:10,border:"1.5px solid #FCA5A5",background:"#FEF2F2",color:"#DC2626",fontWeight:700,cursor:"pointer",fontSize:12,flexShrink:0}}>
                      Hapus & Akhiri
                    </button>
                  </div>
                </div>
              );
            })
        )}

        {tab==="expired" && (
          expired.length===0
          ? <div style={{textAlign:"center",padding:"48px 20px",color:"#9CA3AF"}}>
              <p style={{margin:0,fontSize:14,fontWeight:600}}>Belum ada pesanan berakhir</p>
            </div>
          : expired.map(o => (
            <div key={o.id} style={{background:"white",borderRadius:14,padding:"13px 15px",marginBottom:8,boxShadow:"0 2px 6px rgba(0,0,0,0.04)",opacity:0.75}}>
              <div style={{display:"flex",alignItems:"center",gap:10}}>
                <div style={{width:36,height:36,borderRadius:10,background:"#F3F4F6",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                  <Ico n="timer" size={16} color="#9CA3AF"/>
                </div>
                <div style={{flex:1,minWidth:0}}>
                  <p style={{margin:0,fontWeight:700,fontSize:13,color:"#374151"}}>{o.productName}</p>
                  <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{o.buyerEmail} • {o.durasi.label}</p>
                </div>
                <span style={{fontSize:11,fontWeight:700,color:"#9CA3AF",flexShrink:0}}>BERAKHIR</span>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
}

// ─── ADMIN EMAIL POOL (referensi admin) ───────────────────────────────────────
function AdminEmailPool({emailPool, setEmailPool, notify}) {
  const [newEmail, setNewEmail] = useState("");
  const [newNote,  setNewNote]  = useState("");
  const [adding,   setAdding]   = useState(false);
  const [addError, setAddError] = useState("");

  const addEmail = () => {
    const email = newEmail.trim().toLowerCase();
    if (!email) { setAddError("Email tidak boleh kosong!"); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setAddError("Format email tidak valid!"); return; }
    if (emailPool.find(e=>e.email===email)) { setAddError("Email sudah ada di pool!"); return; }
    setAddError("");
    setEmailPool(prev => [...prev, {id:gid(), email, note:newNote.trim()||"—"}]);
    setNewEmail(""); setNewNote(""); setAdding(false);
    notify("Email berhasil ditambahkan ke pool! ✓");
  };

  const removeEmail = id => {
    setEmailPool(prev=>prev.filter(e=>e.id!==id));
    notify("Email dihapus dari pool.");
  };

  const [copiedId, setCopiedId] = useState(null);
  const copyEmail = (email, id) => {
    navigator.clipboard?.writeText(email).catch(()=>{});
    setCopiedId(id); setTimeout(()=>setCopiedId(null), 2000);
  };

  return (
    <div>
      <div style={{background:"white",padding:"14px 16px",borderBottom:"1px solid #F3F4F6"}}>
        <div style={{display:"flex",justifyContent:"space-between",alignItems:"center",marginBottom:adding?12:0}}>
          <div>
            <p style={{margin:0,fontSize:13,fontWeight:700,color:"#111827"}}>Akun Email Jasteb</p>
            <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>Referensi akun milik admin ({emailPool.length} akun)</p>
          </div>
          <button onClick={()=>{setAdding(p=>!p);setNewEmail("");setNewNote("");setAddError("");}}
            style={{background:adding?"#FEF2F2":"linear-gradient(135deg,#F59E0B,#D97706)",border:"none",borderRadius:12,padding:"9px 14px",color:adding?"#DC2626":"white",fontWeight:700,fontSize:12,cursor:"pointer",display:"flex",alignItems:"center",gap:5,flexShrink:0}}>
            <Ico n={adding?"x":"plus"} size={13} color={adding?"#DC2626":"white"}/>{adding?"Batal":"+ Tambah"}
          </button>
        </div>

        {adding && (
          <div style={{animation:"fadeIn 0.15s ease"}}>
            <div style={{display:"flex",flexDirection:"column",gap:8,marginBottom:10}}>
              <input value={newEmail} onChange={e=>{setNewEmail(e.target.value);setAddError("");}} onKeyDown={e=>{if(e.key==="Enter")addEmail();if(e.key==="Escape"){setAdding(false);setAddError("");}}}
                placeholder="email@domain.com" type="email" autoFocus
                style={{width:"100%",padding:"11px 14px",borderRadius:12,border:`1.5px solid ${addError?"#FCA5A5":"#E5E7EB"}`,fontSize:13,outline:"none",boxSizing:"border-box"}}/>
              <input value={newNote} onChange={e=>setNewNote(e.target.value)} placeholder="Catatan (opsional, mis: Akun Shopee TH)"
                style={{width:"100%",padding:"11px 14px",borderRadius:12,border:"1.5px solid #E5E7EB",fontSize:13,outline:"none",boxSizing:"border-box"}}/>
            </div>
            {addError && <p style={{margin:"0 0 8px",fontSize:11,color:"#DC2626",fontWeight:600}}>{addError}</p>}
            <button onClick={addEmail} style={{width:"100%",padding:"11px",borderRadius:12,border:"none",background:"linear-gradient(135deg,#059669,#065F46)",color:"white",fontWeight:700,cursor:"pointer",fontSize:13}}>
              + Tambah Email
            </button>
          </div>
        )}
      </div>

      <div style={{padding:"12px 16px"}}>
        {emailPool.length===0
          ? <div style={{textAlign:"center",padding:"48px 20px",color:"#9CA3AF"}}>
              <Ico n="mail" size={40} color="#D1D5DB"/>
              <p style={{margin:"12px 0 0",fontSize:14,fontWeight:600}}>Belum ada email di pool</p>
              <p style={{margin:"4px 0 0",fontSize:12}}>Klik "+ Tambah" untuk menambah akun</p>
            </div>
          : emailPool.map(e => (
            <div key={e.id} style={{background:"white",borderRadius:14,padding:"13px 15px",marginBottom:8,boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
              <div style={{display:"flex",alignItems:"center",gap:10}}>
                <div style={{width:38,height:38,borderRadius:10,background:"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
                  <Ico n="mail" size={17} color="#7C3AED"/>
                </div>
                <div style={{flex:1,minWidth:0}}>
                  <p style={{margin:0,fontSize:13,fontWeight:700,color:"#111827",wordBreak:"break-all"}}>{e.email}</p>
                  <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF"}}>{e.note}</p>
                </div>
                <div style={{display:"flex",gap:6,flexShrink:0}}>
                  <button onClick={()=>copyEmail(e.email,e.id)} style={{width:30,height:30,borderRadius:8,background:copiedId===e.id?"#ECFDF5":"#F5F3FF",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
                    <Ico n={copiedId===e.id?"check":"copy"} size={13} color={copiedId===e.id?"#059669":"#7C3AED"}/>
                  </button>
                  <button onClick={()=>removeEmail(e.id)} style={{width:30,height:30,borderRadius:8,background:"#FEF2F2",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
                    <Ico n="trash" size={13} color="#DC2626"/>
                  </button>
                </div>
              </div>
            </div>
          ))
        }
      </div>
    </div>
  );
}

// ─── ADMIN PRODUCTS ───────────────────────────────────────────────────────────
function AdminProducts({products, setProducts, notify}) {
  const [tab,     setTab]     = useState("script");
  const [editing, setEditing] = useState(null);
  const [adding,  setAdding]  = useState(false);
  const [form,    setForm]    = useState({});
  const catTabs = ["script","panel","jasteb","unchek"];
  const setF = (k,v) => setForm(p=>({...p,[k]:v}));

  const startEdit = p => { setEditing(p.id); setAdding(false); setForm({...p, price:p.pricePerHour||p.price||""}); };
  const startAdd  = () => { setAdding(true); setEditing(null); setForm({name:"",price:"",desc:"",stock:100}); };
  const cancel    = () => { setEditing(null); setAdding(false); setForm({}); };

  const save = () => {
    if (!form.name||!form.price) return notify("Nama & harga wajib diisi!","error");
    const base       = {name:form.name, desc:form.desc||"", stock:Number(form.stock||0), rating:5.0};
    const priceField = tab==="jasteb" ? {pricePerHour:Number(form.price)} : {price:Number(form.price)};
    if (editing) {
      setProducts(prev=>({...prev,[tab]:prev[tab].map(p=>p.id===editing?{...p,...base,...priceField}:p)}));
      notify("Produk diperbarui! ✓");
    } else {
      setProducts(prev=>({...prev,[tab]:[...prev[tab],{id:gid(),...base,...priceField,sales:0}]}));
      notify("Produk baru ditambahkan! ✓");
    }
    cancel();
  };

  const del = id => { setProducts(prev=>({...prev,[tab]:prev[tab].filter(p=>p.id!==id)})); notify("Produk dihapus."); };

  return (
    <div>
      <div style={{background:"white",display:"flex",overflowX:"auto",borderBottom:"1px solid #F3F4F6"}}>
        {catTabs.map(t => (
          <button key={t} onClick={()=>{setTab(t);cancel();}} style={{padding:"13px 18px",border:"none",background:"none",cursor:"pointer",borderBottom:tab===t?"3px solid #F59E0B":"3px solid transparent",color:tab===t?"#F59E0B":"#6B7280",fontWeight:tab===t?700:400,fontSize:12,whiteSpace:"nowrap"}}>
            {t.charAt(0).toUpperCase()+t.slice(1)}
          </button>
        ))}
      </div>

      <div style={{padding:"14px 16px"}}>
        <button onClick={startAdd} style={{width:"100%",background:"linear-gradient(135deg,#F59E0B,#D97706)",border:"none",borderRadius:14,padding:13,color:"white",fontWeight:700,fontSize:14,cursor:"pointer",marginBottom:14,display:"flex",alignItems:"center",justifyContent:"center",gap:8}}>
          <Ico n="plus" size={17} color="white"/>Tambah Produk Baru
        </button>

        {(adding||editing!==null) && (
          <div style={{background:"white",borderRadius:18,padding:16,marginBottom:14,boxShadow:"0 2px 16px rgba(0,0,0,0.1)"}}>
            <h4 style={{margin:"0 0 14px",fontSize:15,fontWeight:800}}>{adding?"Tambah Produk Baru":"Edit Produk"}</h4>
            <div style={{display:"flex",flexDirection:"column",gap:10}}>
              <Field label="Nama Produk" value={form.name||""} onChange={v=>setF("name",v)} placeholder="Nama produk"/>
              <Field label={tab==="jasteb"?"Harga per Jam (Rp)":"Harga (Rp)"} type="number" value={form.price||""} onChange={v=>setF("price",v)} placeholder={tab==="jasteb"?"5000":"85000"}/>
              <Field label="Deskripsi" value={form.desc||""} onChange={v=>setF("desc",v)} placeholder="Deskripsi produk"/>
              <Field label="Stok" type="number" value={form.stock||""} onChange={v=>setF("stock",v)} placeholder="100"/>
            </div>
            <div style={{display:"flex",gap:10,marginTop:14}}>
              <button onClick={cancel} style={{flex:1,padding:12,borderRadius:12,border:"2px solid #E5E7EB",background:"white",cursor:"pointer",fontWeight:700}}>Batal</button>
              <button onClick={save}   style={{flex:2,padding:12,borderRadius:12,border:"none",background:"linear-gradient(135deg,#F59E0B,#D97706)",color:"white",cursor:"pointer",fontWeight:700}}>Simpan</button>
            </div>
          </div>
        )}

        {(products[tab]||[]).map(p => (
          <div key={p.id} style={{background:"white",borderRadius:14,padding:"13px 15px",marginBottom:10,boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
            <div style={{display:"flex",justifyContent:"space-between",alignItems:"flex-start"}}>
              <div style={{flex:1}}>
                <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827"}}>{p.name}</p>
                <p style={{margin:"4px 0 0",fontSize:14,fontWeight:800,color:"#F59E0B"}}>
                  {tab==="jasteb"?`${rp(p.pricePerHour)}/jam`:rp(p.price)}
                </p>
                <p style={{margin:"3px 0 0",fontSize:11,color:"#9CA3AF"}}>Stok: {p.stock} • Terjual: {p.sales}</p>
              </div>
              <div style={{display:"flex",gap:6}}>
                <button onClick={()=>startEdit(p)} style={{width:32,height:32,borderRadius:8,background:"#EFF6FF",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
                  <Ico n="edit" size={13} color="#3B82F6"/>
                </button>
                <button onClick={()=>del(p.id)} style={{width:32,height:32,borderRadius:8,background:"#FEF2F2",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
                  <Ico n="trash" size={13} color="#DC2626"/>
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

// ─── ADMIN USERS ──────────────────────────────────────────────────────────────
function AdminUsers({users}) {
  return (
    <div style={{padding:16}}>
      <h3 style={{margin:"0 0 14px",fontSize:15,fontWeight:800,color:"#111827"}}>Semua Pengguna ({users.length})</h3>
      {users.map(u => (
        <div key={u.id} style={{background:"white",borderRadius:16,padding:"14px 15px",marginBottom:10,display:"flex",alignItems:"center",gap:12,boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
          <div style={{width:46,height:46,borderRadius:"50%",background:u.role==="admin"?"#FEF3C7":"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
            <span style={{fontSize:18,fontWeight:800,color:u.role==="admin"?"#D97706":"#7C3AED"}}>{u.avatar}</span>
          </div>
          <div style={{flex:1,minWidth:0}}>
            <div style={{display:"flex",alignItems:"center",gap:8}}>
              <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827"}}>{u.name}</p>
              {u.role==="admin" && <div style={{background:"#F59E0B",borderRadius:5,padding:"1px 7px"}}><span style={{fontSize:10,fontWeight:800,color:"white"}}>ADMIN</span></div>}
            </div>
            <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{u.email}</p>
            <p style={{margin:"2px 0 0",fontSize:12,fontWeight:700,color:"#059669"}}>{rp(u.balance)}</p>
          </div>
          <div style={{background:"#F5F3FF",borderRadius:8,padding:"3px 8px",flexShrink:0}}>
            <span style={{fontSize:10,fontWeight:700,color:"#7C3AED"}}>{u.referralCode}</span>
          </div>
        </div>
      ))}
    </div>
  );
}

// ─── ADMIN CHATS ──────────────────────────────────────────────────────────────
function AdminChats({chats, setChats}) {
  const [selected,  setSelected]  = useState(null);
  const [reply,     setReply]     = useState("");
  const [aiLoading, setAiLoading] = useState(false);
  const chatRef = useRef(null);

  useEffect(() => { if(chatRef.current) chatRef.current.scrollTop=chatRef.current.scrollHeight; }, [selected,chats]);

  const selectedChat = chats.find(c=>c.id===selected);

  const sendReply = txt => {
    if (!txt?.trim()||!selected) return;
    const msg = {from:"admin",text:txt.trim(),time:now()};
    setChats(prev=>prev.map(c=>c.id===selected?{...c,messages:[...c.messages,msg],lastMsg:txt.trim()}:c));
    setReply("");
  };

  const replyWithAI = async () => {
    if (!selected||!selectedChat) return;
    setAiLoading(true);
    try {
      const context = selectedChat.messages.slice(-8).map(m=>({role:m.from==="user"?"user":"assistant",content:m.text}));
      const res = await fetch("https://api.anthropic.com/v1/messages",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({model:"claude-sonnet-4-20250514",max_tokens:500,system:"Kamu adalah admin customer service DikzShop yang profesional dan ramah. Bantu user dengan pertanyaan mereka. Jawab singkat dan helpful dalam bahasa Indonesia.",messages:context})});
      const d = await res.json();
      const text = d.content?.[0]?.text || "Maaf ada gangguan.";
      setChats(prev=>prev.map(c=>c.id===selected?{...c,messages:[...c.messages,{from:"admin",text:"[AI] "+text,time:now()}],lastMsg:text.slice(0,50)}:c));
    } catch {
      setChats(prev=>prev.map(c=>c.id===selected?{...c,messages:[...c.messages,{from:"admin",text:"Maaf ada gangguan sistem.",time:now()}]}:c));
    }
    setAiLoading(false);
  };

  if (selected && selectedChat) return (
    <div style={{display:"flex",flexDirection:"column",height:"calc(100vh - 162px)"}}>
      <div style={{background:"white",padding:"12px 16px",display:"flex",alignItems:"center",gap:10,borderBottom:"1px solid #F3F4F6"}}>
        <button onClick={()=>setSelected(null)} style={{background:"none",border:"none",cursor:"pointer",display:"flex"}}><Ico n="cl" size={20} color="#374151"/></button>
        <div style={{width:36,height:36,borderRadius:"50%",background:"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center"}}>
          <span style={{fontWeight:800,color:"#7C3AED"}}>{selectedChat.userName[0]}</span>
        </div>
        <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827",flex:1}}>{selectedChat.userName}</p>
      </div>

      <div ref={chatRef} style={{flex:1,overflowY:"auto",padding:"14px 16px",background:"#F0F0F5",display:"flex",flexDirection:"column",gap:10}}>
        {selectedChat.messages.map((msg,i) => (
          <div key={i} style={{display:"flex",justifyContent:msg.from==="user"?"flex-start":"flex-end"}}>
            <div style={{background:msg.from==="user"?"white":"linear-gradient(135deg,#1F2937,#374151)",color:msg.from==="user"?"#111827":"white",padding:"10px 14px",borderRadius:14,maxWidth:270,fontSize:13,lineHeight:1.5,boxShadow:"0 2px 8px rgba(0,0,0,0.06)",whiteSpace:"pre-line"}}>
              {msg.from!=="user" && <span style={{fontSize:10,opacity:0.65,display:"block",marginBottom:3}}>{msg.from==="ai"?"AI Bot":"Admin"}</span>}
              {msg.text}
              <p style={{margin:"3px 0 0",fontSize:10,opacity:0.5}}>{msg.time}</p>
            </div>
          </div>
        ))}
      </div>

      <div style={{background:"white",padding:"10px 14px",borderTop:"1px solid #F3F4F6"}}>
        <button onClick={replyWithAI} disabled={aiLoading} style={{width:"100%",background:aiLoading?"#E5E7EB":"linear-gradient(135deg,#3B82F6,#1D4ED8)",border:"none",borderRadius:12,padding:10,color:aiLoading?"#9CA3AF":"white",fontWeight:700,fontSize:13,cursor:aiLoading?"not-allowed":"pointer",display:"flex",alignItems:"center",justifyContent:"center",gap:6,marginBottom:8}}>
          <Ico n="zap" size={14} color={aiLoading?"#9CA3AF":"white"}/>{aiLoading?"AI sedang memproses...":"Balas Otomatis dengan AI"}
        </button>
        <div style={{display:"flex",gap:8}}>
          <input value={reply} onChange={e=>setReply(e.target.value)} onKeyDown={e=>e.key==="Enter"&&sendReply(reply)} placeholder="Balas manual..."
            style={{flex:1,padding:"10px 14px",borderRadius:20,border:"1.5px solid #E5E7EB",fontSize:13,outline:"none"}}/>
          <button onClick={()=>sendReply(reply)} style={{width:40,height:40,borderRadius:"50%",background:"#1F2937",border:"none",cursor:"pointer",display:"flex",alignItems:"center",justifyContent:"center"}}>
            <Ico n="send" size={16} color="white"/>
          </button>
        </div>
      </div>
    </div>
  );

  return (
    <div style={{padding:16}}>
      <h3 style={{margin:"0 0 14px",fontSize:15,fontWeight:800,color:"#111827"}}>Live Chat ({chats.length})</h3>
      {chats.length===0
        ? <div style={{textAlign:"center",padding:"48px 0",color:"#9CA3AF",fontSize:13}}>
            <Ico n="msg" size={40} color="#D1D5DB"/>
            <p style={{margin:"12px 0 0",fontSize:14,fontWeight:600}}>Belum ada chat</p>
          </div>
        : chats.map(c => (
          <button key={c.id} onClick={()=>setSelected(c.id)} style={{width:"100%",background:"white",borderRadius:16,padding:"13px 15px",marginBottom:10,display:"flex",alignItems:"center",gap:12,border:"none",cursor:"pointer",boxShadow:"0 2px 8px rgba(0,0,0,0.04)"}}>
            <div style={{width:44,height:44,borderRadius:"50%",background:"#F5F3FF",display:"flex",alignItems:"center",justifyContent:"center",flexShrink:0}}>
              <span style={{fontWeight:800,color:"#7C3AED",fontSize:18}}>{c.userName[0]}</span>
            </div>
            <div style={{flex:1,textAlign:"left",minWidth:0}}>
              <p style={{margin:0,fontWeight:700,fontSize:14,color:"#111827"}}>{c.userName}</p>
              <p style={{margin:"2px 0 0",fontSize:11,color:"#9CA3AF",overflow:"hidden",textOverflow:"ellipsis",whiteSpace:"nowrap"}}>{c.lastMsg?.slice(0,45)}...</p>
            </div>
            <div style={{display:"flex",alignItems:"center",gap:6,flexShrink:0}}>
              <span style={{fontSize:10,color:"#9CA3AF"}}>{c.messages.at(-1)?.time||""}</span>
              <Ico n="cr" size={14} color="#D1D5DB"/>
            </div>
          </button>
        ))
      }
    </div>
  );
}

// ─── MOUNT ────────────────────────────────────────────────────────────────────
const rootEl = document.getElementById("root");
const root   = ReactDOM.createRoot(rootEl);
root.render(<App/>);
</script>
</body>
</html>
