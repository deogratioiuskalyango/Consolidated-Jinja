<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2>Welcome to the Shareholder Portal</h2>
    <p>Dear {{ $user->first_name }},</p>
    <p>Your shareholder account has been created. Here are your login credentials:</p>
    <table style="width:100%; border-collapse: collapse;">
        <tr><td style="padding:8px; font-weight:bold;">Email:</td><td style="padding:8px;">{{ $user->email }}</td></tr>
        <tr><td style="padding:8px; font-weight:bold;">Password:</td><td style="padding:8px; font-family: monospace; background:#f0f0f0;">{{ $password }}</td></tr>
    </table>
    <p><strong>Important:</strong> You will be required to change your password on first login.</p>
    <p>Login at: <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
    <hr>
    <p style="color:#888; font-size:12px;">This is an automated message. Please do not reply.</p>
</body>
</html>
