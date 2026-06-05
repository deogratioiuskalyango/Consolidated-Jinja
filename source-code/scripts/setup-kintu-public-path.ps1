param(
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path,
    [string]$PublicPath = "/kintu"
)

$ErrorActionPreference = "Stop"

$sourceCode = Resolve-Path $ProjectRoot
$appRoot = Split-Path $sourceCode -Parent
$indexPath = Join-Path $appRoot "index.php"
$htaccessPath = Join-Path $appRoot ".htaccess"

$index = @'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = __DIR__ . '/source-code';
chdir($basePath);

if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $basePath . '/app/Helper/langHelper.php';
require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
'@

$trimmedPath = $PublicPath.Trim('/')
$htaccess = @"
Options -Indexes
DirectoryIndex index.php

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /$trimmedPath/

    RewriteRule ^index\.php$ - [L]

    RewriteCond %{DOCUMENT_ROOT}/$trimmedPath/source-code/public/`$1 -f [OR]
    RewriteCond %{DOCUMENT_ROOT}/$trimmedPath/source-code/public/`$1 -d
    RewriteRule ^(.+)$ source-code/public/`$1 [L]

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>
"@

Set-Content -LiteralPath $indexPath -Value $index -Encoding ASCII
Set-Content -LiteralPath $htaccessPath -Value $htaccess -Encoding ASCII

Write-Host "Configured $appRoot for public path /$trimmedPath/"

$publicStorage = Join-Path $sourceCode "public\storage"
$storageTarget = Join-Path $sourceCode "storage\app\public"
if (Test-Path -LiteralPath $publicStorage) {
    $item = Get-Item -LiteralPath $publicStorage -Force
    $target = @($item.Target) -join ""
    if ($item.LinkType -eq "Junction" -and $target -eq $storageTarget) {
        Write-Host "Public storage is already linked to $storageTarget"
        return
    }
    if ($item.LinkType -ne "Junction") {
        throw "Refusing to replace non-junction path: $publicStorage"
    }
    [System.IO.Directory]::Delete($publicStorage)
}
New-Item -ItemType Junction -Path $publicStorage -Target $storageTarget | Out-Null
Write-Host "Linked public storage to $storageTarget"
