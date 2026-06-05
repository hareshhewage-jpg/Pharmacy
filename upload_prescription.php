<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

$msg = "";

/* ================= INSERT PRESCRIPTION ================= */
if(isset($_POST['submit'])) {

    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $allergies = mysqli_real_escape_string($conn, $_POST['allergies']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);

    /* ================= FILE UPLOAD ================= */
    $file_name = $_FILES['prescription_file']['name'];
    $tmp_name  = $_FILES['prescription_file']['tmp_name'];

    $upload_dir = "uploads/";

    if(!is_dir($upload_dir)){
        mkdir($upload_dir, 0777, true);
    }

    $ext = pathinfo($file_name, PATHINFO_EXTENSION);

    /* Step 1: Insert first with temporary file name */
    $temp_file = "temp_" . time() . "." . $ext;

    $sql = "INSERT INTO prescriptions 
        (patient_name, age, allergies, contact_no, prescription_file)
        VALUES 
        ('$patient_name', '$age', '$allergies', '$contact_no', '$temp_file')";

    if(mysqli_query($conn, $sql)) {

        /* Step 2: Get auto-generated index no */
        $index_no = mysqli_insert_id($conn);

        /* Step 3: Create date & time */
        $date = date("Ymd");
        $time = date("His");

        /* Step 4: Build new file name */
        $new_file_name = $index_no . "_" . $date . "_" . $time . "_" . $contact_no . "." . $ext;

        $target_file = $upload_dir . $new_file_name;

        /* Step 5: Move and rename file */
        if(move_uploaded_file($tmp_name, $target_file)) {

            /* Step 6: Update DB with final file name */
            mysqli_query($conn,
                "UPDATE prescriptions 
                 SET prescription_file='$new_file_name' 
                 WHERE id='$index_no'"
            );

            $msg = "Prescription uploaded successfully!";
        } else {
            $msg = "File upload failed!";
        }

    } else {
        $msg = "Database error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Prescription - PharmaCare</title>

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

        /* Sidebar Settings */
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

        .sidebar a:hover, .sidebar a.active {
            background: #115e59;
        }

        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .container {
            padding: 30px 20px;
            max-width: 800px;
        }

        .top-bar {
            padding: 20px 20px 5px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar h2 {
            margin: 0;
            color: #334155;
        }

        /* Content Card Custom Styling */
        .form-card {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #475569;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        input[type="file"] {
            padding: 7px;
            background: #f8fafc;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .submit-btn {
            background: #0f766e;
            color: #fff;
            width: 100%;
        }

        .submit-btn:hover {
            background: #115e59;
        }

        .msg {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Responsive Layout Overrides */
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

            .full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>

<body>

<div class="wrapper">

    <div class="sidebar">
        <h2>💊 PharmaCare</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="item.php">Items</a>
        <a href="customer.php">Customers</a>
        <a href="purchase_order.php">Purchase Orders</a>
        <a href="prescription_upload.php" class="active">Upload Prescription</a>
    </div>

    <div class="main-content">
        
        <div class="top-bar">
            <h2>Prescription Center</h2>
            <span style="color: #64748b; font-size: 14px;">Customer Account view</span>
        </div>

        <div class="container">
            <div class="form-card">
                
                <?php if($msg != ""): ?>
                    <div class="msg"><?= $msg; ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="grid">
                        
                        <div>
                            <label>Patient Name</label>
                            <input type="text" name="patient_name" required placeholder="John Doe">
                        </div>

                        <div>
                            <label>Age</label>
                            <input type="number" name="age" required placeholder="Years">
                        </div>

                        <div class="full-width">
                            <label>Known Allergies / Medical Notes</label>
                            <textarea name="allergies" placeholder="List medications or components patient is allergic to..."></textarea>
                        </div>

                        <div>
                            <label>Contact Number</label>
                            <input type="text" name="contact_no" required placeholder="e.g. +94xxxxxxxx">
                        </div>

                        <div>
                            <label>Upload Prescription Scan (Image/PDF)</label>
                            <input type="file" name="prescription_file" required>
                        </div>

                    </div>

                    <button type="submit" name="submit" class="submit-btn">Send to Pharmacy</button>
                </form>

            </div>
        </div>

    </div>
</div>

</body>
</html>