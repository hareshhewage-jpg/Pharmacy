<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'connection.php';

// Safe Query Helper if needed for managing dynamic filters or lookups
function runQuery($conn, $sql){
    $result = mysqli_query($conn, $sql);
    if(!$result){
        die("SQL ERROR : " . mysqli_error($conn));
    }
    return $result;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Reports - Drugs4U</title>
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
        /* Master Panel Framework Layout */
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
        .sidebar a.active {
            background: #115e59;
            font-weight: bold;
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
            padding: 20px 20px 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar h2 {
            margin: 0;
            color: #334155;
        }
        
        /* Interactive Grid Engine */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 10px;
        }
        .report-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-left: 5px solid #0f766e;
        }
        .report-card.grn-card {
            border-left-color: #059669; /* Distinct Green accent for GRN Inventory track cards */
        }
        .report-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #1e293b;
            font-size: 17px;
        }
        .report-btn {
            background: #0f766e;
            color: white;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .report-card.grn-card .report-btn {
            background: #059669;
        }
        .report-card.grn-card .report-btn:hover {
            background: #047857;
        }
        .report-btn:hover {
            background: #115e59;
        }

        /* Parameter Modals Configurations Framework */
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
            padding: 10px;
            box-sizing: border-box;
        }
        .popup-content {
            background: #fff;
            width: 100%;
            max-width: 450px;
            padding: 25px;
            position: relative;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
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
        .popup-form-group {
            margin-bottom: 15px;
        }
        .popup-form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #475569;
        }
        input[type=date] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .generate-btn {
            background: #0f766e;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            font-size: 15px;
            font-weight: bold;
        }
        .generate-btn:hover {
            background: #115e59;
        }

        /* Mobile Viewports Adjustments */
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
            .report-grid {
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
        <a href="employee_reg.php">Employees</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_list.php">Prescriptions</a>
        <a href="sales_invoice.php">Sales Invoice</a>
        <a href="report.php" class="active">Report</a>
        <a href="login_emp.php">Logout</a> 
    </div>

    <div class="main-content">
        <div class="top-bar">
            <h2>Management Reports Panel</h2>
        </div>

        <div class="container">
            <div class="report-grid">
                
                <div class="report-card">
                    <h3>Sales Summary Report</h3>
                    <button class="report-btn" onclick="openPopup('sales_report_view.php','Sales Summary Report')">
                        Generate Report
                    </button>
                </div>

                <div class="report-card">
                    <h3>Item Wise Sales Report</h3>
                    <button class="report-btn" onclick="openPopup('item_sales_report_view.php','Item Wise Sales Report')">
                        Generate Report
                    </button>
                </div>

                <div class="report-card grn-card">
                    <h3>GRN No Wise Report</h3>
                    <button class="report-btn" onclick="openPopup('grn_no_report_view.php','GRN No Wise Summary Report')">
                        Generate Report
                    </button>
                </div>

                <div class="report-card grn-card">
                    <h3>Item Wise GRN Report</h3>
                    <button class="report-btn" onclick="openPopup('grn_item_report_view.php','Item Wise GRN Report')">
                        Generate Report
                    </button>
                </div>

                <!-- Live Stock Report Engine Integration Block -->
                <div class="report-card grn-card">
                    <h3>Item Wise Current Stock Report</h3>
                    <a href="item_stock_report.php" target="_blank" class="report-btn">
                        Generate Report
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="popup" id="reportPopup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">×</span>
        
        <h3 id="popupTitle" style="margin-top:0; color:#334155; margin-bottom: 20px;">Select Parameter Boundary</h3>

        <form id="reportForm" method="GET" target="_blank">
            <div class="popup-form-group">
                <label>From Date</label>
                <input type="date" name="from_date" required>
            </div>

            <div class="popup-form-group">
                <label>To Date</label>
                <input type="date" name="to_date" required>
            </div>

            <button type="submit" class="generate-btn">
                Generate Secure Report
            </button>
        </form>
    </div>
</div>

<script>
function openPopup(reportPage, title) {
    document.getElementById('reportPopup').style.display = 'flex';
    document.getElementById('reportForm').action = reportPage;
    document.getElementById('popupTitle').innerText = title;
    document.getElementById('reportForm').reset();
}

function closePopup() {
    document.getElementById('reportPopup').style.display = 'none';
}

document.getElementById('reportForm').onsubmit = function() {
    setTimeout(() => {
        closePopup();
    }, 300);
};
</script>

</body>
</html>
