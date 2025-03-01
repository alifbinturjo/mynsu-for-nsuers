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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_course'])) {
        $course_code = $_POST['code'];
        $course_credit = $_POST['credit'];

        try {
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO courses (code, credit) VALUES (?, ?)");
            $stmt->bind_param("si", $course_code, $course_credit);
            $delete_stmt = $conn->prepare("DELETE FROM course_requests WHERE code = ?");
            $delete_stmt->bind_param("s", $course_code);

            $stmt->execute();
            
            $delete_stmt->execute();
            
            $conn->commit();
            echo '<div class="alert alert-success">Course added.</div>';
        } catch (Exception $e) {
            $conn->rollback();
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }
        $stmt->close();
        $delete_stmt->close();
        header("refresh:1");
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_teacher'])) {
        $teacher_initial = $_POST['initial'];

        try {
            $conn->begin_transaction();

            $stmt = $conn->prepare("INSERT INTO teachers (initial) VALUES (?)");
            $stmt->bind_param("s", $teacher_initial);
            $delete_stmt = $conn->prepare("DELETE FROM teacher_requests WHERE initial = ?");
            $delete_stmt->bind_param("s", $teacher_initial);

            $stmt->execute();

            $delete_stmt->execute();

            $conn->commit();
            echo '<div class="alert alert-success">Teacher added.</div>';
            
        } catch (Exception $e) {
            $conn->rollback();
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }
        $stmt->close();
        $delete_stmt->close();
        header("refresh:1");
        exit;
    }

    
    $course_requests = [];
    try {
        $stmt = $conn->prepare("
            SELECT code, credit, COUNT(user_id) AS request_count 
            FROM course_requests 
            GROUP BY code, credit 
            ORDER BY request_count DESC, code ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $course_requests[] = $row;
        }
        
    } catch (Exception $e) {
        $stmt->close();
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
            
            $conn->close();
            header("refresh:1; url=dashbaord.php");
            exit;
    }
    $stmt->close();
    
    $teacher_requests = [];
    try {
        $stmt = $conn->prepare("
            SELECT initial, COUNT(user_id) AS request_count 
            FROM teacher_requests 
            GROUP BY initial 
            ORDER BY request_count DESC, initial ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $teacher_requests[] = $row;
        }
        
    } catch (Exception $e) {
        $stmt->close();
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
            
            $conn->close();
            header("refresh:1; url=dashbaord.php");
            exit;
    }
    $stmt->close();

    
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
            <h1 class="text-center mb-4">Courses and Teachers</h1>
            <p class="text-center">Manage courses and teachers queue</p>
            <div class="mb-5 border p-3 rounded">
            <h4>Courses</h4>
            <p class="text-muted">All addtion queue for courses will appear here</p>
        <table class="table table-striped ">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Credits</th>
                    <th>Request Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($course_requests as $course_row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course_row['code']); ?></td>
                        <td><?php echo htmlspecialchars($course_row['credit']); ?></td>
                        <td><?php echo htmlspecialchars($course_row['request_count']); ?></td>
                        <td>
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="code" value="<?php echo htmlspecialchars($course_row['code']); ?>">
                                <input type="hidden" name="credit" value="<?php echo htmlspecialchars($course_row['credit']); ?>">
                                <button type="submit" name="approve_course" class="btn btn-success btn-sm">Add</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
                </div>
                <div class="mb-5 border p-3 rounded">
        <h4 class="mt-4">Teachers</h4>
        <p class="text-muted">All addtion queue for teachers will appear here</p>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Teacher Initial</th>
                    <th>Request Count</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teacher_requests as $teacher_row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($teacher_row['initial']); ?></td>
                        <td><?php echo htmlspecialchars($teacher_row['request_count']); ?></td>
                        <td>
                            <form method="post" action="" class="d-inline">
                                <input type="hidden" name="initial" value="<?php echo htmlspecialchars($teacher_row['initial']); ?>">
                                <button type="submit" name="approve_teacher" class="btn btn-success btn-sm">Add</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
            
        </div>
                </div>
        <?php $conn->close(); ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>