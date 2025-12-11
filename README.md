# Simple Todo App

A basic Todo application built from scratch to learn PHP and MySQL fundamentals.

## Setup

1. **Clone the repository:**

## Notificaciones (Worker + RabbitMQ)

Este proyecto incluye un Worker de notificaciones en Node.js que consume eventos de tareas desde RabbitMQ y simula el envío de un email de felicitación.

### Archivos clave

- `notifications-worker.js`: Worker que consume la cola `TASK_EVENTS_QUEUE`.
- `send-test-message.js`: Productor simple para enviar un mensaje de prueba.
- `event-producer-server.js`: Servidor HTTP que publica eventos en RabbitMQ.

### Requisitos

- Node.js 14+
- RabbitMQ corriendo localmente o accesible vía URL

### Instalación

```powershell
cd c:\xampp\htdocs\simple-todo-app
npm init -y
npm install amqplib
```

Para el servidor HTTP productor (no requiere paquetes adicionales, usa `http` nativo):

```powershell
# Sin pasos extra
```

### Ejecutar el Worker

```powershell
$env:RABBITMQ_URL="amqp://localhost"; $env:TASK_EVENTS_QUEUE="TASK_EVENTS_QUEUE"; node .\notifications-worker.js
```

### Enviar un mensaje de prueba

```powershell
$env:RABBITMQ_URL="amqp://localhost"; $env:TASK_EVENTS_QUEUE="TASK_EVENTS_QUEUE"; node .\send-test-message.js
```

### Productor con flags (CLI)

- Enviar 5 mensajes con título y autor personalizados:

```powershell
$env:RABBITMQ_URL="amqp://localhost"; $env:TASK_EVENTS_QUEUE="TASK_EVENTS_QUEUE"; node .\send-test-message.js --title="Refactor de API" --by="Ana" --count=5
```

- Fijar un `taskId` concreto:

```powershell
$env:RABBITMQ_URL="amqp://localhost"; $env:TASK_EVENTS_QUEUE="TASK_EVENTS_QUEUE"; node .\send-test-message.js --id=9999 --title="Task fija" --by="Bot"
```

### Script de servicio para RabbitMQ (Windows)

Ejecuta este script para habilitar el panel, instalar e iniciar el servicio y ver el estado:

```powershell
PowerShell -ExecutionPolicy Bypass -File .\scripts\setup-rabbitmq.ps1
```

### Servidor HTTP Productor

Arranca el servidor para recibir eventos vía HTTP y publicarlos en RabbitMQ:

```powershell
$env:RABBITMQ_URL="amqp://localhost"; $env:TASK_EVENTS_QUEUE="TASK_EVENTS_QUEUE"; $env:PORT=3001; node .\event-producer-server.js
```

Enviar un evento desde PowerShell:

```powershell
Invoke-RestMethod -Method Post -Uri http://localhost:3001/events/task-completed -ContentType application/json -Body '{"taskId":101,"title":"Generar informe","completedBy":"Carlos"}'
```

El Worker debe imprimir el log de felicitación y hacer ACK.

Si todo está correcto, el Worker mostrará en consola un log simulando el envío de un email y marcará el mensaje como ACK.

# Simple Todo App

A basic Todo application built from scratch to learn PHP and MySQL fundamentals.

## 📸 Screenshots

### Main Interface

![Main Interface](screenshots/main-interface.png)

### Adding New Tasks

![Adding Task](screenshots/adding-task.png)

### Task Management

![Task Actions](screenshots/task-actions.png)

### User Authentication System

#### User Registration

![User Registration](screenshots/user-registration.png)

#### User Login

![User Login](screenshots/user-login.png)

#### Login Success

![Login Success](screenshots/login-success.png)

## 🎯 Features

- ✅ Add new tasks
- ✅ Mark tasks as completed/pending
- ✅ Delete tasks
- ✅ Persistent data storage with MySQL
- ✅ Clean and responsive design
- ✅ User registration and authentication
- ✅ Secure password hashing
- ✅ Form validation and error handling

## 🛠️ Technologies Used

- **Frontend:** HTML5, CSS3
- **Backend:** PHP 8+ with PDO
- **Database:** MySQL with relational design
- **Security:** Password hashing, prepared statements
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
├── index.php              # Main todo application
├── login.php              # User login page
├── register.php           # User registration page
├── config.php             # Database configuration
├── database_setup.sql     # Database creation script
├── test_connection.php    # Connection test file
├── ROADMAP.md             # 21-day development plan
├── screenshots/           # Application screenshots
└── README.md              # Project documentation
```

└── README.md # Project documentation

````

## 🎓 Learning Journey

This project was built step by step to understand:

- Database design and table creation
- PHP-MySQL connection with PDO
- CRUD operations (Create, Read, Update, Delete)
- Form handling and data validation
- Prepared statements for security
- Error handling with try/catch
- User authentication and session management
- Password hashing and security best practices
- Foreign key relationships

## 📈 Development Progress

### ✅ Completed (Days 1-2)
- Basic todo functionality with CRUD operations
- User registration and login system
- Database design with relational structure
- Secure password handling
- Form validation and error handling
- Professional UI/UX design

### 🎯 Next Steps (Following 21-day roadmap)
- Session management and user-specific tasks
- Enhanced task features (categories, due dates)
- REST API development
- Modern framework implementation (Laravel)
- Frontend development (React)
- DevOps and production deployment

See [ROADMAP.md](ROADMAP.md) for the complete development plan.

## 🔧 Configuration

Update `config.php` with your database credentials:

```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'simple_todo');
````

## 🤝 Contributing

This is a learning project, but feel free to:

- Report bugs
- Suggest improvements
- Fork and experiment

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

---

**Built with ❤️ for learning PHP & MySQL**
