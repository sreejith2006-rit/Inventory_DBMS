<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inventory Management System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--text-main);
            overflow: hidden;
        }

        /* Abstract shapes background */
        .blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            background: var(--primary-color);
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.15;
            animation: move 20s infinite alternate;
        }

        .blob-1 { width: 400px; height: 400px; top: -100px; right: -100px; }
        .blob-2 { width: 300px; height: 300px; bottom: -50px; left: -50px; background: #ec4899; }

        @keyframes move {
            from { transform: translate(0, 0); }
            to { transform: translate(50px, 50px); }
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 50px 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            margin: 0 auto 20px;
            font-size: 28px;
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: var(--text-dim);
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            font-size: 18px;
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s;
            outline: none;
        }

        input:focus {
            border-color: var(--primary-color);
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        input:focus + i {
            color: var(--primary-color);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #fca5a5;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
            display: <?php echo isset($_GET['error']) ? 'block' : 'none'; ?>;
        }

        .footer-links {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: var(--text-dim);
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="login-card">
        <div class="logo-container">
            <div class="logo-icon">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <h1>Welcome Back</h1>
            <p class="subtitle">Management System Access</p>
        </div>

        <div class="error-msg">
            <i class="fas fa-exclamation-circle"></i> 
            <?php echo isset($_GET['error']) ? htmlspecialchars($_GET['error']) : ''; ?>
        </div>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-login">
                Sign In <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div class="footer-links">
            <p>Managed by Inventory System &copy; 2026</p>
        </div>
    </div>
</body>
</html>