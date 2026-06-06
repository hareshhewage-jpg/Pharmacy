<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'connection.php';

/* ================= SAFE QUERY ================= */
function runQuery($conn, $sql){
    $result = mysqli_query($conn, $sql);

    if(!$result){
        die("SQL ERROR : " . mysqli_error($conn));
    }

    return $result;
}

/* ================= DELETE VENDOR ================= */
if(isset($_GET['delete_vendor'])){

    $vendor_id = mysqli_real_escape_string($conn, $_GET['delete_vendor']);

    $delete = mysqli_query($conn, "DELETE FROM vendor_master WHERE vendor_id='$vendor_id'");

    if($delete){
        $_SESSION['msg'] = "Vendor Deleted Successfully";
    }else{
        $_SESSION['msg'] = mysqli_error($conn);
    }

    echo "<script>
    alert('" . mysqli_real_escape_string($conn, $_SESSION['msg']) . "');
    window.location='vendor.php';
    </script>";
    exit;
}

/* ================= EDIT MODE ================= */
$edit_mode = false;
$edit_vendor = null;

if(isset($_GET['edit_vendor'])){
    $edit_mode = true;
    $vendor_id = mysqli_real_escape_string($conn, $_GET['edit_vendor']);
    
    $res = runQuery($conn, "SELECT * FROM vendor_master WHERE vendor_id='$vendor_id'");
    $edit_vendor = mysqli_fetch_assoc($res);
}

/* ================= SAVE VENDOR ================= */
if(isset($_POST['save_vendor'])){

    $vendor_id = mysqli_real_escape_string($conn, $_POST['vendor_id']);
    $vendor_name = mysqli_real_escape_string($conn, $_POST['vendor_name']);
    $business_registration = mysqli_real_escape_string($conn, $_POST['business_registration']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $postal_code = mysqli_real_escape_string($conn, $_POST['postal_code']);
    $address1 = mysqli_real_escape_string($conn, $_POST['address1']);
    $address2 = mysqli_real_escape_string($conn, $_POST['address2']);
    $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    $fax = mysqli_real_escape_string($conn, $_POST['fax']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if($_POST['edit_mode'] == '1'){
        
        runQuery($conn, "UPDATE vendor_master SET
            vendor_name='$vendor_name',
            business_registration='$business_registration',
            country='$country',
            postal_code='$postal_code',
            address1='$address1',
            address2='$address2',
            contact_person='$contact_person',
            mobile='$mobile',
            contact_no='$contact_no',
            fax='$fax',
            email='$email'
        WHERE vendor_id='$vendor_id'");
        
        $_SESSION['msg'] = "Vendor Updated Successfully";
    } else {
        
        runQuery($conn, "INSERT INTO vendor_master (
            vendor_id, vendor_name, business_registration, country, postal_code,
            address1, address2, contact_person, mobile, contact_no, fax, email
        ) VALUES (
            '$vendor_id', '$vendor_name', '$business_registration', '$country', '$postal_code',
            '$address1', '$address2', '$contact_person', '$mobile', '$contact_no', '$fax', '$email'
        )");
        
        $_SESSION['msg'] = "Vendor Registered Successfully";
    }

    echo "<script>
    alert('" . mysqli_real_escape_string($conn, $_SESSION['msg']) . "');
    window.location='vendor.php';
    </script>";
    exit;
}

/* ================= AUTO VENDOR ID ================= */
$res_id = runQuery($conn, "SELECT vendor_id FROM vendor_master ORDER BY vendor_id DESC LIMIT 1");

if(mysqli_num_rows($res_id) > 0){
    $row_id = mysqli_fetch_assoc($res_id);
    $num = (int)substr($row_id['vendor_id'], 3) + 1;
    $vendor_id = "VEN" . str_pad($num, 4, "0", STR_PAD_LEFT);
} else {
    $vendor_id = "VEN0001";
}

/* ================= FETCH VENDORS ================= */
$vendor_list = runQuery($conn, "SELECT * FROM vendor_master ORDER BY vendor_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Master System</title>
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
        <a href="item.php">Items</a>
        <a href="vendor.php">Vendor</a>
        <a href="customer_list.php">Customers</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
        <a href="sales_invoice.php">Sales Invoice</a>
        <a href="report.php" class="active">Report</a>
    </div>
</body>
</html>