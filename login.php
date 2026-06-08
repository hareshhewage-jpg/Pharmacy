<?php
session_start();
include 'connection.php';

if(isset($_POST['login'])){

    $email = trim($_POST['email']);
    $pass  = md5(trim($_POST['pass'])); // matches your registration MD5

    $sql = "SELECT * FROM customer WHERE email='$email' AND pass='$pass' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        $_SESSION['cus_id']  = $row['cus_id'];
        $_SESSION['fl_name'] = $row['fl_name'];
        $_SESSION['email']   = $row['email'];

        header("Location: upload_prescription.php");
        exit;

    } else {
        $error = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>PharmaCare Login</title>

<style>

body{
    font-family:Poppins;
    background:linear-gradient(135deg,#0f766e,#14b8a6);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.container{
    width:800px;
    display:flex;
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.left{
    width:40%;
    background:#0f766e;
    color:#fff;
    padding:40px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:center;
}

.right{
    width:60%;
    padding:40px;
}

h2{
    margin-bottom:20px;
    color:#0f766e;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:8px;
    outline:none;
}

button{
    width:100%;
    padding:12px;
    background:#0f766e;
    color:#fff;
    border:none;
    border-radius:10px;
    margin-top:10px;
    cursor:pointer;
    font-size:16px;
}

button:hover{
    background:#0d5f58;
}

.error{
    color:red;
    font-size:13px;
    margin-top:10px;
}

.small{
    font-size:13px;
    margin-top:10px;
    text-align:center;
}

.small a{
    color:#0f766e;
    text-decoration:none;
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container">

    <!-- LEFT PANEL -->
    <div class="left">
        <h1>💊 PharmaCare</h1>
        <p>Customer Login Portal</p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right">

        <h2>Login</h2>

        <form method="POST">

            <input type="text" name="email" placeholder="Email" required>

            <input type="password" name="pass" placeholder="Password" required>

            <button type="submit" name="login">Login</button>

            <?php if(isset($error)){ ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>

        </form>

        <div class="small">
            Don't have an account? <a href="set.php">Register</a>
        </div>

    </div>

</div>

</body>
</html>
