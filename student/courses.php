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

    if (isset($_POST['add_course'])) {
        $semester = $_POST['semester'];
        $code = strtoupper(trim($_POST['code']));
        $initial = isset($_POST['initial']) && trim($_POST['initial']) !== '' 
    ? strtoupper(trim($_POST['initial'])) 
    : NULL;
        $grade = $_POST['grade'];
        $experience = $_POST['experience'] ? $_POST['experience'] : NULL;

        $stmt_check_course = $conn->prepare("SELECT * FROM courses WHERE code = ?");
    $stmt_check_course->bind_param("s", $code);

    try {
        $stmt_check_course->execute();
        $result_course = $stmt_check_course->get_result();

        if ($result_course->num_rows === 0) {
            
            echo '<div class="alert alert-danger">Unavailabe course.</div>';
            $stmt_check_course->close();
            header("refresh:1");
            exit;
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Something went wrong.</div>';
        $stmt_check_course->close();
        header("refresh:1");
        exit;
    }
    $stmt_check_course->close();

    if ($initial !== NULL) {
        $stmt_check_teacher = $conn->prepare("SELECT * FROM teachers WHERE initial = ?");
        $stmt_check_teacher->bind_param("s", $initial);

        try {
            $stmt_check_teacher->execute();
            $result_teacher = $stmt_check_teacher->get_result();

            if ($result_teacher->num_rows === 0) {
                
                echo '<div class="alert alert-danger">Unavailable teacher.</div>';
                $stmt_check_teacher->close();
                header("refresh:1");
                exit;
            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            $stmt_check_teacher->close();
            header("refresh:1");
            exit;
        }
        $stmt_check_teacher->close();
    }

    $grade_points = [
        "A" => 4.0, "A-" => 3.7,
        "B+" => 3.3, "B" => 3.0, "B-" => 2.7,
        "C+" => 2.3, "C" => 2, "C-" => 1.7, "D+" => 1.3, "D" => 1, "F" => 0.0
    ];
    $stmt_check_same_semester = $conn->prepare(
        "SELECT semester, code FROM enrolled WHERE user_id = ? AND code = ? AND semester = ?
        UNION 
        SELECT semester, code FROM retakes WHERE user_id = ? AND code = ? AND semester = ?"
    );
    $stmt_check_same_semester->bind_param("isiiis", $user_id, $code, $semester, $user_id, $code, $semester);

    try {
        $stmt_check_same_semester->execute();
        $result_same_semester = $stmt_check_same_semester->get_result();

        if ($result_same_semester->num_rows > 0) {
            echo '<div class="alert alert-danger">Invalid semester.</div>';
            header("refresh:1");
            $stmt_check_same_semester->close();
            exit;
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Something went wrong.</div>';
        $stmt_check_same_semester->close();
        header("refresh:1");
        exit;
    }
    $stmt_check_same_semester->close();

    $stmt_check_enrolled = $conn->prepare("SELECT * FROM enrolled WHERE user_id = ? AND code = ?");
    $stmt_check_enrolled->bind_param("is", $user_id, $code);
    try {
        $stmt_check_enrolled->execute();
        $result_enrolled = $stmt_check_enrolled->get_result();
        if ($result_enrolled->num_rows > 0) {
            $row_enrolled = $result_enrolled->fetch_assoc();
            $existing_grade = $row_enrolled['grade'];
            $existing_semester = $row_enrolled['semester'];
            if($semester==$existing_semester){
        echo '<div class="alert alert-danger">Invalid semester.</div>';
        $stmt_check_enrolled->close();
        header("refresh:1");
        exit;
            }
            if ($grade_points[$grade] > $grade_points[$existing_grade]) {
                $stmt_move_to_retakes = $conn->prepare(
                    "INSERT INTO retakes (user_id, semester, code, grade) VALUES (?, ?, ?, ?)"
                );
                $stmt_move_to_retakes->bind_param("iiss", $user_id, $existing_semester, $code, $existing_grade);
                
                
                $stmt_update_enrolled = $conn->prepare(
                    "UPDATE enrolled SET semester = ?, grade = ?, experience = ?, initial = ? WHERE user_id = ? AND code = ?"
                );
                $stmt_update_enrolled->bind_param("isssis", $semester, $grade, $experience, $initial, $user_id, $code);
                
                $conn->begin_transaction();
                try{
                    $stmt_move_to_retakes->execute();
                    $stmt_update_enrolled->execute();
                    echo '<div class="alert alert-success">Course updated</div>';
                    $conn->commit();
                }
                catch(Exception $e){
                    $conn->rollback();
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt_check_enrolled->close();
                $stmt_move_to_retakes->close();
                $stmt_update_enrolled->close();
                header("refresh:1");
                exit;
            } else {
                $stmt_add_to_retakes = $conn->prepare(
                    "INSERT INTO retakes (user_id, semester, code, grade) VALUES (?, ?, ?, ?)"
                );
                $stmt_add_to_retakes->bind_param("iiss", $user_id, $semester, $code, $grade);
                
                try{
                    $stmt_add_to_retakes->execute();
                    echo '<div class="alert alert-success">Course updated</div>';
                }
                catch(Exception $e){
                    
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt_check_enrolled->close();
                $stmt_add_to_retakes->close();
                header("refresh:1");
                exit;
            }
        } else {
            $stmt_insert_enrolled = $conn->prepare(
                "INSERT INTO enrolled (user_id, semester, code, initial, grade, experience) VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt_insert_enrolled->bind_param("iisssi", $user_id, $semester, $code, $initial, $grade, $experience);
            
            try{
                $stmt_insert_enrolled->execute();
                echo '<div class="alert alert-success">Course added</div>';
            }
            catch(Exception $e){
                
                echo '<div class="alert alert-danger">Something went wrong.</div>';
            }
            $stmt_insert_enrolled->close();
            $stmt_check_enrolled->close();
            
            header("refresh:1");
            exit;
        }
        $stmt_check_enrolled->close();
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Something went wrong.</div>';
        $stmt_check_enrolled->close();
        header("refresh:1");
        exit;
    }

}
        
    
    if (isset($_POST['delete'])) {
        $course_code = $_POST['code'];
        $user_id = $_SESSION['user_id'];  
    
        
        $stmt = $conn->prepare("DELETE FROM enrolled WHERE user_id=? AND code=?");
        $stmt->bind_param("is", $user_id, $course_code);
        try{
            $stmt->execute();
            echo "<div class='alert alert-success'>Course deleted.</div>";
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }
    
        
    
        $stmt->close();
        header("refresh:1");
        exit;
    }
    if (isset($_POST['delete_retake'])) {
        $course_code = $_POST['code'];
        $semester = $_POST['semester'];
        $user_id = $_SESSION['user_id'];  
    
        $stmt = $conn->prepare("DELETE FROM retakes WHERE user_id=? AND code=? AND semester=?");
        $stmt->bind_param("isi", $user_id, $course_code, $semester);
    
        try {
            $stmt->execute();
            echo "<div class='alert alert-success'>Course deleted.</div>";
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }
    
        $stmt->close();
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
            <h1 class="text-center mb-4">Past Courses</h1>
            <p class="text-center">Space to manage and store the stats of past courses</p>

            
    <div class="mb-5 border p-3 rounded">
        <h3>Add a Course</h3>
        
        <form method="post" action="">
        <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <select class="form-control" id="semester" name="semester" required>
                    <option value="">Select Semester</option>
                    <?php for ($i = 1; $i <= 24; $i++): ?>
                        <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="code" class="form-label">Course Code</label>
                <input type="text" class="form-control" name="code" id="code" maxlength="7" placeholder="CSE115" pattern="^\S+$" required>
            </div>
            <div class="mb-3">
                <label for="initial" class="form-label">Teacher Initial (Optional. For rating purposes)</label>
                <input type="text" class="form-control" id="initial" name="initial" placeholder="MLE" pattern="^\S+$" maxlength="5">
            </div>
            <div class="mb-3">
                <label for="grade" class="form-label">Grade</label>
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
    <label for="experience" class="form-label">My experience rating (Optional. For rating purposes)</label>
    <select class="form-control" id="experience" name="experience">
        <option value="">Select Rate</option>
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
</div>
            <button type="submit" class="btn btn-primary" name="add_course">Add</button>
            <p class="text-muted small">*Queue unavailable courses or teachers through <a href="coursesteachers.php">Courses and Teachers</a></p>
        </form>
    </div>
    <div class="mb-5 border p-3 rounded">
    <h3>Courses</h3>
    <p class="text-muted">All added courses will appear here</p>

    <?php
    $stmt = $conn->prepare("SELECT DISTINCT semester FROM enrolled WHERE user_id=? ORDER BY semester");
    $stmt->bind_param("i", $user_id);
    
    try{
        $stmt->execute();
    }
    catch(Exception $e){
        echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
        $stmt->close();
        $conn->close();
        header("refresh:1; url=dashboard.php");
        exit;
    }
    $result = $stmt->get_result();

    $grade_points = [
        "A" => 4.0, "A-" => 3.7, "B+" => 3.3, "B" => 3.0,
        "B-" => 2.7, "C+" => 2.3, "C" => 2.0, "C-" => 1.7,
        "D+" => 1.3, "D" => 1.0, "F" => 0.0
    ];

    while ($semester_row = $result->fetch_assoc()) {
        $semester = $semester_row['semester'];

        $course_stmt = $conn->prepare(
            "SELECT e.code, e.grade, e.initial, c.credit 
            FROM enrolled e 
            JOIN courses c ON e.code = c.code 
            WHERE e.user_id=? AND e.semester=? order by e.code"
        );
        $course_stmt->bind_param("ii", $user_id, $semester);
        
        try{
            $course_stmt->execute();
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong. Ridirecting...</div>';
            $course_stmt->close();
            $stmt->close();
            $conn->close();
            header("refresh:1; url=dashboard.php");
            exit;
        }
        $course_result = $course_stmt->get_result();

        if ($course_result->num_rows > 0) {
            echo "<h5>Semester $semester</h5>";
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>Course Code</th><th>Credit</th><th>Grade</th><th>Grade Points</th><th>Teacher Initial</th><th>Action</th></tr></thead><tbody>";

            
            while ($course_row = $course_result->fetch_assoc()) {
                $course_code = $course_row['code'];
                $credits = $course_row['credit'];
                $grade = $course_row['grade'];
                $grade_point = $grade_points[$grade] ?? 0.0;  
                $initial = $course_row['initial'];

                echo "<tr>
                        <td>$course_code</td>
                        
                        <td>$credits</td>
                        <td>$grade</td>
                        <td>$grade_point</td>
                        <td>$initial</td>
                        <td>
                        <form action='' method='POST'>
                            <input type='hidden' name='code' value='$course_code'>
                            <button type='submit' name='delete' class='btn btn-danger'>Delete</button>
                        </form>
                    </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No courses found for Semester $semester.</p>";
        }

        $course_stmt->close();
    }

    $stmt->close();
    ?>
</div>    
                
<div class="mb-5 border p-3 rounded">
    <h3>Retakes</h3>
    <p class="text-muted">All retaken courses will appear here automatically that is no longer counted</p>

    <?php
    $stmt_retakes = $conn->prepare("SELECT DISTINCT semester FROM retakes WHERE user_id=? ORDER BY semester");
    $stmt_retakes->bind_param("i", $user_id);

    try {
        $stmt_retakes->execute();
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
        $stmt_retakes->close();
        $conn->close();
        header("refresh:1; url=dashboard.php");
        exit;
    }

    $result_retakes = $stmt_retakes->get_result();

    while ($semester_row = $result_retakes->fetch_assoc()) {
        $semester = $semester_row['semester'];

        $retake_course_stmt = $conn->prepare(
            "SELECT r.code, r.grade, c.credit 
            FROM retakes r 
            JOIN courses c ON r.code = c.code 
            WHERE r.user_id=? AND r.semester=? order by r.code"
        );
        $retake_course_stmt->bind_param("ii", $user_id, $semester);

        try {
            $retake_course_stmt->execute();
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
            $retake_course_stmt->close();
            $stmt_retakes->close();
            $conn->close();
            header("refresh:1; url=dashboard.php");
            exit;
        }

        $retake_result = $retake_course_stmt->get_result();

        if ($retake_result->num_rows > 0) {
            echo "<h5>Semester $semester</h5>";
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>Course Code</th><th>Credit</th><th>Grade</th><th>Grade Points</th><th>Action</th></tr></thead><tbody>";

            while ($retake_row = $retake_result->fetch_assoc()) {
                $course_code = $retake_row['code'];
                $credits = $retake_row['credit'];
                $grade = $retake_row['grade'];
                $grade_point = $grade_points[$grade] ?? 0.0;

                echo "<tr>
                        <td>$course_code</td>
                        <td>$credits</td>
                        <td>$grade</td>
                        <td>$grade_point</td>
                        <td>
                        <form action='' method='POST'>
                            <input type='hidden' name='code' value='$course_code'>
                            <input type='hidden' name='semester' value='$semester'>
                            <button type='submit' name='delete_retake' class='btn btn-danger'>Delete</button>
                        </form>
                        </td>
                    </tr>";
            }
            echo "</tbody></table>";
        } 

        $retake_course_stmt->close();
    }

    $stmt_retakes->close();
    ?>
</div>
</div>
<?php
$conn->close();
?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>