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

/* ================= DELETE EMPLOYEE ================= */
if(isset($_GET['delete'])){
    $emp_id = mysqli_real_escape_string($conn, $_GET['delete']);

    $delete = mysqli_query($conn, "DELETE FROM users WHERE emp_id='$emp_id'");
    
    if($delete){
        $_SESSION['msg'] = "Employee Deleted Successfully";
    } else {
        $_SESSION['msg'] = mysqli_error($conn);
    }

    echo "<script>
    alert('" . mysqli_real_escape_string($conn, $_SESSION['msg']) . "');
    window.location='employee_reg.php';
    </script>";
    exit;
}

/* ================= EDIT MODE LOAD ================= */
$isEdit = false;
$edit = null;

if(isset($_GET['edit'])){
    $isEdit = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    
    $res = runQuery($conn, "SELECT * FROM users WHERE emp_id='$id'");
    $edit = mysqli_fetch_assoc($res);
}

/* ================= AUTO EMPLOYEE ID GENERATION ================= */
$result = runQuery($conn, "SELECT emp_id FROM users ORDER BY emp_id DESC LIMIT 1");

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    $num = (int)substr($row['emp_id'], 3) + 1;
    $emp_id = "EMP" . str_pad($num, 4, "0", STR_PAD_LEFT);
} else {
    $emp_id = "EMP0001";
}

/* ================= SAVE / UPDATE EMPLOYEE ================= */
if(isset($_POST['save_employee'])){
    $emp_id    = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $user_role = mysqli_real_escape_string($conn, $_POST['user_role']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);

    if($_POST['edit_mode'] == '1'){
        // Update operational data
        $sql = "UPDATE users SET
                    full_name='$full_name',
                    username='$username',
                    user_role='$user_role',
                    status='$status'";
        
        // Only update password if a new one is provided
        if(!empty($_POST['password'])){
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql .= ", password='$password'";
        }
        
        $sql .= " WHERE emp_id='$emp_id'";
        runQuery($conn, $sql);
        
        $_SESSION['msg'] = "Employee Updated Successfully";
    } else {
        // Insert new record
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $created_date = date("Y-m-d H:i:s");
        
        runQuery($conn, "INSERT INTO users (
            emp_id, full_name, username, password, user_role, status, created_date
        ) VALUES (
            '$emp_id', '$full_name', '$username', '$password', '$user_role', '$status', '$created_date'
        )");
        
        $_SESSION['msg'] = "Employee Registered Successfully";
    }

    echo "<script>
    alert('" . mysqli_real_escape_string($conn, $_SESSION['msg']) . "');
    window.location='employee_reg.php';
    </script>";
    exit;
}

/* ================= SEARCH & FETCH EMPLOYEES ================= */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$query_str = "SELECT * FROM users";
if(!empty($search)){
    $query_str .= " WHERE 
        emp_id LIKE '%$search%' OR
        full_name LIKE '%$search%' OR
        username LIKE '%$search%' OR
        user_role LIKE '%$search%'";
}
$query_str .= " ORDER BY emp_id DESC";

