<?php


/*
    * This file handles the registration of new agencies in the Car Rental Agency application.
    * It validates user input, checks for duplicate emails, and creates a new agency account in the database.
    * After successful registration, it redirects the user to the login page with a success message.
*/
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/layout.php';

if (isLoggedIn()) {
    header('Location: available_cars.php');
    exit;
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') {
        $errors[] = 'Agency name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists. Please use a different email.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $email, $hashedPassword, 'agency']);

            setFlash('Agency account created successfully. Please login.');
            header('Location: login.php');
            exit;
        }
    }
}

renderHeader('Agency Registration');
?>

<section class="row justify-content-center">
    <div class="col-lg-6 col-xl-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4">Agency Registration</h2>

                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Agency Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Official Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Agency Account</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php renderFooter();
