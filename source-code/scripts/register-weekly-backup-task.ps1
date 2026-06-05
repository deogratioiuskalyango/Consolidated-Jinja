param(
    [string]$TaskName = "JCP Weekly Encrypted GitHub Backup",
    [string]$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path,
    [string]$DayOfWeek = "Sunday",
    [string]$At = "02:00"
)

$ErrorActionPreference = "Stop"

$backupScript = Join-Path $ProjectRoot "scripts\backup-to-github-release.ps1"
if (!(Test-Path -LiteralPath $backupScript)) {
    throw "Backup script not found at $backupScript"
}

$action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$backupScript`""
$trigger = New-ScheduledTaskTrigger -Weekly -DaysOfWeek $DayOfWeek -At $At
$settings = New-ScheduledTaskSettingsSet -StartWhenAvailable -MultipleInstances IgnoreNew

Register-ScheduledTask -TaskName $TaskName -Action $action -Trigger $trigger -Settings $settings -Description "Creates an encrypted JCP database/file backup and uploads it to GitHub Releases." -Force

Write-Host "Registered weekly backup task '$TaskName' for $DayOfWeek at $At."
