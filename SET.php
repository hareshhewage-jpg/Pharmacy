
<?php
include 'connection.php';

// ================= AUTO CUSTOMER ID =================
$result = mysqli_query($conn, "SELECT cus_id FROM customer ORDER BY cus_id DESC LIMIT 1");

if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    $num = (int)substr($row['cus_id'], 3) + 1;
    $cus_id = "CUS" . str_pad($num, 4, "0", STR_PAD_LEFT);
}else{
    $cus_id = "CUS0001";
}


// ================= AJAX INSERT =================
if(isset($_POST['ajax'])){

    $errors = [];

    $cus_id  = $_POST['cus_id'];
    $f_name  = trim($_POST['f_name']);
    $m_name  = trim($_POST['m_name']);
    $s_name  = trim($_POST['s_name']);

    // AUTO FULL NAME (SERVER SIDE SAFETY)
    $fl_name = trim($f_name . " " . $m_name . " " . $s_name);

    $gender  = trim($_POST['gender']);
    $DOB     = trim($_POST['DOB']);
    $age     = trim($_POST['age']);
    $address = trim($_POST['address']);
    $city    = trim($_POST['city']);
    $con_no  = trim($_POST['con_no']);
    $email   = trim($_POST['email']);
    $raw_pass = trim($_POST['pass']);
    $pass = md5($raw_pass);
    $re_pass = trim($_POST['re_pass']);
    $re_pass = md5($raw_pass);

    // ================= VALIDATION =================
    if($f_name=="")  $errors['f_name']="Required";
    //if($m_name=="")  $errors['m_name']="Required";
    if($s_name=="")  $errors['s_name']="Required";
    if($gender=="")  $errors['gender']="Required";
    if($DOB=="")     $errors['DOB']="Required";
    if($address=="") $errors['address']="Required";
    if($city=="")    $errors['city']="Required";

    if(!preg_match("/^[0-9]{10}$/",$con_no))
        $errors['con_no']="Invalid Contact";

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email']="Invalid Email";

    if(strlen($pass) < 6)
        $errors['pass']="Min 6 characters";

    if($pass != $re_pass)
        $errors['re_pass']="Not match";

    // RETURN ERRORS
    if(!empty($errors)){
        echo json_encode([
            "status"=>"error",
            "errors"=>$errors
        ]);
        exit;
    }

    // ================= PASSWORD HASH =================
    //$pass = password_hash($raw_pass, PASSWORD_DEFAULT);

                    
    // ================= INSERT =================
    $sql = "INSERT INTO customer (
        cus_id,
        f_name,
        m_name,
        s_name,
        fl_name,
        gender,
        DOB,
        age,
        address,
        city,
        con_no,
        email,
        pass
    ) VALUES (
        '$cus_id',
        '$f_name',
        '$m_name',
        '$s_name',
        '$fl_name',
        '$gender',
        '$DOB',
        '$age',
        '$address',
        '$city',
        '$con_no',
        '$email',
        '$pass'
    )";

    if(mysqli_query($conn,$sql)){
        echo json_encode(["status"=>"success"]);
    }else{
        echo json_encode([
            "status"=>"error",
            "db_error"=>mysqli_error($conn)
        ]);
    }

    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Pharmacy System</title>

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
    width:900px;
    display:flex;
    background:#fff;
    border-radius:20px;
    overflow:hidden;
}

.left{
    width:35%;
    background:#0f766e;
    color:#fff;
    padding:40px;
    text-align:center;
}

