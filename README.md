# Simple Todo App

A basic Todo application built from scratch to learn PHP and MySQL fundamentals.

## 📸 Screenshots

### Main Interface
![Main Interface](screenshots/main-interface.png)

### Adding New Tasks
![Adding Task](screenshots/adding-task.png)

### Task Management
![Task Actions](screenshots/task-actions.png)

## 🎯 Features

- ✅ Add new tasks
- ✅ Mark tasks as completed/pending
- ✅ Delete tasks
- ✅ Persistent data storage with MySQL
- ✅ Clean and responsive design

## 🛠️ Technologies Used

- **Frontend:** HTML5, CSS3
- **Backend:** PHP 8+
- **Database:** MySQL
- **Server:** XAMPP (Apache + MySQL)

## 📋 Requirements

- XAMPP (or similar LAMP/WAMP stack)
- PHP 8.0+
- MySQL 5.7+
- Web browser

## 🚀 Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/yourusername/simple-todo-app.git
   ```

2. **Move to XAMPP htdocs:**

   ```bash
   cp -r simple-todo-app /xampp/htdocs/
   ```

3. **Start XAMPP services:**

   - Start Apache
   - Start MySQL

4. **Create database:**

   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Run the SQL script from `database_setup.sql`

5. **Access the application:**
   ```
   http://localhost/simple-todo-app
   ```

## 📁 Project Structure

```
simple-todo-app/
├── index.php              # Main application file
├── config.php             # Database configuration
├── database_setup.sql     # Database creation script
├── test_connection.php    # Connection test file
└── README.md              # Project documentation
```

## 🎓 Learning Journey

This project was built step by step to understand:

- Database design and table creation
- PHP-MySQL connection with PDO
- CRUD operations (Create, Read, Update, Delete)
- Form handling and data validation
- Prepared statements for security
- Error handling with try/catch

## 🔧 Configuration

Update `config.php` with your database credentials:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'simple_todo');
```

## 🤝 Contributing

This is a learning project, but feel free to:

- Report bugs
- Suggest improvements
- Fork and experiment

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

---

**Built with ❤️ for learning PHP & MySQL**
