<?php

/*
    * This file displays the list of available cars for rent to customers and allows them to book a car.
    * It handles the booking process, including validating user input, checking car availability, and creating a booking record.
    * The page is accessible to all users, but only logged-in customers can complete a booking.
*/
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/layout.php';

$user = currentUser();
$role = userRole();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_car'])) {
    $carId = (int) ($_POST['car_id'] ?? 0);

    if (!$user) {
        setFlash('Please login as a customer to rent a car.', 'warning');
        header('Location: login.php');
        exit;
    }

    if ($role !== 'customer') {
        setFlash('Agencies are not allowed to book cars.', 'danger');
        header('Location: available_cars.php');
        exit;
    }

    $rentalDays = (int) ($_POST['rental_days'] ?? 0);
    $startDate = $_POST['start_date'] ?? '';

    if ($rentalDays < 1 || $rentalDays > 30) {
        setFlash('Please select valid rental days (1 to 30).', 'danger');
        header('Location: available_cars.php');
        exit;
    }

    $dateObject = DateTime::createFromFormat('Y-m-d', $startDate);
    $today = new DateTime('today');
    if (!$dateObject || $dateObject->format('Y-m-d') !== $startDate || $dateObject < $today) {
        setFlash('Please choose a valid start date (today or later).', 'danger');
        header('Location: available_cars.php');
        exit;
    }

    try {
        $pdo->beginTransaction();

        $carStmt = $pdo->prepare('SELECT id, rent_per_day, is_available FROM cars WHERE id = ? FOR UPDATE');
        $carStmt->execute([$carId]);
        $car = $carStmt->fetch();

        if (!$car || (int) $car['is_available'] !== 1) {
            $pdo->rollBack();
            setFlash('This car is no longer available.', 'danger');
            header('Location: available_cars.php');
            exit;
        }

        $totalPrice = ((float) $car['rent_per_day']) * $rentalDays;
        $endDate = (clone $dateObject)->modify('+' . ($rentalDays - 1) . ' day')->format('Y-m-d');

        $insert = $pdo->prepare(
            'INSERT INTO bookings (car_id, customer_id, rental_days, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $insert->execute([
            $carId,
            (int) $user['id'],
            $rentalDays,
            $startDate,
            $endDate,
            $totalPrice,
        ]);

        $updateAvailability = $pdo->prepare('UPDATE cars SET is_available = 0 WHERE id = ?');
        $updateAvailability->execute([$carId]);

        $pdo->commit();
        setFlash('Car booked successfully.');
    } catch (Throwable $throwable) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        setFlash('Booking failed. Please try again.', 'danger');
    }

    header('Location: available_cars.php');
    exit;
}

$carsQuery = $pdo->query(
    'SELECT c.id, c.model, c.vehicle_number, c.seating_capacity, c.rent_per_day, c.is_available, u.name AS agency_name
     FROM cars c
     INNER JOIN users u ON u.id = c.agency_id
     ORDER BY c.created_at DESC'
);
$cars = $carsQuery->fetchAll();

renderHeader('Available Cars');
?>

<section class="p-4 mb-4 bg-white border rounded-2">
    <p class="badge bg-secondary mb-2">Public Listing</p>
    <h1 class="h4">Available Cars To Rent</h1>
    <p class="mb-0">Anyone can view the listings. Only logged-in customers can complete a booking.</p>
</section>

<section class="row g-3">
    <?php if (!$cars): ?>
        <div class="col-12"><p class="text-muted">No cars listed yet.</p></div>
    <?php else: ?>
        <?php foreach ($cars as $car): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title mb-1"><?php echo htmlspecialchars($car['model']); ?></h5>
                        <p class="small text-muted mb-2">By <?php echo htmlspecialchars($car['agency_name']); ?></p>

                        <ul class="list-unstyled small mb-3">
                            <li><strong>Vehicle #:</strong> <?php echo htmlspecialchars($car['vehicle_number']); ?></li>
                            <li><strong>Seats:</strong> <?php echo (int) $car['seating_capacity']; ?></li>
                            <li><strong>Rent/Day:</strong> Rs. <?php echo number_format((float) $car['rent_per_day'], 2); ?></li>
                            <li><strong>Status:</strong> <?php echo (int) $car['is_available'] === 1 ? 'Available' : 'Booked'; ?></li>
                        </ul>

                        <?php if ((int) $car['is_available'] === 1): ?>
                            <form method="post" class="mt-auto">
                                <input type="hidden" name="car_id" value="<?php echo (int) $car['id']; ?>">
                                <?php if ($role === 'customer'): ?>
                                    <div class="mb-2">
                                        <label class="form-label small" for="days_<?php echo (int) $car['id']; ?>">Days</label>
                                        <select class="form-select" id="days_<?php echo (int) $car['id']; ?>" name="rental_days" required>
                                            <option value="">Select days</option>
                                            <?php for ($day = 1; $day <= 30; $day++): ?>
                                                <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small" for="start_<?php echo (int) $car['id']; ?>">Start Date</label>
                                        <input class="form-control" type="date" id="start_<?php echo (int) $car['id']; ?>" name="start_date" min="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                <?php endif; ?>

                                <button class="btn btn-primary w-100" type="submit" name="book_car" value="1">
                                    Rent Car
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 mt-auto" disabled>Not Available</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php renderFooter();
