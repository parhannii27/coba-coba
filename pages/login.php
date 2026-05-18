<?php
session_start();

include '../config/koneksi.php';

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi,
        "SELECT * FROM users 
        WHERE username='$username' 
        AND password='$password'"
    );

    if(mysqli_num_rows($query) > 0){

        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;

        // langsung ke dashboard
        echo "
        <script>
            alert('Login berhasil');
            window.location='dashboard.php';
        </script>
        ";

    } else {

        $error = "Username atau Password salah!";

    }

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login Zhafira Media</title>

<style>

body{
    margin:0;
    padding:0;
    font-family:Arial;
    background:#f1f5f9;
}

.box{
    width:350px;
    background:white;
    padding:30px;
    margin:100px auto;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    color:#2563eb;
}

input{
    width:100%;
    padding:12px;
    margin-top:12px;
    border:1px solid #ccc;
    border-radius:5px;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:12px;
    margin-top:20px;
    border:none;
    background:#2563eb;
    color:white;
    border-radius:5px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

.error{
    color:red;
    text-align:center;
    margin-top:10px;
}

</style>

</head>
<body>

<div class="box">

<h2>Zhafira Media</h2>

<?php if($error != ""){ ?>
<p class="error"><?php echo $error; ?></p>
<?php } ?>

<form method="POST">

<input type="text" 
       name="username" 
       placeholder="Username"
       required>

<input type="password" 
       name="password" 
       placeholder="Password"
       required>

<button type="submit" name="login">
    LOGIN
</button>

</form>

</div>

</body>
</html>