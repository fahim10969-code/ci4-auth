<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>
    <h1>Login</h1>

    <?php if (isset($validation)) : ?>
        <div><?= $validation ?></div>
    <?php endif; ?>

    <form action="<?= base_url('login/process') ?>" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>

        <input type="submit" value="Login">
    </form>
</body>

</html>