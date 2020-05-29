<html lang="en">
<body>
<h1>Hi {{ $user->username }},</h1>

<p>It seems someone tried to signup with email address associated with your account.</p>

<p>If it is you and you have forgotten about the existing account, you can request to reset your password <a href="{{ $url }}"><u>here</u></a>.</p>
</body>
</html>
