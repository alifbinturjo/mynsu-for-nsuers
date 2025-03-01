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

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
        $code = strtoupper(trim($_POST['code']));
        $grade = strtoupper(trim($_POST['grade']));
        $day = isset($_POST['day']) ? strtoupper(trim($_POST['day'])) : null;
        $timing = isset($_POST['timing']) && trim($_POST['timing']) !== '' 
    ? strtoupper(trim($_POST['timing'])) 
    : NULL;
        
        $room = isset($_POST['room']) ? strtoupper(trim($_POST['room'])) : NULL;
        $section = isset($_POST['section']) ? intval($_POST['section']) : null;
        $initial = isset($_POST['initial']) && trim($_POST['initial']) !== '' 
    ? strtoupper(trim($_POST['initial'])) 
    : NULL;
    
        
            $stmt_check_code = $conn->prepare("SELECT COUNT(*) FROM courses WHERE code = ?");
            $stmt_check_code->bind_param('s', $code);
            
            try{
                $stmt_check_code->execute();
            }
            catch(Exception $e){
                $stmt_check_code->close();
                echo '<div class="alert alert-danger">Something went wrong.</div>';
                header("refresh:1");
                exit;
            }
            $stmt_check_code->bind_result($course_exists);
            $stmt_check_code->fetch();
            $stmt_check_code->close();
            if($initial){
            $stmt_check_initial = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE initial = ?");
            $stmt_check_initial->bind_param('s', $initial);
            try{
                $stmt_check_initial->execute();
            }
            catch(Exception $e){
                $stmt_check_initial->close();
                echo '<div class="alert alert-danger">Something went wrong.</div>';
                header("refresh:1");
                exit;
            }
            
            $stmt_check_initial->bind_result($teacher_exists);
            $stmt_check_initial->fetch();
            $stmt_check_initial->close();
        }
            if (!$course_exists) {
                echo '<div class="alert alert-danger">Unavailable course.</div>';
                header("refresh:1");
                exit;
            }
    
            if ($initial && !$teacher_exists) {
                echo '<div class="alert alert-danger">Unavailable teacher.</div>';
                header("refresh:1");
                exit;
            }
    
            $stmt_check_duplicate_course = $conn->prepare("SELECT COUNT(*) FROM enrolls WHERE user_id = ? AND code = ?");
            $stmt_check_duplicate_course->bind_param('ss', $user_id, $code);
            
            try{
                $stmt_check_duplicate_course->execute();
            }
            catch(Exception $e){
                $stmt_check_duplicate_course->close();
                echo '<div class="alert alert-danger">Something went wrong.</div>';
                header("refresh:1");
                exit;
            }
            $stmt_check_duplicate_course->bind_result($duplicate_course);
            $stmt_check_duplicate_course->fetch();
            $stmt_check_duplicate_course->close();
    
            if ($duplicate_course > 0) {
                echo '<div class="alert alert-danger">Already added.</div>';
                header("refresh:1");
                exit;
            }
    
            if ($day && $timing) {
                $stmt_check_duplicate_schedule = $conn->prepare("
        SELECT COUNT(*) 
        FROM enrolls 
        WHERE user_id = ? 
          AND timing = ? 
          AND (
              day = ? OR 
              (day = 'ra' AND (? IN ('r', 'a'))) OR 
              (day IN ('r', 'a') AND ? = 'ra') OR
              (day = 'st' AND (? IN ('s', 't'))) OR 
              (day IN ('s', 't') AND ? = 'st') OR
              (day = 'mw' AND (? IN ('m', 'w'))) OR 
              (day IN ('m', 'w') AND ? = 'mw')
          )
    ");
    $stmt_check_duplicate_schedule->bind_param(
        'sssssssss', 
        $user_id, $timing, 
        $day, $day, $day, 
        $day, $day, 
        $day, $day
    );
                
                try{
                    $stmt_check_duplicate_schedule->execute();
                }
                catch(Exception $e){
                    $stmt_check_duplicate_schedule->close();
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                    header("refresh:1");
                    exit;
                }
                $stmt_check_duplicate_schedule->bind_result($duplicate_schedule);
                $stmt_check_duplicate_schedule->fetch();
                $stmt_check_duplicate_schedule->close();
    
                if ($duplicate_schedule > 0) {
                    echo '<div class="alert alert-danger">Timing conflict.</div>';
                    header("refresh:1");
                    exit;
                }
            }
    
            $stmt_insert = $conn->prepare("
                INSERT INTO enrolls (user_id, grade, code, day, timing, room, section, initial) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt_insert->bind_param(
                'isssssis',
                $user_id,
                $grade,
                $code,
                $day,
                $timing,
                $room,
                $section,
                $initial
            );
            try{
                $stmt_insert->execute();
                echo '<div class="alert alert-success">Courses added.</div>';
            }
            catch(Exception $e){
                echo '<div class="alert alert-danger">Something went wrong.</div>';
            }
            
            $stmt_insert->close();
            header("refresh:1");
            exit;
        
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all'])) {
        $stmt_delete_courses = $conn->prepare("DELETE FROM enrolls WHERE user_id = ?");
        $stmt_delete_courses->bind_param('s', $user_id);
    
        try {
            $stmt_delete_courses->execute();
            echo '<div class="alert alert-success">Deleted successfully.</div>';
            
        } catch (Exception $e) {
            echo "<div class='text-danger'>Something went wrong.</div>";
        }
        $stmt_delete_courses->close();
        
            header("refresh:1");
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
            <h1 class="text-center mb-4">Current Semester</h1>
            <p class="text-center">Space for current semester stats</p>
 

<div class="mb-5 border p-3 rounded">
<h3>Add current course</h3>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="code" class="form-label">Course Code</label>
            <input type="text" class="form-control" id="code" name="code" pattern="^\S+$" placeholder="CSE332" maxlength="7" required>
        </div>
        <div class="mb-3">
                <label for="grade" class="form-label">Expecting Grade</label>
                <select class="form-control" id="grade" name="grade" required>
                    <option value="">Select Grade</option>
                    <option value="A">A</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="B-">B-</option>
                    <option value="C+">C+</option>
                    <option value="C">C</option>
                    <option value="C-">C-</option>
                    <option value="D+">D+</option>
                    <option value="D">D</option>
                    <option value="F">F</option>
                </select>
            </div>
        <div class="mb-3">
            <label for="day" class="form-label">Day (Optional. For routine purposes)</label>
            <select class="form-control" id="day" name="day">
                    <option value="">Select Day</option>
                    <option value="RA">RA</option>
                    <option value="ST">ST</option>
                    <option value="MW">MW</option>
                    <option value="R">R</option>
                    <option value="A">A</option>
                    <option value="S">S</option>
                    <option value="T">T</option>
                    <option value="M">M</option>
                    <option value="W">W</option>
                </select>
        </div>
        <div class="mb-3">
            <label for="timing" class="form-label">Start time (Optional. For routine purposes)</label>
            <input type="time" class="form-control" id="timing" name="timing">
        </div>
        <div class="mb-3">
            <label for="room" class="form-label">Room code (Optional. For routine purposes)</label>
            <input type="text" class="form-control" id="room" maxlength="10" placeholder="SAC206" name="room">
        </div>
        <div class="mb-3">
            <label for="section" class="form-label">Section (Optional. For routine purposes)</label>
            <input type="number" class="form-control" id="section" placeholder="8" name="section" min="1" max="120">
        </div>
        <div class="mb-3">
            <label for="initial" class="form-label">Teacher Initial (Optional. For routine purposes)</label>
            <input type="text" class="form-control" id="initial" placeholder="NLH" name="initial" pattern="^\S+$" maxlength="5">
        </div>
        <button type="submit" class="btn btn-primary" name="add">Add</button>
        <p class="text-muted small">*Queue unavailable courses or teachers through <a href="coursesteachers.php">Courses and Teachers</a></p>
    </form>
</div>

<div class="mb-5 border p-3 rounded">
    <h3>Current Courses</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Credit</th>
                <th>Expecting Grade</th>
                <th>Grade Points</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt_fetch_courses = $conn->prepare("
                SELECT e.code, e.grade, c.credit,
                CASE 
                    WHEN e.grade = 'A' THEN 4.0
                    WHEN e.grade = 'A-' THEN 3.7
                    WHEN e.grade = 'B+' THEN 3.3
                    WHEN e.grade = 'B' THEN 3.0
                    WHEN e.grade = 'B-' THEN 2.7
                    WHEN e.grade = 'C+' THEN 2.3
                    WHEN e.grade = 'C' THEN 2.0
                    WHEN e.grade = 'C-' THEN 1.7
                    WHEN e.grade = 'D+' THEN 1.3
                    WHEN e.grade = 'D' THEN 1.0
                    WHEN e.grade = 'F' THEN 0.0
                    ELSE NULL
                END AS grade_points
                FROM enrolls e
                INNER JOIN courses c ON e.code = c.code
                WHERE e.user_id = ?
            ");
            $stmt_fetch_courses->bind_param('s', $user_id);

            try {
                $stmt_fetch_courses->execute();
                $result_courses = $stmt_fetch_courses->get_result();

                if ($result_courses->num_rows > 0) {
                    while ($row = $result_courses->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['credit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['grade_points']) . "</td>";
                        
                        echo "</tr>";
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-muted">No added courses.</td></tr>';
                }
                $stmt_fetch_courses->close();
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                $stmt_fetch_courses->close();
                $conn->close();
                header("refresh:1; url=dashboard.php");
                exit;
            }
            ?>
        </tbody>
    </table>

    <h3>Current Retakes</h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Course Code</th>
            <th>Expecting Grade</th>
            <th>Previous Grade</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt_fetch_retakes = $conn->prepare("
            SELECT e.code, 
                   e.grade AS current_grade, 
                   en.grade AS previous_grade
            FROM enrolls e
            INNER JOIN enrolled en ON e.code = en.code
            WHERE e.user_id = ? AND e.code IN (SELECT code FROM enrolled WHERE user_id = ?)
        ");
        $stmt_fetch_retakes->bind_param('ss', $user_id, $user_id);

        try {
            $stmt_fetch_retakes->execute();
            $result_retakes = $stmt_fetch_retakes->get_result();

            if ($result_retakes->num_rows > 0) {
                while ($row = $result_retakes->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['current_grade']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['previous_grade']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="3" class="text-muted">No retake courses found.</td></tr>';
            }
            $stmt_fetch_retakes->close();
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
            $stmt_fetch_retakes->close();
            $conn->close();
            header("refresh:1; url=dashboard.php");
            exit;
        }
        ?>
    </tbody>
</table>

    <form method="POST" action="">
        <button type="submit" name="delete_all" class="btn btn-danger mt-3">Delete All</button>
    </form>
</div>
</div>
<?php
$conn->close();
?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>