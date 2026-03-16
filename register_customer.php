<?php


/*
    * This file handles the registration of new customers in the Car Rental Agency application.
    * It validates user input, checks for existing email addresses, and creates a new customer account in the database.
    * After successful registration, it redirects the user to the login page with a flash message.
*/
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/layout.php';

if (isLoggedIn()) {
    header('Location: available_cars.php');
    exit;
}

// Initialize variables for form data and errors
$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim and retrieve form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    // If there are no validation errors, proceed to check for existing email and create account
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists. Please use a different email.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $email, $hashedPassword, 'customer']);

            setFlash('Customer account created successfully. Please login.');
            header('Location: login.php');
            exit;
        }
    }
}

renderHeader('Customer Registration');
?>

<section class="row justify-content-center">
    <div class="col-lg-6 col-xl-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4">Customer Registration</h2>

                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Customer Account</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php renderFooter();
