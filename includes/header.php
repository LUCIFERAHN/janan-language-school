<!-- header.php -->
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل دانشجو</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="#">پنل دانشجو</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="تغییر وضعیت منو">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                            <a class="nav-link" href="../index.php" onclick="return confirmNavigation('آیا مطمئن هستید که می‌خواهید به صفحه اصلی بروید؟')">🏠 صفحه اصلی</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php" onclick="return confirmNavigation('آیا مطمئن هستید که می‌خواهید خارج شوید؟')">🚪 خروج</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
