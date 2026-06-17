<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../index.css">
    <title>Test</title>
</head>

<body>
    <nav>
        <a href="../">Index</a>
        <a href="./account/accountEdit.php">Edit Account</a>
        <a href="./account/accountDelete.php">Delete Account</a>
    </nav>
    <main>
        <h1>
            Create Account
        </h1>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
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
                <input type="radio" name="gender" value="Male" id="">
                <input type="radio" name="gender" value="Female" id="">
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
            <button type="reset">Reset</button>
            <button type="submit">Submit</button>
        </form>
    </main>
</body>

</html>