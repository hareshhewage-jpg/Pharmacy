<?php
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login_emp.php");
    exit;
}
?>
<?php
include 'connection.php';

$so_no = $_GET['so_no'];

/* HEADER */
$inv = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT * FROM sales_order WHERE so_no='$so_no'
"));

/* ITEMS */
$items = mysqli_query($conn,"
SELECT * FROM sales_order_items WHERE so_no='$so_no'
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Print Invoice</title>

<style>

/* ================= A4 STYLE ================= */
body{
    font-family: Arial;
    margin:0;
    padding:20px;
    background:#fff;
}

.invoice-box{
    max-width:800px;
    margin:auto;
    border:1px solid #eee;
    padding:20px;
}

.header{
    text-align:center;
}

h2{margin:0;color:#0f766e;}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#0f766e;
    color:#fff;
    padding:8px;
}

td{
    padding:8px;
    border-bottom:1px solid #ddd;
}

.total{
    text-align:right;
    font-weight:bold;
}

/* ================= POS STYLE ================= */
@media print {
    .no-print{display:none;}
}

.pos{
    width:300px;
    margin:auto;
    font-size:12px;
}

/* toggle */
.pos-mode .invoice-box{
    display:none;
}

.pos-mode .pos{
    display:block;
}

.pos{
    display:none;
}

</style>
</head>

<body>

<div class="no-print" style="text-align:center;margin-bottom:10px;">
<button onclick="window.print()">Print</button>
<button onclick="toggleMode()">Switch A4 / POS</button>
</div>

<!-- ================= A4 INVOICE ================= -->
<div class="invoice-box">

<div class="header">
<h2>Drugs4U Pharmacy</h2>
<p>INVOICE</p>
</div>

<p>
<b>Invoice No:</b> <?= $inv['so_no'] ?><br>
<b>Date:</b> <?= $inv['so_date'] ?><br>
<b>Customer:</b> <?= $inv['cus_name'] ?><br>
<b>Address:</b> <?= $inv['address'] ?>, <?= $inv['city'] ?>
</p>

<table>
<tr>
<th>Item</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
</tr>

<?php
$grand=0;
while($i=mysqli_fetch_assoc($items)){
$grand += $i['total'];
?>

<tr>
<td><?= $i['item_name'] ?></td>
<td><?= $i['price'] ?></td>
<td><?= $i['qty'] ?></td>
<td><?= $i['total'] ?></td>
</tr>

<?php } ?>

<tr>
<td colspan="3" class="total">Grand Total</td>
<td><b><?= $grand ?></b></td>
</tr>

</table>

</div>

<!-- ================= POS RECEIPT ================= -->
<div class="pos">

<h3>PharmaCare</h3>
<p>Invoice: <?= $inv['so_no'] ?></p>
<p>Date: <?= $inv['so_date'] ?></p>

<hr>

<?php
mysqli_data_seek($items,0);
$grand=0;
while($i=mysqli_fetch_assoc($items)){
$grand += $i['total'];
?>

<?= $i['item_name'] ?><br>
<?= $i['qty'] ?> x <?= $i['price'] ?> = <?= $i['total'] ?><br>

<?php } ?>

<hr>

<b>Total: <?= $grand ?></b>

<p style="text-align:center">Thank You!</p>

</div>

<script>
function toggleMode(){
document.body.classList.toggle("pos-mode");
}
</script>

</body>
</html>