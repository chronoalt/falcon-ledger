<h2>Register</h2>
<form action="" method="">
    @csrf 

    <label for="email">Email:</label>
    <input type="email" name="email" required />

    <label for="password">Password:</label>
    <input type="password" name="password" required />

    <label for="confirm-password">Confirm Password:</label>
    <input type="password" name="confirm-password" required />

    <button type="submit">Register</button>
</form>