<h1>This is dashboard page!</h1>
<form action="{{ route("logout") }}" method="POST">
    @csrf
    <button>Logout</button>
</form>