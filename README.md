***
Created search-results.php contains:
-Search function - Search by title, author, genre -Sort by feature - dropdown menu Sort by title, price, publication year, (ascending, descending)

Added Search function into the header of other pages so user can use search bar on other pages, code it visible by comments like:

Added books, authors, images, and ids, to database totaled 60
***


# RowdyBookly

Welcome to Rowdy Bookly! Our mission is to provide a fast, convenient, and secure shopping experience, made to meet the unique needs of the bookworm community. 

This project is the culmination of an intense two-week learning journey where we had to quickly familiarize ourselves with PHP, SQL, and the XAMPP server, even though most of our group members have no prior experience with this technology. Despite the steep learning curve, we were able to develop a functional and interactive platform that showcases both our adaptability and ability to apply new skills in a short period of time.

This guide provides instructions specifically for setting up and testing the database-testing branch locally.
## Core features that we managed to develop for this prototype
1. Display categories directly from the database
2. Display books based on the selected category
3. Add book to cart
4. Compute the total prices: The cart will deliver an estimate checkout price without tax, the checkout page will add tax to the subtotal
5. Remove items from the cart (partly): We can only manage to do this in the order review due to knowledge constraint
## Core features that we have not managed to develop for this prototype but aim to complete for the final product:
1. Account Login
2. Account Register
3. Admin management (managing orders, adding/editing/removing authors, genre, books information)
4. User profile page (this would include user information like email, phone number, address)
5. Search for product 

## Getting Started
There are two methods that you can use to test our website:

1. Method 1: Test the website using this link: "https://rowdybookly-c2e55b3c933f.herokuapp.com/".
2. Method 2: Follow the steps below to locally set up and test the `database-testing` branch(our prototype).



## Prerequisites

Ensure you have the following installed on your machine:
1. **XAMPP** (or similar local PHP server) - [Download XAMPP](https://www.apachefriends.org/index.html)
2. **Git** - [Download Git](https://git-scm.com/) (optional for deployment)
3. **Web Browser** (e.g., Chrome, Firefox, Edge)

---

## Branch Setup

### Step 1: Clone the Repository or download the zip straight from this directory
1. Way one: Clone the Repository
   1. Open a terminal or command prompt.
   2. Navigate to the folder where you want to clone the project.
   3. Run the following command to clone the repository:
      ```bash
      git clone https://github.com/pilar-sol/RowdyBookly.git
      ```
   4. Navigate into the project directory:
      ```bash
      cd RowdyBookly
      ```
2. Way two: Download the zip
   1. Clicking on the coding button
   2. Download the file as zip
   3. extract the zip

### Step 2: Move the Project to XAMPP’s `htdocs` Directory
1. Navigate to the htdocs folder in the directory of your XAMPP installation
   - Windows: `C:\xampp\htdocs`
   - macOS: `/Applications/XAMPP/htdocs`
3. Create a folder in htdocs
4. Move all the surrounding file into the folder you just created
6. Copy the `RowdyBookly` folder.
7. Paste it into the `htdocs` directory of your XAMPP installation **next to** the other folder that you just created:
   - Windows: `C:\xampp\htdocs`
   - macOS: `/Applications/XAMPP/htdocs`

---

## Database Setup

### Step 1: Start MySQL and Apache server
1. Open the XAMPP Control Panel.
2. Click "Start" next to **MySQL** and **Apache** to start the database server.

### Step 2: Create the Database
1. Open a browser and navigate to:
   ```
   http://localhost/phpmyadmin
   ```
   or in XAMPP Control Panel click on Admin next to MySQL
   
3. Create a new database:
   - Click on **New** in the left sidebar.
   - Enter `bookstore_db` as the database name and click **Create**.

### Step 3: Import the Database Schema
1. In phpMyAdmin, select the `bookstore_db` database from the left sidebar.
2. Click on the **Import** tab.
3. Click **Choose File** and select the `bookstore-db` file from the project directory:
   ```
   C:\xampp\htdocs\RowdyBookly\bookstore-database
   ```
4. Click **Go** to import the database schema and initial data.

---

## Running the Project Locally

### Step 1: Start XAMPP Services
1. Open the XAMPP Control Panel.
2. Start **Apache** and **MySQL** by clicking "Start" next to each.

### Step 2: Access the Project
1. Open a web browser.
2. Go to:
   ```
   http://localhost/RowdyBookly
   ```
3. You should see the RowdyBookly homepage.

---

## Usage

### Logging In
1. Navigate to the login page: (not yet implemented)
   ```
   http://localhost/RowdyBookly/login.php
   ```
2. Enter the following credentials: (not yet implemented)
   - Username: `user`
   - Password: `password`
3. Click the **login arrow** button to proceed to the homepage.

### Exploring Categories (implemented)
1. Click on the "Categories" button on the homepage.
2. Select a category to view books in that category.

### Shopping Cart (implemented)
1. Click on the shopping cart icon in the header.
2. Manage your cart, which will display selected items or indicate when it is empty.
