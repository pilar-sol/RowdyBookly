-- Table to store user information
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for authors with a short bio
CREATE TABLE Authors (
    author_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    bio TEXT
);

-- Table for genres
CREATE TABLE Genres (
    genre_id INT AUTO_INCREMENT PRIMARY KEY,
    genre_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Table for books with references to author and genre
CREATE TABLE Books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    cover_image_url TEXT,
    author_id INT NOT NULL,
    publication_year YEAR,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    FOREIGN KEY (author_id) REFERENCES Authors(author_id)
);

-- Many-to-Many relationship table for books and genres
CREATE TABLE BookGenres (
    book_id INT,
    genre_id INT,
    PRIMARY KEY (book_id, genre_id),
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES Genres(genre_id) ON DELETE CASCADE
);

-- Table to store cart information
CREATE TABLE Carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Table for items within a cart
CREATE TABLE CartItems (
    cart_item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    FOREIGN KEY (cart_id) REFERENCES Carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

-- Table for orders with a reference to users
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) DEFAULT 0.00,
    discount DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('Pending', 'Shipped', 'Completed', 'Cancelled') DEFAULT 'Pending',
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Table for order details to list books in each order
CREATE TABLE OrderItems (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE
);

INSERT INTO Authors (name, bio)
VALUES
    ('Siddhartha Mukherjee', 'An Indian-American physician and biologist, known for his work on cancer.'),
    ('Benjamin Graham', 'An economist and investor, known as the father of value investing.'),
    ('Silvanus P. Thompson', 'A British mathematician and author, best known for his work in calculus.'),
    ('Robert C. Martin', 'A software engineer and author, known for his advocacy of clean code principles.'),
    ('Howard Zinn', 'An American historian, playwright, and social activist.'),
    ('Stephen Hawking', 'A British theoretical physicist, cosmologist, and author.');
    ('J.K. Rowling', 'British author, best known for the Harry Potter series.'),
    ('George Orwell', 'English novelist, essayist, journalist, and critic, famous for works like 1984 and Animal Farm.'),
    ('J.R.R. Tolkien', 'English writer, poet, philologist, and academic, known for The Lord of the Rings.'),
    ('Jane Austen', 'English novelist known primarily for her six major novels including Pride and Prejudice.');

INSERT INTO Genres (genre_name, description)
VALUES
    ('Health and Medicine', 'Books covering topics on health, wellness, and medical sciences.'),
    ('Finance', 'Books about managing finances, investing, and understanding economic principles.'),
    ('Mathematics', 'Books exploring mathematical theories, applications, and problem-solving techniques.'),
    ('Coding', 'Books on programming, software development, and various coding languages.'),
    ('History', 'Books documenting historical events, biographies, and ancient cultures.'),
    ('Science', 'Books focused on scientific discoveries, principles, and research.');
    ('Fantasy', 'Books that contain magical or supernatural elements.'),
    ('Science Fiction', 'Books based on imagined future scientific or technological advances.'),
    ('Classics', 'Books that have stood the test of time and are widely regarded as excellent.'),
    ('Romance', 'Books that focus on love and relationships.');

INSERT INTO Books (title, cover_image_url, author_id, publication_year, price, description)
VALUES
    ('The Emperor of All Maladies', 'url_to_image5.jpg', 1, 2010, 16.99, 'A biography of cancer, covering its history, treatment, and research developments.'),
    ('The Intelligent Investor', 'url_to_image6.jpg', 2, 1949, 22.50, 'A definitive book on value investing and financial principles.'),
    ('Calculus Made Easy', 'url_to_image7.jpg', 3, 1910, 13.99, 'A simplified introduction to calculus concepts and applications.'),
    ('Clean Code', 'url_to_image8.jpg', 4, 2008, 29.99, 'A handbook of agile software craftsmanship.'),
    ('A People\'s History of the United States', 'url_to_image9.jpg', 5, 1980, 18.99, 'A retelling of American history from the perspective of marginalized groups.'),
    ('A Brief History of Time', 'url_to_image10.jpg', 6, 1988, 15.99, 'A classic work by Stephen Hawking on cosmology and black holes.');
    ('Harry Potter and the Sorcerer\'s Stone', 'url_to_image1.jpg', 1, 1997, 19.99, 'The first book in the Harry Potter series.'),
    ('1984', 'url_to_image2.jpg', 2, 1949, 9.99, 'A dystopian social science fiction novel and cautionary tale about the dangers of totalitarianism.'),
    ('The Hobbit', 'url_to_image3.jpg', 3, 1937, 14.99, 'A fantasy novel and children\'s book by J.R.R. Tolkien.'),
    ('Pride and Prejudice', 'url_to_image4.jpg', 4, 1813, 12.99, 'A romantic novel that also critiques the British landed gentry at the end of the 18th century.');

INSERT INTO BookGenres (book_id, genre_id)
VALUES
    (1, 1),  -- Harry Potter -> Fantasy
    (2, 2),  -- 1984 -> Science Fiction
    (2, 3),  -- 1984 -> Classics
    (3, 1),  -- The Hobbit -> Fantasy
    (4, 4),  -- Pride and Prejudice -> Romance
    (4, 3);  -- Pride and Prejudice -> Classics
    (5, 7),  -- The Emperor of All Maladies -> Health and Medicine
    (6, 8),  -- The Intelligent Investor -> Finance
    (7, 9),  -- Calculus Made Easy -> Mathematics
    (8, 10), -- Clean Code -> Coding
    (9, 11), -- A People's History of the United States -> History
    (10, 12);-- A Brief History of Time -> Science

INSERT INTO Users (username, password_hash, email, address)
VALUES
    ('jdoe', '123123', 'jdoe@example.com', '123 Main St, Anytown, USA');
    ('darius', 'darius', 'darius@hotmail.com', '123 Homelander, Anytown, USA')

INSERT INTO Carts (user_id)
VALUES
    (1);

INSERT INTO CartItems (cart_id, book_id, quantity)
VALUES
    (1, 1, 1); -- "Harry Potter and the Sorcerer's Stone" with quantity 1
INSERT INTO Orders (user_id, total, tax, discount, status, shipping_address)
VALUES
    (1, 19.99, 1.50, 0.00, 'Pending', '123 Main St, Anytown, USA');

INSERT INTO OrderItems (order_id, book_id, quantity, price)
VALUES
    (1, 1, 1, 19.99);  -- "Harry Potter and the Sorcerer's Stone" with quantity 1 and price 19.99