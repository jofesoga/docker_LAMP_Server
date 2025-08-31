<?php
require_once 'config.php';
checkRole(['admin', 'manager', 'staff']);

if (!isset($_GET['id'])) {
    redirect('orders.php');
}

$id = $_GET['id'];
$order = getOrderById($conn, $id);
$items = getOrderItems($conn, $id);

if (!$order) {
    redirect('orders.php');
}

// Handle status update
if (isset($_POST['update_status'])) {
    $status = $_POST['status'];
    
    if (updateOrderStatus($conn, $id, $status)) {
        $_SESSION['message'] = 'Order status updated successfully';
        $_SESSION['message_type'] = 'success';
        redirect("view_order.php?id=$id");
    } else {
        $_SESSION['message'] = 'Error updating order status';
        $_SESSION['message_type'] = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail Store - Order #<?php echo $order['order_id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Order #<?php echo $order['order_id']; ?></h2>
            <div>
                <a href="invoice.php?id=<?php echo $order['order_id']; ?>" class="btn btn-outline-secondary"><i class="bi bi-receipt"></i> Generate Invoice</a>
                <a href="orders.php" class="btn btn-outline-primary">Back to Orders</a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $item['name']; ?></strong><br>
                                                <small class="text-muted"><?php echo $item['description']; ?></small>
                                            </td>
                                            <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Subtotal:</th>
                                        <th>$<?php echo number_format($order['total_amount'], 2); ?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Tax:</th>
                                        <th>$0.00</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Total:</th>
                                        <th>$<?php echo number_format($order['total_amount'], 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Order Date:</label>
                            <p><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status:</label>
                            <form method="POST">
                                <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Payment Method:</label>
                            <p><?php echo $order['payment_method']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Name:</label>
                            <p><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email:</label>
                            <p><?php echo $order['email']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone:</label>
                            <p><?php echo $order['phone']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p><?php echo nl2br($order['address']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>