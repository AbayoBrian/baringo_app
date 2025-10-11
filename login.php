<?php
/**
 * Login Page
 * IMS Baringo CIDU - PHP Version
 */

require_once 'config/config.php';
require_once 'classes/User.php';

// Redirect if already authenticated
if (is_authenticated()) {
    $userRole = $_SESSION['user_role'];
    if ($userRole === 'agent') {
        redirect('/agent.php');
    } elseif ($userRole === 'admin') {
        redirect('/home.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Check against environment variables for simple auth
        if (($username === AGENT_USERNAME && $password === AGENT_PASSWORD) ||
            ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD)) {
            
            $_SESSION['user_role'] = ($username === AGENT_USERNAME) ? 'agent' : 'admin';
            $_SESSION['username'] = $username;
            
            if ($_SESSION['user_role'] === 'agent') {
                redirect('/agent.php');
            } else {
                redirect('/home.php');
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Baringo Irrigation Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #1B5E20;
            --accent-color: #4CAF50;
            --light-color: #E8F5E8;
            --dark-color: #1B5E20;
            --gradient-primary: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%);
        }
        
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(46, 125, 50, 0.9) 0%, rgba(27, 94, 32, 0.9) 100%),
                        url('https://images.unsplash.com/photo-1574263867128-a3d5c1b1deae?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo img {
            max-width: 120px;
            height: auto;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.25);
        }
        
        .btn-login {
            background: var(--gradient-primary);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/images/baringo-logo.png" alt="Baringo Logo" class="img-fluid">
            <h3 class="mt-3 text-dark">Baringo Irrigation Portal</h3>
            <p class="text-muted">CIDU Management System</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?>" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user me-2"></i>Username
                </label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <small class="text-muted">
                Default credentials:<br>
                Agent: Agent / agent@2025!<br>
                Admin: CiduAdmin / admin@2025#
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
