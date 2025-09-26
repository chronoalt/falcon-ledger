<h1>This is dashboard page!</h1>
<p>Welcome {{ Auth::user()->email }}</p>

<form action="{{ route("logout") }}" method="POST">
    @csrf
    <button>Logout</button>
</form>