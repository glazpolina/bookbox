<h2>Управление книгами</h2>
<button class="btn btn-primary" onclick="showAddForm()">+ Добавить книгу</button>

<div class="books-grid" id="books-grid" style="margin-top: 2rem;"></div>

<div id="modal" class="modal">
    <div class="modal-content">
        <h3 id="modal-title">Добавить книгу</h3>
        <form id="bookForm">
            <input type="hidden" name="id" id="bookId">
            <input type="hidden" name="cover_image" id="cover_image">
            <div class="form-group">
                <label>Название</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Автор</label>
                <input type="text" name="author" required>
            </div>
            <div class="form-group">
                <label>Год</label>
                <input type="number" name="year">
            </div>
            <div class="form-group">
                <label>Описание</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Обложка (загрузить файл)</label>
                <input type="file" name="cover_file" id="coverFile" accept="image/*">
                <small style="color: #888;">Поддерживаются JPG, PNG, WEBP, GIF (макс 2MB)</small>
            </div>
            <div class="form-group" id="currentCoverGroup" style="display:none;">
                <label>Текущая обложка</label>
                <img id="currentCover" style="max-width: 100px; border-radius: 4px;">
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="button" class="btn btn-outline" onclick="closeModal()">Отмена</button>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || 'null');

    if (!token || user?.role !== 'admin') {
        window.location.href = '/bookbox/';
    }

    async function loadBooks() {
        const response = await fetch('/bookbox/api/books');
        const books = await response.json();

        const grid = document.getElementById('books-grid');
        grid.innerHTML = books.map(book => `
            <div class="book-card">
                <div class="book-cover" style="background-color: #3a3a3a; min-height: 250px; display: flex; align-items: center; justify-content: center;">
    ${book.cover_image ? 
        `<img src="/bookbox/public/${book.cover_image}" style="width: 100%; height: 100%; object-fit: cover;">` : 
        ' '
    }
</div>
                <div class="book-info">
                    <div class="book-title">${escapeHtml(book.title)}</div>
                    <div class="book-author">${escapeHtml(book.author)}</div>
                    <div style="margin-top: 0.5rem;">
                        <button class="btn btn-outline" onclick="editBook(${book.id})">✏️</button>
                        <button class="btn btn-danger" onclick="deleteBook(${book.id})">🗑️</button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function showAddForm() {
        document.getElementById('modal-title').textContent = 'Добавить книгу';
        document.getElementById('bookForm').reset();
        document.getElementById('bookId').value = '';
        document.getElementById('cover_image').value = '';
        document.getElementById('currentCoverGroup').style.display = 'none';
        document.getElementById('coverFile').value = '';
        document.getElementById('modal').style.display = 'flex';
    }

    async function editBook(id) {
        const response = await fetch(`/bookbox/api/books/${id}`);
        const book = await response.json();

        document.getElementById('modal-title').textContent = 'Редактировать книгу';
        document.getElementById('bookId').value = book.id;
        document.querySelector('[name="title"]').value = book.title || '';
        document.querySelector('[name="author"]').value = book.author || '';
        document.querySelector('[name="year"]').value = book.year || '';
        document.querySelector('[name="description"]').value = book.description || '';
        document.getElementById('cover_image').value = book.cover_image || '';

        // Показываем текущую обложку, если есть
        const currentCoverGroup = document.getElementById('currentCoverGroup');
        const currentCover = document.getElementById('currentCover');
        if (book.cover_image) {
            currentCover.src = '/bookbox/public/' + book.cover_image;
            currentCoverGroup.style.display = 'block';
        } else {
            currentCoverGroup.style.display = 'none';
        }

        document.getElementById('coverFile').value = '';

        document.getElementById('modal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }




    document.getElementById('bookForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('bookId').value;
        const data = {
            title: document.querySelector('[name="title"]').value,
            author: document.querySelector('[name="author"]').value,
            year: parseInt(document.querySelector('[name="year"]').value) || null,
            description: document.querySelector('[name="description"]').value,
            cover_image: document.querySelector('[name="cover_image"]').value
        };

        const url = id ? `/bookbox/api/books/${id}` : '/bookbox/api/books';
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            const error = await response.json();
            alert(error.errors ? error.errors[0] : 'Ошибка сохранения книги');
            return;
        }

        let bookId = id;
        if (!id) {
            const result = await response.json();
            bookId = result.id;
        }

        const fileInput = document.getElementById('coverFile');
        if (fileInput && fileInput.files.length > 0) {
            const formData = new FormData();
            formData.append('cover', fileInput.files[0]);

            const uploadResponse = await fetch(`/bookbox/index.php?route=api/books/${bookId}/upload`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                body: formData
            });

            if (!uploadResponse.ok) {
                const error = await uploadResponse.json();
                alert('Книга сохранена, но ошибка загрузки обложки: ' + (error.error || 'Неизвестная ошибка'));
            }
        }

        closeModal();
        loadBooks();
    });





    async function deleteBook(id) {
        if (!confirm('Удалить книгу?')) return;

        const response = await fetch(`/bookbox/api/books/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });

        if (response.ok) {
            loadBooks();
        } else {
            alert('Ошибка удаления');
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    loadBooks();
</script>