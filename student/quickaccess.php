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


$stmt_count = $conn->prepare("SELECT COUNT(*) AS total_notifications FROM notices");
try{
    $stmt_count->execute();
}
catch(Exception $e){
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $stmt_count->close();
    $conn->close();
    header("refresh:1; url=dashboard.php");
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
<div class="container mt-5">
            <h1 class="text-center mb-4">Quick Access</h1>
            <p class="text-center">Quick URLs and contacts</p>
            <div class="row g-3 justify-content-center">
        
        <div class="col-4 col-md-2">
            <a href="https://www.northsouth.edu/" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">NSU Website</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="https://www.northsouth.edu/nsu-announcements/" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">NSU Notices</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="https://www.northsouth.edu/academic/academic-calendar/" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">Academic Calendar</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="https://www.northsouth.edu/academic/grading-policy.html" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">Grading Policy</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="https://rds3.northsouth.edu/" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">RDS</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="https://northsouth.instructure.com/login/google" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">Canvas</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="https://rds2.northsouth.edu/index.php/common/showofferedcourses" target="_blank" class="text-decoration-none">
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">Offered Course List</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="" target="_blank" class="text-decoration-none">   <!--url-->
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">MyNSU Facebook</h6>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="mailto:" target="_blank" class="text-decoration-none">          <!--url-->
                <div class="card text-center rounded-3 shadow p-3 d-flex justify-content-center align-items-center 
                    bg-white border border-light h-100 
                    hover-shadow-lg">
                    <h6 class="card-title m-0">MyNSU Contact</h6>
                </div>
            </a>
        </div>
        
    </div>
            
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>