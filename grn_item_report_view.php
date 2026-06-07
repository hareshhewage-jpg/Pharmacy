<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connection.php';

$from_date = mysqli_real_escape_string($conn, $_GET['from_date']);
$to_date = mysqli_real_escape_string($conn, $_GET['to_date']);

$sql = "SELECT g.po_no, g.item_no, im.item_name, im.brand, g.received_qty, g.expiry_date, DATE(g.created_at) as grn_date 
        FROM grn g
        JOIN item_master im ON g.item_no = im.item_no
        WHERE DATE(g.created_at) BETWEEN '$from_date' AND '$to_date'
        ORDER BY DATE(g.created_at) DESC, g.po_no DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Item Wise GRN Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #059669; color: white; padding: 10px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 30px; }
        .print-btn { background: #6b7280; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; float: right; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Print Report</button>
    <div class="header">
        <h2>Drugs4U - Item Wise GRN Operational Report</h2>
        <p>Period: <?= htmlspecialchars($from_date) ?> to <?= htmlspecialchars($to_date) ?></p>
    </div>
    <table>
        <tr>
            <th>Received Date</th>
            <th>PO / GRN Ref</th>
            <th>Item No</th>
            <th>Item Name</th>
            <th>Brand</th>
            <th>Received Qty</th>
            <th>Lot Expiry Date</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?= htmlspecialchars($row['grn_date']) ?></td>
            <td><?= htmlspecialchars($row['po_no']) ?></td>
            <td><?= htmlspecialchars($row['item_no']) ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= htmlspecialchars($row['brand']) ?></td>
            <td><?= htmlspecialchars(number_format($row['received_qty'], 2)) ?></td>
            <td><?= htmlspecialchars($row['expiry_date']) ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>