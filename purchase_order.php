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

/* ================= AJAX LOAD GRN ITEMS ================= */
if(isset($_GET['get_po_items'])){

    $po_no = $_GET['po_no'];

    $res = runQuery($conn,"
        SELECT 
            poi.item_no,
            poi.item_name,
            poi.qty AS ordered_qty,
            COALESCE(SUM(g.received_qty),0) AS received_qty,
            (poi.qty - COALESCE(SUM(g.received_qty),0)) AS balance_qty
        FROM purchase_order_items poi
        LEFT JOIN grn g
            ON poi.po_no = g.po_no
            AND poi.item_no = g.item_no
        WHERE poi.po_no='$po_no'
        GROUP BY poi.item_no, poi.item_name, poi.qty
    ");

    $data = [];

    while($row=mysqli_fetch_assoc($res)){
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

/* ================= DELETE PO ================= */
if(isset($_GET['delete_po'])){

    $po_no = $_GET['delete_po'];

    runQuery($conn,"DELETE FROM grn WHERE po_no='$po_no'");
    runQuery($conn,"DELETE FROM purchase_order_items WHERE po_no='$po_no'");
    runQuery($conn,"DELETE FROM purchase_order WHERE po_no='$po_no'");

    echo "<script>
    alert('PO Deleted');
    window.location='purchase_order.php';
    </script>";
    exit;
}

/* ================= EDIT MODE ================= */
$edit_mode = false;
$edit_po = null;
$edit_items = [];

if(isset($_GET['edit_po'])){

    $edit_mode = true;

    $po_no = $_GET['edit_po'];

    $res = runQuery($conn,"
        SELECT * FROM purchase_order
        WHERE po_no='$po_no'
    ");

    $edit_po = mysqli_fetch_assoc($res);

    $items_res = runQuery($conn,"
        SELECT * FROM purchase_order_items
        WHERE po_no='$po_no'
    ");

    while($r=mysqli_fetch_assoc($items_res)){
        $edit_items[] = $r;
    }
}

/* ================= UPDATE STATUS (FIXED) ================= */
function updatePOStatus($conn, $po_no){
    // 1. Get total ordered items quantity
    $res_ord = runQuery($conn, "SELECT COALESCE(SUM(qty), 0) AS total_ordered FROM purchase_order_items WHERE po_no='$po_no'");
    $row_ord = mysqli_fetch_assoc($res_ord);
    $total_ordered = (float)$row_ord['total_ordered'];

    // 2. Get total received quantity from GRN
    $res_rec = runQuery($conn, "SELECT COALESCE(SUM(received_qty), 0) AS total_received FROM grn WHERE po_no='$po_no'");
    $row_rec = mysqli_fetch_assoc($res_rec);
    $total_received = (float)$row_rec['total_received'];

    // 3. Compare with flat tolerance check to avoid floating-point bugs
    $status = ($total_received >= $total_ordered && $total_ordered > 0) ? 'COMPLETED' : 'OPEN';

    runQuery($conn, "
        UPDATE purchase_order
        SET status='$status'
        WHERE po_no='$po_no'
    ");
}

/* ================= SAVE PO ================= */
if(isset($_POST['save_po'])){

    $po_no = $_POST['po_no'];
    $vendor_id = $_POST['vendor_id'];
    $vendor_name = $_POST['vendor_name'];
    $po_date = $_POST['po_date'];

    if($_POST['edit_mode'] == '1'){

        runQuery($conn,"
            UPDATE purchase_order
            SET
                vendor_id='$vendor_id',
                vendor_name='$vendor_name',
                po_date='$po_date'
            WHERE po_no='$po_no'
        ");

        runQuery($conn,"
            DELETE FROM purchase_order_items
            WHERE po_no='$po_no'
        ");
    }
    else{

        runQuery($conn,"
            INSERT INTO purchase_order
            (
                po_no,
                vendor_id,
                vendor_name,
                po_date,
                status
            )
            VALUES
            (
                '$po_no',
                '$vendor_id',
                '$vendor_name',
                '$po_date',
                'OPEN'
            )
        ");
    }

    for($i=0; $i<count($_POST['item_no']); $i++){

        $item_no = $_POST['item_no'][$i];

        if($item_no == '') continue;

        runQuery($conn,"
            INSERT INTO purchase_order_items
            (
                po_no,
                item_no,
                item_name,
                cost,
                qty,
                total
            )
            VALUES
            (
                '$po_no',
                '{$_POST['item_no'][$i]}',
                '{$_POST['item_name'][$i]}',
                '{$_POST['cost'][$i]}',
                '{$_POST['qty'][$i]}',
                '{$_POST['total'][$i]}'
            )
        ");
    }

    // Recalculate status in case an edited PO now satisfies completion rules
    updatePOStatus($conn, $po_no);

    echo "<script>
    alert('PO Saved');
    window.location='purchase_order.php';
    </script>";
    exit;
}

/* ================= SAVE GRN ================= */
if(isset($_POST['save_grn'])){

    $po_no = $_POST['po_no'];

    for($i=0; $i<count($_POST['item_no']); $i++){

        $item_no = $_POST['item_no'][$i];
        $received_qty = (float)$_POST['received_qty'][$i];
        $expiry_date = $_POST['expiry_date'][$i];

        if($received_qty <= 0){
            continue;
        }

        $check = runQuery($conn,"
            SELECT
                poi.qty - COALESCE(SUM(g.received_qty),0) AS balance_qty
            FROM purchase_order_items poi
            LEFT JOIN grn g
                ON poi.po_no = g.po_no
                AND poi.item_no = g.item_no
            WHERE poi.po_no='$po_no'
            AND poi.item_no='$item_no'
            GROUP BY poi.qty
        ");

        $bal = mysqli_fetch_assoc($check);

        $balance = (float)($bal['balance_qty'] ?? 0);

        if($received_qty < 0){
            $received_qty = 0;
        }

        if($received_qty > $balance){
            $received_qty = $balance;
        }

        if($received_qty > 0){

            runQuery($conn,"
                INSERT INTO grn
                (
                    po_no,
                    item_no,
                    received_qty,
                    expiry_date,
                    created_at
                )
                VALUES
                (
                    '$po_no',
                    '$item_no',
                    '$received_qty',
                    '$expiry_date',
                    NOW()
                )
            ");

            // Update inventory by increasing stock_qty in item_master table
            runQuery($conn,"
                UPDATE item_master 
                SET stock_qty = stock_qty + $received_qty 
                WHERE item_no = '$item_no'
            ");
        }
    }

    updatePOStatus($conn,$po_no);

    echo "<script>
    alert('GRN Saved');
    window.location='purchase_order.php';
    </script>";
    exit;
}

/* ================= PO NUMBER ================= */
$res = runQuery($conn,"
    SELECT po_no
    FROM purchase_order
    ORDER BY po_id DESC
    LIMIT 1
");

if(mysqli_num_rows($res)>0){

    $row = mysqli_fetch_assoc($res);

    $num = (int)substr($row['po_no'],2) + 1;

    $po_no = "PO" . str_pad($num,4,"0",STR_PAD_LEFT);

}
else{

    $po_no = "PO0001";
}

/* ================= FILTER ================= */
$status = strtoupper($_GET['status'] ?? 'ALL');

if($status == 'OPEN'){

    $po_list = runQuery($conn,"
        SELECT *
        FROM purchase_order
        WHERE UPPER(status)='OPEN'
        ORDER BY po_id DESC
    ");
}
elseif($status == 'COMPLETED'){

    $po_list = runQuery($conn,"
        SELECT *
        FROM purchase_order
        WHERE UPPER(status)='COMPLETED'
        ORDER BY po_id DESC
    ");
}
else{

    $po_list = runQuery($conn,"
        SELECT *
        FROM purchase_order
        ORDER BY po_id DESC
    ");
}

$vendors = runQuery($conn,"SELECT * FROM vendor_master");
$items = runQuery($conn,"SELECT * FROM item_master");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>PO & GRN System</title>

<style>

body{
    font-family: Arial, sans-serif;
    background: #f4f7fb;
    margin: 0;
}
.sidebar a.active {
background: #115e59;
font-weight: bold;
}

.wrapper{
    display: flex;
    flex-direction: row;
}

/* Sidebar Responsive Settings */
.sidebar{
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

.sidebar h2{
    text-align: center;
    margin-bottom: 30px;
}


.sidebar a{
    display: block;
    color: #fff;
    text-decoration: none;
    padding: 12px;
    margin-bottom: 8px;
    border-radius: 10px;
}

.sidebar a:hover{
    background: #115e59;
}

.main-content{
    margin-left: 260px;
    width: calc(100% - 260px);
    box-sizing: border-box;
}

.container{
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

button{
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.po-btn{background:#2563eb;color:#fff;}
.grn-btn{background:#059669;color:#fff;}
.edit-btn{background:#f59e0b;color:#fff;}
.del-btn{background:#dc2626;color:#fff;}
.print-btn{background:#6b7280;color:#fff;}

/* Responsive Tables */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

table{
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

th{
    background: #0f766e;
    color: #fff;
    padding: 12px 10px;
    text-align: left;
}

td{
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

input, select{
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Modals & Popups Setup */
.popup{
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

.popup-content{
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

.close-btn{
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 25px;
    cursor: pointer;
    color: #666;
}

.grid{
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
        <a href="purchase_order.php" class="active">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
		<a href="sales_invoice.php">Sales Invoice</a>
		<a href="report.php">Report</a>
    </div>

    <div class="main-content">

<div class="top-bar">
    <h2>Purchase Order System</h2>
    <div class="action-buttons">
        <button class="po-btn" onclick="openPO()">+ Create PO</button>
        <button class="grn-btn" onclick="openGRN()">GRN</button>
    </div>
</div>

<div class="filter-bar">
    <a href="?status=ALL"><button>All</button></a>
    <a href="?status=OPEN"><button style="background:#f59e0b;color:#fff;">Open</button></a>
    <a href="?status=COMPLETED"><button style="background:#10b981;color:#fff;">Completed</button></a>
</div>

<div class="container">
    <div class="table-responsive">
        <table>
            <tr>
                <th>PO No</th>
                <th>Vendor</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while($po=mysqli_fetch_assoc($po_list)){ ?>
            <tr>
                <td><?= $po['po_no'] ?></td>
                <td><?= $po['vendor_name'] ?></td>
                <td><?= $po['po_date'] ?></td>
                <td><?= $po['status'] ?></td>
                <td>
                    <div class="action-cell">
                        <a href="?edit_po=<?= $po['po_no'] ?>"><button class="edit-btn">Edit</button></a>
                        <a href="print_po.php?po_no=<?= urlencode($po['po_no']) ?>" target="_blank"><button class="print-btn" type="button">Print</button></a>
                        <a href="?delete_po=<?= $po['po_no'] ?>" onclick="return confirm('Delete PO?')"><button class="del-btn">Delete</button></a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<div class="popup" id="poPopup" style="<?= $edit_mode ? 'display:flex' : '' ?>">
    <div class="popup-content">
        <span class="close-btn" onclick="window.location.href='purchase_order.php'">×</span>
        <h3><?= $edit_mode ? 'Edit PO' : 'Create PO' ?></h3>

        <form method="POST">
            <input type="hidden" name="edit_mode" value="<?= $edit_mode ? 1 : 0 ?>">

            <div class="grid">
                <div>
                    <label>PO No</label>
                    <input name="po_no" value="<?= $edit_mode ? $edit_po['po_no'] : $po_no ?>" readonly>
                </div>

                <div>
                    <label>Vendor</label>
                    <select name="vendor_id" id="vendor" onchange="setVendor()">
                        <option value="">Select Vendor</option>
                        <?php while($v=mysqli_fetch_assoc($vendors)){ ?>
                        <option value="<?= $v['vendor_id'] ?>" data-name="<?= $v['vendor_name'] ?>" <?= ($edit_mode && $edit_po['vendor_id']==$v['vendor_id']) ? 'selected' : '' ?>>
                            <?= $v['vendor_id'] ?> - <?= $v['vendor_name'] ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label>Vendor Name</label>
                    <input name="vendor_name" id="vendor_name" value="<?= $edit_mode ? $edit_po['vendor_name'] : '' ?>" readonly>
                </div>

                <div>
                    <label>PO Date</label>
                    <input type="date" name="po_date" value="<?= $edit_mode ? $edit_po['po_date'] : '' ?>">
                </div>
            </div>

            <div class="table-responsive">
                <table id="poTable">
                    <tr>
                        <th>Item No</th>
                        <th>Item Name</th>
                        <th>Cost</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>

                    <?php if($edit_mode){ ?>
                        <?php foreach($edit_items as $row){ ?>
                        <tr>
                            <td>
                                <select name="item_no[]" onchange="setItem(this)">
                                    <option value="">Select</option>
                                    <?php
                                    mysqli_data_seek($items,0);
                                    while($it=mysqli_fetch_assoc($items)){
                                    ?>
                                    <option value="<?= $it['item_no'] ?>" data-name="<?= $it['item_name'] ?>" data-cost="<?= $it['unit_cost'] ?>" <?= ($row['item_no']==$it['item_no']) ? 'selected' : '' ?>>
                                        <?= $it['item_no'] ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td><input name="item_name[]" value="<?= $row['item_name'] ?>"></td>
                            <td><input name="cost[]" value="<?= $row['cost'] ?>" oninput="calcTotal(this)"></td>
                            <td><input name="qty[]" value="<?= $row['qty'] ?>" oninput="calcTotal(this)"></td>
                            <td><input name="total[]" value="<?= $row['total'] ?>" readonly></td>
                            <td><button type="button" class="del-btn" onclick="removeRow(this)">X</button></td>
                        </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </div>

            <br>
            <button type="button" class="po-btn" onclick="addRow()">+ Add Item</button>
            <button type="submit" class="grn-btn" name="save_po">Save PO</button>
        </form>
    </div>
</div>

<div class="popup" id="grnPopup">
    <div class="popup-content">
        <span class="close-btn" onclick="closeGRN()">×</span>
        <h3>GRN</h3>

        <form method="POST">
            <label style="font-weight:bold; display:block; margin-bottom:5px;">Select PO</label>
            <select name="po_no" id="grn_po" onchange="loadGRN(this.value)">
                <option value="">Select PO</option>
                <?php
                $po_all = runQuery($conn,"SELECT po_no FROM purchase_order WHERE status='OPEN'");
                while($p=mysqli_fetch_assoc($po_all)){
                ?>
                <option value="<?= $p['po_no'] ?>"><?= $p['po_no'] ?></option>
                <?php } ?>
            </select>

            <br><br>

            <div class="table-responsive">
                <table id="grnTable"></table>
            </div>

            <br>
            <button type="submit" class="grn-btn" name="save_grn">Save GRN</button>
        </form>
    </div>
</div>

<script>
let items = [
<?php
mysqli_data_seek($items,0);
while($i=mysqli_fetch_assoc($items)){
?>
{
    no:"<?= $i['item_no'] ?>",
    name:"<?= $i['item_name'] ?>",
    cost:"<?= $i['unit_cost'] ?>"
},
<?php } ?>
];

function openPO(){
    document.getElementById("poPopup").style.display='flex';
}

function closePO(){
    document.getElementById("poPopup").style.display='none';
}

function openGRN(){
    document.getElementById("grnPopup").style.display='flex';
}

function closeGRN(){
    document.getElementById("grnPopup").style.display='none';
}

function setVendor(){
    let s = document.getElementById("vendor");
    document.getElementById("vendor_name").value = s.options[s.selectedIndex].getAttribute("data-name") || '';
}

function addRow(){
    let table = document.getElementById("poTable");
    let row = table.insertRow();
    row.innerHTML = `
    <td>
        <select name="item_no[]" onchange="setItem(this)">
            <option value="">Select</option>
            ${items.map(i=>`<option value="${i.no}" data-name="${i.name}" data-cost="${i.cost}">${i.no}</option>`).join('')}
        </select>
    </td>
    <td><input name="item_name[]"></td>
    <td><input name="cost[]" oninput="calcTotal(this)"></td>
    <td><input name="qty[]" oninput="calcTotal(this)"></td>
    <td><input name="total[]" readonly></td>
    <td><button type="button" class="del-btn" onclick="removeRow(this)">X</button></td>
    `;
}

function setItem(el){
    let row = el.closest("tr");
    let option = el.options[el.selectedIndex];
    row.querySelector('input[name="item_name[]"]').value = option.getAttribute("data-name") || '';
    row.querySelector('input[name="cost[]"]').value = option.getAttribute("data-cost") || '';
    calcRow(row);
}

function calcTotal(el){
    let row = el.closest("tr");
    calcRow(row);
}

function calcRow(row){
    let cost = parseFloat(row.querySelector('input[name="cost[]"]').value) || 0;
    let qty = parseFloat(row.querySelector('input[name="qty[]"]').value) || 0;
    row.querySelector('input[name="total[]"]').value = (cost * qty).toFixed(2);
}

function removeRow(btn){
    btn.closest("tr").remove();
}

/* ================= LOAD GRN ================= */
function loadGRN(po){
    fetch("?get_po_items=1&po_no="+po)
    .then(r=>r.json())
    .then(data=>{
        let t = document.getElementById("grnTable");
        t.innerHTML = `
        <tr>
            <th>Item</th>
            <th>Name</th>
            <th>Ordered</th>
            <th>Received</th>
            <th>Balance</th>
            <th>Receive</th>
            <th>Expiry</th>
        </tr>
        `;
        data.forEach(d=>{
            t.innerHTML += `
            <tr>
                <td><input name="item_no[]" value="${d.item_no}" readonly></td>
                <td><input value="${d.item_name}" readonly></td>
                <td><input value="${d.ordered_qty}" readonly></td>
                <td><input value="${d.received_qty}" readonly></td>
                <td><input value="${d.balance_qty}" readonly></td>
                <td><input name="received_qty[]" value="${d.balance_qty}" data-balance="${d.balance_qty}" oninput="validateReceive(this)"></td>
                <td><input type="date" name="expiry_date[]"></td>
            </tr>
            `;
        });
    });
}

/* ================= VALIDATION ================= */
function validateReceive(el){
    let balance = parseFloat(el.getAttribute("data-balance")) || 0;
    let value = parseFloat(el.value) || 0;

    if(el.value === ''){
        el.value = 0;
        return;
    }
    if(value < 0){
        el.value = 0;
    }
    if(value > balance){
        el.value = balance;
    }
}
</script>

</div>
</div> 

</body>
</html>
