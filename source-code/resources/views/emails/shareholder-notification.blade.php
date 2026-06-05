<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $notifTitle }}</title>
<style>
  body { margin:0; padding:0; background:#f4f6f9; font-family: 'Segoe UI', Arial, sans-serif; color:#1a1a2e; }
  .wrapper { max-width:600px; margin:32px auto; }
  .header  { background:#1d4ed8; border-radius:12px 12px 0 0; padding:32px 40px; text-align:center; }
  .header img { max-height:48px; margin-bottom:12px; }
  .header h1  { color:#fff; font-size:20px; margin:0; font-weight:700; }
  .body    { background:#ffffff; padding:36px 40px; }
  .badge   { display:inline-block; padding:4px 14px; border-radius:20px; font-size:12px;
             font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:18px; }
  .badge-meeting  { background:#dbeafe; color:#1d4ed8; }
  .badge-approval { background:#fef9c3; color:#854d0e; }
  .badge-dividend { background:#dcfce7; color:#15803d; }
  .badge-vote     { background:#ede9fe; color:#6d28d9; }
  .badge-document { background:#f1f5f9; color:#475569; }
  .badge-general  { background:#f1f5f9; color:#475569; }
  h2 { font-size:22px; margin:0 0 12px; color:#0f172a; }
  p  { font-size:15px; line-height:1.7; color:#374151; margin:0 0 20px; }
  .cta { text-align:center; margin:28px 0; }
  .cta a { display:inline-block; background:#1d4ed8; color:#fff!important;
            text-decoration:none; padding:14px 36px; border-radius:8px;
            font-size:15px; font-weight:600; }
  .cta a:hover { background:#1e40af; }
  .divider { border:none; border-top:1px solid #e5e7eb; margin:24px 0; }
  .login-hint { background:#f8fafc; border-left:4px solid #1d4ed8; border-radius:4px;
                padding:14px 18px; margin-bottom:24px; }
  .login-hint p { margin:0; font-size:13px; color:#374151; }
  .login-hint a { color:#1d4ed8; font-weight:600; }
  .footer { background:#f4f6f9; border-radius:0 0 12px 12px; padding:20px 40px;
            text-align:center; font-size:12px; color:#9ca3af; }
  .footer a { color:#6b7280; }
  @media (max-width:600px) {
    .header, .body, .footer { padding:24px 20px !important; }
    h2 { font-size:18px; }
  }
</style>
</head>
<body>
<div class="wrapper">

  {{-- Header --}}
  <div class="header">
    @php $logo = getSettingImage('app_logo'); @endphp
    @if($logo)
    <img src="{{ $logo }}" alt="{{ getOption('app_name') }}">
    @else
    <h1 style="font-size:24px; letter-spacing:-.5px;">{{ getOption('app_name') }}</h1>
    @endif
  </div>

  {{-- Body --}}
  <div class="body">

    {{-- Type badge --}}
    @php
      $badgeClass = match($notifType) {
        'meeting'          => 'badge-meeting',
        'approval_request' => 'badge-approval',
        'dividend'         => 'badge-dividend',
        'new_resolution',
        'vote_reminder'    => 'badge-vote',
        'document'         => 'badge-document',
        default            => 'badge-general',
      };
      $badgeLabel = match($notifType) {
        'meeting'          => 'Meeting Notification',
        'approval_request' => 'Approval Required',
        'dividend'         => 'Dividend',
        'new_resolution'   => 'Resolution & Voting',
        'vote_reminder'    => 'Vote Reminder',
        'document'         => 'Document',
        default            => 'Notification',
      };
    @endphp
    <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>

    @if($recipientName)
    <p>Dear <strong>{{ $recipientName }}</strong>,</p>
    @endif

    <h2>{{ $notifTitle }}</h2>
    <p>{{ $notifMessage }}</p>

    {{-- CTA button --}}
    @if($actionUrl)
    <div class="cta">
      <a href="{{ $actionUrl }}">{{ $actionLabel }} &rarr;</a>
    </div>
    @endif

    <hr class="divider">

    {{-- Login hint --}}
    <div class="login-hint">
      <p>
        <strong>How to access:</strong> Log into the <a href="{{ url('/login') }}">shareholder portal</a>
        and navigate to the relevant section. Once logged in, you can take any required action directly.
      </p>
    </div>

    <p style="font-size:13px; color:#9ca3af; margin:0;">
      This is an automated notification from <strong>{{ getOption('app_name') }}</strong>.
      Please do not reply to this email.
    </p>
  </div>

  {{-- Footer --}}
  <div class="footer">
    <p style="margin:0 0 4px;">&copy; {{ date('Y') }} {{ getOption('app_name') }}. All rights reserved.</p>
    <p style="margin:0;">
      <a href="{{ url('/login') }}">Login to Portal</a>
    </p>
  </div>

</div>
</body>
</html>
