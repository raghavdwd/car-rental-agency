<?php

/*
    * This file handles user login for both customers and agencies in the Car Rental Agency application.
    * It validates user input, checks credentials against the database, and manages user sessions.
    * The login form includes a toggle for showing/hiding the password for better user experience.
*/

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/layout.php';

if (isLoggedIn()) {
    header('Location: available_cars.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }


    // If there are no validation errors, proceed to check credentials and log the user in
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Invalid credentials.';
        } else {
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            setFlash('Login successful. Welcome back, ' . $user['name'] . '.');

            if ($user['role'] === 'agency') {
                header('Location: agency_cars.php');
                exit;
            }

            header('Location: available_cars.php');
            exit;
        }
    }
}

renderHeader('Login');
?>

<section class="row justify-content-center">
    <div class="col-lg-6 col-xl-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4">Login</h2>
                <p class="text-muted">Customers and agencies can log in from the same page.</p>

                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Show password">Show</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
const passwordInput = document.getElementById('password');
const togglePasswordButton = document.getElementById('togglePassword');

if (passwordInput && togglePasswordButton) {
    togglePasswordButton.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        togglePasswordButton.textContent = isHidden ? 'Hide' : 'Show';
        togglePasswordButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
}
</script>

<?php renderFooter();
