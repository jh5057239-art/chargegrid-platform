<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#123b32">
  <title>ChargeGrid | Movilidad eléctrica</title>
  <style>
    :root{--green:#174c3d;--green2:#236b52;--mint:#e8f3ed;--ink:#1e2925;--muted:#718078;--line:#e4eae6;--surface:#fff;--bg:#f5f7f5}
    *{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
    a{color:inherit;text-decoration:none}.app{max-width:1180px;margin:auto;padding:0 22px 96px}.top{height:76px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);gap:16px}
    .brand{display:flex;align-items:center;gap:11px;font-size:20px;font-weight:750;letter-spacing:-.6px}.logo{width:38px;height:38px;border-radius:12px;background:var(--green);display:grid;place-items:center;color:white;font-size:20px}
    .user{display:flex;align-items:center;gap:10px;color:var(--muted);font-size:13px}.avatar{width:36px;height:36px;border-radius:50%;display:grid;place-items:center;background:var(--mint);color:var(--green);font-weight:700}
    .eyebrow{font-size:11px;font-weight:750;text-transform:uppercase;letter-spacing:1.5px;color:var(--green2)}h1{font-size:clamp(27px,4vw,38px);letter-spacing:-1.2px;line-height:1.12;margin:9px 0 10px}.intro{padding:32px 0 24px;display:flex;align-items:flex-end;justify-content:space-between;gap:24px}.intro p{margin:0;color:var(--muted);max-width:560px}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:0;border-radius:11px;background:var(--green);color:#fff;font-weight:650;padding:12px 17px;font-size:13px;cursor:pointer}.btn.light{background:#fff;color:var(--green);border:1px solid var(--line)}
    .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.card{background:var(--surface);border:1px solid var(--line);border-radius:18px;padding:21px;min-width:0}.metric-head{display:flex;align-items:center;justify-content:space-between;color:var(--muted);font-size:13px}.metric-icon{width:38px;height:38px;border-radius:12px;background:var(--mint);display:grid;place-items:center;color:var(--green);font-size:18px}.metric{font-size:29px;font-weight:750;letter-spacing:-1px;margin:15px 0 3px}.sub{font-size:12px;color:var(--muted)}.section-head{display:flex;align-items:center;justify-content:space-between;margin:30px 0 15px;gap:12px}.section-head h2{font-size:19px;margin:0;letter-spacing:-.4px}.section-head a{font-size:12px;color:var(--green2);font-weight:700}
    .station{padding:0;overflow:hidden}.station-art{height:130px;background:linear-gradient(140deg,#e8f0e9,#dce9e1);display:flex;align-items:center;justify-content:center;position:relative}.charger{width:62px;height:90px;background:#fff;border:3px solid #315e4c;border-radius:12px;box-shadow:0 10px 20px #173c2515;display:grid;place-items:center;color:var(--green);font-size:28px;position:relative}.charger:after{content:"";position:absolute;right:-17px;top:18px;width:15px;height:35px;border:3px solid #315e4c;border-left:0;border-radius:0 10px 10px 0}.station-body{padding:18px}.tag{display:inline-block;border-radius:30px;padding:5px 9px;background:var(--mint);color:var(--green);font-size:10px;font-weight:750;letter-spacing:.3px}.station h3{margin:11px 0 5px;font-size:16px}.station p{margin:0 0 17px;font-size:12px;color:var(--muted)}.station-foot{border-top:1px solid var(--line);padding-top:14px;display:flex;align-items:center;justify-content:space-between;gap:8px}.price{font-size:19px;font-weight:750}.price small{font-size:11px;color:var(--muted);font-weight:500}
    .notice{margin-top:22px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:15px 17px;color:var(--muted);font-size:12px}.notice strong{color:var(--ink)}.bottom{position:fixed;bottom:0;left:0;right:0;display:flex;justify-content:center;background:#ffffffed;border-top:1px solid var(--line);backdrop-filter:blur(14px);z-index:3}.nav{width:min(100%,700px);display:flex;justify-content:space-around;padding:9px 8px max(9px,env(safe-area-inset-bottom));}.nav a{min-width:58px;text-align:center;color:#87918b;font-size:10px;display:grid;justify-items:center;gap:4px}.nav a span{font-size:19px;line-height:1.2}.nav a.active{color:var(--green);font-weight:750}
    @media(max-width:760px){.app{padding:0 16px 100px}.top{height:67px}.intro{padding:26px 0 20px;display:block}.intro .btn{margin-top:17px}.grid{grid-template-columns:1fr;gap:12px}.metric-card{display:flex;align-items:center;gap:14px}.metric-card .metric-icon{flex-shrink:0}.metric-card .metric{margin:0;font-size:24px}.metric-card .metric-head{display:block}.metric-card .metric-head .metric-icon{display:none}.metric-copy{min-width:0}.section-head{margin-top:26px}.station-art{height:112px}.user .username{display:none}}
  </style>
</head>
<body>
  <main class="app">
    <header class="top">
      <a class="brand" href="/"><span class="logo">ϟ</span><span>ChargeGrid</span></a>
      <div class="user"><span class="username">Panel de demostración</span><span class="avatar">CG</span></div>
    </header>
    <section class="intro">
      <div><div class="eyebrow">MOVILIDAD · ENERGÍA · CONECTIVIDAD</div><h1>La energía que mueve<br>el siguiente paso.</h1><p>Consulta estaciones de carga y administra tu actividad desde un solo lugar.</p></div>
      <div style="display:flex;gap:10px;flex-wrap:wrap"><a class="btn" href="{{ route('register') }}">Crear cuenta</a><a class="btn light" href="{{ route('login') }}">Iniciar sesión</a></div>
    </section>
    <section class="grid" aria-label="Resumen">
      <article class="card metric-card"><div class="metric-icon">▣</div><div class="metric-copy"><div class="metric-head">Saldo de demostración</div><div class="metric">$0.00 <span style="font-size:13px;color:var(--muted)">MXN</span></div><div class="sub">Sin movimientos registrados</div></div></article>
      <article class="card metric-card"><div class="metric-icon">ϟ</div><div class="metric-copy"><div class="metric-head">Estaciones disponibles</div><div class="metric">03</div><div class="sub">Equipos de muestra del catálogo</div></div></article>
      <article class="card metric-card"><div class="metric-icon">↗</div><div class="metric-copy"><div class="metric-head">Actividad del mes</div><div class="metric">0</div><div class="sub">Operaciones registradas</div></div></article>
    </section>
    <section id="estaciones">
      <div class="section-head"><h2>Estaciones destacadas</h2><a href="#estaciones">Ver catálogo&nbsp; →</a></div>
      <div class="grid">
        <article class="card station"><div class="station-art"><div class="charger">ϟ</div></div><div class="station-body"><span class="tag">CARGA AC</span><h3>ChargeGrid Urban 7</h3><p>Equipo de carga urbana · Ficha de muestra</p><div class="station-foot"><div><div class="price">$8,900 <small>MXN</small></div><div class="sub">Precio ilustrativo</div></div><a class="btn light" href="#informacion">Detalles</a></div></div></article>
        <article class="card station"><div class="station-art" style="background:linear-gradient(140deg,#e8eee9,#d8e5df)"><div class="charger">ϟ</div></div><div class="station-body"><span class="tag">CARGA AC</span><h3>ChargeGrid Pro 22</h3><p>Equipo comercial · Ficha de muestra</p><div class="station-foot"><div><div class="price">$18,500 <small>MXN</small></div><div class="sub">Precio ilustrativo</div></div><a class="btn light" href="#informacion">Detalles</a></div></div></article>
        <article class="card station"><div class="station-art" style="background:linear-gradient(140deg,#e7eeea,#d1e0d7)"><div class="charger">ϟ</div></div><div class="station-body"><span class="tag">CARGA DC</span><h3>ChargeGrid Rapid 60</h3><p>Estación rápida · Ficha de muestra</p><div class="station-foot"><div><div class="price">$42,000 <small>MXN</small></div><div class="sub">Precio ilustrativo</div></div><a class="btn light" href="#informacion">Detalles</a></div></div></article>
      </div>
    </section>
    <div class="notice" id="informacion"><strong>Versión inicial de interfaz.</strong> Los equipos y precios mostrados son ejemplos de diseño, no ofertas de venta ni inversiones disponibles. Esta pantalla no procesa pagos ni calcula rendimientos.</div>
  </main>
  <footer class="bottom"><nav class="nav" aria-label="Navegación principal"><a class="active" href="/"><span>⌂</span>Inicio</a><a href="#estaciones"><span>ϟ</span>Estaciones</a><a href="#informacion"><span>⇄</span>Actividad</a><a href="#informacion"><span>♧</span>Equipo</a><a href="#informacion"><span>◎</span>Perfil</a></nav></footer>
</body>
</html>