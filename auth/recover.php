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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot'])) {
    $email = $_POST['email'];
    $verification_method = $_POST['verification_method'];

    $stmt_email = $conn->prepare("SELECT user_id,first_name FROM users WHERE email=?");
    $stmt_email->bind_param("s", $email);
    try{
        $stmt_email->execute();
    }
    catch(Exception $e){
        $stmt_email->close();
        session_destroy();
        $conn->close();
        echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
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
    $user_id = $user['user_id'];
    $first_name=$user['first_name'];
    $stmt_email->close();

    if ($verification_method === 'security_variable') {
        $stmt_sec = $conn->prepare("SELECT security_variable FROM info WHERE user_id=?");
        $stmt_sec->bind_param("i", $user_id);
        try{
            $stmt_sec->execute();
        }
        catch(Exception $e){
        $stmt_sec->close();
        session_destroy();
        $conn->close();
        echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
        header("refresh:1; url=../index.php");
        exit;
        }
        $result = $stmt_sec->get_result();
        $variable = $result->fetch_assoc();
        $security_variable_hashed = $variable['security_variable'];
        $_SESSION['security_variable_hashed']=$security_variable_hashed;
        $stmt_sec->close();
        echo '
        <div class="container mt-3">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="POST" action="" class="p-3 border rounded bg-light">
                        <div class="mb-3">
                            <label for="security_answer" class="form-label">Enter your security key</label>
                            <input type="text" class="form-control" id="security_answer" name="security_answer" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,18}$" title="Length must be 6 to 18 including letter, number, and special character(@$!%*#?&)" maxlength="18" required>
                        </div>
                        <button type="submit" name="update_pass" class="btn btn-primary">Verify & Update</button>
                    </form>
                </div>
            </div>
        </div>';

    }
    else if($verification_method === 'email_code'){

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
    $mail->Subject = "Account Recovery";

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
            Account Recovery
        </div>
        <div class='body'>
            <p>Hello <strong>$first_name</strong>,</p>
            <p>Received a request to reset your password for your MyNSU account. Use the recovery code below to proceed:</p>
            <div class='code'>$otp</div>
            <p>If you did not request this, you can ignore this email. Your password will remain unchanged.</p>
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
                            <label for="entered_otp" class="form-label">Enter the verification code sent to your email</label>
                            <input type="number" class="form-control" id="entered_otp" name="entered_otp" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,18}$" title="Length must be 6 to 18 including letter, number, and special character(@$!%*#?&)" maxlength="18" required>
                        </div>
                        <button type="submit" name="update_pass2" class="btn btn-primary">Verify & Update</button>
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
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pass'])) {
    $entered_sec = $_POST['security_answer'];
    $new_pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    if (password_verify($entered_sec,$_SESSION['security_variable_hashed'])) {
        $stmt_pass = $conn->prepare("UPDATE info SET password=? WHERE user_id=?");
        $stmt_pass->bind_param("si", $new_pass, $user_id);
        try{
            $stmt_pass->execute();
            echo '<div class="alert alert-success">Password updated successfully. Redirecting...</div>';
        }
        catch(Exception $e){
     echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
        }
        $stmt_pass->close();
    }
    else{
        echo '<div class="alert alert-danger">Wrong security key. Redirecting...</div>';
    }
    $conn->close();
    session_destroy();
    header("refresh:1; url=../index.php");
    exit;
}
else if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_pass2'])){
    $entered_otp = $_POST['entered_otp'];
    $new_pass = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    if ($entered_otp==$_SESSION['otp']) {
        $stmt_pass = $conn->prepare("UPDATE info SET password=? WHERE user_id=?");
        $stmt_pass->bind_param("si", $new_pass, $user_id);
        try{
            $stmt_pass->execute();
            echo '<div class="alert alert-success">Password updated successfully. Redirecting...</div>';
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
    session_destroy();
    header("refresh:1; url=../index.php");
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
