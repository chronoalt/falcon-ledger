<!DOCTYPE html>
<html>
    <head>
        <title>@yield("title")</title>
        <link rel="stylesheet" href="{{ asset("css/app.css") }}">
    </head>
    <body>
        <h1>This is dashboard page!</h1>
        <p>Welcome {{ Auth::user()->email }}</p>

        <form action="{{ route("logout") }}" method="POST">
            @csrf
            <button>Logout</button>
        </form>

        <div>
            @yield("content")
        </div>
    </body>
</html>