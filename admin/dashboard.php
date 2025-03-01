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
            if(isset($_SESSION['user_id'])&&$_SESSION['role']=='admin'){
                $user_id=$_SESSION['user_id'];
                $first_name=$_SESSION['first_name'];
                
                $stmt_level = $conn->prepare("SELECT level FROM admins WHERE user_id=?");
                $stmt_level->bind_param("i", $user_id);
                
                try{
                    $stmt_level->execute();
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong. Logging out...</div>';
    $stmt_level->close();
    $conn->close();
    header("refresh:1; url=../auth/logout.php");
    exit;
                }
                $result_level = $stmt_level->get_result();
                $admin = $result_level->fetch_assoc();
                $level = $admin['level'];
                $_SESSION['level']=$level;
                $stmt_level->close();
            }
            else{
                session_destroy();
                $conn->close();
                echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
                header("refresh:1; url=../index.php");
                exit;
            }
            $conn->close();
        ?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="">Hi <?php echo $first_name; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid mt-4">
            <?php
                if($level==0){
        echo'<div class="row mb-4">
                <div class="col-12">
                    <a href="overview.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #6f42c1; border-color: #5a32a3;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Overview</h4>
                                <p class="text-white-50">Platform insights</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="notification.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #17a2b8; border-color: #138496;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Notification</h4>
                                <p class="text-white-50">Manage notifications</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
    <div class="col-12">
        <a href="coursesteachers.php" class="text-decoration-none">
            <div class="card text-center mb-3" style="background-color: #28a745; border-color: #218838;">
                <div class="card-body">
                    <h4 class="card-title text-white">Courses and Teachers</h4>
                    <p class="text-white-50">Manage courses and teachers queue</p>
                </div>
            </div>
        </a>
    </div>
</div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="admins.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #007bff; border-color: #0056b3;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Admins</h4>
                                <p class="text-white-50">Manage admins</p> 
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="notepad.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #2c6e49; border-color: #235e42;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Notepad</h4>
                                <p class="text-white-50">Store short note</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>';
                }
            ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>