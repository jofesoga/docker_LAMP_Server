<?php
require_once 'config.php';
checkRole(['admin', 'manager', 'staff']);

if (!isset($_GET['id'])) {
    redirect('customers.php');
}

$id = $_GET['id'];
$customer = getCustomerById($conn, $id);

if (!$customer) {
    redirect('customers.php');
}

// Get customer orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC");
$stmt->execute([$id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Store - View Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Customer Details</h2>
            <a href="edit_customer.php?id=<?php echo $customer['customer_id']; ?>" class="btn btn-outline-primary">Edit Customer</a>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name:</label>
                            <p><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p><?php echo $customer['email']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone:</label>
                            <p><?php echo $customer['phone'] ? $customer['phone'] : 'N/A'; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p><?php echo $customer['address'] ? nl2br($customer['address']) : 'N/A'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Order History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($orders) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><?php echo $order['order_id']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td><span class="badge bg-<?php echo getStatusColor($order['status']); ?>"><?php echo $order['status']; ?></span></td>
                                                <td><a href="view_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>This customer has no orders yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="customers.php" class="btn btn-secondary mt-3">Back to Customers</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>