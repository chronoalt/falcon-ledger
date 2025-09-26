<h2>Register</h2>
<form action="{{ route("register") }}" method="POST">
    @csrf 

    <label for="email">Email:</label>
    <input type="email" name="email" required />

    <label for="password">Password:</label>
    <input type="password" name="password" required />

    <label for="password_confirmation">Confirm Password:</label>
    <input type="password" name="password_confirmation" required />

    <button type="submit">Register</button>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</form>