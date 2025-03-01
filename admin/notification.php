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
        
    }
    else{
        session_destroy();
        $conn->close();
        echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
        header("refresh:1; url=../index.php");
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publish'])) {
        $title = $_POST['title'];
        $message = $_POST['message'];

        $stmt_insert = $conn->prepare("INSERT INTO notices (user_id, title, message, notice_date) VALUES (?, ?, ?, NOW())");
        $stmt_insert->bind_param("iss", $user_id, $title, $message);
        
        try{
            $stmt_insert->execute();
            echo '<div class="alert alert-success">Notification published successfully.</div>';
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            
        }
        $stmt_insert->close();

        
        header("Refresh:1");
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $stmt_delete = $conn->prepare("DELETE FROM notices WHERE notice_id = ?");
        $stmt_delete->bind_param("i", $delete_id);
        
        try{
            $stmt_delete->execute();
            echo '<div class="alert alert-danger">Notification deleted successfully.</div>';
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }
        $stmt_delete->close();
        
        
        header("Refresh:1");
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
            <h1 class="text-center mb-4">Notification</h1>
            <p class="text-center">Manage notifications</p>
            <div class="mb-5 border p-3 rounded">
            <form method="POST" class="mb-4">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" maxlength="100" required>
                <small class="form-text text-muted">Maximum 100 characters.</small>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="3" maxlength="500" required></textarea>
                <small class="form-text text-muted">Maximum 500 characters.</small>
            </div>
            <button type="submit" class="btn btn-primary" name="publish">Publish</button>
        </form>
</div>
<div class="mb-5 border p-3 rounded">
        <h4>All Notifications</h4>
        <p class="text-muted">Published notification will appear here</p>
    <div class="list-group">
    <?php

$stmt_fetch = $conn->prepare("SELECT n.notice_id, n.title, n.message, n.notice_date, u.first_name 
                              FROM notices n 
                              JOIN users u ON n.user_id = u.user_id 
                              ORDER BY n.notice_date DESC");

try{
    $stmt_fetch->execute();
}
catch(Exception $e){
    $stmt_fetch->close();
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $conn->close();
    header("refresh:1; url=dashbaord.php");
    exit;
}
$result = $stmt_fetch->get_result();


while ($row = $result->fetch_assoc()) {
    echo '<div class="notification mb-4">';
    echo '<h5>' . htmlspecialchars($row['title']) . '</h5>';
    
    echo '<p>' . htmlspecialchars($row['message']) . '</p>';

    echo '<p class="text-muted"><small>Published by: ' . htmlspecialchars($row['first_name']) . ' | ' . htmlspecialchars($row['notice_date']) . '</small></p>';
    
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="delete_id" value="' . $row['notice_id'] . '">';
    echo '<button type="submit" name="delete" class="btn btn-danger">Delete</button>';
    echo '</form>';

    echo '</div>';
}
$stmt_fetch->close();
?>
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