
<!DOCTYPE html>
<html>
<head>
    <title>Our Services</title>
</head>
<body>
    <h1>Available Services</h1>
    <ul>
        <?php foreach ($services as $service): ?>
            <li><?= htmlspecialchars($service['name']) ?> - <?= htmlspecialchars($service['description']) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>