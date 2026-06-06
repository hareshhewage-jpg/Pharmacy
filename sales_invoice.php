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

/* ================= LOAD DATA ================= */
$customers = runQuery($conn, "SELECT * FROM customer");
$items = runQuery($conn, "SELECT * FROM item_master");

/* ================= INVOICE NO ================= */
$res = runQuery($conn, "SELECT so_no FROM sales_order ORDER BY so_id DESC LIMIT 1");

if(mysqli_num_rows($res) > 0){
    $row = mysqli_fetch_assoc($res);
    $num = (int)substr($row['so_no'], 3) + 1;
    $so_no = "INV" . str_pad($num, 5, "0", STR_PAD_LEFT);
} else {
    $so_no = "INV00001";
}

/* ================= SAVE INVOICE ================= */
if(isset($_POST['save_invoice'])){

    $so_no = $_POST['so_no'];
    $so_date = $_POST['so_date'];
    $cus_id = $_POST['cus_id'];
    $cus_name = $_POST['cus_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];

    $total_invoice = 0;

    runQuery($conn, "INSERT INTO sales_order
    (so_no, so_date, cus_id, cus_name, address, city, total_amount)
    VALUES
    ('$so_no', '$so_date', '$cus_id', '$cus_name', '$address', '$city', 0)");

    for($i = 0; $i < count($_POST['item_no']); $i++){

        if($_POST['item_no'][$i] == '') continue;

        $item_no = $_POST['item_no'][$i];
        $item_name = $_POST['item_name'][$i];
        $price = (float)$_POST['price'][$i];
        $qty = (int)$_POST['qty'][$i];
        $total = (float)$_POST['total'][$i];

        $total_invoice += $total;

        /* ITEM INSERT */
        runQuery($conn, "INSERT INTO sales_order_items
        (so_no, item_no, item_name, price, qty, total)
        VALUES
        ('$so_no', '$item_no', '$item_name', '$price', '$qty', '$total')");

        /* STOCK UPDATE */
        runQuery($conn, "UPDATE item_master 
        SET stock_qty = stock_qty - $qty 
        WHERE item_no='$item_no'");
    }

    runQuery($conn, "UPDATE sales_order SET total_amount='$total_invoice' WHERE so_no='$so_no'");

    echo "<script>alert('Invoice Saved Successfully');window.location='sales_invoice.php';</script>";
    exit;
}

/* ================= LIST ================= */
$list = runQuery($conn, "SELECT * FROM sales_order ORDER BY so_id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - PharmaCare</title>

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

        .sidebar a.active {
        background: #115e59;
        font-weight: bold;
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

        button {
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .po-btn { background: #2563eb; color: #fff; }
        .grn-btn { background: #059669; color: #fff; }
        .edit-btn { background: #f59e0b; color: #fff; }
        .del-btn { background: #dc2626; color: #fff; }
        .print-btn { background: #6b7280; color: #fff; }

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

        input, select {
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
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
		<a href="sales_invoice.php" class="active">Sales Invoice</a>
		<a href="report.php">Report</a>
    </div>

    <div class="main-content">

        <div class="top-bar">
            <h2>Sales Invoice System</h2>
            <div class="action-buttons">
                <button class="po-btn" onclick="openPopup()">+ Create Invoice</button>
            </div>
        </div>

        <div class="container">
            <div class="table-responsive">
                <table>
                    <tr>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>

                    <?php while($r = mysqli_fetch_assoc($list)){ ?>
                    <tr>
                        <td><?= $r['so_no'] ?></td>
                        <td><?= $r['so_date'] ?></td>
                        <td><?= $r['cus_name'] ?></td>
                        <td><?= number_format($r['total_amount'], 2) ?></td>
                        <td>
                            <div class="action-cell">
                                <a href="print_invoice.php?so_no=<?= urlencode($r['so_no']) ?>" target="_blank">
                                    <button class="print-btn" type="button">Print</button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <div class="popup" id="invPopup">
            <div class="popup-content">
                <span class="close-btn" onclick="closePopup()">×</span>
                <h3>Create Sales Invoice</h3>

                <form method="POST">
                    <div class="grid">
                        <div>
                            <label>Invoice No</label>
                            <input name="so_no" value="<?= $so_no ?>" readonly>
                        </div>

                        <div>
                            <label>Invoice Date</label>
                            <input type="date" name="so_date" value="<?= date('Y-m-d') ?>">
                        </div>

                        <div>
                            <label>Select Customer</label>
                            <select name="cus_id" id="cus" onchange="fillCustomer()">
                                <option value="">Select Customer</option>
                                <?php while($c = mysqli_fetch_assoc($customers)){ ?>
                                <option value="<?= $c['cus_id'] ?>"
                                        data-name="<?= $c['fl_name'] ?>"
                                        data-address="<?= $c['address'] ?>"
                                        data-city="<?= $c['city'] ?>">
                                    <?= $c['cus_id'] ?> - <?= $c['fl_name'] ?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div>
                            <label>Customer Name</label>
                            <input name="cus_name" id="cus_name" readonly>
                        </div>

                        <div>
                            <label>Billing Address</label>
                            <input name="address" id="address" readonly>
                        </div>

                        <div>
                            <label>City</label>
                            <input name="city" id="city" readonly>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="invTable">
                            <tr>
                                <th>Item No</th>
                                <th>Item Name</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </table>
                    </div>

                    <br>
                    <button type="button" class="po-btn" onclick="addRow()">+ Add Item</button>
                    <button type="submit" class="grn-btn" name="save_invoice">Save Invoice</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
// JSON representation of stock master items
let items = [
    <?php 
    mysqli_data_seek($items, 0); 
    while($i = mysqli_fetch_assoc($items)){ 
    ?>
    {
        no: "<?= $i['item_no'] ?>",
        name: "<?= $i['item_name'] ?>",
        price: "<?= $i['unit_price'] ?>"
    },
    <?php } ?>
];

/* Popup Visibility Controllers */
function openPopup(){
    document.getElementById("invPopup").style.display = "flex";
}

function closePopup(){
    document.getElementById("invPopup").style.display = "none";
}

/* Auto fill processing hooks */
function fillCustomer(){
    let s = document.getElementById("cus");
    if(s.selectedIndex > 0) {
        let option = s.options[s.selectedIndex];
        document.getElementById("cus_name").value = option.getAttribute("data-name") || '';
        document.getElementById("address").value = option.getAttribute("data-address") || '';
        document.getElementById("city").value = option.getAttribute("data-city") || '';
    } else {
        document.getElementById("cus_name").value = '';
        document.getElementById("address").value = '';
        document.getElementById("city").value = '';
    }
}

/* Dynamic Line Item Entry Generation */
function addRow(){
    let t = document.getElementById("invTable");
    let row = t.insertRow();

    row.innerHTML = `
        <td>
            <select name="item_no[]" onchange="setItem(this)">
                <option value="">Select</option>
                ${items.map(i => `<option value="${i.no}" data-name="${i.name}" data-price="${i.price}">${i.no}</option>`).join('')}
            </select>
        </td>
        <td><input name="item_name[]" readonly></td>
        <td><input name="price[]" oninput="calcTotal(this)"></td>
        <td><input name="qty[]" oninput="calcTotal(this)"></td>
        <td><input name="total[]" readonly></td>
        <td><button type="button" class="del-btn" onclick="this.closest('tr').remove()">X</button></td>
    `;
}

function setItem(el){
    let row = el.closest("tr");
    let option = el.options[el.selectedIndex];

    row.querySelector('[name="item_name[]"]').value = option.getAttribute("data-name") || '';
    row.querySelector('[name="price[]"]').value = option.getAttribute("data-price") || '';
    calcTotal(row.querySelector('[name="price[]"]'));
}

function calcTotal(el){
    let row = el.closest("tr");
    let price = parseFloat(row.querySelector('[name="price[]"]').value) || 0;
    let qty = parseFloat(row.querySelector('[name="qty[]"]').value) || 0;

    row.querySelector('[name="total[]"]').value = (price * qty).toFixed(2);
}
</script>

</body>
</html>
