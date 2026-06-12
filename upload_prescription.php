<?php
session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true){
    header("Location: login.php");
    exit;
}
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

$msg = "";
$msg_type = ""; 

/* ================= INSERT PRESCRIPTION ================= */
if(isset($_POST['submit'])) {

    $patient_name = $_POST['patient_name'];
    $age          = $_POST['age'];
    $allergies    = $_POST['allergies'];
    $contact_no   = $_POST['contact_no'];
    $cus_id       = $_SESSION['cus_id'];
    

    /* ================= FILE UPLOAD ================= */
    if (isset($_FILES['prescription_file']) && $_FILES['prescription_file']['error'] == 0) {
        
        $file_name = $_FILES['prescription_file']['name'];
        $tmp_name  = $_FILES['prescription_file']['tmp_name'];
        $upload_dir = "uploads/";

        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0775, true);
        }

        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_exts = array("jpg", "jpeg", "png", "pdf");

        if(in_array($ext, $allowed_exts)) {
            
            $date = date("Ymd");
            $time = date("His");
            $unique_id = uniqid(); 
            $new_file_name = $date . "_" . $time . "_" . $contact_no . "_" . $unique_id . "." . $ext;
            $target_file = $upload_dir . $new_file_name;

            if(move_uploaded_file($tmp_name, $target_file)) {
                
                $stmt = $conn->prepare("INSERT INTO prescriptions (patient_name, age, allergies, contact_no, prescription_file,cus_id) VALUES (?, ?, ?, ?, ?,?)");
                $stmt->bind_param("ssssss", $patient_name, $age, $allergies, $contact_no, $new_file_name,$cus_id);

                if($stmt->execute()) {
                    $msg = "🎉 Prescription uploaded successfully! Our pharmacist will review it shortly.";
                    $msg_type = "success";
                } else {
                    $msg = "❌ Database error: Could not save records.";
                    $msg_type = "error";
                }
                $stmt->close();

            } else {
                $msg = "❌ File system error: Failed to move uploaded file.";
                $msg_type = "error";
            }
        } else {
            $msg = "❌ Invalid file type! Please upload a JPG, PNG, or PDF.";
            $msg_type = "error";
        }
    } else {
        $msg = "❌ Please select a valid prescription file to upload.";
        $msg_type = "error";
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
        :root {
            --primary: #0d9488;
            --primary-hover: #0f766e;
            --primary-light: #f0fdfa;
            --bg-main: #f8fafc;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --radius: 12px;
            --danger: #ef4444;
            --danger-hover: #dc2626;
        }

        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg-main);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        /* Centered App Container Layout (No Sidebar) */
        .main-content {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .top-bar {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }

        .top-bar-left h2 {
            margin: 0 0 4px 0;
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        .top-bar-left .brand-sub {
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Action Buttons */
        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: #475569;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .btn-secondary:hover {
            background: #f1f5f9;
            color: #1e293b;
            border-color: #cbd5e1;
        }

        /* Logout Specific Styling */
        .btn-logout {
            border-color: #fca5a5;
            color: var(--danger);
        }

        .btn-logout:hover {
            background: #fef2f2;
            color: var(--danger-hover);
            border-color: #f87171;
        }

        /* Main Form block */
        .form-card {
            background: #fff;
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            border: 1px solid var(--border-color);
        }

        .form-card h3 {
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 18px;
            color: #334155;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 30px;
        }

        .full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
        }

        input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 12px 16px;
            box-sizing: border-box;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            background-color: #f8fafc;
            transition: all 0.2s ease;
            color: var(--text-dark);
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
        }

        textarea {
            resize: vertical;
            min-height: 110px;
        }

        .file-upload-wrapper {
            position: relative;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            background: #fafafa;
            text-align: center;
            transition: border-color 0.2s;
        }
        
        .file-upload-wrapper:hover {
            border-color: var(--primary);
        }

        input[type="file"] {
            width: 100%;
            font-size: 14px;
            color: var(--text-muted);
            cursor: pointer;
        }

        button.submit-btn {
            background: var(--primary);
            color: #fff;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: background 0.2s ease, transform 0.1s ease;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
        }

        button.submit-btn:hover {
            background: var(--primary-hover);
        }

        button.submit-btn:active {
            transform: scale(0.99);
        }

        /* Status Notifications styling */
        .msg {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 15px;
            font-weight: 500;
        }

        .msg.success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .msg.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .main-content {
                margin: 20px auto;
            }

            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .full-width {
                grid-column: span 1;
            }
            
            .form-card {
                padding: 24px;
            }
        }
    </style>
</head>

<body>

<div class="main-content">
    
    <div class="top-bar">
        <div class="top-bar-left">
            <span class="brand-sub">💊 Drugs4U Customer Portal</span>
            <h2>Prescription Center</h2>
        </div>
        
        <div class="top-bar-right">
            <a href="cu_logout.php" class="btn-secondary btn-logout">
                👋 Log Out
            </a>
        </div>
    </div>

    <?php if($msg != ""): ?>
        <div class="msg <?= $msg_type; ?>"><?= $msg; ?></div>
    <?php endif; ?>

    <div class="form-card">
        <h3>Submit New Prescription</h3>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="grid">
                
                <div>
                    <label for="patient_name">Patient Name</label>
                    <input type="text" id="patient_name" name="patient_name" required placeholder="e.g. John Doe">
                </div>

                <div>
                    <label for="age">Age</label>
                    <input type="number" id="age" name="age" required min="0" max="130" placeholder="Years">
                </div>

                <div class="full-width">
                    <label for="allergies">Known Allergies / Medical Notes</label>
                    <textarea id="allergies" name="allergies" placeholder="Please list components, specific drugs or foods the patient is allergic to..."></textarea>
                </div>

                <div>
                    <label for="contact_no">Contact Number</label>
                    <input type="text" id="contact_no" name="contact_no" required placeholder="e.g. +94xxxxxxxx">
                </div>

                <div>
                    <label for="prescription_file">Prescription Scan (Image / PDF)</label>
                    <div class="file-upload-wrapper">
                        <input type="file" id="prescription_file" name="prescription_file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                </div>

            </div>

            <button type="submit" name="submit" class="submit-btn">Send to Pharmacy</button>
        </form>

    </div>
</div>

</body>
</html>