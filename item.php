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

/* ================= DELETE ITEM ================= */
if(isset($_GET['delete'])){
    $item_no = mysqli_real_escape_string($conn, $_GET['delete']);

    $delete = mysqli_query($conn, "DELETE FROM item_master WHERE item_no='$item_no'");
    
    if($delete){
        $_SESSION['msg'] = "Item Deleted Successfully";
    } else {
        $_SESSION['msg'] = mysqli_error($conn);
    }

    echo "<script>
    alert('" . mysqli_real_escape_string($conn, $_SESSION['msg']) . "');
    window.location='item.php';
    </script>";
    exit;
}

/* ================= EDIT MODE LOAD ================= */
$isEdit = false;
$edit = null;

if(isset($_GET['edit'])){
    $isEdit = true;
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    
    $res = runQuery($conn, "SELECT * FROM item_master WHERE item_no='$id'");
    $edit = mysqli_fetch_assoc($res);
}

/* ================= AUTO ITEM NO GENERATION ================= */
$result = runQuery($conn, "SELECT item_no FROM item_master ORDER BY item_no DESC LIMIT 1");

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    $num = (int)substr($row['item_no'], 3) + 1;
    $item_no = "ITM" . str_pad($num, 4, "0", STR_PAD_LEFT);
} else {
    $item_no = "ITM0001";
}

