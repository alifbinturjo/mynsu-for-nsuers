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
        $user_id=$_SESSION['user_id'];
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
            <h1 class="text-center mb-4">Routine</h1>
            <p class="text-center">Current semester class schedule based on provided current semester info</p>
            
<div class="mb-5 border p-3 rounded">
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Day</th>
                <th>Start time</th>
                <th>Room code</th>
                <th>Section</th>
                <th>Teacher Initial</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt_fetch_routine = $conn->prepare("
                SELECT code, day, timing, room, section, initial
    FROM enrolls 
    WHERE user_id = ? 
    ORDER BY 
        CASE 
            WHEN day IN ('r', 'a', 'ra') THEN 1  -- Group 1: r, a, ra
            WHEN day IN ('s', 't', 'st') THEN 2  -- Group 2: s, t, st
            WHEN day IN ('m', 'w', 'mw') THEN 3  -- Group 3: m, w, mw
            ELSE 4                               -- Other days (if any)
        END,
        timing,
        code
            ");
            $stmt_fetch_routine->bind_param('s', $user_id);

            try {
                $stmt_fetch_routine->execute();
                $result_routine = $stmt_fetch_routine->get_result();

                if ($result_routine->num_rows > 0) {
                    while ($row = $result_routine->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['day']) . "</td>";
                        echo "<td>" . htmlspecialchars(date("h:i A", strtotime($row['timing']))) . "</td>";
                        echo "<td>" . htmlspecialchars($row['room']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['section']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['initial']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo '<p class="text-muted">No added courses.</p>';
                }
                $stmt_fetch_routine->close();
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
                $stmt_fetch_routine->close();
            $conn->close();
            header("refresh:1; url=dashboard.php");
            exit;
            }
            ?>
        </tbody>
    </table>
    <p class="text-muted small">*Provide correct informations for current semester to get organized routine</p>
</div>
    
        </div>
        <?php
            $conn->close();
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>