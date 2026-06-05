# GitHub backup setup

Repository: `deogratioiuskalyango/JCP-rental-management-system`

The script `scripts/backup-to-github-release.ps1` creates:

- a MySQL dump from the Laravel `.env` database settings
- a copy of `storage/app/public`
- a compressed archive
- an encrypted `.enc` file
- a GitHub Release with the encrypted backup attached

## Required secrets on this Windows machine

Set these as user environment variables, not in the repo:

```powershell
[Environment]::SetEnvironmentVariable("GITHUB_TOKEN", "<github-token>", "User")
[Environment]::SetEnvironmentVariable("BACKUP_ENCRYPTION_PASSPHRASE", "<long-random-passphrase>", "User")
```

Create the GitHub token as a fine-grained personal access token for this repo with:

- Repository access: `deogratioiuskalyango/JCP-rental-management-system`
- Contents: Read and write

Open a new PowerShell window after setting the variables.

## Test backup manually

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\backup-to-github-release.ps1
```

## Register weekly schedule

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\register-weekly-backup-task.ps1 -DayOfWeek Sunday -At 02:00
```

## Restore a backup

Download the `.enc` file from GitHub Releases, set `BACKUP_ENCRYPTION_PASSPHRASE`, then run:

```powershell
C:\xampp\php\php.exe .\scripts\decrypt-backup.php .\jcp-backup.enc .\jcp-backup.zip
```

Extract the zip. Import the SQL file into MySQL and copy the public storage files back into `storage/app/public`.