.right{
    width:65%;
    padding:30px;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.full{grid-column:1/3;}

input,select,textarea{
    padding:10px;
    border:1px solid #ccc;
    border-radius:8px;
}

.error{
    color:red;
    font-size:12px;
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
}

</style>

</head>

<body>

<div class="container">

<div class="left">
<h1>💊 Drugs4U</h1>
<p>Customer Registration System</p>
</div>

<div class="right">

<form id="regForm">

<div class="form-grid">

<input type="hidden" name="ajax" value="1">

<!-- AUTO ID -->
<div>
<input type="text" name="cus_id" value="<?php echo $cus_id; ?>" readonly>
</div>

<!-- FIRST NAME -->
<div>
<input type="text" name="f_name" placeholder="First Name">
<div class="error" id="f_name"></div>
</div>

<!-- MIDDLE NAME -->
<div>
<input type="text" name="m_name" placeholder="Middle Name">
<div class="error" id="m_name"></div>
</div>

<!-- SURNAME -->
<div>
<input type="text" name="s_name" placeholder="Surname">
<div class="error" id="s_name"></div>
</div>

<!-- FULL NAME (AUTO) -->
<div class="full">
<input type="text" name="fl_name" id="fl_name" placeholder="Full Name" readonly>
<div class="error" id="fl_name"></div>
</div>

<!-- GENDER -->
<div>
<select name="gender">
<option value="">Gender</option>
<option>Male</option>
<option>Female</option>
</select>
<div class="error" id="gender"></div>
</div>

<!-- DOB -->
<div>
<input type="date" name="DOB" id="DOB">
<div class="error" id="DOB"></div>
</div>

<!-- AGE -->
<div>
<input type="text" name="age" id="age" readonly placeholder="Age">
<div class="error" id="age"></div>
</div>

<!-- CITY -->
<div>
<input type="text" name="city" placeholder="City">
<div class="error" id="city"></div>
</div>

<!-- ADDRESS -->
<div class="full">
<textarea name="address" placeholder="Address"></textarea>
<div class="error" id="address"></div>
</div>

<!-- CONTACT -->
<div>
<input type="text" name="con_no" placeholder="Contact">
<div class="error" id="con_no"></div>
</div>

<!-- EMAIL -->
<div>
<input type="text" name="email" placeholder="Email">
<div class="error" id="email"></div>
</div>

<!-- PASSWORD -->
<div>
<input type="password" name="pass" placeholder="Password">
<div class="error" id="pass"></div>
</div>

<!-- CONFIRM -->
<div>
<input type="password" name="re_pass" placeholder="Confirm Password">
<div class="error" id="re_pass"></div>
</div>

</div>

<button type="submit">Register</button>

</form>

</div>

</div>

<script>

// ================= AGE CALC =================
document.getElementById("DOB").addEventListener("change", function(){

    let dob = new Date(this.value);
    let today = new Date();

    let age = today.getFullYear() - dob.getFullYear();
    let m = today.getMonth() - dob.getMonth();

    if(m < 0 || (m === 0 && today.getDate() < dob.getDate())){
        age--;
    }

    document.getElementById("age").value = age;
});


// ================= FULL NAME AUTO CONCAT =================
function updateFullName(){

    let f = document.querySelector("[name='f_name']").value;
    let m = document.querySelector("[name='m_name']").value;
    let s = document.querySelector("[name='s_name']").value;

    document.getElementById("fl_name").value =
        (f + " " + m + " " + s).replace(/\s+/g,' ').trim();
}

document.querySelector("[name='f_name']").addEventListener("input", updateFullName);
document.querySelector("[name='m_name']").addEventListener("input", updateFullName);
document.querySelector("[name='s_name']").addEventListener("input", updateFullName);


// ================= AJAX SUBMIT =================
document.getElementById("regForm").addEventListener("submit", function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch("",{
        method:"POST",
        body:formData
    })
    .then(res => res.json())
    .then(data => {

        document.querySelectorAll(".error").forEach(e=>e.innerHTML="");

        if(data.status === "error"){

            for(let key in data.errors){
                document.getElementById(key).innerHTML = data.errors[key];
            }

            if(data.db_error){
                alert("DB Error: " + data.db_error);
            }
        }

        if(data.status === "success"){
            alert("Customer Registered Successfully!");
            location.reload();
        }

    });

});

</script>

</body>
</html>