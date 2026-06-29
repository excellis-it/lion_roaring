<?php
/**
 * Laravel Logs Monitor — single-file, drop-in dashboard.
 * Place in any Laravel project's /public folder. Access: /logs-monitor.php
 * No framework deps. Reads storage/logs/*.log directly.
 *
 * Settings & state are stored in storage/ (NOT web-accessible).
 */

// ===== Base config (overridable via the in-app Settings panel) ==============
$PASSWORD     = 'changeme';                 // login password; set '' to disable auth
$IP_ALLOWLIST = [];                         // e.g. ['127.0.0.1','203.0.113.5']; empty = allow all
$BASE_DIR     = __DIR__ . '/..';
$LOG_DIR      = $BASE_DIR . '/storage/logs';
$SETTINGS_FILE= $BASE_DIR . '/storage/logs-monitor.json';
$STATE_FILE   = $BASE_DIR . '/storage/logs-monitor-state.json';
$MAX_BYTES    = 8 * 1024 * 1024;            // max bytes tailed from one file
// ===========================================================================

session_start();

// ---- Settings persistence -------------------------------------------------
function defaultSettings() {
    return [
        'readOnly'      => false,
        'webhookUrl'    => '',
        'webhookEvents' => ['ERROR','CRITICAL','ALERT','EMERGENCY'],
        'retentionDays' => 14,
        'extraLogDirs'  => [],   // additional absolute dirs to scan
    ];
}
function loadSettings($file) {
    $d = defaultSettings();
    if (is_file($file)) { $j = json_decode(file_get_contents($file), true); if (is_array($j)) $d = array_merge($d, $j); }
    return $d;
}
function saveSettings($file, $data) { return @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) !== false; }
$SETTINGS = loadSettings($SETTINGS_FILE);

// ---- IP allowlist ---------------------------------------------------------
if (!empty($IP_ALLOWLIST) && !in_array($_SERVER['REMOTE_ADDR'] ?? '', $IP_ALLOWLIST, true)) {
    http_response_code(403); exit('403 — Access denied (IP not allowed)');
}

