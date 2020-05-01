<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?= __DIR__ . '/../styling/main.css' ?>">

    <title>Leaderboard</title>
</head>
<body style="width: 25%; text-align: center;">
    <form method="POST">
        <input type="submit" value="Simulate Game" style="width: 100%;">
    </form>
    <h2>
       Player Leader Board
    </h2>
    <?php echo $output ?>
</body>
</html>
