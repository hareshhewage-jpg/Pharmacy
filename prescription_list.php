<?php
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login_emp.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

/* ================= UPDATE STATUS ================= */
if(isset($_POST['update_status'])){
    $id = $_POST['id'];
    $status = $_POST['status'];

    $updateSql = "UPDATE prescriptions SET status='$status' WHERE id='$id'";
    if(!mysqli_query($conn, $updateSql)){
        die("UPDATE ERROR: " . mysqli_error($conn));
    }

    header("Location: prescription_list.php");
    exit;
}

/* ================= FETCH PRESCRIPTIONS ================= */
$sql = "SELECT * FROM prescriptions ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("SQL ERROR: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription List - PharmaCare</title>

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

        .sidebar a.active {
            background: #115e59;
            font-weight: bold;
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

        /* Action elements uniform styles */
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            color: #fff;
            text-align: center;
        }

        .view-btn { background: #2563eb; }
        .download-btn { background: #059669; }
        .update-btn { background: #f59e0b; }

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
            min-width: 700px;
        }

        th {
            background: #0f766e;
            color: #fff;
            padding: 12px 10px;
            text-align: left;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            color: #334155;
        }

        .action-cell {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #64748b;
            font-style: italic;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 200;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            color: #64748b;
        }
        
        .close:hover {
            color: #000;
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
        <a href="customer_list.php">Customers</a>
        <a href="employee_reg.php">Employees</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php" class="active">Prescriptions</a>
        <a href="sales_invoice.php">Sales Invoice</a>
        <a href="report.php">Report</a>
        <a href="logout.php">Logout</a> 
    </div>

    <div class="main-content">

        <div class="top-bar">
            <h2>Prescription List</h2>
        </div>

        <div class="container">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Customer ID</th>
                            <th>Patient Name</th>
                            <th style="width: 80px;">Age</th>
                            <th>Allergies</th>
                            <th>Contact No</th>
                            <th>Status</th>
                            <th style="text-align: center; width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0) { ?>
                            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><strong><?= $row['id']; ?></strong></td>
                                    <td><?= htmlspecialchars($row['cus_id']); ?></td>
                                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                                    <td><?= htmlspecialchars($row['age']); ?></td>
                                    <td><?= htmlspecialchars($row['allergies']); ?></td>
                                    <td><?= htmlspecialchars($row['contact_no']); ?></td>
                                    <td><?= htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <div class="action-cell">
                                            <a class="btn view-btn" 
                                               href="uploads/<?= urlencode($row['prescription_file']); ?>" 
                                               target="_blank">View</a>
                                             
                                            <a class="btn download-btn" 
                                               href="uploads/<?= urlencode($row['prescription_file']); ?>" 
                                               download>Download</a>
                                             
                                            <button class="btn update-btn"
                                                    onclick="openModal(<?= $row['id']; ?>, '<?= $row['status']; ?>')">
                                                Update
                                            </button>            
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="empty">No prescriptions found in the system database.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div class="modal" id="statusModal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>

        <h3>Update Status</h3>

        <form method="POST">
            <input type="hidden" name="id" id="prescription_id">

            <label>Status</label><br><br>
            <select name="status" id="status" required>
                <option value="Pending">Pending</option>
                <option value="Invoiced">Invoiced</option>
                <option value="Rejected">Rejected</option>
            </select>

            <br><br>
            <button type="submit" name="update_status" class="btn update-btn">
                Save
            </button>
        </form>
    </div>
</div>

<script>
function openModal(id, status){
    document.getElementById('prescription_id').value = id;
    document.getElementById('status').value = status;
    document.getElementById('statusModal').style.display = 'flex';
}

function closeModal(){
    document.getElementById('statusModal').style.display = 'none';
}

window.onclick = function(event){
    let modal = document.getElementById('statusModal');
    if(event.target == modal){
        modal.style.display = 'none';
    }
}
</script>

</body>
</html>