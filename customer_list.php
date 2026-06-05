<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

/* ================= SAFE QUERY ================= */
function runQuery($conn, $sql){
    $result = mysqli_query($conn, $sql);
    if(!$result){
        die("SQL ERROR : " . mysqli_error($conn));
    }
    return $result;
}

/* ================= DELETE CUSTOMER ================= */
if (isset($_GET['delete'])) {
    $cus_id = $_GET['delete'];
    
    runQuery($conn, "DELETE FROM customer WHERE cus_id='$cus_id'");
    
    echo "<script>
    alert('Customer Deleted');
    window.location='customer_list.php';
    </script>";
    exit;
}

/* ================= SEARCH & FILTER ================= */
$search = "";
if (isset($_GET['search']) && $_GET['search'] != "") {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM customer 
            WHERE cus_id LIKE '%$search%' 
            OR fl_name LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR con_no LIKE '%$search%'
            ORDER BY cus_id DESC";
} else {
    $sql = "SELECT * FROM customer ORDER BY cus_id DESC";
}

$result = runQuery($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
        }

        .wrapper {
            display: flex;
            flex-direction: row;
        }

        /* Sidebar Responsive Settings */
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

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .container {
            padding: 20px;
        }

        .top-bar {
            padding: 20px 20px 15px 20px;
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

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-form {
            display: flex;
            gap: 8px;
        }

        input {
            padding: 8px 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-input {
            width: 250px;
        }

        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .po-btn { background: #2563eb; color: #fff; }
        .grn-btn { background: #059669; color: #fff; }
        .del-btn { background: #dc2626; color: #fff; text-decoration: none; display: inline-block; }

        /* Responsive Tables */
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
            min-width: 800px;
        }

        th {
            background: #0f766e;
            color: #fff;
            padding: 12px 10px;
            text-align: left;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: #334155;
            font-size: 14px;
        }

        .action-cell {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        /* Media Queries for Mobile/Tablet views */
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

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
                flex-direction: column;
                align-items: flex-start;
            }

            .search-form {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }

                    }

        .sidebar a.active {
            background: #115e59;
            font-weight: bold;
        }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="sidebar">
        <h2>💊 Drugs4U</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="item.php">Items</a>
	    <a href="vendor.php">Vendor</a>
        <a href="customer_list.php" class="active">Customers</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
	<a href="sales_invoice.php">Sales Invoice</a>
    </div>

    <div class="main-content">

        <div class="top-bar">
            <h2>👤 Customer List</h2>
            
            <div class="action-buttons">
                <form method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="Search customer..." value="<?= htmlspecialchars($search); ?>">
                    <button type="submit" class="po-btn">Search</button>
                </form>

                <a href="customer_registration.php">
                    <button type="button" class="grn-btn">+ Add Customer</button>
                </a>
            </div>
        </div>

        <div class="container">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Age</th>
                            <th>City</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0) { ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['cus_id'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['fl_name'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['gender'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['DOB'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['age'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['city'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['con_no'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                                <td>
                                    <div class="action-cell">
                                        <a href="?delete=<?= urlencode($row['cus_id']); ?>" class="del-btn" onclick="return confirm('Delete this customer?')">
                                            <button type="button" class="del-btn">Delete</button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?> <tr>
                                <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">No customers found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div> 

</body>
</html>