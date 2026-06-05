<?php
include 'connection.php';

if(!isset($_GET['po_no'])){
    die("PO No missing");
}

$po_no = $_GET['po_no'];

/* PO HEADER */
$po = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM purchase_order WHERE po_no='$po_no'
"));

/* PO ITEMS */
$items = mysqli_query($conn,"
    SELECT * FROM purchase_order_items WHERE po_no='$po_no'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print PO</title>
    <style>
        body{font-family:Arial;padding:20px;}
        h2{text-align:center;}
        table{width:100%;border-collapse:collapse;margin-top:20px;}
        th,td{border:1px solid #000;padding:8px;text-align:left;}
        th{background:#eee;}
        .no-print{margin-top:20px;}
        @media print {
            .no-print{display:none;}
        }
    </style>
</head>
<body>

<h2>Purchase Order</h2>

<p><b>PO No:</b> <?= $po['po_no'] ?></p>
<p><b>Vendor:</b> <?= $po['vendor_name'] ?></p>
<p><b>Date:</b> <?= $po['po_date'] ?></p>
<p><b>Status:</b> <?= $po['status'] ?></p>

<table>
    <tr>
        <th>Item No</th>
        <th>Item Name</th>
        <th>Cost</th>
        <th>Qty</th>
        <th>Total</th>
    </tr>

    <?php while($row = mysqli_fetch_assoc($items)){ ?>
    <tr>
        <td><?= $row['item_no'] ?></td>
        <td><?= $row['item_name'] ?></td>
        <td><?= $row['cost'] ?></td>
        <td><?= $row['qty'] ?></td>
        <td><?= $row['total'] ?></td>
    </tr>
    <?php } ?>
</table>

<div class="no-print">
    <button onclick="window.print()">Print</button>
    <button onclick="window.close()">Close</button>
</div>

</body>
</html>