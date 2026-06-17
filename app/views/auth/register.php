<h1>
    Create Account
</h1>
<form action="<?= APP_URL . "auth/register" ?>" method="post">
    <label for="fullname" class="field">
        <span>Fullname: </span>
        <input type="text" name="fullname" id="fullname">
    </label>
    <label for="username" class="field">
        <span>Username: </span>
        <input type="text" name="username" id="username">
    </label>
    <label for="email" class="field">
        <span>Email: </span>
        <input type="text" name="email" id="email">
    </label>
    <label for="gender" class="field">
        <span>Gender: </span>
        <input type="radio" name="gender" value="male" id="">
        <input type="radio" name="gender" value="female" id="">
    </label>
    <label for="phone" class="field">
        <span>Phone No.: </span>
        <input type="text" name="phone" id="">
    </label>
    <label for="password" class="field">
        <span>Password: </span>
        <input type="password" name="password" id="">
    </label>
    <label for="licence" class="field">
        <input type="file" name="licence" id="">
    </label>
    <button type="reset" name="reset">Reset</button>
    <button type="submit" name="submit">Submit</button>
</form>