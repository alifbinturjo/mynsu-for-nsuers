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
session_start();
include '../dbxs/mnxcon.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_SESSION['user_id'])&&isset($_SESSION['role'])){
    $user_id=$_SESSION['user_id'];
    $first_name=$_SESSION['first_name'];
}
else{
    session_destroy();
    $conn->close();
    echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
    header("refresh:1; url=../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_email'])) {
    $email = $_SESSION['email'];
    $newEmail = $_POST['newEmail'];
    $_SESSION['newEmail']=$newEmail;

require '../mailmgr/src/PHPMailer.php';
require '../mailmgr/src/SMTP.php';
require '../mailmgr/src/Exception.php';

        $otp=random_int(100000,999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_user_id'] = $user_id;
        $mail=new PHPMailer;

        $mail->isSMTP();
        $mail->Host='';               //smtp host domain
        $mail->SMTPAuth=true;
        $mail->Username='';                  //sender mail address
        $mail->Password='';                       // sender mail password
        $mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port=587;
    
        $mail->setFrom('', 'MyNSU');  //sender mail address
        $mail->addAddress($email,$first_name);

    $mail->isHTML(true);
    $mail->Subject = "Email Change";

    $emailBody = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #00274d;
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .body {
            padding: 20px;
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }
        .body p {
            margin: 10px 0;
        }
        .code {
            display: inline-block;
            font-size: 20px;
            color: #00274d;
            font-weight: bold;
            background-color: #f4f4f4;
            padding: 10px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            background-color: #f4f4f4;
            color: #888888;
            text-align: center;
            padding: 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>
            Email Change
        </div>
        <div class='body'>
            <p>Hello <strong>$first_name</strong>,</p>
            <p>Received a request to change your email to $newEmail for your MyNSU account. Use the code below to proceed:</p>
            <div class='code'>$otp</div>
            <p>If you did not request this, you can ignore this email. Your email will remain unchanged.</p>
            <p>Best regards,<br>MyNSU</p>
        </div>
        <div class='footer'>
            © MyNSU. All rights reserved.
        </div>
    </div>
</body>
</html>
";

    $mail->Body=$emailBody;
    try{
    $mail->send();
        echo '
        <div class="container mt-3">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="POST" action="" class="p-3 border rounded bg-light">
                        <div class="mb-3">
                            <label for="entered_otp" class="form-label">Enter the verification code sent to your current email</label>
                            <input type="number" class="form-control" id="entered_otp" name="entered_otp" required>
                        </div>
                        <button type="submit" name="update_email" class="btn btn-primary">Verify & Update</button>
                    </form>
                </div>
            </div>
        </div>';
    }
    catch(Exception $e){
        
    session_destroy();
    $conn->close();
    echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
    header("refresh:1; url=../index.php");
    exit;
    }
    }

    else if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_email'])){
        $entered_otp = $_POST['entered_otp'];
        $newEmail=$_SESSION['newEmail'];
        
        if ($entered_otp==$_SESSION['otp']) {
            $stmt_pass = $conn->prepare("UPDATE users SET email=? WHERE user_id=?");
            $stmt_pass->bind_param("si", $newEmail, $user_id);
            try{
                $stmt_pass->execute();
                echo '<div class="alert alert-success">Email updated successfully. Redirecting...</div>';
            }
            catch(Exception $e){
         echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
            }
    
            $stmt_pass->close();
        }
        else{
            echo '<div class="alert alert-danger">Invalid code. Redirecting...</div>';
        }
    
        $conn->close();
        header("refresh:1; url=../student/profile.php");
        exit;
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
