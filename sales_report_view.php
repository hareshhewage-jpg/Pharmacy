<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connection.php';

// Secure the GET parameters against SQL Injection
$from_date = mysqli_real_escape_string($conn, $_GET['from_date']);
$to_date   = mysqli_real_escape_string($conn, $_GET['to_date']);

$sql = "
SELECT *
FROM sales_order
WHERE so_date BETWEEN '$from_date' AND '$to_date'
ORDER BY so_date ASC
";

$result = mysqli_query($conn, $sql);
$total_sales = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f766e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 30px; }
        .summary-section { margin-top: 20px; text-align: right; }
        .print-btn { background: #6b7280; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; float: right; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    
    <div class="header">
        <h2>Sales Summary Report</h2>
        <p>Period: <?= htmlspecialchars($from_date) ?> to <?= htmlspecialchars($to_date) ?></p>
    </div>

    <table>
        <tr>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Customer</th>
            <th class="text-right">Total Amount</th>
        </tr>
        <?php 
        while($row = mysqli_fetch_assoc($result)) { 
            $total_sales += $row['total_amount'];
        ?>
        <tr>
            <td><?= htmlspecialchars($row['so_no']) ?></td>
            <td><?= htmlspecialchars($row['so_date']) ?></td>
            <td><?= htmlspecialchars($row['cus_name']) ?></td>
            <td class="text-right"><?= htmlspecialchars(number_format($row['total_amount'], 2)) ?></td>
        </tr>
        <?php } ?>
    </table>

    <div class="summary-section">
        <h3>Total Sales: Rs. <?= htmlspecialchars(number_format($total_sales, 2)) ?></h3>
    </div>
</body>
</html>