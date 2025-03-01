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
    <?php
    include'../dbxs/mnxcon.php';
    session_start();
    if(isset($_SESSION['user_id'])&&$_SESSION['role']=='admin'){
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
<div class="container mt-5">
            <h1 class="text-center mb-4">Overview</h1>
            <p class="text-center">Key stats of the platform</p>
            
    <div class="row mt-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">Users</h4>
                    <p class="card-text">
                    <?php
                    try{
                        $stmt_users = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
                        $stmt_logged_in = $conn->prepare("SELECT COUNT(*) AS logged_in_today FROM users WHERE login_date = CURDATE()");
                        $stmt_signed_up = $conn->prepare("SELECT COUNT(*) AS signed_up_today FROM users WHERE registration_date = CURDATE()");

                        $stmt_users->execute();
                        $result_users = $stmt_users->get_result();
                        $total_users = $result_users->fetch_assoc()['total_users'];

                        
                        $stmt_logged_in->execute();
                        $result_logged_in = $stmt_logged_in->get_result();
                        $logged_in_today = $result_logged_in->fetch_assoc()['logged_in_today'];

                        
                        $stmt_signed_up->execute();
                        $result_signed_up = $stmt_signed_up->get_result();
                        $signed_up_today = $result_signed_up->fetch_assoc()['signed_up_today'];
                    }
                        
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_users->close();
                        $stmt_logged_in->close();
                        $stmt_signed_up->close();
                            $conn->close();
                            header("refresh:1; url=dashbaord.php");
                            exit;
                        }
                        
                        echo "<br>Total users: $total_users <br><br> Logged in today: $logged_in_today <br><br> Signed up today: $signed_up_today";

                        $stmt_users->close();
                        $stmt_logged_in->close();
                        $stmt_signed_up->close();
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">Notification</h4>
                    <p class="card-text">
                    <?php
                        $stmt_notices = $conn->prepare("SELECT COUNT(*) AS total_notices FROM notices");
                        
                        try{
                            $stmt_notices->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_notices->close();
                            $conn->close();
                            header("refresh:1; url=dashbaord.php");
                            exit;
                        }
                        $result_notices = $stmt_notices->get_result();
                        $total_notices = $result_notices->fetch_assoc()['total_notices'];
                        echo"<br>Active notifications: $total_notices";
                        $stmt_notices->close();
                    ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">Queue</h4>
                    <p class="card-text">
                    <?php
                        
                        
                        try{
                        $stmt_distinct_courses = $conn->prepare("SELECT COUNT(DISTINCT code) AS distinct_course_requests FROM course_requests");
                        $stmt_distinct_initials = $conn->prepare("SELECT COUNT(DISTINCT initial) AS distinct_teacher_requests FROM teacher_requests");

                        $stmt_distinct_courses->execute();
                        $result_distinct_courses = $stmt_distinct_courses->get_result();
                        $distinct_course_requests = $result_distinct_courses->fetch_assoc()['distinct_course_requests'];
                        
                        $stmt_distinct_initials->execute();
                        $result_distinct_initials = $stmt_distinct_initials->get_result();
                        $distinct_teacher_requests = $result_distinct_initials->fetch_assoc()['distinct_teacher_requests'];
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_distinct_initials->close();
                        $stmt_distinct_courses->close();
                            $conn->close();
                            header("refresh:1; url=dashbaord.php");
                            exit;
                        }
                        
                        echo"<br>Courses: $distinct_course_requests <br><br> Teachers: $distinct_teacher_requests";

                        $stmt_distinct_initials->close();
                        $stmt_distinct_courses->close();
                    ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">Availability</h4>
                    <p class="card-text">
                    <?php
                        try {
                        $stmt_distinct_courses = $conn->prepare("SELECT COUNT(DISTINCT code) AS distinct_course_requests FROM courses");
                        $stmt_distinct_initials = $conn->prepare("SELECT COUNT(DISTINCT initial) AS distinct_teacher_requests FROM teachers");

                        $stmt_distinct_courses->execute();
                        $result_distinct_courses = $stmt_distinct_courses->get_result();
                        $distinct_course_requests = $result_distinct_courses->fetch_assoc()['distinct_course_requests'];
                        
                        $stmt_distinct_initials->execute();
                        $result_distinct_initials = $stmt_distinct_initials->get_result();
                        $distinct_teacher_requests = $result_distinct_initials->fetch_assoc()['distinct_teacher_requests'];
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_distinct_initials->close();
                        $stmt_distinct_courses->close();
                            $conn->close();
                            header("refresh:1; url=dashbaord.php");
                            exit;
                        }
                        echo"<br>Courses: $distinct_course_requests <br><br> Teachers: $distinct_teacher_requests";

                        $stmt_distinct_initials->close();
                        $stmt_distinct_courses->close();
                    ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">Platform rating</h4>
                    <p class="card-text">
                    <?php
                        $stmt_rating = $conn->prepare("SELECT AVG(user_interface) AS avg_ui, AVG(user_functionalities) AS avg_uf, COUNT(DISTINCT user_id) AS user_count FROM platform_rate");
                        
                        try{
                            $stmt_rating->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_rating->close();
                            $conn->close();
                            header("refresh:1; url=dashbaord.php");
                            exit;
                        }
                        $result_rating = $stmt_rating->get_result();
                        $ratings = $result_rating->fetch_assoc();

                        $avg_ui = round($ratings['avg_ui'], 2);
                        $avg_uf = round($ratings['avg_uf'], 2);
                        $total_ratings = $ratings['user_count'];

                        echo "<br>Interface: $avg_ui <br><br> Functionalities: $avg_uf  <br><br> Rate count: $total_ratings";

                        $stmt_rating->close();
                    ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4 mb-5">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">Admin</h4>
                    <p class="card-text">
                    <?php
                        $stmt_admins = $conn->prepare("SELECT COUNT(*) AS total_admins FROM admins");
                        
                        try{
                            $stmt_admins->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_admins->close();
                            $conn->close();
                            header("refresh:1; url=dashbaord.php");
                            exit;
                        }
                        $result_admins = $stmt_admins->get_result();
                        $total_admins = $result_admins->fetch_assoc()['total_admins'];
                        
                        echo "<br>Total admins: $total_admins";

                        $stmt_admins->close();
                    ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    
</div>
        <?php
            $conn->close();
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>