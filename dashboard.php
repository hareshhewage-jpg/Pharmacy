<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'connection.php';

/* ================= SAFE QUERY HELPER ================= */
function runQuery($conn, $sql){
    $result = mysqli_query($conn, $sql);
    if(!$result){
        die("SQL ERROR : " . mysqli_error($conn));
    }
    return $result;
}

// ================= CUSTOMER COUNTS =================
$totalCustomersRes = runQuery($conn, "SELECT COUNT(*) as total FROM customer");
$totalCustomers = mysqli_fetch_assoc($totalCustomersRes)['total'];

$todayCustomersRes = runQuery($conn, "SELECT COUNT(*) as total FROM customer WHERE DATE(DOB)=CURDATE()");
$todayCustomers = mysqli_fetch_assoc($todayCustomersRes)['total'];

$customers = runQuery($conn, "SELECT * FROM customer ORDER BY cus_id DESC LIMIT 5");

// ================= PURCHASE ORDER COUNTS =================
$totalPOsRes = runQuery($conn, "SELECT COUNT(*) as total FROM purchase_order");
$totalPOs = mysqli_fetch_assoc($totalPOsRes)['total'];

$openPOsRes = runQuery($conn, "SELECT COUNT(*) as total FROM purchase_order WHERE UPPER(status)='OPEN'");
$openPOs = mysqli_fetch_assoc($openPOsRes)['total'];

// Calculate total value ordered across all orders
$poValueRes = runQuery($conn, "SELECT COALESCE(SUM(total), 0) as total_val FROM purchase_order_items");
$poValue = mysqli_fetch_assoc($poValueRes)['total_val'];

// Fetch the 5 most recent purchase orders
$recent_pos = runQuery($conn, "SELECT * FROM purchase_order ORDER BY po_id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare Admin Dashboard</title>
    <style>
        /* ================= RESET & BASE TYPOGRAPHY ================= */
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: row;
        }

        /* ================= NAVIGATION SIDEBAR (Identical to Item Page) ================= */
        .sidebar {
            width: 260px;
            background: #0f766e;
            color: #fff;
            height: 100vh;
            position: fixed;
            padding: 20px;
            box-sizing: border-box;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 10px;
        }

        .sidebar a:hover {
            background: #115e59;
        }

        .sidebar a.active {
            background: #115e59;
            font-weight: bold;
        }

        /* ================= MAIN CONTENT WORKSPACE ================= */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .container {
            padding: 20px;
        }

        .top-bar {
            padding: 20px 20px 5px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .top-bar h2 {
            margin: 0;
            color: #334155;
        }

        /* ================= STATISTICAL CARDS ================= */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .card h3 {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card h1 {
            font-size: 28px;
            color: #1e293b;
        }

        /* ================= DATA TABLES BREAKDOWN ================= */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }

        @media (min-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .table-section {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .table-section h2 {
            margin-bottom: 15px;
            color: #334155;
            font-size: 18px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            min-width: 500px;
        }

        th {
            background: #0f766e;
            color: #fff;
            padding: 12px 10px;
            font-size: 14px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: #475569;
            font-size: 14px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-open { background: #fef3c7; color: #d97706; }
        .status-completed { background: #d1fae5; color: #059669; }

        /* ================= RESPONSIVE MEDIA QUERIES ================= */
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                padding: 15px;
            }
            .sidebar h2 {
                margin-bottom: 15px;
            }
            .sidebar a {
                display: inline-block;
                margin-right: 10px;
                margin-bottom: 5px;
                padding: 8px 12px;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="sidebar">
        <h2>💊 Drugs4U</h2>
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="item.php">Items</a>
        <a href="vendor.php">Vendor</a>
        <a href="customer_list.php">Customers</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
        <a href="sales_invoice.php">Sales Invoice</a>
    </div>

    <div class="main-content">
        
        <div class="top-bar">
            <h2>Admin Dashboard</h2>
        </div>

        <div class="container">

            <div class="cards">
                <div class="card">
                    <h3>Total Customers</h3>
                    <h1><?php echo htmlspecialchars($totalCustomers); ?></h1>
                </div>

                <div class="card">
                    <h3>Today Registrations</h3>
                    <h1><?php echo htmlspecialchars($todayCustomers); ?></h1>
                </div>

                <div class="card">
                    <h3>Total POs Issued</h3>
                    <h1><?php echo htmlspecialchars($totalPOs); ?></h1>
                </div>

                <div class="card">
                    <h3>Open Purchase Orders</h3>
                    <h1 style="color: #d97706;"><?php echo htmlspecialchars($openPOs); ?></h1>
                </div>

                <div class="card">
                    <h3>Total Capital Value</h3>
                    <h1>Rs. <?php echo number_format($poValue, 2); ?></h1>
                </div>
            </div>

            <div class="dashboard-grid">

                <div class="table-section">
                    <h2>Recent Customers</h2>
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>Contact</th>
                            </tr>
                            <?php if(mysqli_num_rows($customers) > 0) { ?>
                                <?php while($row = mysqli_fetch_assoc($customers)){ ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['cus_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fl_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['city']); ?></td>
                                    <td><?php echo htmlspecialchars($row['con_no']); ?></td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4" style="text-align:center;">No recent customers found</td></tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

                <div class="table-section">
                    <h2>Recent Purchase Orders</h2>
                    <div class="table-responsive">
                        <table>
                            <tr>
                                <th>PO No</th>
                                <th>Vendor Name</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                            <?php if(mysqli_num_rows($recent_pos) > 0) { ?>
                                <?php while($po = mysqli_fetch_assoc($recent_pos)){ 
                                    $statusClass = (strtoupper($po['status']) == 'COMPLETED') ? 'status-completed' : 'status-open';
                                ?>
                                <tr>
                                    <td><a href="purchase_order.php?edit_po=<?= urlencode($po['po_no']) ?>" style="color:#0f766e; font-weight:500; text-decoration:none;"><?= htmlspecialchars($po['po_no']) ?></a></td>
                                    <td><?= htmlspecialchars($po['vendor_name']) ?></td>
                                    <td><?= htmlspecialchars($po['po_date']) ?></td>
                                    <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($po['status']) ?></span></td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4" style="text-align:center;">No recent purchase orders found</td></tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>

            </div> 
        </div> 
    </div> 
</div> 
</body>
</html>