<div id="book-detail"></div>
<div class="reviews-section">
    <h3>Отзывы</h3>
    <div id="reviews-list"></div>
    <div id="add-review-form" style="display:none;">
        <h4>Оставить отзыв</h4>
        <form id="reviewForm">
            <div class="form-group">
                <label>Оценка (1-10)</label>
                <input type="number" name="rating" min="1" max="10" required>
            </div>
            <div class="form-group">
                <label>Текст отзыва</label>
                <textarea name="review_text" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Отправить</button>
        </form>
    </div>
</div>

<script>
    const bookId = <?php echo isset($bookId) ? json_encode($bookId) : 'null'; ?>;

    if (!bookId) {
        console.error('Book ID not set');
        document.getElementById('book-detail').innerHTML = '<p>Ошибка: книга не найдена</p>';
    }

    const user = JSON.parse(localStorage.getItem('user') || 'null');

    if (user) {
        const addForm = document.getElementById('add-review-form');
        if (addForm) addForm.style.display = 'block';
    }

    async function loadBook() {
        const response = await fetch(`/bookbox/api/books/${bookId}`);
        if (!response.ok) {
            document.getElementById('book-detail').innerHTML = '<p>Книга не найдена</p>';
            return;
        }
        const book = await response.json();

        document.getElementById('book-detail').innerHTML = `
            <div class="book-detail">
                <div class="book-detail-cover" style="background: rgb(58, 58, 58); min-height:400px; display:flex; align-items:center; justify-content:center;">
    ${book.cover_image ? 
        `<img src="/bookbox/public/${book.cover_image}" style="width:100%; border-radius:12px; object-fit:cover;">` : 
        ' '
    }
</div>
                <div class="book-detail-info">
                    <h1>${escapeHtml(book.title)}</h1>
                    <div class="book-detail-author">${escapeHtml(book.author)}</div>
                    <div class="book-detail-year">${book.year || 'Год не указан'}</div>
                    <div class="book-detail-description">${escapeHtml(book.description || 'Нет описания')}</div>
                    <div class="book-rating">Средний рейтинг: ${renderStars(book.calculated_rating)} (${parseFloat(book.calculated_rating).toFixed(2)})</div>
                </div>
            </div>
        `;
    }

    async function loadReviews() {
        const response = await fetch(`/bookbox/api/books/${bookId}/reviews`);
        const reviews = await response.json();

        if (reviews.length === 0) {
            document.getElementById('reviews-list').innerHTML = '<p>Пока нет отзывов.</p>';
            return;
        }
        const currentUser = JSON.parse(localStorage.getItem('user') || 'null');
        document.getElementById('reviews-list').innerHTML = reviews.map(review => `
            <div class="review">
                <div class="review-header">
                    <span><strong>${escapeHtml(review.username)}</strong></span>
                    <span class="review-rating">${renderStars(review.rating)} (${review.rating})</span>
                </div>
                <div class="review-text">${escapeHtml(review.review_text)}</div>
                <div class="review-date">${new Date(review.created_at).toLocaleDateString()}</div>
                ${(currentUser && (currentUser.username === review.username || currentUser.role === 'admin')) ? `
                <button class="btn btn-danger btn-sm" onclick="deleteReview(${review.id})" style="margin-top: 10px; padding: 5px 10px; font-size: 12px;">Удалить отзыв</button>
            ` : ''}
            </div>

        `).join('');
    }
    async function deleteReview(reviewId) {
        if (!confirm('Удалить этот отзыв?')) return;

        const token = localStorage.getItem('token');
        if (!token) {
            alert('Необходимо войти');
            return;
        }

        const response = await fetch(`/bookbox/api/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });

        if (response.ok) {
            //document.getElementById(`review-${reviewId}`)?.remove();
            loadReviews(); //
            loadBook();
            const remainingReviews = document.querySelectorAll('.review').length;
            if (remainingReviews === 0) {
                document.getElementById('reviews-list').innerHTML = '<p>Пока нет отзывов.</p>';
            }
        } else {
            const error = await response.json();
            alert('Ошибка удаления: ' + (error.error || 'Неизвестная ошибка'));
        }
    }

    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = {
                book_id: bookId,
                rating: parseInt(formData.get('rating')),
                review_text: formData.get('review_text')
            };

            const token = localStorage.getItem('token');
            if (!token) {
                alert('Необходимо войти');
                window.location.href = '/bookbox/login';
                return;
            }

            const response = await fetch('/bookbox/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Отзыв добавлен');
                e.target.reset();
                loadReviews();
                loadBook();
            } else {
                const error = await response.json();
                alert(error.errors ? error.errors[0] : 'Ошибка');
            }
        });
    }

    function renderStars(rating) {
        const full = Math.floor(rating);
        let stars = '';
        for (let i = 0; i < full; i++) stars += '&#9733;';
        for (let i = stars.length; i < 5; i++) stars += '&#9734;';
        return stars;
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

    if (bookId) {
        loadBook();
        loadReviews();
    }
</script>