$employees = runQuery($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
        }
        .sidebar a.active {
            background: #115e59;
            font-weight: bold;
        }
        .wrapper {
            display: flex;
            flex-direction: row;
        }
        /* Sidebar Layout */
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
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .filter-bar {
            padding: 5px 20px 15px 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        #searchBox {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button, .btn-link {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .po-btn { background:#2563eb; color:#fff; }
        .grn-btn { background:#059669; color:#fff; }
        .edit-btn { background:#f59e0b; color:#fff; }
        .del-btn { background:#dc2626; color:#fff; }
        
        /* Table Structure Alignment */
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
            min-width: 600px;
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
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        /* Modals & Popups Setup */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            overflow-y: auto;
            padding: 10px;
            box-sizing: border-box;
        }
        .popup-content {
            background: #fff;
            width: 100%;
            max-width: 700px;
            padding: 20px;
            position: relative;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
            max-height: 90vh;
            overflow-y: auto;
            box-sizing: border-box;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 25px;
            cursor: pointer;
            color: #666;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .grid label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
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
            .grid {
                grid-template-columns: 1fr;
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
        <a href="employee_reg.php" class="active">Employees</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
        <a href="sales_invoice.php">Sales Invoice</a>
        <a href="report.php">Report</a>
        <a href="login_emp.php">Logout</a> 
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Employee Master</h2>
            <div class="action-buttons">
                <button class="po-btn" onclick="openEmployeePopup()">+ Register Employee</button>
            </div>
        </div>

        <div class="filter-bar">
            <form method="GET" id="searchForm" style="width: 100%;">
                <input type="text" name="search" id="searchBox" placeholder="Search Employee across all fields..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <div class="container">
            <div class="table-responsive">
                <table id="employeeTable">
                    <tr>
                        <th>Employee ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php while($row = mysqli_fetch_assoc($employees)){ ?>
                    <tr>
                        <td><?= htmlspecialchars($row['emp_id']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['user_role']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <div class="action-cell">
                                <a href="?edit=<?= urlencode($row['emp_id']) ?>"><button class="edit-btn">Edit</button></a>
                                <a href="?delete=<?= urlencode($row['emp_id']) ?>" onclick="return confirm('Delete employee permanently?')"><button class="del-btn">Delete</button></a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="popup" id="employeePopup" style="<?= $isEdit ? 'display:flex' : '' ?>">
    <div class="popup-content">
        <span class="close-btn" onclick="window.location.href='employee_reg.php'">×</span>
        <h3><?= $isEdit ? 'Edit Employee Profile' : 'Register New Employee' ?></h3>

        <form method="POST">
            <input type="hidden" name="edit_mode" value="<?= $isEdit ? 1 : 0 ?>">

            <div class="grid">
                <div>
                    <label>Employee ID</label>
                    <input name="emp_id" value="<?= $isEdit ? htmlspecialchars($edit['emp_id']) : htmlspecialchars($emp_id) ?>" readonly>
                </div>

                <div>
                    <label>Full Name</label>
                    <input name="full_name" value="<?= $isEdit ? htmlspecialchars($edit['full_name']) : '' ?>" required>
                </div>

                <div>
                    <label>Username</label>
                    <input name="username" value="<?= $isEdit ? htmlspecialchars($edit['username']) : '' ?>" required>
                </div>

                <div>
                    <label>Password <?= $isEdit ? '(Leave blank to keep unchanged)' : '' ?></label>
                    <input type="password" name="password" <?= $isEdit ? '' : 'required' ?>>
                </div>

                <div>
                    <label>User Role</label>
                    <select name="user_role" required>
                        <option value="Admin" <?= ($isEdit && $edit['user_role'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="Pharmacist" <?= ($isEdit && $edit['user_role'] == 'Pharmacist') ? 'selected' : '' ?>>Pharmacist</option>
                        <option value="Cashier" <?= ($isEdit && $edit['user_role'] == 'Cashier') ? 'selected' : '' ?>>Cashier</option>
                    </select>
                </div>

                <div>
                    <label>Status</label>
                    <select name="status" required>
                        <option value="Active" <?= ($isEdit && $edit['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                        <option value="Inactive" <?= ($isEdit && $edit['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <br>
            <button type="submit" class="grn-btn" name="save_employee">Save Employee Profile</button>
            <button type="button" class="del-btn" onclick="window.location.href='employee_reg.php'">Cancel</button>
        </form>
    </div>
</div>

<script>
function openEmployeePopup() {
    document.getElementById("employeePopup").style.display = 'flex';
}

function closeEmployeePopup() {

    document.getElementById("employeePopup").style.display = 'none';
}

/* Debounced Server Search Form submission */
document.getElementById("searchBox").addEventListener("keyup", function(){
    clearTimeout(this.delay);
    this.delay = setTimeout(() => document.getElementById("searchForm").submit(), 400);
});
</script>

</body>
</html>