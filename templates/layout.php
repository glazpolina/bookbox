<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BookBoxd' ?> BookBox</title>
    <link rel="stylesheet" href="/bookbox/public/css/style.css">
</head>

<body>
    <header>
        <nav>
            <a href="/bookbox/" class="logo">BookBox</a>
            <div class="nav-links">
                <a href="/bookbox/">Главная</a>
                <a href="/bookbox/admin/books" id="adminLink" style="display:none">Админка</a>
                <a href="/bookbox/profile" id="profileLink" style="display:none">Профиль</a>
                <a href="/bookbox/login" id="loginLink">Вход</a>
                <a href="#" id="logoutLink" style="display:none">Выйти</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div id="flash-messages"></div>
        <?php
        if (isset($view) && file_exists(__DIR__ . "/{$view}.php")) {
            include __DIR__ . "/{$view}.php";
        } else {
            echo "<!-- View not found: " . ($view ?? 'null') . " -->";
        }
        ?>
    </div>

    <script src="/bookbox/public/js/app.js"></script>
    <script>
        function updateNav() {
            const token = localStorage.getItem('token');
            const user = JSON.parse(localStorage.getItem('user') || 'null');

            const loginLink = document.getElementById('loginLink');
            const profileLink = document.getElementById('profileLink');
            const logoutLink = document.getElementById('logoutLink');
            const adminLink = document.getElementById('adminLink');

            console.log('Token:', token ? 'есть' : 'нет');
            console.log('User:', user);

            if (token && user) {
                if (loginLink) loginLink.style.display = 'none';
                if (profileLink) profileLink.style.display = 'inline';
                if (logoutLink) logoutLink.style.display = 'inline';
                if (adminLink && user.role === 'admin') adminLink.style.display = 'inline';
                console.log('Пользователь авторизован, роль:', user.role);
            } else {
                if (loginLink) loginLink.style.display = 'inline';
                if (profileLink) profileLink.style.display = 'none';
                if (logoutLink) logoutLink.style.display = 'none';
                if (adminLink) adminLink.style.display = 'none';
                console.log('Пользователь не авторизован');
            }
        }

        updateNav();

        window.addEventListener('storage', updateNav);

        const logoutBtn = document.getElementById('logoutLink');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                updateNav();
                window.location.href = '/bookbox/';
            });
        }
    </script>
</body>

</html>