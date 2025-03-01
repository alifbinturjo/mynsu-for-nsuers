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
    header("refresh:1; url=dashboard.php");
    exit;
}
$result_count = $stmt_count->get_result();
$count_data = $result_count->fetch_assoc();
$total_notifications = $count_data['total_notifications'];

$stmt_count->close();
    
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
            <h1 class="text-center mb-4">Notification</h1>
            <p class="text-center">View active notifications</p>
            
            <?php
        $stmt_notices = $conn->prepare("SELECT title, message, DATE(notice_date) AS notice_date FROM notices ORDER BY notice_date DESC");
        
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

        if ($result_notices->num_rows > 0) {
            while ($row = $result_notices->fetch_assoc()) {
                echo '<div class="card mb-3">';
                echo '<div class="card-header fw-bold">' . htmlspecialchars($row['title']) . '</div>';
                echo '<div class="card-body">';
                echo '<p class="card-text">' . htmlspecialchars($row['message']) . '</p>';
                echo '<p class="text-muted">Published on ' . htmlspecialchars($row['notice_date']) . '</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center text-muted small">No active notification.</p>';
        }

        $stmt_notices->close();
        
        ?>
    
        </div>
        <?php
            $conn->close();
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>