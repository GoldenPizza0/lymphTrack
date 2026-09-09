<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LymphTrack - Sign In</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #F8F9FD;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            box-sizing: border-box;
        }
        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .logo-icon {
            width: 42px;
            height: 42px;
        }
        .logo-title {
            font-size: 32px;
            font-weight: 700;
            color: #4361EE;
            margin: 0;
        }
        .logo-title span {
            color: #3B54C8;
        }
        .subtitle {
            margin-top: 8px;
            color: #718096;
            font-size: 14px;
        }
        .login-card {
            background: #FFFFFF;
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            border: 1px solid #EEF2F6;
        }
        .form-group {
            margin-bottom: 16px;
            position: relative;
        }
        .form-group input {
            width: 100%;
            height: 48px;
            padding: 0 18px;
            font-size: 14px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background-color: #FFFFFF;
            color: #2D3748;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: #638CDE;
        }
        .password-group input {
            padding-right: 48px;
        }
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .forgot-password {
            margin-top: -6px;
            margin-bottom: 24px;
        }
        .forgot-password a {
            color: #5B7FDE;
            font-size: 12px;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-signin {
            width: 100%;
            height: 46px;
            background-color: #638CDE;
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 23px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(99, 140, 222, 0.35);
        }
        .btn-signin:hover {
            background-color: #5077C5;
        }
        .alert-error {
            background-color: #FED7D7;
            color: #C53030;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-header">
        <div class="logo-container">
            <svg class="logo-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="22" y="6" width="20" height="52" rx="4" fill="#5882C7"/>
                <rect x="6" y="22" width="52" height="20" rx="4" fill="#5882C7"/>
                <path d="M32 20C32 20 24 30 24 36C24 40.4183 27.5817 44 32 44C36.4183 44 40 40.4183 40 36C40 30 32 20 32 20Z" fill="white"/>
            </svg>
            <h1 class="logo-title">Lymph<span>Track</span></h1>
        </div>
        <p class="subtitle">Secure Medical Data Management</p>
    </div>

    <div class="login-card">
        <?php if (!empty($erreur)) : ?>
            <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form action="index.php?uc=connexion&action=validerConnexion" method="POST">
            <div class="form-group">
                <input type="email" name="email" id="email" placeholder="Email Address" required>
            </div>

            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                    <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8A92A6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>

            <div class="forgot-password">
                <a href="#">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-signin">Sign In</button>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const p = document.getElementById('password');
    p.type = (p.type === 'password') ? 'text' : 'password';
}
</script>
</body>
</html>