<?php
// Comment for assignment -Madi
// Template for new VMS pages. Base your new page on this one

// Make session information accessible, allowing us to associate
// data with the logged-in user.
session_cache_expire(30);
session_start();

ini_set("display_errors", 1);
error_reporting(E_ALL);

// redirect to index if already logged in
if (isset($_SESSION['_id'])) {
  header('Location: index.php');
  die();
}
$badLogin = false;
$archivedAccount = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once('include/input-validation.php');
  $ignoreList = array('password');
  $args = sanitize($_POST, $ignoreList);
  $required = array('username', 'password');
  if (wereRequiredFieldsSubmitted($args, $required)) {
    require_once('domain/Person.php');
    require_once('database/dbPersons.php');
    /*@require_once('database/dbMessages.php');*/
    /*@dateChecker();*/
    $username = strtolower($args['username']);
    $password = $args['password'];
    $user = retrieve_person($username);
    if (!$user) {
      $badLogin = true;
    } else if ($user->get_status() === "Inactive") {
      // If the user is archived, block login
      $archivedAccount = true;
    } else if (password_verify($password, $user->get_password())) {
      $_SESSION['logged_in'] = true;

      $_SESSION['access_level'] = $user->get_access_level();
      $_SESSION['f_name'] = $user->get_first_name();
      $_SESSION['l_name'] = $user->get_last_name();


      $_SESSION['type'] = 'admin';
      $_SESSION['_id'] = $user->get_id();

      //hard code root privileges
      if ($user->get_id() == 'vmsroot') {
        $_SESSION['access_level'] = 3;
        $_SESSION['locked'] = false;
        header('Location: index.php');
      }

      //if ($changePassword) {
      //    $_SESSION['access_level'] = 0;
      //    $_SESSION['change-password'] = true;
      //    header('Location: changePassword.php');
      //    die();
      //} 
      else {
        header('Location: index.php');
        die();
      }
      die();
    } else {
      $badLogin = true;
    }
  }
}
//<p>Or <a href="register.php">register as a new volunteer</a>!</p>
//Had this line under login button, took user to register page
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/theme-toggle.css">
  <script src="js/theme-toggle.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Quicksand', sans-serif;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--bg-color);
      padding: 20px;
      transition: background-color 0.3s ease;
    }

    .login-container {
      width: 100%;
      max-width: 440px;
      background: var(--card-bg);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
      border: 1px solid var(--border-color);
      transition: all 0.3s ease;
    }

    .login-header {
      text-align: center;
      margin-bottom: 40px;
    }

    .logo-container {
      width: 80px;
      height: 80px;
      margin: 0 auto 24px;
      background: linear-gradient(135deg, var(--main-color), var(--accent-color));
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      box-shadow: 0 4px 20px rgba(232, 196, 184, 0.3);
    }

    .login-title {
      font-size: 28px;
      font-weight: 600;
      color: var(--text-color);
      margin-bottom: 8px;
      letter-spacing: -0.02em;
    }

    .login-subtitle {
      font-size: 15px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .alert {
      padding: 14px 16px;
      border-radius: 12px;
      margin-bottom: 24px;
      font-size: 14px;
      font-weight: 500;
      line-height: 1.5;
      animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .alert-error {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.1));
      border: 1px solid rgba(239, 68, 68, 0.3);
      color: #dc2626;
    }

    .alert-success {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.1));
      border: 1px solid rgba(34, 197, 94, 0.3);
      color: #16a34a;
    }

    .alert a {
      text-decoration: underline;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: var(--text-color);
      margin-bottom: 8px;
      letter-spacing: -0.01em;
    }

    .form-input {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid var(--border-color);
      border-radius: 12px;
      font-size: 15px;
      font-weight: 500;
      color: var(--text-color);
      background: var(--input-bg, var(--bg-color));
      transition: all 0.2s ease;
      outline: none;
    }

    .form-input:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
      transform: translateY(-1px);
    }

    .form-input::placeholder {
      color: var(--text-muted);
    }

    .login-btn {
      width: 100%;
      padding: 14px 24px;
      background: var(--main-color);
      color: var(--button-text);
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px rgba(232, 196, 184, 0.3);
      letter-spacing: -0.01em;
    }

    .login-btn:hover {
      background: var(--accent-color);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }

    .login-btn:active {
      transform: translateY(0);
    }

    .form-footer {
      margin-top: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .form-link {
      font-size: 13px;
      font-weight: 600;
      color: var(--main-color);
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .form-link:hover {
      color: var(--accent-color);
      text-decoration: underline;
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 32px 0;
      gap: 16px;
    }

    .divider-line {
      flex: 1;
      height: 1px;
      background: var(--border-color);
    }

    .divider-text {
      font-size: 13px;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .signup-text {
      text-align: center;
      font-size: 14px;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .signup-link {
      color: var(--accent-color);
      font-weight: 600;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .signup-link:hover {
      text-decoration: underline;
      color: var(--main-color);
    }

    .theme-toggle-wrapper {
      position: absolute;
      top: 24px;
      right: 24px;
    }

    .theme-toggle-btn {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .theme-toggle-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .theme-toggle-btn svg {
      width: 20px;
      height: 20px;
      stroke: var(--text-color);
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 32px 24px;
      }

      .login-title {
        font-size: 24px;
      }

      .form-footer {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
  <title>FXBG Pride Volunteer System | Log In</title>
</head>

<body>
  <!-- Theme Toggle -->
  <div class="theme-toggle-wrapper">
    <button class="theme-toggle theme-toggle-btn" aria-label="Toggle theme">
      <svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="5"></circle>
        <line x1="12" y1="1" x2="12" y2="3"></line>
        <line x1="12" y1="21" x2="12" y2="23"></line>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
        <line x1="1" y1="12" x2="3" y2="12"></line>
        <line x1="21" y1="12" x2="23" y2="12"></line>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
      </svg>
      <svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
      </svg>
    </button>
  </div>

  <div class="login-container">
    <!-- Header -->
    <div class="login-header">
      <div class="logo-container">
        🏳️‍🌈
      </div>
      <h1 class="login-title">Welcome Back</h1>
      <p class="login-subtitle">Sign in to continue to your account</p>
    </div>

    <!-- Form -->
    <form method="post">
      <?php
      if ($badLogin) {
        echo '<div class="alert alert-error">No account found with that username and password combination.</div>';
      }
      if ($archivedAccount) {
        echo '<div class="alert alert-error">This account has been archived or not yet approved. For help, contact <a href="mailto:info@fxbgpride.org">info@fxbgpride.org</a>.</div>';
      }
      if (isset($_GET['registerSuccess'])) {
        echo '<div class="alert alert-success">Registration successful! Please sign in below.</div>';
      }
      ?>

      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input class="form-input" type="text" name="username" id="username" placeholder="Enter your username" required autocomplete="username">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" name="password" id="password" placeholder="Enter your password" required autocomplete="current-password">
      </div>

      <button type="submit" class="login-btn">Sign In</button>

      <div class="form-footer">
        <a href="#" class="form-link">Forgot password?</a>
        <a href="https://fxbgpride.org/" class="form-link" target="_blank">FXBG Pride Website →</a>
      </div>
    </form>

    <!-- Divider -->
    <div class="divider">
      <div class="divider-line"></div>
      <span class="divider-text">or</span>
      <div class="divider-line"></div>
    </div>

    <!-- Sign Up -->
    <p class="signup-text">
      Don't have an account? <a href="VolunteerRegister.php" class="signup-link">Sign up now</a>
    </p>
  </div>
</body>

</html>