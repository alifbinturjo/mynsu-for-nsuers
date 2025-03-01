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

    $sort_order = isset($_POST['sort']) && $_POST['sort'] === 'asc' ? 'ASC' : 'DESC';
    $filter_course = isset($_POST['course']) ? strtoupper(trim($_POST['course'])) : '';
    $filter_initial = isset($_POST['initial']) ? strtoupper(trim($_POST['initial'])) : '';
    $query = "
        SELECT 
            t.initial, 
            AVG(e.experience) AS avg_rating, 
            COUNT(e.user_id) AS student_count 
        FROM enrolled e
        JOIN teachers t ON e.initial = t.initial
        JOIN courses c ON e.code = c.code
        WHERE e.experience IS NOT NULL AND e.initial IS NOT NULL
    ";
    $conditions = [];
    if (!empty($filter_course)) {
        $conditions[] = "c.code = ?";
    }
    if (!empty($filter_initial)) {
        $conditions[] = "t.initial = ?";
    }

    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }
    $query .= " GROUP BY t.initial ORDER BY avg_rating $sort_order, student_count, t.initial ASC LIMIT 10";
    $stmt = $conn->prepare($query);
    $params = [];
    $types = '';
    if (!empty($filter_course)) {
        $params[] = $filter_course;
        $types .= 's';
    }
    if (!empty($filter_initial)) {
        $params[] = $filter_initial;
        $types .= 's';
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    try{
        $stmt->execute();
        $result = $stmt->get_result();
    }
    catch(Exception $e){
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
        $stmt->close();
        $conn->close();
        header("refresh:1; url=dashboard.php");
        exit;
    }

$stmt_count = $conn->prepare("SELECT COUNT(*) AS total_notifications FROM notices");
try{
    $stmt_count->execute();
}
catch(Exception $e){
    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
    $stmt_count->close();
    $stmt->close();
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
            <h1 class="text-center mb-4">Ratings</h1>
            <p class="text-center">Experience ratigns from students about their teachers</p>
            
            <form class="mb-4" method="POST">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="course" class="form-control" placeholder="Enter Course Code" value="<?php echo htmlspecialchars($filter_course); ?>"maxlength="7">
                </div>
                <div class="col-md-4">
                    <input type="text" name="initial" class="form-control" placeholder="Enter Teacher Initial" value="<?php echo htmlspecialchars($filter_initial); ?>"maxlength="5">
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="desc" <?php echo $sort_order === 'DESC' ? 'selected' : ''; ?>>Rating: High to Low</option>
                        <option value="asc" <?php echo $sort_order === 'ASC' ? 'selected' : ''; ?>>Rating: Low to High</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Teacher Initial</th>
                        <th>Average Rating</th>
                        <th>Rate Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['initial']); ?></td>
                            <td><?php echo number_format($row['avg_rating'], 1); ?></td>
                            <td>
                                
                                <p class="text-muted"><?php echo $row['student_count']; ?> time(s)</p>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning text-center">No ratings available.</div>
        <?php endif; ?>

        <?php $stmt->close();
        $conn->close(); ?>
</div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>