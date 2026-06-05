param(
    [string]$TunnelName = "jcp-xampp",
    [string]$Domain = "jinjaconsolidated.com",
    [string]$CloudflaredPath = "C:\tmp\cloudflared\cloudflared.exe",
    [string]$OriginService = "http://localhost"
)

$ErrorActionPreference = "Stop"

if (!(Test-Path -LiteralPath $CloudflaredPath)) {
    throw "cloudflared was not found at $CloudflaredPath"
}

$cloudflaredDir = Join-Path $env:USERPROFILE ".cloudflared"
$certPath = Join-Path $cloudflaredDir "cert.pem"

if (!(Test-Path -LiteralPath $certPath)) {
    throw "Cloudflare login is not complete. Run: $CloudflaredPath tunnel login"
}

function Get-TunnelByName {
    param([string]$Name)

    try {
        $existing = & $CloudflaredPath tunnel list --output json 2>$null | ConvertFrom-Json
        $match = $existing | Where-Object { $_.name -eq $Name } | Select-Object -First 1
        if ($null -ne $match) {
            return [pscustomobject]@{ id = $match.id; name = $match.name }
        }
    }
    catch {
        Write-Warning "Unable to list tunnels through the Cloudflare API. Trying tunnel info fallback."
    }

    try {
        $info = & $CloudflaredPath tunnel info $Name 2>$null
        $idLine = $info | Where-Object { $_ -match "^ID:\s+" } | Select-Object -First 1
        if ($idLine -match "^ID:\s+(.+)$") {
            return [pscustomobject]@{ id = $Matches[1].Trim(); name = $Name }
        }
    }
    catch {
        return $null
    }

    return $null
}

$tunnel = Get-TunnelByName -Name $TunnelName

if ($null -eq $tunnel) {
    & $CloudflaredPath tunnel create $TunnelName
    if ($LASTEXITCODE -ne 0) {
        throw "Unable to create Cloudflare tunnel '$TunnelName'."
    }

    $tunnel = Get-TunnelByName -Name $TunnelName
}

if ($null -eq $tunnel) {
    $credentialFile = Get-ChildItem -LiteralPath $cloudflaredDir -Filter "*.json" |
        Sort-Object LastWriteTime -Descending |
        Select-Object -First 1

    if ($null -eq $credentialFile) {
        throw "Tunnel '$TunnelName' was not found after creation and no credentials JSON file was found."
    }

    $tunnel = [pscustomobject]@{
        id   = [System.IO.Path]::GetFileNameWithoutExtension($credentialFile.Name)
        name = $TunnelName
    }
}

$tunnelId = $tunnel.id
$credentialsFile = Join-Path $cloudflaredDir "$tunnelId.json"

$config = @"
tunnel: $tunnelId
credentials-file: $credentialsFile

ingress:
  - hostname: $Domain
    service: $OriginService
  - hostname: www.$Domain
    service: $OriginService
  - service: http_status:404
"@

New-Item -ItemType Directory -Force -Path $cloudflaredDir | Out-Null
Set-Content -LiteralPath (Join-Path $cloudflaredDir "config.yml") -Value $config -Encoding UTF8

& $CloudflaredPath tunnel route dns --overwrite-dns $TunnelName $Domain
if ($LASTEXITCODE -ne 0) {
    Write-Warning "Could not route $Domain automatically. Delete the existing DNS record in Cloudflare DNS, then rerun this script."
}

& $CloudflaredPath tunnel route dns --overwrite-dns $TunnelName "www.$Domain"
if ($LASTEXITCODE -ne 0) {
    Write-Warning "Could not route www.$Domain automatically. Delete the existing DNS record in Cloudflare DNS, then rerun this script."
}

Write-Host "Cloudflare tunnel '$TunnelName' is configured for $Domain and www.$Domain."
Write-Host "Test it with: $CloudflaredPath tunnel run $TunnelName"
