<div class="form-container" style="max-width: 400px; margin: 0 auto;">
    <h2>Вход</h2>
    <form id="loginForm">
        <div class="form-group">
            <label>Логин</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Войти</button>
    </form>
    <p style="margin-top: 1rem;">Нет аккаунта? <a href="/bookbox/register">Зарегистрироваться</a></p>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.querySelector('[name="username"]').value;
        const password = document.querySelector('[name="password"]').value;

        console.log('Отправка запроса:', {
            username,
            password
        });

        try {
            const response = await fetch('/bookbox/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    username,
                    password
                })
            });

            console.log('Статус ответа:', response.status);
            const result = await response.json();
            console.log('Результат:', result);

            if (response.ok) {
                localStorage.setItem('token', result.token);
                localStorage.setItem('user', JSON.stringify(result.user));
                window.location.href = '/bookbox/';
            } else {
                alert(result.errors ? result.errors[0] : 'Ошибка входа');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('Ошибка соединения с сервером');
        }
    });
</script>