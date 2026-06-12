<?php
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login_emp.php");
    exit;
}
?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connection.php';

// Secure the GET inputs
$from = mysqli_real_escape_string($conn, $_GET['from_date']);
$to   = mysqli_real_escape_string($conn, $_GET['to_date']);

$sql = "
SELECT
    soi.item_no,
    soi.item_name,
    SUM(soi.qty) total_qty,
    SUM(soi.total) total_sales
FROM sales_order_items soi
INNER JOIN sales_order so
ON soi.so_no = so.so_no
WHERE so.so_date BETWEEN '$from' AND '$to'
GROUP BY soi.item_no, soi.item_name
ORDER BY total_sales DESC
";

$result = mysqli_query($conn, $sql);
$grand_total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Item Wise Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f766e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 30px; }
        .grand-total-container { margin-top: 25px; text-align: right; }
        .grand-total-box { display: inline-block; background: #f3f4f6; padding: 12px 25px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .grand-total-box h3 { margin: 0; color: #0f766e; }
        .print-btn { background: #6b7280; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; float: right; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    
    <div class="header">
        <h2>Drugs4U - Item Wise Sales Report</h2>
        <p>Period: <?= htmlspecialchars($from) ?> to <?= htmlspecialchars($to) ?></p>
    </div>

    <table>
        <tr>
            <th>Item No</th>
            <th>Item Name</th>
            <th class="text-center">Qty Sold</th>
            <th class="text-right">Sales Value</th>
        </tr>
        
        <?php 
        while($row = mysqli_fetch_assoc($result)) { 
            $grand_total += $row['total_sales'];
        ?>
        <tr>
            <td><?= htmlspecialchars($row['item_no']) ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td class="text-center"><?= htmlspecialchars($row['total_qty']) ?></td>
            <td class="text-right">Rs. <?= htmlspecialchars(number_format($row['total_sales'], 2)) ?></td>
        </tr>
        <?php } ?>
    </table>

    <div class="grand-total-container">
        <div class="grand-total-box">
            <h3>Grand Total: Rs. <?= number_format($grand_total, 2) ?></h3>
        </div>
    </div>
</body>
</html>