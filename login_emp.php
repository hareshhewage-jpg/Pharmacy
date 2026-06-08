<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

function runQuery($conn, $sql){
    $result = mysqli_query($conn, $sql);
    if(!$result){
        die("SQL ERROR : " . mysqli_error($conn));
    }
    return $result;
}

/* ================= LOGIN PROCESS ================= */
$error = "";

if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users 
            WHERE username='$username' AND status='Active' LIMIT 1";

    $result = runQuery($conn, $sql);

    if(mysqli_num_rows($result) == 1){

        $user = mysqli_fetch_assoc($result);

        if(password_verify($password, $user['password'])){

            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['emp_id']    = $user['emp_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['user_role'];

            /* ================= ROLE BASED REDIRECT ================= */
            if($user['user_role'] == "Admin"){
                header("Location: dashboard.php");
            }
            else if($user['user_role'] == "Pharmacist"){
                header("Location: dashboard.php");
            }
            else if($user['user_role'] == "Cashier"){
                header("Location: dashboard.php");
            }
            else{
                header("Location: dashboard.php");
            }

            exit;

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found or inactive!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>Employee Login</title>

<style>
/* Modern Reset & Box Sizing */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #0f766e, #2563eb);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px; /* Prevents box from touching screen edges on mobile */
}

/* RESPONSIVE LOGIN BOX */
.login-box {
    background: #fff;
    padding: 40px 30px;
    width: 100%;
    max-width: 400px; /* Desktop width limit */
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.login-box h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #0f766e;
    font-size: 1.6rem;
}

input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px; /* Prevents iOS Safari from auto-zooming on focus */
}

button {
    width: 100%;
    padding: 12px;
    background: #0f766e;
    color: #fff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background 0.2s ease;
}

button:hover {
    background: #115e59;
}

.error {
    color: #dc2626;
    background: #fee2e2;
    padding: 10px;
    border-radius: 6px;
    text-align: center;
    margin-bottom: 15px;
    font-size: 14px;
}

.footer {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
    color: #64748b;
}

/* Media Query for Tiny Screens (Optional Fine Tuning) */
@media (max-width: 360px) {
    .login-box {
        padding: 25px 15px;
    }
    .login-box h2 {
        font-size: 1.3rem;
    }
}
</style>
</head>

<body>

<div class="login-box">

    <h2>💊 Employee Login</h2>

    <?php if($error != "") { ?>
        <div class="error"><?= $error; ?></div>
    <?php } ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>

    </form>

    <div class="footer">
        Drugs4U Pharmacy System
    </div>

</div>

</body>
</html>