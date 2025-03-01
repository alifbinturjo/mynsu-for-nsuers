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
            if(isset($_SESSION['user_id'])&&isset($_SESSION['status'])){
            $user_id = $_SESSION['user_id'];
             $stmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE user_id = ?");
             $stmt->bind_param("i", $user_id);
             try{
                $stmt->execute();
             }
             catch(Exception $e){
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $stmt->close();
    $conn->close();
    header("refresh:1; url=logout.php");
    exit;
             }
             
             $stmt->bind_result($isAdmin);
             $stmt->fetch();
             $stmt->close();
             $conn->close();

             if ($isAdmin > 0) {
                $_SESSION['role']='admin';
                echo '
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="card text-center" style="width: 24rem;">
                <div class="card-body">
                    <h3 class="card-title">Welcome!</h3>
                    <p class="card-text">You have admin privileges. Choose how to proceed.</p>
                    <a href="../admin/dashboard.php" class="btn btn-primary mb-2">Go to Admin Dashboard</a>
                    <a href="../student/dashboard.php" class="btn btn-outline-secondary">Continue as Student</a>
                </div>
            </div>
        </div>';
        
            } else {
                $_SESSION['role']='student';
                header("Location: ../student/dashboard.php");
                
                exit;
            }
            }
            else{
                session_destroy();
                $conn->close();
                echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
                header("refresh:1; url=../index.php");
                exit;
            }
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>