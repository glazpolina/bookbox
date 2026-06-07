<h2>Мой профиль</h2>
<div id="profile-info" style="background: rgb(42, 42, 42); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
</div>

<h3>Мои отзывы и оценки</h3>
<div id="my-reviews" class="reviews-section">
</div>

<script>
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || 'null');

    if (!token || !user) {
        window.location.href = '/bookbox/login';
    }

    document.getElementById('profile-info').innerHTML = `
        <p><strong>Логин:</strong> ${escapeHtml(user.username)}</p>
        <p><strong>Роль:</strong> ${user.role === 'admin' ? 'Администратор' : 'Пользователь'}</p>
            `;

    async function loadMyReviews() {
        const response = await fetch('/bookbox/api/books');
        const books = await response.json();

        const allReviews = [];
        for (const book of books) {
            const reviewsRes = await fetch(`/bookbox/api/books/${book.id}/reviews`);
            const reviews = await reviewsRes.json();
            const myReviews = reviews.filter(r => r.username === user.username);

            for (const review of myReviews) {
                allReviews.push({
                    ...review,
                    book_title: book.title,
                    book_id: book.id,
                    book_cover: book.cover_image
                });
            }
        }

        const container = document.getElementById('my-reviews');

        if (allReviews.length === 0) {
            container.innerHTML = `
            <div style="background: #2a2a2a; padding: 2rem; border-radius: 12px; text-align: center; color: #888;">
                Вы ещё не оставили ни одного отзыва.<br>
                Перейдите на страницу книги, чтобы оценить её.
            </div>
        `;
            return;
        }

        allReviews.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        container.innerHTML = allReviews.map(review => `
        <div class="review" style="cursor: pointer;" onclick="location.href='/bookbox/book/${review.book_id}'">
            <div class="review-header">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 70px; background: rgb(58, 58, 58); border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        ${review.book_cover ? 
                            `<img src="/bookbox/public/${review.book_cover}" style="width:100%; height:100%; object-fit:cover;">` : 
                            ' '
                        }
                    </div>
                    <div>
                        <strong style="font-size: 1.1rem;">${escapeHtml(review.book_title)}</strong>
                        <div class="review-rating" style="margin-top: 5px;">${renderStars(review.rating)} (${review.rating}/10)</div>
                    </div>
                </div>
            </div>
            <div class="review-text" style="margin-top: 12px;">${escapeHtml(review.review_text)}</div>
            <div class="review-date" style="margin-top: 8px;">${new Date(review.created_at).toLocaleDateString()}</div>
        </div>
    `).join('');
    }

    function renderStars(rating) {
        const full = Math.floor(rating);
        let stars = '';
        for (let i = 0; i < full; i++) stars += '&#9733;';
        for (let i = stars.length; i < 10; i++) stars += '&#9734;';
        return stars;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    loadMyReviews();
</script>