// ---- Auth with brute-force lockout ----------------------------------------
if ($PASSWORD !== '') {
    if (isset($_GET['logout'])) { unset($_SESSION['logs_ok']); header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?')); exit; }
    $lockUntil = $_SESSION['lock_until'] ?? 0;
    if (isset($_POST['password'])) {
        if (time() < $lockUntil) {
            $authError = 'Too many attempts. Try again in ' . ceil(($lockUntil - time())/60) . ' min.';
        } elseif (hash_equals($PASSWORD, (string)$_POST['password'])) {
            $_SESSION['logs_ok'] = true; $_SESSION['fails'] = 0;
        } else {
            $_SESSION['fails'] = ($_SESSION['fails'] ?? 0) + 1;
            if ($_SESSION['fails'] >= 5) { $_SESSION['lock_until'] = time() + 900; $authError = 'Locked for 15 minutes.'; }
            else $authError = 'Incorrect password (' . (5 - $_SESSION['fails']) . ' left)';
        }
    }
    if (empty($_SESSION['logs_ok'])) {
        ?><!doctype html><html><head><meta charset="utf-8"><title>Logs Monitor</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <style>
        :root{color-scheme:dark}*{box-sizing:border-box}
        body{background:radial-gradient(1200px 600px at 50% -10%,#1b2640,#0b0f17 60%);color:#e6edf3;font:15px system-ui,sans-serif;display:flex;height:100dvh;margin:0;align-items:center;justify-content:center}
        form{background:rgba(22,27,34,.85);backdrop-filter:blur(12px);border:1px solid #30363d;padding:36px;border-radius:18px;width:330px;box-shadow:0 24px 60px rgba(0,0,0,.5)}
        h1{font-size:20px;margin:0 0 4px;display:flex;gap:10px}p{color:#8b949e;font-size:13px;margin:0 0 22px}
        input{width:100%;padding:12px 14px;background:#0d1117;border:1px solid #30363d;border-radius:10px;color:#e6edf3;margin-bottom:14px;font-size:15px}
        input:focus{outline:none;border-color:#388bfd;box-shadow:0 0 0 3px rgba(56,139,253,.25)}
        button{width:100%;padding:12px;background:linear-gradient(180deg,#2ea043,#238636);border:0;border-radius:10px;color:#fff;font-weight:600;font-size:15px;cursor:pointer}
        button:hover{filter:brightness(1.08)}.err{color:#f85149;font-size:13px;margin-bottom:12px}
        </style></head><body>
        <form method="post"><h1>🔒 Logs Monitor</h1><p>Enter your password to continue</p>
        <?php if (!empty($authError)) echo '<div class="err">'.htmlspecialchars($authError).'</div>'; ?>
        <input type="password" name="password" placeholder="Password" autofocus>
        <button>Unlock Dashboard</button></form></body></html><?php
        exit;
    }
}

// ---- Helpers --------------------------------------------------------------
function humanBytes($b){ if($b<=0)return '0 B'; $u=['B','KB','MB','GB','TB']; $i=(int)floor(log($b,1024)); return round($b/pow(1024,$i),1).' '.$u[$i]; }
function allLogDirs($base, $settings) {
    $dirs = [$base.'/storage/logs'];
    foreach (($settings['extraLogDirs'] ?? []) as $d) if ($d && is_dir($d)) $dirs[] = $d;
    return array_values(array_unique($dirs));
}
function logFiles($dirs) {
    $out = [];
    foreach ((array)$dirs as $dir) { if (is_dir($dir)) foreach (glob($dir.'/*.log') ?: [] as $f) $out[basename($f)] = $f; }
    uasort($out, fn($a,$b)=>filemtime($b)<=>filemtime($a));
    return $out; // [basename => fullpath]
}
function tailFile($path,$max,$since=0){
    $size=filesize($path); $fh=fopen($path,'rb');
    $start = $since>0 ? min($since,$size) : ($size>$max ? $size-$max : 0);
    fseek($fh,$start); $data=stream_get_contents($fh); fclose($fh);
    return [$data,$size];
}
function parseEntries($content){
    $entries=[]; $re='/^\[(\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+(\S+?)\.(\w+):/m';
    preg_match_all($re,$content,$m,PREG_OFFSET_CAPTURE); $c=count($m[0]);
    for($i=0;$i<$c;$i++){ $s=$m[0][$i][1]; $e=($i+1<$c)?$m[0][$i+1][1]:strlen($content);
        $entries[]=['date'=>$m[1][$i][0],'env'=>$m[2][$i][0],'level'=>strtoupper($m[3][$i][0]),'body'=>trim(substr($content,$s,$e-$s))]; }
    return array_reverse($entries);
}
function laravelVersion($base){
    $f="$base/vendor/laravel/framework/src/Illuminate/Foundation/Application.php";
    if(is_file($f)&&preg_match("/const VERSION = '([^']+)'/",file_get_contents($f),$m))return $m[1];
    $lock="$base/composer.lock";
    if(is_file($lock)){$j=json_decode(file_get_contents($lock),true);foreach(($j['packages']??[])as $p)if(($p['name']??'')==='laravel/framework')return ltrim($p['version'],'v');}
    return 'Unknown';
}
function envVal($base,$key,$def='—'){
    static $c=null; if($c===null){$c=is_file("$base/.env")?file_get_contents("$base/.env"):'';}
    if(preg_match('/^'.preg_quote($key,'/').'=(.*)$/m',$c,$m))return trim($m[1]," \"'");
    return $def;
}
function webhookSend($url,$text){
    if(!$url)return false;
    $payload=json_encode(['text'=>$text]);
    if(function_exists('curl_init')){
        $ch=curl_init($url);curl_setopt_array($ch,[CURLOPT_POST=>1,CURLOPT_POSTFIELDS=>$payload,CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_RETURNTRANSFER=>1,CURLOPT_TIMEOUT=>5,CURLOPT_SSL_VERIFYPEER=>0]);
        $ok=curl_exec($ch)!==false;curl_close($ch);return $ok;
    }
    $ctx=stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/json\r\n",'content'=>$payload,'timeout'=>5]]);
    return @file_get_contents($url,false,$ctx)!==false;
}
function shellOk(){ return function_exists('shell_exec') && !in_array('shell_exec',array_map('trim',explode(',',ini_get('disable_functions')))); }
function sh($cmd){ return shellOk() ? @shell_exec($cmd) : null; }
function cpuStat(){ // Linux /proc/stat snapshot
    if(!is_readable('/proc/stat'))return null;
    foreach(explode("\n",file_get_contents('/proc/stat')) as $l){ if(strpos($l,'cpu ')===0){
        $p=preg_split('/\s+/',trim($l));$n=array_map('intval',array_slice($p,1));
        $idle=($n[3]??0)+($n[4]??0);$total=array_sum($n);return ['idle'=>$idle,'total'=>$total]; } }
    return null;
}
function serverMetrics($base){
    $os=PHP_OS_FAMILY; $m=['os'=>$os,'time'=>date('H:i:s')];
    // cores
    $cores=(int)(sh('nproc 2>/dev/null') ?: sh('sysctl -n hw.ncpu 2>/dev/null') ?: 0);
    $m['cores']=$cores ?: 1;
    // load average
    $la=function_exists('sys_getloadavg')?sys_getloadavg():[null,null,null];
    $m['load']=array_map(fn($x)=>$x===null?null:round($x,2),$la);
    // CPU %
    $cpu=null;
    if($os==='Linux'){ $a=cpuStat(); if($a){ usleep(200000); $b=cpuStat();
        $dt=$b['total']-$a['total']; $di=$b['idle']-$a['idle']; if($dt>0)$cpu=round((1-$di/$dt)*100,1); } }
    elseif($os==='Darwin'){ $t=sh('top -l 1 -n 0 2>/dev/null'); if($t&&preg_match('/CPU usage:.*?([\d.]+)%\s*idle/',$t,$mm))$cpu=round(100-(float)$mm[1],1); }
    if($cpu===null && $m['load'][0]!==null) $cpu=min(100,round($m['load'][0]/$m['cores']*100,1)); // fallback estimate
    $m['cpu']=$cpu;
    // memory
    $total=$used=null;
    if(is_readable('/proc/meminfo')){ $mi=file_get_contents('/proc/meminfo');
        preg_match('/MemTotal:\s+(\d+)/',$mi,$t1);preg_match('/MemAvailable:\s+(\d+)/',$mi,$a1);
        if($t1){$total=$t1[1]*1024;$avail=($a1[1]??0)*1024;$used=$total-$avail;}
        preg_match('/SwapTotal:\s+(\d+)/',$mi,$st);preg_match('/SwapFree:\s+(\d+)/',$mi,$sf);
        if($st&&$st[1]>0)$m['swap']=['total'=>$st[1]*1024,'used'=>($st[1]-$sf[1])*1024];
    } elseif($os==='Darwin'){
        $total=(int)sh('sysctl -n hw.memsize 2>/dev/null');
        $vm=sh('vm_stat 2>/dev/null');
        if($vm){ $ps=preg_match('/page size of (\d+)/',$vm,$pp)?(int)$pp[1]:4096;
            $g=fn($k)=>preg_match('/'.$k.':\s+(\d+)/',$vm,$x)?(int)$x[1]:0;
            $free=($g('Pages free')+$g('Pages inactive')+$g('Pages speculative'))*$ps;
            if($total)$used=$total-$free; }
    }
    if($total)$m['mem']=['total'=>$total,'used'=>$used,'pct'=>round($used/$total*100,1)];
    // disk
    $df=@disk_free_space($base);$dt=@disk_total_space($base);
    if($dt)$m['disk']=['total'=>$dt,'free'=>$df,'used'=>$dt-$df,'pct'=>round(($dt-$df)/$dt*100,1)];
    // uptime
    if(is_readable('/proc/uptime')){$u=(float)strtok(file_get_contents('/proc/uptime'),' ');$m['uptime']=(int)$u;}
    else{$ut=sh('sysctl -n kern.boottime 2>/dev/null');if($ut&&preg_match('/sec = (\d+)/',$ut,$bm))$m['uptime']=time()-(int)$bm[1];}
    // php process
    $m['phpMem']=memory_get_usage(true);$m['phpPeak']=memory_get_peak_usage(true);
    return $m;
}

$LEVEL_COLORS=['EMERGENCY'=>'#ff5c5c','ALERT'=>'#ff5c5c','CRITICAL'=>'#ff5c5c','ERROR'=>'#f85149','WARNING'=>'#d29922','NOTICE'=>'#58a6ff','INFO'=>'#3fb950','DEBUG'=>'#8b949e'];

$DIRS  = allLogDirs($BASE_DIR,$SETTINGS);
$files = logFiles($DIRS);
$names = array_keys($files);
$readOnly = !empty($SETTINGS['readOnly']);

// =========================== AJAX / ACTIONS ================================
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Raw download (not JSON)
if ($action === 'download') {
    $req=basename($_GET['file']??'');
    if(!isset($files[$req])){http_response_code(404);exit('Not found');}
    header('Content-Type: text/plain');header('Content-Disposition: attachment; filename="'.$req.'"');
    readfile($files[$req]); exit;
}

if ($action) {
    header('Content-Type: application/json');
    $guard = function() use ($readOnly){ if($readOnly){echo json_encode(['ok'=>false,'error'=>'Read-only mode is enabled']);exit;} };

    if ($action === 'entries') {
        $req=basename($_GET['file']??''); $since=(int)($_GET['since']??0);
        if(!isset($files[$req])){echo json_encode(['error'=>'File not found']);exit;}
        [$data,$size]=tailFile($files[$req],$MAX_BYTES,$since);
        $entries=parseEntries($data);
        $counts=['ERROR'=>0,'WARNING'=>0,'INFO'=>0,'DEBUG'=>0,'OTHER'=>0];
        foreach($entries as $e){$lv=$e['level'];
            if(isset($counts[$lv]))$counts[$lv]++;
            elseif(in_array($lv,['CRITICAL','ALERT','EMERGENCY']))$counts['ERROR']++;
            elseif($lv==='NOTICE')$counts['INFO']++; else $counts['OTHER']++;}
        // Webhook alerts on NEW entries during live tail
        if($since>0 && $SETTINGS['webhookUrl']){
            $want=$SETTINGS['webhookEvents']; $hits=array_filter($entries,fn($e)=>in_array($e['level'],$want));
            if($hits){$first=reset($hits);$app=envVal($BASE_DIR,'APP_NAME','App');
                webhookSend($SETTINGS['webhookUrl'],"🚨 *$app* — ".count($hits)." new ".$first['level']." in $req\n```".substr($first['body'],0,500)."```");}
        }
        echo json_encode(['entries'=>$entries,'size'=>$size,'mtime'=>filemtime($files[$req]),'counts'=>$counts]); exit;
    }

    if ($action === 'searchall') {
        $q=trim($_GET['q']??''); $res=[];
        if($q!==''){foreach($files as $name=>$path){[$data]=tailFile($path,$MAX_BYTES);
            foreach(parseEntries($data) as $e){ if(stripos($e['body'],$q)!==false){$e['file']=$name;$res[]=$e; if(count($res)>=400)break 2;} }}}
        echo json_encode(['entries'=>$res,'q'=>$q]); exit;
    }

    if ($action === 'health') {
        $h=[];
        $dbOk=false;$dbMsg='';
        try{ $conn=envVal($BASE_DIR,'DB_CONNECTION','mysql');$host=envVal($BASE_DIR,'DB_HOST','127.0.0.1');
            $port=envVal($BASE_DIR,'DB_PORT','3306');$db=envVal($BASE_DIR,'DB_DATABASE','');$u=envVal($BASE_DIR,'DB_USERNAME','');$pw=envVal($BASE_DIR,'DB_PASSWORD','');
            if($conn==='sqlite'){$dbOk=is_file($db)||is_file("$BASE_DIR/database/database.sqlite");$dbMsg='sqlite';}
            else{$dsn="$conn:host=$host;port=$port;dbname=$db";$pdo=new PDO($dsn,$u,$pw==='null'?'':$pw,[PDO::ATTR_TIMEOUT=>3,PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);$dbOk=true;$dbMsg="$conn @ $host";}
        }catch(Throwable $e){$dbMsg=substr($e->getMessage(),0,80);}
        $h[]=['DB Connection',$dbOk,$dbMsg];
        $h[]=['storage/ writable',is_writable("$BASE_DIR/storage"),''];
        $h[]=['storage/logs writable',is_writable("$BASE_DIR/storage/logs"),''];
        $h[]=['bootstrap/cache writable',is_writable("$BASE_DIR/bootstrap/cache"),''];
        $h[]=['APP_DEBUG off',strtolower(envVal($BASE_DIR,'APP_DEBUG','false'))!=='true','prod safety'];
        $h[]=['APP_KEY set',envVal($BASE_DIR,'APP_KEY','')!=='','' ];
        echo json_encode(['checks'=>$h]); exit;
    }

    if ($action === 'metrics') { echo json_encode(serverMetrics($BASE_DIR)); exit; }

    if ($action === 'settings') { echo json_encode(['settings'=>$SETTINGS]); exit; }

    if ($action === 'savesettings') {
        $in=json_decode(file_get_contents('php://input'),true)?:[];
        $SETTINGS['webhookUrl']=trim($in['webhookUrl']??'');
        $SETTINGS['webhookEvents']=array_values(array_intersect((array)($in['webhookEvents']??[]),array_keys($LEVEL_COLORS)));
        $SETTINGS['retentionDays']=max(1,(int)($in['retentionDays']??14));
        $SETTINGS['readOnly']=!empty($in['readOnly']);
        $SETTINGS['extraLogDirs']=array_values(array_filter(array_map('trim',(array)($in['extraLogDirs']??[]))));
        echo json_encode(['ok'=>saveSettings($SETTINGS_FILE,$SETTINGS)]); exit;
    }

    if ($action === 'testwebhook') {
        $url=trim($_POST['url']??$SETTINGS['webhookUrl']);
        echo json_encode(['ok'=>webhookSend($url,'✅ Logs Monitor test message — webhook is working!')]); exit;
    }

    if ($action === 'clear')  { $guard(); $req=basename($_POST['file']??'');
        if(isset($files[$req])&&is_writable($files[$req])){file_put_contents($files[$req],'');echo json_encode(['ok'=>true]);}else echo json_encode(['ok'=>false,'error'=>'Not writable']); exit; }

    if ($action === 'delete') { $guard(); $req=basename($_POST['file']??'');
        if(isset($files[$req])&&is_writable($files[$req])){unlink($files[$req]);echo json_encode(['ok'=>true]);}else echo json_encode(['ok'=>false,'error'=>'Not writable']); exit; }

    if ($action === 'backup') { $guard(); $req=basename($_POST['file']??'');
        if(!isset($files[$req])){echo json_encode(['ok'=>false,'error'=>'Not found']);exit;}
        $src=$files[$req];$dir=dirname($src);
        if(!is_writable($src)||!is_writable($dir)){echo json_encode(['ok'=>false,'error'=>'Not writable']);exit;}
        $n=pathinfo($req,PATHINFO_FILENAME);$x=pathinfo($req,PATHINFO_EXTENSION)?:'log';
        $dest="$dir/{$n}-backup-".date('Y-m-d_His').".$x";
        if(rename($src,$dest)){touch($src);@chmod($src,0664);echo json_encode(['ok'=>true,'backup'=>basename($dest)]);}
        else echo json_encode(['ok'=>false,'error'=>'Rename failed']); exit; }

    if ($action === 'prune') { $guard();
        $days=max(1,(int)($SETTINGS['retentionDays']??14));$cut=time()-$days*86400;$del=[];
        foreach($files as $name=>$path){ if(strpos($name,'-backup-')!==false && filemtime($path)<$cut){ if(@unlink($path))$del[]=$name; } }
        echo json_encode(['ok'=>true,'deleted'=>$del,'days'=>$days]); exit; }

    echo json_encode(['error'=>'Unknown action']); exit;
}

// ---- System info ----------------------------------------------------------
$totalLogSize=array_sum(array_map('filesize',$files));
$diskFree=@disk_free_space($BASE_DIR);$diskTotal=@disk_total_space($BASE_DIR);
$sys=[
    'Laravel'=>laravelVersion($BASE_DIR),'PHP'=>PHP_VERSION,
    'App Name'=>envVal($BASE_DIR,'APP_NAME'),'Environment'=>envVal($BASE_DIR,'APP_ENV'),'Debug'=>envVal($BASE_DIR,'APP_DEBUG'),
    'Server'=>$_SERVER['SERVER_SOFTWARE']??'—','OS'=>php_uname('s').' '.php_uname('r'),'Host'=>php_uname('n'),'Arch'=>php_uname('m'),
    'Memory Limit'=>ini_get('memory_limit'),'Max Upload'=>ini_get('upload_max_filesize'),'Timezone'=>date_default_timezone_get(),
    'Server Time'=>date('Y-m-d H:i:s'),'Disk Free'=>$diskFree?humanBytes($diskFree).' / '.humanBytes($diskTotal):'—',
    'Log Files'=>count($files),'Logs Size'=>humanBytes($totalLogSize),
];
?><!doctype html>
<html lang="en" data-theme="dark"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Laravel Logs Monitor</title>
<style>
:root{
  --bg:#080b12;--panel:#11161f;--panel2:#161b25;--border:#1e2733;--border2:#2c3645;
  --text:#e9eef5;--muted:#8593a6;--accent:#3d8bff;--accent2:#8b5cff;--shadow:0 8px 30px rgba(0,0,0,.4);
  --glow1:rgba(61,139,255,.10);--glow2:rgba(139,92,255,.10);color-scheme:dark;
}
[data-theme="light"]{
  --bg:#f4f6fb;--panel:#fff;--panel2:#eef1f7;--border:#e1e6ef;--border2:#d3dae6;
  --text:#1a2231;--muted:#5e6b80;--accent:#2563eb;--accent2:#7c3aed;--shadow:0 8px 24px rgba(30,50,90,.12);
  --glow1:rgba(37,99,235,.07);--glow2:rgba(124,58,237,.07);color-scheme:light;
}
*{box-sizing:border-box}html,body{height:100%}
body{margin:0;font:14px/1.55 -apple-system,BlinkMacSystemFont,"Segoe UI",Inter,system-ui,sans-serif;
  background:radial-gradient(1200px 600px at 85% -15%,var(--glow1),transparent 60%),radial-gradient(900px 500px at 0% 110%,var(--glow2),transparent 55%),var(--bg);
  color:var(--text);height:100dvh;display:flex;flex-direction:column;overflow:hidden;-webkit-font-smoothing:antialiased}

.topbar{display:flex;align-items:center;gap:14px;padding:12px 20px;background:color-mix(in srgb,var(--panel) 72%,transparent);
  backdrop-filter:blur(18px) saturate(140%);-webkit-backdrop-filter:blur(18px) saturate(140%);border-bottom:1px solid var(--border);z-index:30;flex-shrink:0}
.brand{display:flex;align-items:center;gap:11px;font-size:16px;font-weight:700}
.brand .t{background:linear-gradient(90deg,var(--text),var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent}
.brand .logo{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:18px;background:linear-gradient(135deg,var(--accent),var(--accent2));box-shadow:0 6px 20px rgba(61,139,255,.4)}
.pill{font-size:11.5px;font-weight:600;padding:5px 11px;border-radius:20px;background:var(--panel2);border:1px solid var(--border2);color:var(--muted);display:flex;align-items:center;gap:6px;white-space:nowrap}
.pill b{color:var(--text)}
.dot{width:7px;height:7px;border-radius:50%;background:#3fb950;box-shadow:0 0 8px #3fb950;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.grow{flex:1}
.icon-btn{background:var(--panel2);border:1px solid var(--border2);color:var(--muted);width:38px;height:38px;border-radius:10px;cursor:pointer;font-size:16px;display:grid;place-items:center;flex-shrink:0;transition:.15s}
.icon-btn:hover{color:var(--text);border-color:var(--accent);background:var(--panel)}
.icon-btn:active{transform:scale(.94)}
.hamburger{display:none}
a.logout{color:var(--muted);text-decoration:none;font-size:13px;padding:9px 13px;border:1px solid var(--border2);border-radius:10px}
a.logout:hover{color:#f85149;border-color:#f85149}
.show-sm{display:none}.ro-badge{font-size:11px;font-weight:700;color:#d29922;border:1px solid #d29922;border-radius:20px;padding:4px 10px}

.wrap{flex:1;display:flex;min-height:0;position:relative}
.scrim{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(2px);z-index:40}
.sidebar{width:296px;flex-shrink:0;background:color-mix(in srgb,var(--panel) 60%,transparent);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow:hidden}
.side-sec{padding:15px 16px 7px;font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);display:flex;justify-content:space-between;align-items:center}
.side-sec a{color:var(--accent);text-decoration:none;font-size:11px;cursor:pointer}
.files{overflow:auto;padding:0 10px 10px;flex:1}
.file{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;cursor:pointer;margin-bottom:3px;border:1px solid transparent}
.file:hover{background:var(--panel)}
.file.active{background:linear-gradient(90deg,color-mix(in srgb,var(--accent) 18%,transparent),transparent);border-color:var(--border2)}
.file .fn{flex:1;min-width:0;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.file .fm{font-size:11px;color:var(--muted)}
.file.backup .fn::before{content:"🗂 ";opacity:.7}
.sysinfo{border-top:1px solid var(--border);padding:12px 14px;max-height:46%;overflow:auto}
.sysrow{display:flex;justify-content:space-between;gap:10px;font-size:12.5px;margin-bottom:6px}
.sysrow span{color:var(--muted)}.sysrow b{font-weight:600;text-align:right;word-break:break-word}
.health{padding:0 14px 12px}
.hrow{display:flex;align-items:center;gap:8px;font-size:12.5px;margin-bottom:5px}
.hdot{width:9px;height:9px;border-radius:50%;flex-shrink:0;background:var(--muted)}
.hdot.ok{background:#3fb950}.hdot.bad{background:#f85149}
.hrow small{color:var(--muted);margin-left:auto}

.main{flex:1;display:flex;flex-direction:column;min-width:0}
.stats{display:flex;gap:12px;padding:16px 20px 4px;flex-wrap:wrap}
.stat{flex:1;min-width:120px;background:linear-gradient(180deg,var(--panel),var(--panel2));border:1px solid var(--border);border-radius:14px;padding:14px 16px;position:relative;overflow:hidden;transition:transform .15s,border-color .15s;cursor:pointer}
.stat:hover{transform:translateY(-2px);border-color:var(--border2)}
.stat.sel{border-color:var(--accent);box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 18%,transparent)}
.stat .n{font-size:24px;font-weight:700;line-height:1;font-variant-numeric:tabular-nums}
.stat .l{font-size:12px;color:var(--muted);margin-top:5px}
.stat .bar{position:absolute;left:0;top:0;bottom:0;width:4px}
.spike{position:absolute;top:8px;right:10px;font-size:10px;font-weight:800;color:#f85149;animation:pulse 1.2s infinite}

.trend{padding:6px 20px 0}
.trend svg{width:100%;height:46px;display:block}
.trend .tlabel{font-size:11px;color:var(--muted);margin-bottom:2px}

.toolbar{display:flex;align-items:center;gap:10px;padding:12px 20px;flex-wrap:wrap}
.search{position:relative;flex:1;min-width:240px}
.search input{width:100%;padding:10px 84px 10px 38px;background:var(--panel);border:1px solid var(--border2);border-radius:10px;color:var(--text);font-size:14px}
.search input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 20%,transparent)}
.search .si{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted)}
.search .scope{position:absolute;right:6px;top:50%;transform:translateY(-50%);font-size:11px;color:var(--muted);background:var(--panel2);border:1px solid var(--border2);border-radius:8px;padding:3px 7px;cursor:pointer}
.search .scope.on{color:var(--accent);border-color:var(--accent)}
.chips{display:flex;gap:7px;flex-wrap:wrap}
.chip{display:flex;align-items:center;gap:7px;padding:8px 13px;border:1px solid var(--border2);border-radius:22px;cursor:pointer;font-size:12.5px;font-weight:600;user-select:none;background:var(--panel);opacity:.55;transition:.15s}
.chip:hover{opacity:.85}
.chip.active{opacity:1;border-color:var(--accent);box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 14%,transparent)}
.chip-all.active{background:linear-gradient(90deg,color-mix(in srgb,var(--accent) 18%,transparent),transparent);border-color:var(--accent2)}
.chip .cdot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.chip .cnt{background:var(--panel2);padding:1px 7px;border-radius:10px;font-size:11px;color:var(--muted)}
.chip.active .cnt{color:var(--text)}
.daterow{display:flex;gap:8px;align-items:center;padding:0 20px 8px;flex-wrap:wrap}
.daterow input,.daterow select{background:var(--panel);border:1px solid var(--border2);color:var(--text);border-radius:9px;padding:7px 9px;font-size:12.5px}
.btn{padding:9px 14px;background:var(--panel);border:1px solid var(--border2);color:var(--text);border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;display:flex;align-items:center;gap:7px;white-space:nowrap}
.btn:hover{border-color:var(--accent)}
.btn.accent{background:linear-gradient(180deg,color-mix(in srgb,var(--accent) 18%,transparent),transparent);border-color:var(--border2)}
.btn.accent:hover{border-color:var(--accent2)}
.btn.danger:hover{border-color:#f85149;color:#f85149}
.switch{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--muted);cursor:pointer}
.switch input{accent-color:var(--accent)}

.list{flex:1;overflow:auto;padding:4px 20px 24px}
.entry{border:1px solid var(--border);border-left-width:4px;border-radius:12px;margin-bottom:9px;background:linear-gradient(180deg,var(--panel),color-mix(in srgb,var(--panel) 70%,transparent));overflow:hidden;transition:border-color .15s,box-shadow .15s}
.entry:hover{border-color:var(--border2);box-shadow:var(--shadow)}
.entry .head{display:flex;align-items:center;gap:13px;padding:12px 15px;cursor:pointer}
.entry .head:hover{background:color-mix(in srgb,var(--text) 3%,transparent)}
.badge{font-size:10.5px;font-weight:800;letter-spacing:.4px;padding:3px 9px;border-radius:7px;color:#08101e;white-space:nowrap}
.gcount{font-size:10.5px;font-weight:700;background:var(--panel2);border:1px solid var(--border2);color:var(--muted);border-radius:20px;padding:2px 8px;white-space:nowrap}
.date{color:var(--muted);font-size:12px;white-space:nowrap;font-variant-numeric:tabular-nums}
.env{font-size:11px;color:var(--muted);background:var(--panel2);padding:2px 8px;border-radius:6px;white-space:nowrap}
.fileTag{font-size:11px;color:var(--accent);background:color-mix(in srgb,var(--accent) 12%,transparent);padding:2px 8px;border-radius:6px;white-space:nowrap}
.msg{flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-family:ui-monospace,"SF Mono",Menlo,monospace;font-size:13px}
.caret{color:var(--muted);transition:transform .15s;font-size:11px;flex-shrink:0}
.entry.open .caret{transform:rotate(90deg)}
.acts{display:flex;gap:5px;flex-shrink:0;opacity:0;transition:.15s}
.entry:hover .acts{opacity:1}
.mini{background:var(--panel2);border:1px solid var(--border2);color:var(--muted);height:28px;min-width:30px;padding:0 7px;border-radius:8px;cursor:pointer;font-size:13px}
.mini:hover{color:var(--text);border-color:var(--accent)}
.mini.ok{color:#3fb950;border-color:#3fb950}
.entry pre{margin:0;padding:2px 16px 16px 44px;white-space:pre-wrap;word-break:break-word;font-family:ui-monospace,"SF Mono",Menlo,monospace;font-size:12.5px;color:var(--text);display:none;line-height:1.65}
.entry.open pre{display:block;animation:fade .2s}
.entry pre .tr{color:var(--muted)}.entry pre .fp{color:var(--accent)}
@keyframes fade{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:none}}
.loadmore{display:block;margin:8px auto 0;}
.empty{text-align:center;color:var(--muted);padding:80px 20px}.empty .e{font-size:42px;margin-bottom:12px;opacity:.45}

/* Modal */
.modal{display:none;position:fixed;inset:0;z-index:60;align-items:center;justify-content:center;padding:20px}
.modal.show{display:flex}
.modal .bg{position:absolute;inset:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px)}
.modal .card{position:relative;background:var(--panel);border:1px solid var(--border2);border-radius:18px;width:520px;max-width:100%;max-height:88vh;overflow:auto;box-shadow:0 30px 80px rgba(0,0,0,.5)}
.modal h2{margin:0;padding:20px 22px;font-size:17px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
.modal .body{padding:20px 22px}
.fld{margin-bottom:18px}
.fld label{display:block;font-size:13px;font-weight:600;margin-bottom:7px}
.fld small{color:var(--muted);font-weight:400}
.fld input[type=text],.fld input[type=url],.fld input[type=number],.fld textarea{width:100%;padding:10px 12px;background:var(--bg);border:1px solid var(--border2);border-radius:10px;color:var(--text);font-size:13.5px;font-family:inherit}
.fld textarea{resize:vertical;min-height:60px;font-family:ui-monospace,monospace}
.evchips{display:flex;gap:6px;flex-wrap:wrap}
.evchips label{font-size:12px;border:1px solid var(--border2);border-radius:20px;padding:5px 11px;cursor:pointer;display:flex;gap:6px;align-items:center}
.evchips label.on{border-color:var(--accent);color:var(--accent)}
.evchips input{display:none}
.modal .foot{padding:16px 22px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end}
.toggle{display:flex;align-items:center;gap:10px;cursor:pointer}
.gauges{display:flex;gap:16px;margin-bottom:18px}
.gauge{flex:1;text-align:center}
.ring{width:120px;height:120px;margin:0 auto;border-radius:50%;display:grid;place-items:center;
  background:conic-gradient(var(--gc,var(--accent)) calc(var(--p,0)*1%),var(--panel2) 0);transition:--p .5s;position:relative}
.ring::before{content:"";position:absolute;inset:11px;border-radius:50%;background:var(--panel)}
.gv{position:relative;font-size:22px;font-weight:700;font-variant-numeric:tabular-nums}
.gl{margin-top:8px;font-size:12.5px;color:var(--muted);font-weight:600}
.spark{width:100%;height:30px;margin-top:6px;display:block}
.metrows{display:grid;grid-template-columns:1fr 1fr;gap:8px 22px}
.metrow{display:flex;justify-content:space-between;font-size:12.5px;padding:7px 0;border-bottom:1px solid var(--border)}
.metrow span{color:var(--muted)}.metrow b{font-variant-numeric:tabular-nums}
@property --p{syntax:'<number>';inherits:false;initial-value:0}
.toast{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);background:var(--panel);border:1px solid var(--border2);padding:12px 18px;border-radius:12px;box-shadow:var(--shadow);z-index:80;font-size:13.5px;opacity:0;transition:.25s;pointer-events:none}
.toast.show{opacity:1;transform:translateX(-50%) translateY(-4px)}

::-webkit-scrollbar{width:10px;height:10px}
::-webkit-scrollbar-thumb{background:var(--border2);border-radius:6px;border:2px solid transparent;background-clip:padding-box}

@media(max-width:900px){.stat{min-width:calc(33.33% - 8px)}.stat .n{font-size:21px}}
@media(max-width:760px){
  .hamburger{display:grid}.hide-sm{display:none!important}.show-sm{display:inline}
  .topbar{padding:10px 14px;gap:10px}
  .sidebar{position:fixed;top:0;bottom:0;left:0;z-index:50;width:84vw;max-width:330px;background:color-mix(in srgb,var(--panel) 98%,transparent);backdrop-filter:blur(20px);transform:translateX(-100%);transition:transform .26s cubic-bezier(.4,0,.2,1);box-shadow:0 0 50px rgba(0,0,0,.6)}
  body.nav-open .sidebar{transform:none}body.nav-open .scrim{display:block}
  .stats{padding:12px 14px 0;gap:9px}.stat{min-width:calc(50% - 5px);padding:12px 14px}.stat .n{font-size:20px}
  .trend,.daterow{padding-left:14px;padding-right:14px}
  .toolbar{padding:12px 14px;gap:9px}.search{min-width:100%;order:-1}
  .chips{width:100%;overflow-x:auto;flex-wrap:nowrap;padding-bottom:4px}.chips::-webkit-scrollbar{display:none}.chip{flex-shrink:0}
  .toolbar .btn{flex:1;justify-content:center}
  .switch{width:100%;justify-content:center;padding:6px;border:1px solid var(--border2);border-radius:10px}
  .list{padding:2px 12px 20px}
  .entry .head{flex-wrap:wrap;gap:8px 10px}.msg{flex-basis:100%;order:5;white-space:normal}.acts{opacity:1;order:4;margin-left:auto}.entry pre{padding-left:14px}
}
@media(max-width:380px){.stat{min-width:100%}}
</style></head>
<body>

<div class="topbar">
  <button class="icon-btn hamburger" id="menuBtn">☰</button>
  <div class="brand"><div class="logo">📜</div><span class="t">Logs Monitor</span></div>
  <span class="pill"><span class="dot"></span> <?= htmlspecialchars($sys['Environment']) ?></span>
  <span class="pill hide-sm">⚡ Laravel <b><?= htmlspecialchars($sys['Laravel']) ?></b></span>
  <span class="pill hide-sm">🐘 PHP <b><?= htmlspecialchars($sys['PHP']) ?></b></span>
  <?php if($readOnly): ?><span class="ro-badge hide-sm">READ-ONLY</span><?php endif; ?>
  <span class="grow"></span>
  <button class="icon-btn" id="monitorBtn" title="System monitor">📊</button>
  <button class="icon-btn" id="themeBtn" title="Toggle theme">🌙</button>
  <button class="icon-btn" id="settingsBtn" title="Settings">⚙️</button>
  <button class="icon-btn" id="refresh" title="Refresh">↻</button>
  <?php if ($PASSWORD !== ''): ?><a class="logout" href="?logout=1"><span class="hide-sm">Logout</span><span class="show-sm">⏻</span></a><?php endif; ?>
</div>

<div class="wrap">
  <div class="scrim" id="scrim"></div>
  <aside class="sidebar" id="sidebar">
    <div class="side-sec"><span>Log Files (<?= count($names) ?>)</span><a id="pruneBtn">Prune old</a></div>
    <div class="files" id="files">
      <?php if(!$names): ?><div style="padding:14px;color:var(--muted);font-size:13px">No log files found.</div><?php endif; ?>
      <?php foreach($files as $n=>$f): ?>
        <div class="file <?= strpos($n,'-backup-')!==false?'backup':'' ?>" data-file="<?= htmlspecialchars($n) ?>">
          <div style="flex:1;min-width:0">
            <div class="fn"><?= htmlspecialchars($n) ?></div>
            <div class="fm"><?= humanBytes(filesize($f)) ?> · <?= date('M j, H:i',filemtime($f)) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="side-sec">Server Details</div>
    <div class="sysinfo">
      <?php foreach($sys as $k=>$v): ?><div class="sysrow"><span><?= htmlspecialchars($k) ?></span><b><?= htmlspecialchars($v) ?></b></div><?php endforeach; ?>
    </div>
    <div class="side-sec"><span>Health</span><a id="healthBtn">Run check</a></div>
    <div class="health" id="health"><div style="color:var(--muted);font-size:12.5px">Tap “Run check”.</div></div>
  </aside>

  <div class="main">
    <div class="stats" id="stats">
      <div class="stat" data-f="ERROR"><div class="bar" style="background:#f85149"></div><div class="n" id="s-err">0</div><div class="l">🔴 Errors</div><span class="spike" id="spike" style="display:none">▲ SPIKE</span></div>
      <div class="stat" data-f="WARNING"><div class="bar" style="background:#d29922"></div><div class="n" id="s-warn">0</div><div class="l">🟡 Warnings</div></div>
      <div class="stat" data-f="INFO"><div class="bar" style="background:#3fb950"></div><div class="n" id="s-info">0</div><div class="l">🟢 Info</div></div>
      <div class="stat" data-f="DEBUG"><div class="bar" style="background:#8b949e"></div><div class="n" id="s-debug">0</div><div class="l">⚪ Debug</div></div>
      <div class="stat" data-f="ALL"><div class="bar" style="background:var(--accent)"></div><div class="n" id="s-total">0</div><div class="l">📊 Total</div></div>
    </div>

    <div class="trend"><div class="tlabel">Activity (per hour)</div><svg id="trend" viewBox="0 0 600 46" preserveAspectRatio="none"></svg></div>

    <div class="toolbar">
      <div class="search"><span class="si">🔍</span><input id="search" placeholder="Search messages, exceptions, traces…"><span class="scope" id="scope" title="Search across all files">All files</span></div>
      <div class="chips" id="chips">
        <div class="chip chip-all active" data-lv="ALL"><span class="cdot" style="background:linear-gradient(135deg,var(--accent),var(--accent2))"></span>All<span class="cnt" data-cnt="ALL">0</span></div>
        <?php foreach(['ERROR'=>'#f85149','WARNING'=>'#d29922','INFO'=>'#3fb950','DEBUG'=>'#8b949e','CRITICAL'=>'#ff5c5c','NOTICE'=>'#58a6ff'] as $lv=>$c): ?>
          <div class="chip" data-lv="<?= $lv ?>"><span class="cdot" style="background:<?= $c ?>"></span><?= ucfirst(strtolower($lv)) ?><span class="cnt" data-cnt="<?= $lv ?>">0</span></div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="daterow">
      <select id="preset">
        <option value="">All time</option><option value="1">Last hour</option><option value="24">Today (24h)</option><option value="168">Last 7 days</option>
      </select>
      <input type="datetime-local" id="from" title="From"><input type="datetime-local" id="to" title="To">
      <span class="grow"></span>
      <label class="switch" title="Group identical exceptions"><input type="checkbox" id="group"> Group</label>
      <label class="switch" title="Highlight stack-trace paths"><input type="checkbox" id="pretty" checked> Pretty</label>
      <button class="btn" id="expandAll">⤢ Expand</button>
      <button class="btn" id="downloadRaw">⬇ Raw</button>
      <button class="btn" id="downloadFilt">⬇ Filtered</button>
      <button class="btn accent" id="backupBtn">🗂 Backup &amp; New</button>
      <button class="btn danger" id="clearBtn">🗑 Clear</button>
      <button class="btn danger" id="deleteBtn">✕ Delete</button>
      <label class="switch"><input type="checkbox" id="auto"> Live tail</label>
    </div>

    <div class="list" id="list"><div class="empty"><div class="e">📂</div>Select a log file to begin</div></div>
  </div>
</div>

<!-- Settings modal -->
<div class="modal" id="settingsModal">
  <div class="bg" data-close></div>
  <div class="card">
    <h2>⚙️ Settings <button class="icon-btn" data-close>✕</button></h2>
    <div class="body">
      <div class="fld"><label class="toggle"><input type="checkbox" id="set-readonly"> <b>Read-only mode</b> <small>— disables clear / backup / delete / prune</small></label></div>
      <div class="fld">
        <label>Webhook URL <small>(Slack / Discord / generic — alerts on new errors during Live tail)</small></label>
        <input type="url" id="set-webhook" placeholder="https://hooks.slack.com/services/...">
      </div>
      <div class="fld">
        <label>Alert on levels</label>
        <div class="evchips" id="set-events">
          <?php foreach(['EMERGENCY','ALERT','CRITICAL','ERROR','WARNING','NOTICE','INFO','DEBUG'] as $lv): ?>
          <label data-lv="<?= $lv ?>"><input type="checkbox" value="<?= $lv ?>"><?= ucfirst(strtolower($lv)) ?></label>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="fld" style="display:flex;gap:12px;align-items:flex-end">
        <div style="flex:1"><label>Backup retention (days) <small>for Prune</small></label><input type="number" id="set-retention" min="1" value="14"></div>
        <button class="btn" id="testWebhook">📨 Test webhook</button>
      </div>
      <div class="fld">
        <label>Extra log directories <small>(one absolute path per line)</small></label>
        <textarea id="set-dirs" placeholder="/var/www/other/storage/logs"></textarea>
      </div>
      <p style="color:var(--muted);font-size:12px">Password &amp; IP allowlist are set at the top of <code>logs-monitor.php</code> for security.</p>
    </div>
    <div class="foot"><button class="btn" data-close>Cancel</button><button class="btn accent" id="saveSettings">Save settings</button></div>
  </div>
</div>

<!-- System Monitor modal -->
<div class="modal" id="monitorModal">
  <div class="bg" data-mclose></div>
  <div class="card" style="width:640px">
    <h2>📊 System Monitor <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:auto" id="mon-meta"></span> <button class="icon-btn" data-mclose>✕</button></h2>
    <div class="body">
      <div class="gauges">
        <div class="gauge"><div class="ring" id="g-cpu"><div class="gv" id="cpu-v">–</div></div><div class="gl">CPU</div><svg class="spark" id="cpu-spark" viewBox="0 0 120 30" preserveAspectRatio="none"></svg></div>
        <div class="gauge"><div class="ring" id="g-mem"><div class="gv" id="mem-v">–</div></div><div class="gl">Memory</div><svg class="spark" id="mem-spark" viewBox="0 0 120 30" preserveAspectRatio="none"></svg></div>
        <div class="gauge"><div class="ring" id="g-disk"><div class="gv" id="disk-v">–</div></div><div class="gl">Disk</div><svg class="spark" id="disk-spark" viewBox="0 0 120 30" preserveAspectRatio="none"></svg></div>
      </div>
      <div class="metrows" id="metrows"></div>
    </div>
    <div class="foot"><label class="switch"><input type="checkbox" id="monAuto" checked> Auto-refresh 2s</label><span class="grow"></span><button class="btn" id="monRefresh">↻ Refresh</button><button class="btn" data-mclose>Close</button></div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const COLORS=<?= json_encode($LEVEL_COLORS) ?>;
const KNOWN=['ERROR','WARNING','INFO','DEBUG','CRITICAL','NOTICE','ALERT','EMERGENCY'];
const READONLY=<?= $readOnly?'true':'false' ?>;
const $=id=>document.getElementById(id);
const filesEl=$('files'),list=$('list'),search=$('search'),body=document.body;
let entries=[],current=null,allOpen=false,active=new Set(),pageSize=80,shown=0,searchAll=false,lastSize=0,prevErr=null;

// ---- prefs (localStorage) ----
const PREFS=JSON.parse(localStorage.getItem('lm_prefs')||'{}');
function savePrefs(){localStorage.setItem('lm_prefs',JSON.stringify(PREFS));}
function applyTheme(){document.documentElement.dataset.theme=PREFS.theme||'dark';$('themeBtn').textContent=(PREFS.theme==='light')?'☀️':'🌙';}
applyTheme();
$('group').checked=!!PREFS.group;$('pretty').checked=PREFS.pretty!==false;
$('themeBtn').onclick=()=>{PREFS.theme=(PREFS.theme==='light')?'dark':'light';savePrefs();applyTheme();};

function toast(m){const t=$('toast');t.textContent=m;t.classList.add('show');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('show'),2600);}
function esc(s){return s.replace(/[&<>]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;'}[c]));}
function hl(s,q){if(!q)return esc(s);const i=s.toLowerCase().indexOf(q.toLowerCase());if(i<0)return esc(s);return esc(s.slice(0,i))+'<mark style="background:#ffd33d;color:#08101e;border-radius:3px">'+esc(s.slice(i,i+q.length))+'</mark>'+esc(s.slice(i+q.length));}
function prettyTrace(s){if(!$('pretty').checked)return s;return s.replace(/(#\d+\s)([^\n]*?)(\(\d+\))?:/g,'<span class="tr">$1</span>').replace(/(\/[^\s:()]+\.php)(:\d+)?/g,'<span class="fp">$1$2</span>');}
function normExc(b){return b.split('\n')[0].replace(/\d+/g,'#').replace(/0x[0-9a-f]+/gi,'#').slice(0,160);}
function levelGroup(lv){if(['CRITICAL','ALERT','EMERGENCY'].includes(lv))return 'ERROR';if(lv==='NOTICE')return 'INFO';return KNOWN.includes(lv)?lv:'OTHER';}
function entryTs(e){return Date.parse(e.date.replace(' ','T'))||0;}

// ---- filters ----
$('preset').onchange=()=>{const h=+$('preset').value;if(!h){$('from').value=$('to').value='';}else{const to=new Date(),from=new Date(Date.now()-h*3600e3);$('from').value=fmtLocal(from);$('to').value=fmtLocal(to);}render();};
function fmtLocal(d){const p=n=>String(n).padStart(2,'0');return d.getFullYear()+'-'+p(d.getMonth()+1)+'-'+p(d.getDate())+'T'+p(d.getHours())+':'+p(d.getMinutes());}
['from','to'].forEach(id=>$(id).onchange=render);
$('group').onchange=()=>{PREFS.group=$('group').checked;savePrefs();render();};
$('pretty').onchange=()=>{PREFS.pretty=$('pretty').checked;savePrefs();render();};

document.querySelectorAll('.chip').forEach(c=>c.onclick=()=>{const lv=c.dataset.lv;
  if(lv==='ALL')active.clear();else if(active.has(lv))active.delete(lv);else active.add(lv);syncChips();render();});
function syncChips(){$('chips').querySelector('.chip-all').classList.toggle('active',active.size===0);
  document.querySelectorAll('.chip:not(.chip-all)').forEach(x=>x.classList.toggle('active',active.has(x.dataset.lv)));
  document.querySelectorAll('.stat').forEach(s=>s.classList.toggle('sel',s.dataset.f!=='ALL'&&active.has(s.dataset.f)));}
document.querySelectorAll('.stat').forEach(s=>s.onclick=()=>{const f=s.dataset.f;if(f==='ALL')active.clear();else if(active.has(f))active.delete(f);else active.add(f);syncChips();render();});

function passFilters(e,q){
  if(active.size>0 && ![...active].some(a=>a===e.level||levelGroup(e.level)===a))return false;
  if(q && !e.body.toLowerCase().includes(q.toLowerCase()))return false;
  const f=$('from').value,t=$('to').value;
  if(f||t){const ts=entryTs(e);if(f&&ts<Date.parse(f))return false;if(t&&ts>Date.parse(t))return false;}
  return true;
}

function filtered(){const q=search.value.trim();let rows=entries.filter(e=>passFilters(e,q));
  if($('group').checked){const map=new Map();rows.forEach(e=>{const k=e.level+'|'+normExc(e.body);if(map.has(k))map.get(k)._n++;else{map.set(k,Object.assign({_n:1},e));}});rows=[...map.values()];}
  return rows;
}

function rowHtml(e,i,q){const color=COLORS[e.level]||'#8b949e';
  const first=e.body.split('\n')[0].replace(/^\[[^\]]*\]\s*\S+:\s*/,'');
  return `<div class="entry${allOpen?' open':''}" data-i="${i}" style="border-left-color:${color}">
    <div class="head"><span class="caret">▶</span>
      <span class="badge" style="background:${color}">${e.level}</span>
      ${e._n>1?`<span class="gcount">×${e._n}</span>`:''}
      <span class="date">${esc(e.date)}</span>
      ${e.file?`<span class="fileTag">${esc(e.file)}</span>`:`<span class="env">${esc(e.env)}</span>`}
      <span class="msg">${hl(first,q)}</span>
      <span class="acts">
        <button class="mini" data-act="copy" title="Copy">⧉</button>
        <button class="mini" data-act="md" title="Copy as Markdown">M↓</button>
        <button class="mini" data-act="json" title="Copy as JSON">{ }</button>
        <button class="mini" data-act="link" title="Permalink">🔗</button>
      </span></div>
    <pre>${prettyTrace(hl(e.body,q))}</pre></div>`;
}

let curRows=[];
function render(){
  const q=search.value.trim();curRows=filtered();shown=Math.min(pageSize,curRows.length);
  if(!curRows.length){list.innerHTML='<div class="empty"><div class="e">🔎</div>No entries match your filters</div>';drawTrend([]);return;}
  paint();drawTrend(entries.filter(e=>passFilters(e,q)));
}
function paint(){
  const q=search.value.trim();
  list.innerHTML=curRows.slice(0,shown).map((e,i)=>rowHtml(e,i,q)).join('')+
    (shown<curRows.length?`<button class="btn loadmore" id="loadmore">Load more (${curRows.length-shown})</button>`:'');
  bind();const lm=$('loadmore');if(lm)lm.onclick=()=>{shown=Math.min(shown+pageSize,curRows.length);paint();};
}
function bind(){
  list.querySelectorAll('.entry .head').forEach(h=>h.onclick=e=>{if(e.target.closest('.acts'))return;h.parentElement.classList.toggle('open');});
  list.querySelectorAll('.mini').forEach(b=>b.onclick=async e=>{e.stopPropagation();
    const ent=curRows[+b.closest('.entry').dataset.i],act=b.dataset.act;let text='';
    if(act==='copy')text=ent.body;
    else if(act==='md')text='```\n'+ent.body+'\n```';
    else if(act==='json')text=JSON.stringify({level:ent.level,date:ent.date,env:ent.env,file:ent.file||current,message:ent.body},null,2);
    else if(act==='link'){const u=location.origin+location.pathname+'#f='+encodeURIComponent(ent.file||current)+'&t='+encodeURIComponent(ent.date);text=u;}
    try{await navigator.clipboard.writeText(text);}catch(_){const ta=document.createElement('textarea');ta.value=text;document.body.appendChild(ta);ta.select();document.execCommand('copy');ta.remove();}
    b.classList.add('ok');b.textContent='✓';setTimeout(()=>{b.classList.remove('ok');b.textContent={copy:'⧉',md:'M↓',json:'{ }',link:'🔗'}[act];},1100);
  });
}

function drawTrend(rows){
  const svg=$('trend');const buckets={};rows.forEach(e=>{const d=new Date(entryTs(e));if(!d.getTime())return;const k=d.getFullYear()+''+d.getMonth()+d.getDate()+d.getHours();buckets[k]=(buckets[k]||0)+1;});
  const vals=Object.values(buckets);if(!vals.length){svg.innerHTML='';return;}
  const max=Math.max(...vals),W=600,H=46,n=vals.length,bw=W/Math.max(n,1);
  svg.innerHTML=vals.map((v,i)=>{const h=max?(v/max)*(H-6):0;return `<rect x="${i*bw+1}" y="${H-h}" width="${Math.max(bw-2,1)}" height="${h}" rx="1" fill="var(--accent)" opacity="0.8"/>`;}).join('');
}

async function load(initial){
  if(!current)return;
  const since=initial?0:lastSize;
  const r=await fetch('?action=entries&file='+encodeURIComponent(current)+'&since='+since+'&_='+Date.now());
  const d=await r.json();
  if(d.error){list.innerHTML='<div class="empty"><div class="e">⚠️</div>'+d.error+'</div>';return;}
  if(initial){entries=d.entries||[];}
  else if(d.entries&&d.entries.length){entries=d.entries.concat(entries);}
  lastSize=d.size;
  const c=d.counts;
  // counts reflect full reload only; for incremental recompute from entries
  const cc={ERROR:0,WARNING:0,INFO:0,DEBUG:0};entries.forEach(e=>{const g=levelGroup(e.level);if(cc[g]!==undefined)cc[g]++;});
  $('s-err').textContent=cc.ERROR;$('s-warn').textContent=cc.WARNING;$('s-info').textContent=cc.INFO;$('s-debug').textContent=cc.DEBUG;$('s-total').textContent=entries.length;
  // spike detection
  if(prevErr!==null && cc.ERROR-prevErr>=5){$('spike').style.display='block';setTimeout(()=>$('spike').style.display='none',6000);
    if(Notification.permission==='granted')new Notification('Error spike',{body:(cc.ERROR-prevErr)+' new errors in '+current});}
  prevErr=cc.ERROR;
  const lc={ALL:entries.length};entries.forEach(e=>lc[e.level]=(lc[e.level]||0)+1);
  document.querySelectorAll('[data-cnt]').forEach(el=>el.textContent=lc[el.dataset.cnt]||0);
  render();
}

async function runSearchAll(){
  const q=search.value.trim();if(!q){searchAll=false;$('scope').classList.remove('on');if(current)load(true);return;}
  const r=await fetch('?action=searchall&q='+encodeURIComponent(q));const d=await r.json();
  entries=d.entries||[];lastSize=0;
  const lc={ALL:entries.length};entries.forEach(e=>lc[e.level]=(lc[e.level]||0)+1);
  document.querySelectorAll('[data-cnt]').forEach(el=>el.textContent=lc[el.dataset.cnt]||0);
  $('s-total').textContent=entries.length;render();
}

// ---- nav / drawer ----
const closeNav=()=>body.classList.remove('nav-open');
$('menuBtn').onclick=()=>body.classList.toggle('nav-open');$('scrim').onclick=closeNav;
addEventListener('keydown',e=>{if(e.key==='Escape'){closeNav();$('settingsModal').classList.remove('show');}});

filesEl.querySelectorAll('.file').forEach(f=>f.onclick=()=>{
  filesEl.querySelectorAll('.file').forEach(x=>x.classList.remove('active'));f.classList.add('active');
  current=f.dataset.file;allOpen=false;searchAll=false;$('scope').classList.remove('on');prevErr=null;closeNav();load(true);});

search.oninput=()=>{if(searchAll)runSearchAll();else render();};
$('scope').onclick=()=>{searchAll=!searchAll;$('scope').classList.toggle('on',searchAll);if(searchAll)runSearchAll();else if(current)load(true);};
$('refresh').onclick=()=>{if(searchAll)runSearchAll();else load(true);};
$('expandAll').onclick=()=>{allOpen=!allOpen;$('expandAll').textContent=allOpen?'⤡ Collapse':'⤢ Expand';paint();};

$('downloadRaw').onclick=()=>{if(!current)return;location.href='?action=download&file='+encodeURIComponent(current);};
$('downloadFilt').onclick=()=>{const txt=curRows.map(e=>e.body).join('\n\n');const blob=new Blob([txt],{type:'text/plain'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download=(current||'logs')+'-filtered.log';a.click();};

function post(action,data){const fd=new FormData();fd.append('action',action);for(const k in data)fd.append(k,data[k]);return fetch('',{method:'POST',body:fd}).then(r=>r.json());}
$('backupBtn').onclick=async()=>{if(READONLY)return toast('Read-only mode');if(!current||!confirm('Archive '+current+' with date & start fresh?'))return;const d=await post('backup',{file:current});if(d.ok){toast('Archived as '+d.backup);setTimeout(()=>location.reload(),700);}else toast('Failed: '+d.error);};
$('clearBtn').onclick=async()=>{if(READONLY)return toast('Read-only mode');if(!current||!confirm('Clear all contents of '+current+'?'))return;const d=await post('clear',{file:current});if(d.ok)load(true);else toast('Failed: '+d.error);};
$('deleteBtn').onclick=async()=>{if(READONLY)return toast('Read-only mode');if(!current||!confirm('Permanently DELETE '+current+'?'))return;const d=await post('delete',{file:current});if(d.ok){toast('Deleted');setTimeout(()=>location.reload(),700);}else toast('Failed: '+d.error);};
$('pruneBtn').onclick=async()=>{if(READONLY)return toast('Read-only mode');if(!confirm('Delete backup files older than retention period?'))return;const d=await post('prune',{});if(d.ok){toast('Pruned '+d.deleted.length+' file(s) (>'+d.days+'d)');if(d.deleted.length)setTimeout(()=>location.reload(),800);}else toast('Failed');};

let timer=null;
$('auto').onchange=e=>{clearInterval(timer);if(e.target.checked){if('Notification'in window&&Notification.permission==='default')Notification.requestPermission();timer=setInterval(()=>{if(!searchAll)load(false);},5000);}};

// ---- health ----
$('healthBtn').onclick=async()=>{const el=$('health');el.innerHTML='<div style="color:var(--muted);font-size:12.5px">Checking…</div>';
  const d=await(await fetch('?action=health')).json();
  el.innerHTML=d.checks.map(([name,ok,note])=>`<div class="hrow"><span class="hdot ${ok?'ok':'bad'}"></span>${esc(name)}${note?`<small>${esc(note)}</small>`:''}</div>`).join('');};

// ---- settings modal ----
const modal=$('settingsModal');
$('settingsBtn').onclick=async()=>{const d=await(await fetch('?action=settings')).json();const s=d.settings;
  $('set-readonly').checked=!!s.readOnly;$('set-webhook').value=s.webhookUrl||'';$('set-retention').value=s.retentionDays||14;
  $('set-dirs').value=(s.extraLogDirs||[]).join('\n');
  document.querySelectorAll('#set-events label').forEach(l=>{const on=(s.webhookEvents||[]).includes(l.dataset.lv);l.classList.toggle('on',on);l.querySelector('input').checked=on;});
  modal.classList.add('show');};
document.querySelectorAll('[data-close]').forEach(b=>b.onclick=()=>modal.classList.remove('show'));
document.querySelectorAll('#set-events label').forEach(l=>l.onclick=e=>{e.preventDefault();const i=l.querySelector('input');i.checked=!i.checked;l.classList.toggle('on',i.checked);});
$('testWebhook').onclick=async()=>{const d=await post('testwebhook',{url:$('set-webhook').value});toast(d.ok?'Webhook sent ✓':'Webhook failed');};
$('saveSettings').onclick=async()=>{
  const events=[...document.querySelectorAll('#set-events input:checked')].map(i=>i.value);
  const r=await fetch('?action=savesettings',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({
    readOnly:$('set-readonly').checked,webhookUrl:$('set-webhook').value.trim(),webhookEvents:events,
    retentionDays:+$('set-retention').value,extraLogDirs:$('set-dirs').value.split('\n').map(s=>s.trim()).filter(Boolean)})});
  const d=await r.json();if(d.ok){toast('Settings saved');setTimeout(()=>location.reload(),700);}else toast('Save failed');};

// ---- system monitor ----
const monModal=$('monitorModal');let monTimer=null;
const hist={cpu:[],mem:[],disk:[]};
function hb(b){if(b==null)return '—';const u=['B','KB','MB','GB','TB'];let i=0;b=+b;while(b>=1024&&i<4){b/=1024;i++;}return b.toFixed(1)+' '+u[i];}
function gColor(p){return p>=85?'#f85149':p>=60?'#d29922':'#3fb950';}
function setGauge(id,vid,pct,label){const r=$(id);if(pct==null){r.style.setProperty('--p',0);$(vid).textContent='n/a';return;}
  r.style.setProperty('--p',pct);r.style.setProperty('--gc',gColor(pct));$(vid).textContent=label??(pct+'%');}
function spark(id,arr,color){const svg=$(id);if(!arr.length){svg.innerHTML='';return;}
  const max=100,W=120,H=30,n=arr.length,step=W/Math.max(n-1,1);
  const pts=arr.map((v,i)=>`${(i*step).toFixed(1)},${(H-(v/max)*H).toFixed(1)}`).join(' ');
  svg.innerHTML=`<polyline points="${pts}" fill="none" stroke="${color}" stroke-width="1.5"/>`;}
function push(a,v){a.push(v==null?0:v);if(a.length>40)a.shift();}
async function loadMetrics(){
  let d;try{d=await(await fetch('?action=metrics&_='+Date.now())).json();}catch(_){return;}
  $('mon-meta').textContent=d.os+' · '+d.cores+' cores · '+d.time;
  setGauge('g-cpu','cpu-v',d.cpu);
  setGauge('g-mem','mem-v',d.mem?d.mem.pct:null);
  setGauge('g-disk','disk-v',d.disk?d.disk.pct:null);
  push(hist.cpu,d.cpu);push(hist.mem,d.mem?d.mem.pct:null);push(hist.disk,d.disk?d.disk.pct:null);
  spark('cpu-spark',hist.cpu,gColor(d.cpu||0));spark('mem-spark',hist.mem,gColor(d.mem?.pct||0));spark('disk-spark',hist.disk,gColor(d.disk?.pct||0));
  const rows=[];
  rows.push(['Load avg (1/5/15m)',(d.load||[]).map(x=>x==null?'—':x).join(' / ')]);
  if(d.mem)rows.push(['Memory',hb(d.mem.used)+' / '+hb(d.mem.total)]);
  if(d.swap)rows.push(['Swap',hb(d.swap.used)+' / '+hb(d.swap.total)]);
  if(d.disk)rows.push(['Disk',hb(d.disk.used)+' / '+hb(d.disk.total)+' ('+hb(d.disk.free)+' free)']);
  if(d.uptime!=null){const u=d.uptime,dd=Math.floor(u/86400),hh=Math.floor(u%86400/3600),mm=Math.floor(u%3600/60);rows.push(['Uptime',(dd?dd+'d ':'')+hh+'h '+mm+'m']);}
  rows.push(['PHP memory',hb(d.phpMem)+' (peak '+hb(d.phpPeak)+')']);
  $('metrows').innerHTML=rows.map(([k,v])=>`<div class="metrow"><span>${esc(k)}</span><b>${esc(String(v))}</b></div>`).join('');
}
function startMon(){clearInterval(monTimer);if($('monAuto').checked)monTimer=setInterval(loadMetrics,2000);}
$('monitorBtn').onclick=()=>{monModal.classList.add('show');loadMetrics();startMon();};
document.querySelectorAll('[data-mclose]').forEach(b=>b.onclick=()=>{monModal.classList.remove('show');clearInterval(monTimer);});
$('monAuto').onchange=startMon;$('monRefresh').onclick=loadMetrics;
addEventListener('keydown',e=>{if(e.key==='Escape'){monModal.classList.remove('show');clearInterval(monTimer);}});

// ---- init: handle permalink, then select first file ----
(function init(){
  const m=new URLSearchParams(location.hash.slice(1));const wantFile=m.get('f'),wantTs=m.get('t');
  const target=wantFile?[...filesEl.querySelectorAll('.file')].find(f=>f.dataset.file===wantFile):filesEl.querySelector('.file');
  if(target){target.click();if(wantTs)setTimeout(()=>{const row=[...list.querySelectorAll('.entry')].find(el=>el.querySelector('.date')?.textContent===wantTs);if(row){row.classList.add('open');row.scrollIntoView({behavior:'smooth',block:'center'});}},400);}
})();
</script>
</body></html>
