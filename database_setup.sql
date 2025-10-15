-- Simple Todo App - Database Setup
-- Este archivo contiene la configuración inicial de la base de datos

-- 1. Crear la base de datos 'simple_todo'
CREATE DATABASE IF NOT EXISTS simple_todo;

-- 2. Usar la base de datos
USE simple_todo;

-- 3. Crear la tabla 'tasks'
CREATE TABLE IF NOT EXISTS tasks (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    task_text VARCHAR(255) NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 4. Insertar algunas tareas de ejemplo para probar
INSERT INTO tasks (task_text, completed) VALUES 
('Learn PHP fundamentals', FALSE),
('Set up MySQL database', TRUE),
('Create CRUD operations', FALSE),
('Style the todo app', FALSE),
('Deploy to production', FALSE);

-- 5. Verificar que la tabla se creó correctamente
SELECT * FROM tasks;