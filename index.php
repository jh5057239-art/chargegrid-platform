<?php
declare(strict_types=1);
// Cookie hardening. Secure cookies are enabled automatically when HTTPS is active.
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
require __DIR__ . '/config.php';

function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    return $pdo;
}
function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        http_response_code(419); exit('La sesión expiró. Recarga la página e inténtalo de nuevo.');
    }
}
function user(): ?array {
    if (empty($_SESSION['uid'])) return null;
    $q = db()->prepare('SELECT id,name,email,balance,is_admin,ref_code FROM users WHERE id=?');
    $q->execute([$_SESSION['uid']]); return $q->fetch() ?: null;
}
function go(string $to='home'): never { header('Location: ?page='.rawurlencode($to)); exit; }
$page = $_GET['page'] ?? 'home'; $notice = ''; $error = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf(); $action = $_POST['action'] ?? '';
        if ($action === 'register') {
            $name=trim((string)$_POST['name']); $email=strtolower(trim((string)$_POST['email']));
            $password=(string)$_POST['password']; $invite=trim((string)($_POST['invite'] ?? ''));
            if (strlen($name)<2 || trim($name)==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<12) throw new RuntimeException('Revisa tus datos. La contraseña debe tener al menos 12 caracteres.');
            $existing=db()->prepare('SELECT id FROM users WHERE email=? LIMIT 1'); $existing->execute([$email]);
            if ($existing->fetch()) throw new RuntimeException('Ya existe una cuenta con ese correo.');
            if ($invite !== '') {
                $inviter=db()->prepare('SELECT id FROM users WHERE ref_code=? LIMIT 1'); $inviter->execute([$invite]);
                if (!$inviter->fetch()) throw new RuntimeException('El código de invitación no es válido.');
            }
            $ref = strtoupper(bin2hex(random_bytes(8)));
            $q=db()->prepare('INSERT INTO users(name,email,password,ref_code,referred_by) VALUES(?,?,?,?,?)');
            $q->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT),$ref,$invite ?: null]);
            session_regenerate_id(true); $_SESSION['uid']=(int)db()->lastInsertId(); go('dashboard');
        }
        if ($action === 'login') {
            $q=db()->prepare('SELECT id,password FROM users WHERE email=?'); $q->execute([strtolower(trim((string)$_POST['email']))]); $u=$q->fetch();
            if (!$u || !password_verify((string)$_POST['password'],$u['password'])) throw new RuntimeException('Correo o contraseña incorrectos.');
            session_regenerate_id(true); $_SESSION['uid']=(int)$u['id']; go('dashboard');
        }
        if ($action === 'logout') {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', ['expires'=>time()-42000,'path'=>$params['path'],'domain'=>$params['domain'],'secure'=>$params['secure'],'httponly'=>$params['httponly'],'samesite'=>'Lax']);
            }
            session_destroy(); go('home');
        }
        $me=user(); if (!$me) go('login');
        if ($action === 'request') {
            $type=$_POST['type'] ?? ''; $amount=(float)($_POST['amount'] ?? 0); $reference=trim((string)($_POST['reference'] ?? ''));
            if (!in_array($type,['deposit','withdraw'],true) || $amount<50 || $amount>100000) throw new RuntimeException('El monto debe estar entre $50 y $100,000 MXN.');
            if ($type==='withdraw' && $amount>(float)$me['balance']) throw new RuntimeException('Saldo insuficiente.');
            db()->prepare('INSERT INTO requests(user_id,type,amount,reference,status) VALUES(?,?,?,?,?)')->execute([$me['id'],$type,$amount,$reference,'pending']);
            $notice='Solicitud registrada como demostración. No se ha movido dinero real.'; $page='dashboard';
        }
        if ($action === 'admin_decide' && (int)$me['is_admin']===1) {
            $rid=(int)$_POST['request_id']; $decision=$_POST['decision'] ?? '';
            $pdo=db(); $pdo->beginTransaction();
            $q=$pdo->prepare("SELECT * FROM requests WHERE id=? AND status='pending' FOR UPDATE"); $q->execute([$rid]); $req=$q->fetch();
            if ($req && in_array($decision,['approve','reject'],true)) {
                if ($decision==='approve') {
                    $delta=$req['type']==='deposit' ? (float)$req['amount'] : -(float)$req['amount'];
                    $check=$pdo->prepare('SELECT balance FROM users WHERE id=? FOR UPDATE'); $check->execute([$req['user_id']]); $bal=(float)$check->fetchColumn();
                    if ($delta<0 && $bal < -$delta) { $pdo->rollBack(); throw new RuntimeException('El usuario ya no tiene saldo suficiente.'); }
                    $pdo->prepare('UPDATE users SET balance=balance+? WHERE id=?')->execute([$delta,$req['user_id']]);
                }
                $pdo->prepare('UPDATE requests SET status=? WHERE id=?')->execute([$decision==='approve'?'approved':'rejected',$rid]);
            }
            $pdo->commit(); $notice='Solicitud actualizada.'; $page='admin';
        }
    }
} catch (Throwable $ex) {
    if ($ex instanceof PDOException) {
        error_log('ChargeGrid database error: ' . $ex->getMessage());
        $error = $ex->getCode() === '23000'
            ? 'Ese correo ya está registrado o existe un dato duplicado.'
            : 'Ocurrió un error de base de datos. Revisa la configuración y los registros del servidor.';
    } else {
        $error = $ex->getMessage();
    }
}
$me = user();
if ($page==='dashboard' && !$me) go('login');
if ($page==='admin' && (!$me || (int)$me['is_admin']!==1)) go('dashboard');
$products = [
 ['name'=>'Carga Urbana 22 kW','type'=>'AC','power'=>'22 kW','price'=>1200,'description'=>'Estación de carga para uso urbano y comercial.'],
 ['name'=>'Carga Rápida 60 kW','type'=>'DC','power'=>'60 kW','price'=>3500,'description'=>'Equipo de carga rápida para flotas y corredores.'],
 ['name'=>'Carga Ultra 120 kW','type'=>'DC','power'=>'120 kW','price'=>6900,'description'=>'Solución de alta capacidad para centros de movilidad.']
];
$requests=[];
if ($me) {
    if ($page==='dashboard') { $q=db()->prepare('SELECT * FROM requests WHERE user_id=? ORDER BY id DESC'); $q->execute([$me['id']]); $requests=$q->fetchAll(); }
    if ($page==='admin' && (int)$me['is_admin']===1) $requests=db()->query('SELECT r.*,u.name,u.email FROM requests r JOIN users u ON u.id=r.user_id ORDER BY r.id DESC')->fetchAll();
}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#123e35"><title>ChargeGrid | Movilidad eléctrica</title><style>
:root{--green:#174b3e;--green2:#26735b;--ink:#172b27;--muted:#6c7b76;--bg:#f4f7f5;--line:#e2eae5;--white:#fff;--gold:#d9b66f}*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--ink);font:15px/1.5 system-ui,-apple-system,Segoe UI,sans-serif}a{color:inherit;text-decoration:none}.top{background:var(--white);border-bottom:1px solid var(--line);position:sticky;top:0;z-index:5}.nav{max-width:1100px;margin:auto;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px}.brand{display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:.2px}.mark{width:36px;height:36px;border-radius:12px;background:var(--green);color:white;display:grid;place-items:center;font-size:20px}.links{display:flex;align-items:center;gap:18px;color:var(--muted);font-size:14px}.wrap{max-width:1100px;margin:28px auto;padding:0 20px 100px}.hero{background:linear-gradient(125deg,#123e35,#276d56);border-radius:24px;padding:34px;color:white;display:grid;grid-template-columns:1.4fr .6fr;gap:20px;align-items:center;overflow:hidden}.eyebrow{text-transform:uppercase;letter-spacing:1.8px;font-size:11px;font-weight:700;color:#c8ddcf}.hero h1{font-size:clamp(30px,5vw,48px);line-height:1.08;max-width:600px;margin:12px 0}.hero p{max-width:520px;color:#d9e8df}.hero-art{font-size:90px;text-align:center;opacity:.9}.btn{border:0;border-radius:12px;padding:12px 16px;background:var(--green);color:white;font-weight:700;display:inline-block;cursor:pointer}.btn.light{background:white;color:var(--green)}.btn.ghost{background:#eaf2ed;color:var(--green)}.btn.red{background:#9e3b35}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:20px}.card{background:white;border:1px solid var(--line);border-radius:18px;padding:20px;box-shadow:0 4px 18px #173c2b08}.card h2,.card h3{margin:0 0 8px}.muted{color:var(--muted)}.price{font-size:27px;font-weight:800;color:var(--green);margin:14px 0}.section-title{display:flex;justify-content:space-between;align-items:end;margin:28px 0 14px;gap:12px}.section-title h2{margin:0;font-size:23px}.stat{font-size:26px;font-weight:800;margin:5px 0}.formcard{max-width:520px;margin:30px auto}.field{display:block;margin:14px 0 6px;font-size:13px;font-weight:700}.input{width:100%;padding:13px;border:1px solid #d5e0d9;border-radius:10px;font:inherit;background:white;color:var(--ink)}.row{display:flex;gap:12px}.row>*{flex:1;min-width:0}.alert{padding:12px 14px;border-radius:12px;margin:12px 0;background:#e7f4eb;color:#1d6440}.alert.err{background:#fff0ee;color:#9a352d}.tablewrap{overflow:auto}table{border-collapse:collapse;width:100%;min-width:560px}th,td{text-align:left;padding:12px;border-bottom:1px solid var(--line);font-size:13px}th{color:var(--muted);font-weight:700}.pill{font-size:11px;border-radius:99px;padding:4px 9px;background:#edf2ee}.pending{background:#fff3d7;color:#815d10}.approved{background:#e0f4e8;color:#21683d}.rejected{background:#ffe9e6;color:#9a352d}.bottom{display:none}.footer{padding:25px 0;color:var(--muted);font-size:12px;text-align:center}.disclaimer{font-size:12px;color:var(--muted);margin-top:20px}
@media(max-width:760px){.links{display:none}.wrap{margin:18px auto;padding:0 14px 90px}.hero{grid-template-columns:1fr;padding:25px 22px}.hero-art{display:none}.grid{grid-template-columns:1fr}.row{display:block}.bottom{display:flex;position:fixed;bottom:0;left:0;right:0;background:white;border-top:1px solid var(--line);justify-content:space-around;padding:10px 4px calc(10px + env(safe-area-inset-bottom));z-index:9}.bottom a{display:grid;justify-items:center;gap:2px;color:var(--muted);font-size:10px}.bottom b{font-size:19px;color:var(--green)}.section-title{align-items:start;flex-direction:column}}
</style></head><body><header class="top"><nav class="nav"><a class="brand" href="?"><span class="mark">ϟ</span><span>ChargeGrid</span></a><div class="links"><a href="?">Inicio</a><a href="?page=products">Estaciones</a><?php if($me):?><a href="?page=dashboard">Mi cuenta</a><?php if((int)$me['is_admin']===1):?><a href="?page=admin">Administración</a><?php endif;?><form method="post" style="display:inline"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="logout"><button class="btn ghost" type="submit">Salir</button></form><?php else:?><a href="?page=login">Iniciar sesión</a><a class="btn" href="?page=register">Crear cuenta</a><?php endif;?></div></nav></header><main class="wrap">
<?php if($error):?><div class="alert err"><?=e($error)?></div><?php endif;?><?php if($notice):?><div class="alert"><?=e($notice)?></div><?php endif;?>
<?php if($page==='home'):?><section class="hero"><div><div class="eyebrow">Infraestructura para un futuro eléctrico</div><h1>Movilidad eléctrica. Una red más inteligente.</h1><p>Explora soluciones de carga, conoce equipos y administra tus solicitudes desde una plataforma sencilla.</p><a class="btn light" href="?page=products">Explorar estaciones →</a></div><div class="hero-art">ϟ</div></section><div class="section-title"><div><div class="eyebrow" style="color:var(--green2)">Catálogo</div><h2>Estaciones destacadas</h2></div><a href="?page=products" class="muted">Ver catálogo →</a></div><div class="grid"><?php foreach($products as $p):?><article class="card"><div class="eyebrow" style="color:var(--green2)"><?=e($p['type'])?> · <?=e($p['power'])?></div><h3><?=e($p['name'])?></h3><p class="muted"><?=e($p['description'])?></p><div class="price">$<?=number_format($p['price'])?> <small>MXN</small></div><a class="btn ghost" href="?page=products">Ver detalles</a></article><?php endforeach;?></div><p class="disclaimer">ChargeGrid es una demostración de software. Los equipos, precios y operaciones son ilustrativos; no se procesan pagos ni se prometen rendimientos financieros.</p>
<?php elseif($page==='products'):?><div class="section-title"><div><div class="eyebrow" style="color:var(--green2)">Catálogo de equipos</div><h2>Soluciones de carga</h2></div></div><div class="grid"><?php foreach($products as $p):?><article class="card"><div class="eyebrow" style="color:var(--green2)"><?=e($p['type'])?> · <?=e($p['power'])?></div><h3><?=e($p['name'])?></h3><p class="muted"><?=e($p['description'])?></p><div class="price">$<?=number_format($p['price'])?> MXN</div><a class="btn" href="?page=register">Crear cuenta</a></article><?php endforeach;?></div>
<?php elseif($page==='login' || $page==='register'):?><section class="card formcard"><div class="brand"><span class="mark">ϟ</span><span>ChargeGrid</span></div><h2 style="margin-top:20px"><?= $page==='login'?'Bienvenido de nuevo':'Crea tu cuenta'?></h2><p class="muted"><?= $page==='login'?'Accede a tu panel de usuario.':'Regístrate para explorar la plataforma de demostración.'?></p><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="<?= $page==='login'?'login':'register'?>"><?php if($page==='register'):?><label class="field">Nombre completo</label><input class="input" name="name" required minlength="2" autocomplete="name"><label class="field">Código de invitación (opcional)</label><input class="input" name="invite" autocomplete="off"><?php endif;?><label class="field">Correo electrónico</label><input class="input" type="email" name="email" required autocomplete="email"><label class="field">Contraseña</label><input class="input" type="password" name="password" required minlength="8" autocomplete="<?= $page==='login'?'current-password':'new-password'?>"><?php if($page==='register'):?><p class="muted" style="font-size:12px">Usa al menos 12 caracteres.</p><?php endif;?><button class="btn" style="width:100%;margin-top:12px" type="submit"><?= $page==='login'?'Iniciar sesión':'Crear cuenta'?></button></form><p class="muted"><?= $page==='login'?'¿No tienes cuenta?':'¿Ya tienes cuenta?'?> <a style="color:var(--green2);font-weight:700" href="?page=<?= $page==='login'?'register':'login'?>"><?= $page==='login'?'Regístrate':'Inicia sesión'?></a></p></section>
<?php elseif($page==='dashboard' && $me):?><div class="section-title"><div><div class="eyebrow" style="color:var(--green2)">Panel personal</div><h2>Hola, <?=e($me['name'])?></h2><p class="muted">Resumen de tu cuenta ChargeGrid.</p></div></div><div class="grid"><section class="card"><div class="muted">Saldo de demostración</div><div class="stat">$<?=number_format((float)$me['balance'],2)?> MXN</div><small class="muted">No representa dinero real.</small></section><section class="card"><div class="muted">Tu código de invitación</div><div class="stat"><?=e($me['ref_code'])?></div><button class="btn ghost" onclick="navigator.clipboard.writeText('<?=e($me['ref_code'])?>')">Copiar código</button></section><section class="card"><div class="muted">Cuenta</div><div class="stat">Activa</div><small class="muted"><?=e($me['email'])?></small></section></div><div class="grid"><section class="card"><h3>Solicitar depósito de prueba</h3><p class="muted">Registra una solicitud para revisión administrativa. No subas datos bancarios reales.</p><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="request"><input type="hidden" name="type" value="deposit"><label class="field">Monto (MXN)</label><input class="input" type="number" name="amount" min="50" max="100000" step="1" required><label class="field">Referencia de prueba (opcional)</label><input class="input" name="reference" maxlength="120"><button class="btn" style="margin-top:12px">Enviar solicitud</button></form></section><section class="card"><h3>Solicitar retiro de prueba</h3><p class="muted">El retiro solo se registra; la aprobación no implica una transferencia bancaria.</p><form method="post"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="request"><input type="hidden" name="type" value="withdraw"><label class="field">Monto (MXN)</label><input class="input" type="number" name="amount" min="50" max="100000" step="1" required><label class="field">Nota (opcional)</label><input class="input" name="reference" maxlength="120"><button class="btn" style="margin-top:12px">Enviar solicitud</button></form></section></div><div class="section-title"><h2>Mis solicitudes</h2></div><section class="card tablewrap"><table><thead><tr><th>Tipo</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr></thead><tbody><?php foreach($requests as $r):?><tr><td><?= $r['type']==='deposit'?'Depósito de prueba':'Retiro de prueba'?></td><td>$<?=number_format((float)$r['amount'],2)?> MXN</td><td><span class="pill <?=e($r['status'])?>"><?=e($r['status'])?></span></td><td><?=e($r['created_at'])?></td></tr><?php endforeach;?><?php if(!$requests):?><tr><td colspan="4">Todavía no hay solicitudes.</td></tr><?php endif;?></tbody></table></section>
<?php elseif($page==='admin' && $me && (int)$me['is_admin']===1):?><div class="section-title"><div><div class="eyebrow" style="color:var(--green2)">Control de plataforma</div><h2>Administración</h2></div></div><section class="card tablewrap"><h3>Solicitudes de usuarios</h3><table><thead><tr><th>Usuario</th><th>Tipo</th><th>Monto</th><th>Estado</th><th>Acción</th></tr></thead><tbody><?php foreach($requests as $r):?><tr><td><?=e($r['name'])?><br><small><?=e($r['email'])?></small></td><td><?=e($r['type'])?></td><td>$<?=number_format((float)$r['amount'],2)?></td><td><span class="pill <?=e($r['status'])?>"><?=e($r['status'])?></span></td><td><?php if($r['status']==='pending'):?><form method="post" style="display:flex;gap:5px"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="admin_decide"><input type="hidden" name="request_id" value="<?=(int)$r['id']?>"><button class="btn" name="decision" value="approve">Aprobar</button><button class="btn red" name="decision" value="reject">Rechazar</button></form><?php else:?>—<?php endif;?></td></tr><?php endforeach;?><?php if(!$requests):?><tr><td colspan="5">No hay solicitudes.</td></tr><?php endif;?></tbody></table></section><p class="disclaimer">Aprobaciones solo modifican el saldo interno de prueba. No existe integración de pagos reales.</p>
<?php else:?><section class="card"><h2>Página no encontrada</h2><a class="btn" href="?">Volver al inicio</a></section><?php endif;?>
<div class="footer">ChargeGrid · Plataforma demostrativa · <?=date('Y')?></div></main><nav class="bottom"><a href="?"><b>⌂</b>Inicio</a><a href="?page=products"><b>ϟ</b>Estaciones</a><?php if($me):?><a href="?page=dashboard"><b>◉</b>Mi cuenta</a><?php if((int)$me['is_admin']===1):?><a href="?page=admin"><b>⚙</b>Admin</a><?php endif;?><?php else:?><a href="?page=login"><b>↪</b>Entrar</a><?php endif;?></nav></body></html>