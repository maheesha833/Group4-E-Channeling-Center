<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle . ' - E-Channeling Center' : 'E-Channeling Center'; ?></title>
<link rel="stylesheet" href="<?php echo isset($basePath) ? $basePath : ''; ?>css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php" class="logo">
            E-Channeling<span>Center</span>
        </a>
        <nav class="main-nav">
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php">Home</a>
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>admin/login.php">Admin Login</a>
        </nav>
    </div>
</header>
