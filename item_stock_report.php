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

// Prevents key verification bugs if called without explicit tracking tags
$from_date = isset($_GET['from_date']) ? mysqli_real_escape_string($conn, $_GET['from_date']) : '';
$to_date   = isset($_GET['to_date']) ? mysqli_real_escape_string($conn, $_GET['to_date']) : '';

// Selecting all records from item master registry list
$sql = "SELECT item_no, item_name, unit_cost, stock_qty, (unit_cost * stock_qty) AS stock_value 
        FROM item_master
        ORDER BY item_no ASC";
        
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Item Wise Current Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #059669; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 30px; }
        .print-btn { background: #6b7280; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; float: right; }
        .out-of-stock { color: #dc2626; font-weight: bold; background-color: #fef2f2; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    <div class="header">
        <h2>Drugs4U - Item Wise Complete Stock Inventory Report</h2>
        <p>As of Date: <?= htmlspecialchars(date('Y-m-d')) ?></p>
    </div>
    <table>
        <tr>
            <th>Item No</th>
            <th>Item Name</th>
            <th class="text-right">Unit Cost</th>
            <th class="text-right">Available Stock Qty</th>
            <th class="text-right">Total Stock Value</th>
        </tr>
        <?php 
        $total_value = 0;
        while($row = mysqli_fetch_assoc($result)){ 
            $total_value += $row['stock_value'];
            
            // Highlight items that have zero stock
            $is_out = ((float)$row['stock_qty'] <= 0) ? 'class="out-of-stock"' : '';
        ?>
        <tr <?= $is_out ?>>
            <td><?= htmlspecialchars($row['item_no']) ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td class="text-right">Rs. <?= htmlspecialchars(number_format($row['unit_cost'], 2)) ?></td>
            <td class="text-right">
                <?= htmlspecialchars(number_format($row['stock_qty'], 0)) ?>
                <?= ((float)$row['stock_qty'] <= 0) ? ' (Out of Stock)' : '' ?>
            </td>
            <td class="text-right">Rs. <?= htmlspecialchars(number_format($row['stock_value'], 2)) ?></td>
        </tr>
        <?php } ?>
        <tr style="font-weight: bold; background: #f9fafb;">
            <td colspan="4" class="text-right" style="border-top: 2px solid #333;">Total Inventory Valuation:</td>
            <td class="text-right" style="border-top: 2px solid #333;">Rs. <?= htmlspecialchars(number_format($total_value, 2)) ?></td>
        </tr>
    </table>
</body>
</html>