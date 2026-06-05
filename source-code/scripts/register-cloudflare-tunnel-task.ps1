param(
    [string]$TaskName = "JCP Cloudflare Tunnel",
    [string]$CloudflaredPath = "C:\tmp\cloudflared\cloudflared.exe",
    [string]$ConfigPath = "$env:USERPROFILE\.cloudflared\config.yml",
    [string]$TunnelName = "jcp-xampp"
)

$ErrorActionPreference = "Stop"

if (!(Test-Path -LiteralPath $CloudflaredPath)) {
    throw "cloudflared was not found at $CloudflaredPath"
}

if (!(Test-Path -LiteralPath $ConfigPath)) {
    throw "Cloudflare config was not found at $ConfigPath"
}

$arguments = "tunnel --config `"$ConfigPath`" run $TunnelName"
$action = New-ScheduledTaskAction -Execute $CloudflaredPath -Argument $arguments
$trigger = New-ScheduledTaskTrigger -AtLogOn
$settings = New-ScheduledTaskSettingsSet -StartWhenAvailable -MultipleInstances IgnoreNew -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 1)

Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger $trigger -Settings $settings -Description "Keeps the JCP Cloudflare Tunnel connector running for jinjaconsolidated.com." -Force

Write-Host "Registered '$TaskName'. It will start cloudflared at Windows logon."
