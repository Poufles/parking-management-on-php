<h1>Login</h1>

<form action="<?= APP_URL . "auth/login" ?>" method="post">
    <label for="" class="field">
        <span>Username: </span>
        <input type="text" name="username" id="">
    </label>
    <label for="" class="field">
        <span>Password: </span>
        <input type="text" name="password" id="">
    </label>
    <button type="submit" name="login">Log in</button>
</form>