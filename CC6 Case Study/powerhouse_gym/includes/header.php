<?php
$flash = take_flash();
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? 'Members') ?> | PowerHouse Gym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="site-header">
        <h1>PowerHouse Gym</h1>
        <p class="p-header">GYM &amp; FITNESS STUDIO · MEMBER MANAGEMENT</p>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php"<?= $current !== 'create.php' ? ' class="active"' : '' ?>>Members</a></li>
                <li><a href="create.php"<?= $current === 'create.php' ? ' class="active"' : '' ?>>Add Member</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if ($flash): ?>
        <div class="msg <?= h($flash[0]) ?>" role="alert"><?= h($flash[1]) ?></div>
        <?php endif; ?>