/* ================= SAVE / UPDATE ITEM ================= */
if(isset($_POST['save_item'])){
    $item_no = mysqli_real_escape_string($conn, $_POST['item_no']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit_measurement']);
    $country = mysqli_real_escape_string($conn, $_POST['manufacturer_country']);
    $pack = mysqli_real_escape_string($conn, $_POST['pack_size']);
    $cost = mysqli_real_escape_string($conn, $_POST['unit_cost']);
    $price = mysqli_real_escape_string($conn, $_POST['unit_price']);

    if($_POST['edit_mode'] == '1'){
        runQuery($conn, "UPDATE item_master SET
            item_name='$item_name',
            description='$description',
            brand='$brand',
            unit_measurement='$unit',
            manufacturer_country='$country',
            pack_size='$pack',
            unit_cost='$cost',
            unit_price='$price'
        WHERE item_no='$item_no'");
        
        $_SESSION['msg'] = "Item Updated Successfully";
    } else {
        runQuery($conn, "INSERT INTO item_master (
            item_no, item_name, description, brand,
            unit_measurement, manufacturer_country,
            pack_size, unit_cost, unit_price
        ) VALUES (
            '$item_no', '$item_name', '$description', '$brand',
            '$unit', '$country', '$pack', '$cost', '$price'
        )");
        
        $_SESSION['msg'] = "Item Registered Successfully";
    }

    echo "<script>
    alert('" . mysqli_real_escape_string($conn, $_SESSION['msg']) . "');
    window.location='item.php';
    </script>";
    exit;
}

/* ================= SEARCH & FETCH ITEMS ================= */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : "";

$query_str = "SELECT * FROM item_master";
if(!empty($search)){
    $query_str .= " WHERE 
        item_no LIKE '%$search%' OR
        item_name LIKE '%$search%' OR
        brand LIKE '%$search%' OR
        manufacturer_country LIKE '%$search%'";
}
$query_str .= " ORDER BY item_no DESC";

$items = runQuery($conn, $query_str);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Master System</title>
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
        /* Sidebar Layout (Identical to Vendor page) */
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
        textarea {
            resize: none;
            height: 60px;
        }
        /* Modals & Popups Setup matching Vendor layout */
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
            max-width: 1100px;
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
        .full-width {
            grid-column: span 2;
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
            .full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <h2>💊 Drugs4U</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="item.php" class="active">Items</a>
        <a href="vendor.php">Vendor</a>
        <a href="customer_list.php">Customers</a>
        <a href="employee_reg.php">Employees</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
        <a href="sales_invoice.php">Sales Invoice</a>
        <a href="report.php">Report</a>
        <a href="login_emp.php">Logout</a> 
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Item Master</h2>
            <div class="action-buttons">
                <button class="po-btn" onclick="openItemPopup()">+ Register Item</button>
            </div>
        </div>

        <div class="filter-bar">
            <form method="GET" id="searchForm" style="width: 100%;">
                <input type="text" name="search" id="searchBox" placeholder="Search Item across all fields..." value="<?php echo htmlspecialchars($search); ?>">
            </form>
        </div>

        <div class="container">
            <div class="table-responsive">
                <table id="itemTable">
                    <tr>
                        <th>Item No</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Unit</th>
                        <th>Country</th>
                        <th>Cost</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                    <?php while($row = mysqli_fetch_assoc($items)){ ?>
                    <tr>
                        <td><?= htmlspecialchars($row['item_no']) ?></td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['brand']) ?></td>
                        <td><?= htmlspecialchars($row['unit_measurement']) ?></td>
                        <td><?= htmlspecialchars($row['manufacturer_country']) ?></td>
                        <td><?= htmlspecialchars($row['unit_cost']) ?></td>
                        <td><?= htmlspecialchars($row['unit_price']) ?></td>
                        <td>
                            <div class="action-cell">
                                <a href="?edit=<?= urlencode($row['item_no']) ?>"><button class="edit-btn">Edit</button></a>
                                <a href="?delete=<?= urlencode($row['item_no']) ?>" onclick="return confirm('Delete item master data permanently?')"><button class="del-btn">Delete</button></a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="popup" id="itemPopup" style="<?= $isEdit ? 'display:flex' : '' ?>">
    <div class="popup-content">
        <span class="close-btn" onclick="window.location.href='item.php'">×</span>
        <h3><?= $isEdit ? 'Edit Item Profile' : 'Register New Item' ?></h3>

        <form method="POST">
            <input type="hidden" name="edit_mode" value="<?= $isEdit ? 1 : 0 ?>">

            <div class="grid">
                <div>
                    <label>Item No</label>
                    <input name="item_no" value="<?= $isEdit ? htmlspecialchars($edit['item_no']) : htmlspecialchars($item_no) ?>" readonly>
                </div>

                <div>
                    <label>Item Name</label>
                    <input name="item_name" value="<?= $isEdit ? htmlspecialchars($edit['item_name']) : '' ?>" required>
                </div>

                <div>
                    <label>Brand</label>
                    <input name="brand" value="<?= $isEdit ? htmlspecialchars($edit['brand']) : '' ?>">
                </div>

                <div>
                    <label>Unit of Measurement</label>
                    <input name="unit_measurement" value="<?= $isEdit ? htmlspecialchars($edit['unit_measurement']) : '' ?>">
                </div>

                <div class="full-width">
                    <label>Description</label>
                    <textarea name="description"><?= $isEdit ? htmlspecialchars($edit['description']) : '' ?></textarea>
                </div>

                <div>
                    <label>Manufacturer Country</label>
                    <input name="manufacturer_country" value="<?= $isEdit ? htmlspecialchars($edit['manufacturer_country']) : '' ?>">
                </div>

                <div>
                    <label>Pack Size</label>
                    <input name="pack_size" value="<?= $isEdit ? htmlspecialchars($edit['pack_size']) : '' ?>">
                </div>

                <div>
                    <label>Unit Cost</label>
                    <input type="number" step="0.01" name="unit_cost" value="<?= $isEdit ? htmlspecialchars($edit['unit_cost']) : '' ?>">
                </div>

                <div>
                    <label>Unit Price</label>
                    <input type="number" step="0.01" name="unit_price" value="<?= $isEdit ? htmlspecialchars($edit['unit_price']) : '' ?>">
                </div>
            </div>

            <br>
            <button type="submit" class="grn-btn" name="save_item">Save Item Profile</button>
            <button type="button" class="del-btn" onclick="window.location.href='item.php'">Cancel</button>
        </form>
    </div>
</div>

<script>
function openItemPopup() {
    document.getElementById("itemPopup").style.display = 'flex';
}

function closeItemPopup() {
    document.getElementById("itemPopup").style.display = 'none';
}

/* Debounced Server Search Form submission */
document.getElementById("searchBox").addEventListener("keyup", function(){
    clearTimeout(this.delay);
    this.delay = setTimeout(() => document.getElementById("searchForm").submit(), 400);
});
</script>

</body>
</html>
