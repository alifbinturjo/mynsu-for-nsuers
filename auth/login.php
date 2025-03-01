<!doctype html>
<html lang="en">
    <head>
    <link rel="icon" type="image/webp" href="../pcs/icon.webp">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyNSU - For NSUers</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
<?php
include'../dbxs/mnxcon.php';
session_start();

if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['login'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $stmt_email = $conn->prepare("SELECT user_id,first_name FROM users WHERE email=?");
    $stmt_email->bind_param("s", $email);
    try{
        $stmt_email->execute();
    }
    catch(Exception $e){
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
        $stmt_email->close();
        session_destroy();
        $conn->close();
        header("refresh:1; url=../index.php");
        exit;
    }
    
    $res = $stmt_email->get_result();

    if ($res->num_rows === 0) {
        echo '<div class="alert alert-danger">Email not found. Redirecting...</div>';
        $stmt_email->close();
        $conn->close();
        session_destroy();
        header("refresh:1; url=../index.php");
        exit;
    }

    $user = $res->fetch_assoc();
    $user_id = htmlspecialchars($user['user_id']);
    $_SESSION['user_id']=$user_id;
    $first_name=htmlspecialchars($user['first_name']);
    $stmt_email->close();

    $stmt_pass=$conn->prepare("select password from info where user_id=?");
    $stmt_pass->bind_param("i",$user_id);
    try{
        $stmt_pass->execute();
    }
    catch(Exception $e){
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
        $stmt_pass->close();
        session_destroy();
        $conn->close();
        header("refresh:1; url=../index.php");
        exit;
    }
    $result=$stmt_pass->get_result();
    $fetch=$result->fetch_assoc();
    $hashed_pass=$fetch['password'];
    $stmt_pass->close();

    if(password_verify($password,$hashed_pass)){
        try{
            $query = "UPDATE users SET login_date = CURDATE() WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        
        $_SESSION['status']="ok";
        echo '<div class="alert alert-success">Login successful. Ridirecting...</div>';
        $conn->close();
        
        $_SESSION['first_name']=$first_name;
        header("refresh:1; url=role.php");
        exit;
        }
        catch(Exception $e){
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $stmt->close();
    $conn->close();
    session_destroy();
    header("refresh:1; url=../index.php");
    exit;
        }
    }
    else{
        echo '<div class="alert alert-danger">Wrong password. Redirecting...</div>';
        $conn->close();
        session_destroy();
        header("refresh:1; url=../index.php");
        exit;
    }
    
}
else{
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $conn->close();
    session_destroy();
    header("refresh:1; url=../index.php");
    exit;
}
   
?>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>
