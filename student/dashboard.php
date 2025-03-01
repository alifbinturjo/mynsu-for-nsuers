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
            if(isset($_SESSION['user_id'])&&isset($_SESSION['role'])){
                $first_name=$_SESSION['first_name'];
                
            }
            else{
                session_destroy();
                $conn->close();
                echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
                header("refresh:1; url=../index.php");
                exit;
            }
$stmt_count = $conn->prepare("SELECT COUNT(*) AS total_notifications FROM notices");

try{
    $stmt_count->execute();
}
catch(Exception $e){
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $stmt_count->close();
    $conn->close();
    header("refresh:1; url=../auth/logout.php");
    exit;
}
$result_count = $stmt_count->get_result();
$count_data = $result_count->fetch_assoc();
$total_notifications = $count_data['total_notifications'];

$stmt_count->close();
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
                    <a class="nav-link" href="notification.php">Notification<span class="badge bg-primary"><?php echo $total_notifications; ?></span></a>
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
            <div class="row mb-4">
                <div class="col-12">
                    <a href="overview.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #007bff; border-color: #0056b3;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Overview</h4>
                                <p class="text-white-50">Key insights based on academic data</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="current.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #17a2b8; border-color: #138496;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Current Semester</h4>
                                <p class="text-white-50">Manage current semester stats</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="routine.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #2c6e49; border-color: #235e42;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Routine</h4>
                                <p class="text-white-50">Current semester class schedule</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="courses.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #28a745; border-color: #218838;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Past Courses</h4>
                                <p class="text-white-50">Manage past course stats</p> 
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <a href="coursesteachers.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #005f73; border-color: #004753;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Courses and Teachers</h4>
                                <p class="text-white-50">Search or add courses and teachers</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="ratings.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #495057; border-color: #343a40;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Ratings</h4>
                                <p class="text-white-50">Experience ratings from students</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="notepad.php" class="text-decoration-none">
                        <div class="card text-center mb-3" style="background-color: #6c757d; border-color: #5a6268;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Notepad</h4>
                                <p class="text-white-50">Store short note</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <a href="quickaccess.php" class="text-decoration-none">
                    <div class="card text-center mb-3" style="background-color: #20c997; border-color: #17a589;">
                            <div class="card-body">
                                <h4 class="card-title text-white">Quick Access</h4>
                                <p class="text-white-50">URLs and contacts</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>