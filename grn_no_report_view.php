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

$from_date = mysqli_real_escape_string($conn, $_GET['from_date']);
$to_date = mysqli_real_escape_string($conn, $_GET['to_date']);

$sql = "SELECT g.po_no, DATE(g.created_at) as grn_date, po.vendor_name, COUNT(g.item_no) as total_items, SUM(g.received_qty) as total_qty 
        FROM grn g
        JOIN purchase_order po ON g.po_no = po.po_no 
        WHERE DATE(g.created_at) BETWEEN '$from_date' AND '$to_date'
        GROUP BY g.po_no, DATE(g.created_at), po.vendor_name
        ORDER BY g.po_no DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>GRN No Wise Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0f766e; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 30px; }
        .print-btn { background: #6b7280; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; float: right; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    <div class="header">
        <h2>Drugs4U - GRN No Wise Summary Report</h2>
        <p>Period: <?= htmlspecialchars($from_date) ?> to <?= htmlspecialchars($to_date) ?></p>
    </div>
    <table>
        <tr>
            <th>PO / GRN No Reference</th>
            <th>Received Date</th>
            <th>Vendor Name</th>
            <th>Distinct Line Items</th>
            <th>Total Qty Received</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?= htmlspecialchars($row['po_no']) ?></td>
            <td><?= htmlspecialchars($row['grn_date']) ?></td>
            <td><?= htmlspecialchars($row['vendor_name']) ?></td>
            <td><?= htmlspecialchars($row['total_items']) ?></td>
            <td><?= htmlspecialchars(number_format($row['total_qty'], 2)) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>