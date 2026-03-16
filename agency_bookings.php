<?php

/*
    * This file displays the list of bookings for cars added by the logged-in agency in the Car Rental Agency application.
    * It shows details of each booking, including the car model, vehicle number, customer name and email, rental days, start and end dates, and total price.
    * The page is protected so that only logged-in users with the 'agency' role can access it.
*/
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/layout.php';

requireRole('agency', 'available_cars.php');

$agencyId = (int) currentUser()['id'];

$query = $pdo->prepare(
    'SELECT b.id, b.rental_days, b.start_date, b.end_date, b.total_price, b.created_at,
            c.model, c.vehicle_number,
            u.name AS customer_name, u.email AS customer_email
     FROM bookings b
     INNER JOIN cars c ON c.id = b.car_id
     INNER JOIN users u ON u.id = b.customer_id
     WHERE c.agency_id = ?
     ORDER BY b.created_at DESC'
);
$query->execute([$agencyId]);
$bookings = $query->fetchAll();

renderHeader('Agency Bookings');
?>

<section>
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h4">View Booked Cars</h2>
            <p class="text-muted">List of all customers who booked cars added by your agency.</p>

            <?php if (!$bookings): ?>
                <p class="mb-0">No bookings available yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Car</th>
                                <th>Vehicle #</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Days</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Total (Rs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['model']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['vehicle_number']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                                    <td><?php echo (int) $booking['rental_days']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                                    <td><?php echo number_format((float) $booking['total_price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php renderFooter();
