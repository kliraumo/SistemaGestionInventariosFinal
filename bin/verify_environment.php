<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
$checks=['PHP >= 8.2'=>version_compare(PHP_VERSION,'8.2.0','>='),'PDO SQLSRV'=>extension_loaded('pdo_sqlsrv'),'mbstring'=>extension_loaded('mbstring'),'openssl'=>extension_loaded('openssl')];
foreach($checks as $name=>$ok) echo sprintf("[%s] %s\n",$ok?'OK':'FALLO',$name);
try { $pdo=App\Helpers\Database::connection(); echo "[OK] Conexión SQL Server\n"; $tables=['Usuarios','Roles','Permisos','UsuarioRol','RolPermiso','Auditoria','IntentosLogin']; foreach($tables as $t){$n=(int)$pdo->query("SELECT COUNT(*) FROM sys.tables WHERE name='$t'")->fetchColumn(); echo sprintf("[%s] Tabla %s\n",$n?'OK':'FALLO',$t);} } catch(Throwable $e){echo "[FALLO] ".$e->getMessage()."\n"; exit(1);} 
