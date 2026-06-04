<div class="hero">
    <h1>BookBox</h1>
    <p>Твой личный трекер книг. Оценивай, рецензируй, открывай новое.</p>
</div>

<h2>Популярные книги</h2>
<div id="debug" style="background:#333; padding:10px; margin-bottom:20px; border-radius:8px;"></div>
<div class="books-grid" id="books-grid"></div>

<script>
    (async () => {
        const debug = document.getElementById('debug');
        debug.innerText = 'Загружаем книги...';

        try {
            const response = await fetch('/bookbox/api/books');
            debug.innerText = 'Статус ответа: ' + response.status;

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const books = await response.json();
            debug.innerText = 'Получено книг: ' + books.length;

            const grid = document.getElementById('books-grid');
            if (!books.length) {
                grid.innerHTML = '<p>Нет книг в базе данных</p>';
                return;
            }

            grid.innerHTML = books.map(book => `
            <div class="book-card" onclick="location.href='/bookbox/book/${book.id}'">
                <div class="book-cover" style="background-color: #3a3a3a; min-height: 250px; display: flex; align-items: center; justify-content: center;">
                    ${book.cover_image ? 
                        `<img src="/bookbox/public/${book.cover_image}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px 8px 0 0;">` : 
                        ' '
                    }
                </div>
                <div class="book-info">
                    <div class="book-title">${escapeHtml(book.title)}</div>
                    <div class="book-author">${escapeHtml(book.author)}</div>
                    <div class="book-rating">&#9733;${book.calculated_rating || 0}</div>
                </div>
            </div>
        `).join('');

        } catch (err) {
            debug.innerText = 'Ошибка: ' + err.message;
            console.error(err);
        }
    })();

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, m => m === '&' ? '&amp;' : m === '<' ? '&lt;' : '&gt;');
    }
</script>