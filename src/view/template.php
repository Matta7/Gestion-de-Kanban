    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="skin/screen.css">
    <script src="script/script.js"></script>
</head>
<body>
    <?php echo "<p>$feedback</p>"?>
    <div id="menu"><?php echo $menu?></div>
    <div id="content"><?php echo $content?></div>
    <script type="text/javascript">dragTasks()</script>
</body>
</html>