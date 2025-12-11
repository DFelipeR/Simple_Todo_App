<?php
session_start();
require_once 'config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $connection = getDBConnection();


        // Handle adding new task
        if (isset($_POST['add_task']) && !empty($_POST['new_task'])) {
            $taskText = trim($_POST['new_task']);
            $stmt = $connection->prepare("INSERT INTO tasks (task_text, user_id) VALUES (?, ?)");
            $stmt->execute([$taskText, $user_id]);
            $message = "Task added successfully!";
            $messageType = 'success';
        }

        // Handle edit task
        if (isset($_POST['edit_task']) && isset($_POST['task_id']) && !empty($_POST['edited_task_text'])) {
            $taskId = $_POST['task_id'];
            $editedText = trim($_POST['edited_task_text']);
            $stmt = $connection->prepare("UPDATE tasks SET task_text = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$editedText, $taskId, $user_id]);
            $message = "Task updated successfully!";
            $messageType = 'success';
        }

        // Handle toggle task completion (only for user's own tasks)
        if (isset($_POST['toggle_task']) && isset($_POST['task_id'])) {
            $taskId = $_POST['task_id'];
            $stmt = $connection->prepare("UPDATE tasks SET completed = NOT completed WHERE id = ? AND user_id = ?");
            $stmt->execute([$taskId, $user_id]);
            $message = "Task status updated!";
            $messageType = 'success';
        }

        // Handle delete task (only for user's own tasks)
        if (isset($_POST['delete_task']) && isset($_POST['task_id'])) {
            $taskId = $_POST['task_id'];
            $stmt = $connection->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            $stmt->execute([$taskId, $user_id]);
            $message = "Task deleted successfully!";
            $messageType = 'success';
        }

    } catch(Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Test database connection
try {
    $connection = getDBConnection();
    // Connection successful - ready to work with database
} catch(Exception $e) {
    $message = "Database connection failed: " . $e->getMessage();
    $messageType = 'error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Todo App</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-style: italic;
        }
        form {
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        button:hover {
            background: #5a6fd8;
        }
        .task {
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task.completed {
            text-decoration: line-through;
            opacity: 0.6;
            border-left-color: #28a745;
        }
        .task-actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
        }
        .btn-complete {
            background: #28a745;
        }
        .btn-delete {
            background: #dc3545;
        }
        .message {
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            font-weight: bold;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
    <h1>Simple Todo App</h1>
        <p class="subtitle">Built from scratch to learn PHP & MySQL fundamentals</p>
        
        <!-- Show messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- User Navigation -->
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="logout.php" style="color: #dc3545; text-decoration: none; margin: 0 10px;">Logout</a>
        </div>
        
        <!-- Add Task Form -->
        <form method="POST" action="">
            <input type="text" name="new_task" placeholder="Enter your task here..." required>
            <button type="submit" name="add_task">Add Task</button>
        </form>

        <!-- Tasks List -->
        <div id="tasks-list">
            <?php
            // Fetch tasks for the logged-in user
            try {
                $connection = getDBConnection();
                $stmt = $connection->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$user_id]);
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($tasks) > 0) {
                    // Detect if editing a task
                    $editingTaskId = isset($_POST['start_edit']) ? intval($_POST['task_id']) : null;
                    foreach ($tasks as $task) {
                        $completedClass = $task['completed'] ? 'completed' : '';
                        echo '<div class="task ' . $completedClass . '">';
                        // Si está en modo edición, mostrar formulario
                        if (isset($editingTaskId) && $editingTaskId === intval($task['id'])) {
                            echo '<form method="POST" style="display:inline; width:100%;">';
                            echo '<input type="hidden" name="task_id" value="' . $task['id'] . '">';
                            echo '<input type="text" name="edited_task_text" value="' . htmlspecialchars($task['task_text']) . '" style="width:60%; padding:5px;">';
                            echo '<button type="submit" name="edit_task" class="btn-small btn-complete">Save</button>';
                            echo '<button type="submit" name="cancel_edit" class="btn-small">Cancel</button>';
                            echo '</form>';
                        } else {
                            echo '<span>' . htmlspecialchars($task['task_text']) . '</span>';
                            echo '<div class="task-actions">';
                            // Edit button
                            echo '<form method="POST" style="display:inline;">';
                            echo '<input type="hidden" name="task_id" value="' . $task['id'] . '">';
                            echo '<button type="submit" name="start_edit" class="btn-small">Edit</button>';
                            echo '</form>';
                            // Complete/Uncomplete button
                            $completeText = $task['completed'] ? 'Mark as Pending' : 'Mark as Complete';
                            $completeClass = $task['completed'] ? 'btn-complete' : 'btn-complete';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="task_id" value="' . $task['id'] . '">';
                            echo '<button type="submit" name="toggle_task" class="btn-small ' . $completeClass . '">' . $completeText . '</button>';
                            echo '</form>';
                            // Delete button
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="task_id" value="' . $task['id'] . '">';
                            echo '<button type="submit" name="delete_task" class="btn-small btn-delete" onclick="return confirm(\'Are you sure you want to delete this task?\')">Delete</button>';
                            echo '</form>';
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<div class="task">';
                    echo '<span>No tasks yet. Add your first task above!</span>';
                    echo '</div>';
                }
            } catch(Exception $e) {
                echo '<div class="message error">Error loading tasks: ' . $e->getMessage() . '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>