//<?php
//src/core/Database.php

// class Database
// {
//     private static $connection = null;
//     private static $dbPath = __DIR__ . '/../../database/bookbox.db';

//     public static function getConnection()
//     {
//         if (self::$connection === null) {
//             try {
//                 $dbDir = dirname(self::$dbPath);
//                 if (!file_exists($dbDir)) {
//                     mkdir($dbDir, 0777, true);
//                 }

//                 self::$connection = new PDO('sqlite:' . self::$dbPath);
//                 self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                 self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//                 self::$connection->exec("PRAGMA foreign_keys = ON");

//                 self::initTables();
//             } catch (PDOException $e) {
//                 die("Database error: " . $e->getMessage());
//             }
//         }
//         return self::$connection;
//     }

//     private static function initTables()
//     {
//         $stmt = self::$connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
//         if ($stmt->fetch()) return;

//         $sql = "
//             CREATE TABLE users (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 username TEXT UNIQUE NOT NULL,
//                 password_hash TEXT NOT NULL,
//                 role TEXT NOT NULL DEFAULT 'user',
//                 created_at TEXT DEFAULT CURRENT_TIMESTAMP,
//                 CHECK (role IN ('user', 'admin'))
//             );
            
//             CREATE TABLE books (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 title TEXT NOT NULL,
//                 author TEXT NOT NULL,
//                 year INTEGER,
//                 description TEXT,
//                 cover_image TEXT,
//                 avg_rating REAL DEFAULT 0,
//                 created_at TEXT DEFAULT CURRENT_TIMESTAMP,
//                 updated_at TEXT DEFAULT CURRENT_TIMESTAMP
//             );
            
//             CREATE TABLE genres (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 name TEXT UNIQUE NOT NULL
//             );
            
//             CREATE TABLE book_genres (
//                 book_id INTEGER NOT NULL,
//                 genre_id INTEGER NOT NULL,
//                 FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
//                 FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE,
//                 PRIMARY KEY (book_id, genre_id)
//             );
            
//             CREATE TABLE reviews (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 user_id INTEGER NOT NULL,
//                 book_id INTEGER NOT NULL,
//                 rating INTEGER NOT NULL,
//                 review_text TEXT,
//                 created_at TEXT DEFAULT CURRENT_TIMESTAMP,
//                 updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
//                 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
//                 FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
//                 UNIQUE(user_id, book_id)
//             );
            
//             CREATE VIEW v_books_with_rating AS
//             SELECT 
//                 b.*,
//                 COALESCE(AVG(r.rating), 0) AS calculated_rating,
//                 COUNT(r.id) AS reviews_count
//             FROM books b
//             LEFT JOIN reviews r ON b.id = r.book_id
//             GROUP BY b.id;
            
//             CREATE VIEW v_top_users AS
//             SELECT 
//                 u.id,
//                 u.username,
//                 COUNT(r.id) AS reviews_count
//             FROM users u
//             LEFT JOIN reviews r ON u.id = r.user_id
//             GROUP BY u.id
//             ORDER BY reviews_count DESC;
            
//             CREATE VIEW v_recent_reviews AS
//             SELECT 
//                 r.id,
//                 r.rating,
//                 r.review_text,
//                 r.created_at,
//                 u.username AS user_name,
//                 b.title AS book_title,
//                 b.id AS book_id
//             FROM reviews r
//             JOIN users u ON r.user_id = u.id
//             JOIN books b ON r.book_id = b.id
//             ORDER BY r.created_at DESC
//             LIMIT 20;
            
//             CREATE TRIGGER trg_books_updated_at
//             AFTER UPDATE ON books
//             BEGIN
//                 UPDATE books SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
//             END;
            
//             CREATE TRIGGER trg_reviews_check_rating
//             BEFORE INSERT ON reviews
//             WHEN NEW.rating < 1 OR NEW.rating > 10
//             BEGIN
//                 SELECT RAISE(ABORT, 'Rating must be between 1 and 10');
//             END;
            
//             CREATE TRIGGER trg_reviews_update_avg_rating
//             AFTER INSERT ON reviews
//             BEGIN
//                 UPDATE books 
//                 SET avg_rating = (SELECT AVG(rating) FROM reviews WHERE book_id = NEW.book_id)
//                 WHERE id = NEW.book_id;
//             END;
            
//             INSERT INTO genres (name) VALUES 
//                 ('Фантастика'), ('Детектив'), ('Роман'), ('Классика'), ('Поэзия'), ('Мемуары');
            
//             INSERT INTO books (title, author, year, description) VALUES 
//                 ('Братья Карамазовы', 'Федор Михайлович Достоевский', 1880, 'Последний роман Достоевского о Боге, свободе и морали.'),
//                 ('Женщина в белом', 'Уильям Уилки Коллинз', 1860, 'Классический викторианский детектив.'),
//                 ('Грозовой перевал', 'Эмили Бронте', 1847, 'Мрачная готическая драма о роковой любви.'),
//                 ('Год магического мышления', 'Джоан Дидион', 2005, 'Правдивый рассказ о переживании утраты.');
            
//             INSERT INTO users (username, password_hash, role) VALUES 
//                 ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
//                 ('user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
//         ";

//         self::$connection->exec($sql);
//     }
// } 
