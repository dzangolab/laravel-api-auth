<html lang="en">
<body>
    <h1>Hi {{ $user->username }},</h1>

    <p>Please <a href="{{ $url }}"><u>click here</u></a> to reset your demos account password.</p>

    <p>If you are not able to click on the link, please copy and paste:</p>

    <p>{{ $url }} in your browser.</p>

    <p>If you did not request to reset your password, you can safely ignore this email.</p>
</body>
</html>
