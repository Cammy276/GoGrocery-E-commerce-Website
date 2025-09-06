<?php
$host='localhost'; $db='gogrocery'; $user='gogrocery_customer'; $pass='StrongCustomerPassword123!';
$conn=new mysqli($host,$user,$pass,$db);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

$action=$_GET['action']??'';

if($action==='select'){
    $res=$conn->query("SELECT user_id,name,email,phone,profile_image_url,created_at FROM users");
    if($res){
        echo "<h3>Users Table</h3><pre>";
        while($row=$res->fetch_assoc()) print_r($row);
        echo "</pre>";
    }else echo "SELECT failed: ".$conn->error;
}

if($action==='insert'){
    $stmt=$conn->prepare("INSERT INTO users (name,email,phone,password_hash) VALUES (?,?,?,?)");
    $name="Test User"; $email="testuser".rand(1,1000)."@example.com"; $phone="+601234567".rand(10,99);
    $password_hash=password_hash("Password123!",PASSWORD_BCRYPT);
    $stmt->bind_param("ssss",$name,$email,$phone,$password_hash);
    echo $stmt->execute() ? "INSERT successful, ID: ".$stmt->insert_id : "INSERT failed: ".$stmt->error;
}

if($action==='update'){
    $stmt=$conn->prepare("UPDATE users SET name=? WHERE user_id=?");
    $new_name="Updated User"; $user_id=1; // change to existing ID
    $stmt->bind_param("si",$new_name,$user_id);
    echo $stmt->execute() ? "UPDATE success, affected: ".$stmt->affected_rows : "UPDATE failed: ".$stmt->error;
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head><title>Users Test</title></head>
<body>
<h2>Users Table Test</h2>
<button onclick="window.location='?action=select'">SELECT</button>
<button onclick="window.location='?action=insert'">INSERT</button>
<button onclick="window.location='?action=update'">UPDATE</button>
</body>
</html>
