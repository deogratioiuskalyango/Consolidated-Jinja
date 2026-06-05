# Cloudflare Tunnel setup for JCP

This keeps the Laravel/PHP/MySQL app running on the XAMPP machine and exposes it through Cloudflare.

## Recommended local origin

For a clean custom domain, configure Apache so the domain points to Laravel's `public` directory:

```apache
<VirtualHost *:443>
    ServerName jinjaconsolidated.com
    DocumentRoot "C:/xampp/htdocs/kintu/source-code/public"

    <Directory "C:/xampp/htdocs/kintu/source-code/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Then set these values in `.env`:

```dotenv
APP_URL=https://jinjaconsolidated.com
FILESYSTEM_DISK=public
```

## Tunnel commands

`cloudflared` is available locally at:

```powershell
C:\tmp\cloudflared\cloudflared.exe
```

Log in from a normal PowerShell window so the browser authorization can complete:

```powershell
C:\tmp\cloudflared\cloudflared.exe tunnel login
```

After login, create the tunnel/config/DNS route:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\setup-cloudflare-tunnel.ps1
```

The script creates `%USERPROFILE%\.cloudflared\config.yml` like this:

```yaml
tunnel: <TUNNEL-ID>
credentials-file: C:\Users\<YOUR-WINDOWS-USER>\.cloudflared\<TUNNEL-ID>.json

ingress:
  - hostname: jinjaconsolidated.com
    service: http://localhost
  - hostname: www.jinjaconsolidated.com
    service: http://localhost
  - service: http_status:404
```

Run the tunnel manually first:

```powershell
C:\tmp\cloudflared\cloudflared.exe tunnel run jcp-xampp
```

After it works, install it as a Windows service:

```powershell
C:\tmp\cloudflared\cloudflared.exe service install
```

If you do not want to install a Windows service, register a per-user scheduled task instead:

```powershell
powershell -ExecutionPolicy Bypass -File .\scripts\register-cloudflare-tunnel-task.ps1
```

## DNS note

Because `jinjaconsolidated.com` is currently hosted at Hostinger, only switch Cloudflare DNS when you are ready for Cloudflare to send traffic to this XAMPP machine. Keep the Hostinger DNS records documented before changing them.
