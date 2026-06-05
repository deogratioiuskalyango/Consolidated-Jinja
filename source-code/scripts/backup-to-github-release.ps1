param(
    [string]$Repo = "deogratioiuskalyango/JCP-rental-management-system",
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path,
    [string]$PhpPath = "C:\xampp\php\php.exe",
    [string]$MysqldumpPath = "C:\xampp\mysql\bin\mysqldump.exe",
    [string]$BackupDir = (Join-Path (Resolve-Path (Join-Path $PSScriptRoot "..")).Path "storage\app\backups"),
    [switch]$SkipGitHubUpload
)

$ErrorActionPreference = "Stop"

function Read-DotEnv {
    param([string]$Path)

    $values = @{}
    if (!(Test-Path -LiteralPath $Path)) {
        throw "Missing .env file at $Path"
    }

    Get-Content -LiteralPath $Path | ForEach-Object {
        $line = $_.Trim()
        if ($line.Length -eq 0 -or $line.StartsWith("#") -or !$line.Contains("=")) {
            return
        }

        $name, $value = $line.Split("=", 2)
        $value = $value.Trim()
        if (($value.StartsWith('"') -and $value.EndsWith('"')) -or ($value.StartsWith("'") -and $value.EndsWith("'"))) {
            $value = $value.Substring(1, $value.Length - 2)
        }
        $values[$name.Trim()] = $value
    }

    return $values
}

function Invoke-GitHubApi {
    param(
        [string]$Method,
        [string]$Uri,
        [object]$Body = $null,
        [string]$ContentType = "application/json"
    )

    $headers = @{
        Authorization          = "Bearer $env:GITHUB_TOKEN"
        Accept                 = "application/vnd.github+json"
        "X-GitHub-Api-Version" = "2022-11-28"
        "User-Agent"           = "jcp-backup-script"
    }

    if ($null -eq $Body) {
        return Invoke-RestMethod -Method $Method -Uri $Uri -Headers $headers
    }

    return Invoke-RestMethod -Method $Method -Uri $Uri -Headers $headers -Body $Body -ContentType $ContentType
}

if (!$SkipGitHubUpload -and [string]::IsNullOrWhiteSpace($env:GITHUB_TOKEN)) {
    throw 'Set GITHUB_TOKEN to a GitHub fine-grained token with repo Contents read/write access.'
}

if ([string]::IsNullOrWhiteSpace($env:BACKUP_ENCRYPTION_PASSPHRASE)) {
    throw "Set BACKUP_ENCRYPTION_PASSPHRASE before running. Store it in a password manager."
}

if (!(Test-Path -LiteralPath $PhpPath)) {
    throw "PHP not found at $PhpPath"
}

if (!(Test-Path -LiteralPath $MysqldumpPath)) {
    throw "mysqldump not found at $MysqldumpPath"
}

$envValues = Read-DotEnv -Path (Join-Path $ProjectRoot ".env")
$dbName = $envValues["DB_DATABASE"]
$dbUser = $envValues["DB_USERNAME"]
$dbPass = $envValues["DB_PASSWORD"]
$dbHost = if ($envValues["DB_HOST"]) { $envValues["DB_HOST"] } else { "127.0.0.1" }
$dbPort = if ($envValues["DB_PORT"]) { $envValues["DB_PORT"] } else { "3306" }

if ([string]::IsNullOrWhiteSpace($dbName)) {
    throw "DB_DATABASE is missing in .env"
}

New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null

$timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
$workDir = Join-Path $BackupDir "work-$timestamp"
$releaseTag = "backup-$timestamp"
$dumpPath = Join-Path $workDir "$dbName.sql"
$zipPath = Join-Path $BackupDir "jcp-backup-$timestamp.zip"
$encryptedPath = "$zipPath.enc"

New-Item -ItemType Directory -Force -Path $workDir | Out-Null

try {
    Write-Host "Creating MySQL dump for database '$dbName'..."
    $oldMysqlPwd = $env:MYSQL_PWD
    $env:MYSQL_PWD = $dbPass
    & $MysqldumpPath "--host=$dbHost" "--port=$dbPort" "--user=$dbUser" "--single-transaction" "--quick" "--routines" "--events" "--result-file=$dumpPath" $dbName
    if ($LASTEXITCODE -ne 0) {
        throw "mysqldump failed with exit code $LASTEXITCODE"
    }
    $env:MYSQL_PWD = $oldMysqlPwd

    $itemsToZip = @($dumpPath)
    $publicStorage = Join-Path $ProjectRoot "storage\app\public"
    if (Test-Path -LiteralPath $publicStorage) {
        $itemsToZip += $publicStorage
    }

    Write-Host "Compressing database dump and public storage files..."
    Compress-Archive -LiteralPath $itemsToZip -DestinationPath $zipPath -Force

    Write-Host "Encrypting backup archive..."
    $encryptScript = Join-Path $PSScriptRoot "encrypt-backup.php"
    & $PhpPath $encryptScript $zipPath $encryptedPath
    if ($LASTEXITCODE -ne 0) {
        throw "Encryption failed with exit code $LASTEXITCODE"
    }

    Remove-Item -LiteralPath $zipPath -Force

    if ($SkipGitHubUpload) {
        Write-Host "Encrypted backup created locally: $encryptedPath"
        return
    }

    Write-Host "Creating GitHub Release '$releaseTag'..."
    $releaseBody = @{
        tag_name   = $releaseTag
        name       = "JCP encrypted backup $timestamp"
        body       = "Encrypted weekly backup for JCP Rental Management System. Requires BACKUP_ENCRYPTION_PASSPHRASE to decrypt."
        draft      = $false
        prerelease = $false
    } | ConvertTo-Json

    $release = Invoke-GitHubApi -Method "POST" -Uri "https://api.github.com/repos/$Repo/releases" -Body $releaseBody

    $uploadUrl = ($release.upload_url -replace "\{\?name,label\}", "") + "?name=$(Split-Path $encryptedPath -Leaf)"
    Write-Host "Uploading encrypted backup asset..."
    $headers = @{
        Authorization          = "Bearer $env:GITHUB_TOKEN"
        Accept                 = "application/vnd.github+json"
        "X-GitHub-Api-Version" = "2022-11-28"
        "User-Agent"           = "jcp-backup-script"
    }
    Invoke-RestMethod -Method "POST" -Uri $uploadUrl -Headers $headers -InFile $encryptedPath -ContentType "application/octet-stream" | Out-Null

    Write-Host "Backup uploaded successfully: $($release.html_url)"
}
finally {
    if (Test-Path -LiteralPath $workDir) {
        Remove-Item -LiteralPath $workDir -Recurse -Force
    }
}
