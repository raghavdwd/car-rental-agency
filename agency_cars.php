<?php


/*
    * This file allows agency users to manage their fleet of cars in the Car Rental Agency application.
    * Agencies can add new cars, edit existing car details, and view the list of cars they have added.
    * The page is protected so that only logged-in users with the 'agency' role can access it.
*/
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/layout.php';

requireRole('agency', 'available_cars.php');

$agencyId = (int) currentUser()['id'];
$errors = [];
$editingCar = null;

if (isset($_GET['edit'])) {
    $carId = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM cars WHERE id = ? AND agency_id = ? LIMIT 1');
    $stmt->execute([$carId, $agencyId]);
    $editingCar = $stmt->fetch();

    if (!$editingCar) {
        setFlash('Car not found for editing.', 'danger');
        header('Location: agency_cars.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $model = trim($_POST['model'] ?? '');
    $vehicleNumber = strtoupper(trim($_POST['vehicle_number'] ?? ''));
    $seatingCapacity = (int) ($_POST['seating_capacity'] ?? 0);
    $rentPerDay = (float) ($_POST['rent_per_day'] ?? 0);

    if ($model === '') {
        $errors[] = 'Vehicle model is required.';
    }

    if ($vehicleNumber === '') {
        $errors[] = 'Vehicle number is required.';
    }

    if ($seatingCapacity < 1) {
        $errors[] = 'Seating capacity must be at least 1.';
    }

    if ($rentPerDay <= 0) {
        $errors[] = 'Rent per day must be greater than 0.';
    }

    if (!$errors) {
        if ($id > 0) {
            $check = $pdo->prepare('SELECT id FROM cars WHERE id = ? AND agency_id = ? LIMIT 1');
            $check->execute([$id, $agencyId]);
            if (!$check->fetch()) {
                setFlash('Unauthorized edit attempt detected.', 'danger');
                header('Location: agency_cars.php');
                exit;
            }

            $update = $pdo->prepare('UPDATE cars SET model = ?, vehicle_number = ?, seating_capacity = ?, rent_per_day = ? WHERE id = ? AND agency_id = ?');
            $update->execute([$model, $vehicleNumber, $seatingCapacity, $rentPerDay, $id, $agencyId]);
            setFlash('Car updated successfully.');
        } else {
            $insert = $pdo->prepare('INSERT INTO cars (agency_id, model, vehicle_number, seating_capacity, rent_per_day, is_available) VALUES (?, ?, ?, ?, ?, 1)');
            $insert->execute([$agencyId, $model, $vehicleNumber, $seatingCapacity, $rentPerDay]);
            setFlash('New car added successfully.');
        }

        header('Location: agency_cars.php');
        exit;
    }

    $editingCar = [
        'id' => $id,
        'model' => $model,
        'vehicle_number' => $vehicleNumber,
        'seating_capacity' => $seatingCapacity,
        'rent_per_day' => $rentPerDay,
    ];
}

$listQuery = $pdo->prepare('SELECT id, model, vehicle_number, seating_capacity, rent_per_day, is_available, created_at FROM cars WHERE agency_id = ? ORDER BY created_at DESC');
$listQuery->execute([$agencyId]);
$cars = $listQuery->fetchAll();

renderHeader('Manage Cars');
?>

<section class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4"><?php echo $editingCar ? 'Edit Car' : 'Add New Car'; ?></h2>

                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>

                <form method="post">
                    <input type="hidden" name="id" value="<?php echo (int) ($editingCar['id'] ?? 0); ?>">
                    <div class="mb-3">
                        <label class="form-label" for="model">Vehicle Model</label>
                        <input class="form-control" id="model" name="model" type="text" value="<?php echo htmlspecialchars($editingCar['model'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="vehicle_number">Vehicle Number</label>
                        <input class="form-control" id="vehicle_number" name="vehicle_number" type="text" value="<?php echo htmlspecialchars($editingCar['vehicle_number'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="seating_capacity">Seating Capacity</label>
                        <input class="form-control" id="seating_capacity" name="seating_capacity" type="number" min="1" value="<?php echo htmlspecialchars((string) ($editingCar['seating_capacity'] ?? '')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="rent_per_day">Rent Per Day</label>
                        <input class="form-control" id="rent_per_day" name="rent_per_day" type="number" min="1" step="0.01" value="<?php echo htmlspecialchars((string) ($editingCar['rent_per_day'] ?? '')); ?>" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit"><?php echo $editingCar ? 'Update Car' : 'Add Car'; ?></button>
                        <?php if ($editingCar): ?>
                            <a class="btn btn-outline-primary" href="agency_cars.php">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4">Your Fleet</h2>
                <?php if (!$cars): ?>
                    <p class="text-muted mb-0">No cars added yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Model</th>
                                    <th>Vehicle #</th>
                                    <th>Seats</th>
                                    <th>Rent/Day</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cars as $car): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($car['model']); ?></td>
                                        <td><?php echo htmlspecialchars($car['vehicle_number']); ?></td>
                                        <td><?php echo (int) $car['seating_capacity']; ?></td>
                                        <td>Rs. <?php echo number_format((float) $car['rent_per_day'], 2); ?></td>
                                        <td><?php echo (int) $car['is_available'] === 1 ? 'Available' : 'Booked'; ?></td>
                                        <td><a class="btn btn-sm btn-outline-primary" href="agency_cars.php?edit=<?php echo (int) $car['id']; ?>">Edit</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php renderFooter();
