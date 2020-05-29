<html lang="en">
<body>
    <h1>Hi {{ $user->username }},</h1>

    <h2>Welcome to the app</h2>

    <p>Please <a href="{{ $url }}"><u>click here</u></a> to enable your account.</p>

    <p>If you are not able to click on the link, please copy and paste:</p>

    <p>{{ $url }} in your browser.</p>
</body>
</html>
