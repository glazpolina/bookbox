<div class="form-container" style="max-width: 400px; margin: 0 auto;">
    <h2>Регистрация</h2>
    <form id="registerForm">
        <div class="form-group">
            <label>Логин (мин. 3 символа)</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Пароль (мин. 4 символа)</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
    </form>
    <p style="margin-top: 1rem;">Уже есть аккаунт? <a href="/bookbox/login">Войти</a></p>
</div>

<script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const username = document.querySelector('[name="username"]').value;
        const password = document.querySelector('[name="password"]').value;

        const response = await fetch('/bookbox/api/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username,
                password
            })
        });

        const result = await response.json();

        if (response.ok) {
            alert('Регистрация успешна. Теперь войдите.');
            window.location.href = '/bookbox/login';
        } else {
            alert(result.errors ? result.errors[0] : 'Ошибка регистрации');
        }
    });
</script>