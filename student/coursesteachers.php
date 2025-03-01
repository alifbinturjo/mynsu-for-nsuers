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
    
    $THRESHOLD = 3;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['course'])) {
            $course_code = strtoupper(trim($_POST['course_code']));
            $credit = (float) trim($_POST['credit']);

            $stmt_check = $conn->prepare("SELECT * FROM courses WHERE code = ?");
            $stmt_check->bind_param("s", $course_code);
            
            try{
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                echo '<div class="alert alert-info">Course already available.</div>';
                $stmt_check->close();
                header("Refresh:1");
                exit;
            } else {
                $stmt_check->close();
                $stmt_check_request = $conn->prepare("SELECT * FROM course_requests WHERE user_id = ? AND code = ? and credit=?");
                $stmt_check_request->bind_param("isd", $user_id, $course_code,$credit);
                
                try{
                    $stmt_check_request->execute();
                    $result_check_request = $stmt_check_request->get_result();

                if ($result_check_request->num_rows > 0) {
                    echo '<div class="alert alert-warning">Already in queue.</div>';
                    $stmt_check_request->close();
                    header("Refresh:1");
                    exit;
                } else {
                    $stmt_check_request->close();
                    $stmt_insert = $conn->prepare("INSERT INTO course_requests (user_id, code, credit) VALUES (?, ?, ?)");
                    $stmt_insert->bind_param("isd", $user_id, $course_code, $credit);
                    try{
                        $stmt_insert->execute();
                        echo '<div class="alert alert-success">Added to queue.</div>';
                        $stmt_insert->close();
                        
                        $stmt_count = $conn->prepare("SELECT COUNT(*) AS request_count FROM course_requests WHERE code = ? and credit=?");
                    $stmt_count->bind_param("sd", $course_code,$credit);
                    
                    try{
                        $stmt_count->execute();
                        $result_count = $stmt_count->get_result();
                    $row_count = $result_count->fetch_assoc();

                    if ($row_count['request_count'] >= $THRESHOLD) {
                        $stmt_add_course = $conn->prepare("INSERT INTO courses (code, credit) VALUES (?, ?)");
                        $stmt_add_course->bind_param("sd", $course_code, $credit);
                        $stmt_delete_requests = $conn->prepare("DELETE FROM course_requests WHERE code = ?");
                        $stmt_delete_requests->bind_param("s", $course_code);
                        $conn->begin_transaction();
                        try{
                            $stmt_add_course->execute();
                            
                            $stmt_delete_requests->execute();
                            
            
                        $conn->commit();
                    }
                    catch(Exception $e){
                        $conn->rollback();
                        $stmt_count->close();
                        $stmt_add_course->close();
                        $stmt_delete_requests->close();
                        echo '<div class="alert alert-danger">Something went wrong.</div>';
                        header("refresh:1");
                        exit;
                    }
                    $stmt_add_course->close();
                    $stmt_delete_requests->close();
                    }
                    $stmt_count->close();
                    }
                    catch(Exception $e){
                        echo '<div class="alert alert-danger">Something went wrong.</div>';
                        $stmt_count->close();
                        header("refresh:1");
                        exit;
                    }
                    header("refresh:1");
                    exit;
                    }
                    catch(Exception $e){
                        echo '<div class="alert alert-danger">Something went wrong.</div>';
                        $stmt_insert->close();
                        header("refresh:1");
                        exit;
                    }
                    

                    
                }
                
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                    $stmt_check_request->close();
                    header("refresh:1");
                    exit;
                }
                
            }
            
            }
            catch(Exception $e){
                echo '<div class="alert alert-danger">Something went wrong.</div>';
                $stmt_check->close();
                header("refresh:1");
                exit;
            }
            
        }
        else if(isset($_POST['teachers'])){
            $initial = strtoupper(trim($_POST['initial']));

            $stmt_check = $conn->prepare("SELECT * FROM teachers WHERE initial = ?");
            $stmt_check->bind_param("s", $initial);
            try{
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $stmt_check->close();
                echo '<div class="alert alert-info">Teacher already available.</div>';
                header("refresh:1");
                exit;
            } else {
                $stmt_check_request = $conn->prepare("SELECT * FROM teacher_requests WHERE user_id = ? AND initial = ?");
                $stmt_check_request->bind_param("is", $user_id, $initial);
                try{
                    $stmt_check_request->execute();
                    $result_check_request = $stmt_check_request->get_result();

                if ($result_check_request->num_rows > 0) {
                    $stmt_check_request->close();
                    echo '<div class="alert alert-warning">Already in queue.</div>';
                    header("refresh:1");
                    exit;
                }else {
                    $stmt_insert = $conn->prepare("INSERT INTO teacher_requests (user_id, initial) VALUES (?, ?)");
                    $stmt_insert->bind_param("is", $user_id, $initial);

                    try{
                        $stmt_insert->execute();
                        echo '<div class="alert alert-success">Added to queue.</div>';
                        $stmt_insert->close();
                        
                        $stmt_count = $conn->prepare("SELECT COUNT(*) AS request_count FROM teacher_requests WHERE initial = ?");
                    $stmt_count->bind_param("s", $initial);

                    try{
                        $stmt_count->execute();
                        $result_count = $stmt_count->get_result();
                    $row_count = $result_count->fetch_assoc();

                    if ($row_count['request_count'] >= $THRESHOLD) {
                        $stmt_add_course = $conn->prepare("INSERT INTO teachers (initial) VALUES (?)");
                        $stmt_add_course->bind_param("s", $initial);
                        $stmt_delete_requests = $conn->prepare("DELETE FROM teacher_requests WHERE initial = ?");
                        $stmt_delete_requests->bind_param("s", $initial);

                        $conn->begin_transaction();
                        try{
                        
                        $stmt_add_course->execute();
                            
                        $stmt_delete_requests->execute();
                            
                        
                        

                        $conn->commit();
                    }
                    catch(Exception $e){
                        $conn->rollback();
                        $stmt_count->close();
                        $stmt_add_course->close();
                        $stmt_delete_requests->close();
                        echo '<div class="alert alert-danger">Something went wrong.</div>';
                        header("refresh:1");
                        exit;
                    }
                    $stmt_add_course->close();
                    $stmt_delete_requests->close();
                    }
                    $stmt_count->close();
                    }
                    catch(Exception $e){
                        echo '<div class="alert alert-danger">Something went wrong.</div>';
                        $stmt_count->close();
                        header("refresh:1");
                        exit;
                    }
                    
                    header("refresh:1");
                    exit;
                    }
                    catch(Exception $e){
                        echo '<div class="alert alert-danger">Something went wrong.</div>';
                        $stmt_insert->close();
                        header("refresh:1");
                        exit;
                        
                    }
                    

                    
                }
                
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                    $stmt_check_request->close();
                    header("refresh:1");
                    exit;
                }
                
            }
            
            }
            catch(Exception $e){
                echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                $stmt_check->close();
                header("refresh:1");
                exit;
            }
            
        }
        else if (isset($_POST['delete_course_info'])) {
            list($code, $credit) = explode('|', $_POST['delete_course_info']);
            $stmt_delete = $conn->prepare("DELETE FROM course_requests WHERE code = ? AND credit = ? AND user_id = ?");
            $stmt_delete->bind_param("sdi", $code, $credit, $user_id);
            try{
                $stmt_delete->execute();
                echo '<div class="alert alert-success">Course deleted.</div>';
            }
            catch(Exception $e){
                echo '<div class="alert alert-danger">Something went wrong.</div>';
            }
            $stmt_delete->close();
            header("refresh:1");
            exit;
        }
        else if (isset($_POST['delete_teacher_info'])) {
            $initial = $_POST['delete_teacher_info'];
            $stmt_delete = $conn->prepare("DELETE FROM teacher_requests WHERE initial = ? AND user_id = ?");
            $stmt_delete->bind_param("si", $initial, $user_id);
            try{
                $stmt_delete->execute();
                echo '<div class="alert alert-success">Teacher deleted.</div>';
            }
            catch(Exception $e){
                echo '<div class="alert alert-danger">Something went wrong.</div>';
            }
            $stmt_delete->close();
            header("refresh:1");
            exit;
        }
        
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
            <h1 class="text-center mb-4">Courses and Teachers</h1>
            <p class="text-center">Search or queue unavailable courses and teachers</p>
            
<div class="mb-5 border p-3 rounded">
    <h3>Search courses and teachers</h3>
    <p class="text-muted small">If unavailable, add it to the queue</p>
    <form method="post" action="">
        <div class="mb-3">
            <label for="course_code" class="form-label">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" maxlength="7" placeholder="CSE115L" required>
        </div>
        <button type="submit" class="btn btn-primary" name="search_course">Search course</button>
    </form>

    <?php
    if (isset($_POST['search_course'])) {
        $course_code = $_POST['course_code'];

        $stmt = $conn->prepare("SELECT code, credit FROM courses WHERE code LIKE ?");
        $searchTerm = "%" . $course_code . "%";
        $stmt->bind_param("s", $searchTerm);
       
        try{
            $stmt->execute();
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            $stmt->close();
            header("refresh:1");
            exit;
            
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>Course Code</th><th>Credit</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['code']) . "</td><td>" . htmlspecialchars($row['credit']) . "</td></tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No courses found for the code: " . htmlspecialchars($course_code) . "</p>";
        }
        $stmt->close();
    }
    ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="teacher_initial" class="form-label">Teacher Initial</label>
            <input type="text" class="form-control" id="teacher_initial" name="teacher_initial" maxlength="5" placeholder="MLE" required>
        </div>
        <button type="submit" class="btn btn-primary" name="search_teacher">Search teacher</button>
    </form>

    <?php
    if (isset($_POST['search_teacher'])) {
        $teacher_initial = $_POST['teacher_initial'];

        $stmt = $conn->prepare("SELECT initial FROM teachers WHERE initial LIKE ?");
        $searchTerm = "%" . $teacher_initial . "%";
        $stmt->bind_param("s", $searchTerm);
        try{
            $stmt->execute();
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            $stmt->close();
            header("refresh:1");
            exit;
        }
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            
            echo "<table class='table table-striped'>";
            echo "<thead><tr><th>Teacher Initial</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . htmlspecialchars($row['initial']) . "</td></tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No teachers found for the initial: ".htmlspecialchars($teacher_initial)."</p>";
        }
        $stmt->close();
    }

    ?>
</div>
    <div class="mb-5 border p-3 rounded">
        <h3>Add Unavailabe Course</h3>
        <form method="post" action="">
                <div class="mb-3">
                    <label for="course_code" class="form-label">Course Code</label>
                    <input type="text" class="form-control" id="course_code" name="course_code" maxlength="7" pattern="^\S+$" placeholder="CSE115" required>
                </div>
                <div class="mb-3">
                    <label for="credit" class="form-label">Course Credit</label>
                    <input type="number" class="form-control" id="credit" name="credit" min="0" step="1.0" max="3.0" placeholder="3.0" required>
                </div>
                <button type="submit" class="btn btn-primary" name="course">Add</button>
            </form>
    </div>
    <div class="mb-5 border p-3 rounded">
        <h3>Add Unavailabe Teacher</h3>
        <form method="post" action="">
                <div class="mb-3">
                    <label for="initial" class="form-label">Teacher Initial</label>
                    <input type="text" class="form-control" id="initial" name="initial" maxlength="5" pattern="^\S+$" placeholder="HAR" required>
                </div>
                <button type="submit" class="btn btn-primary" name="teachers">Add</button>
            </form>
    </div>
    <div class="mb-5 border p-3 rounded">
        <h3>My queue</h3>
        <p class="text-muted small">Approved entries will disappear automatically.</p>
        <h5>Courses</h5>
        <?php
        $stmt_courses = $conn->prepare("SELECT code, credit FROM course_requests WHERE user_id = ?");
        $stmt_courses->bind_param("i", $user_id);
       
        try{
            $stmt_courses->execute();
        }
        catch(Exception $e){
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
        $stmt_courses->close();
        $conn->close();
        header("refresh:1; url=dashboard.php");
        exit;
        }
        $result_courses = $stmt_courses->get_result();

        if ($result_courses->num_rows > 0) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>Course Code</th><th>Credit</th><th>Action</th></tr></thead><tbody>';
            while ($row = $result_courses->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['code']) . '</td>';
                echo '<td>' . htmlspecialchars($row['credit']) . '</td>';
                echo '<td><form method="post" action=""><input type="hidden" name="delete_course_info" value="' . htmlspecialchars($row['code']) . '|' . htmlspecialchars($row['credit']) . '"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="text-muted">No queue for courses.</p>';
        }
        $stmt_courses->close();
        ?>
        <h5>Teachers</h5>
        <?php
        $stmt_teachers = $conn->prepare("SELECT initial FROM teacher_requests WHERE user_id = ?");
        $stmt_teachers->bind_param("i", $user_id);
        
        try{
            $stmt_teachers->execute();
        }
        catch(Exception $e){
        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
        $stmt_teachers->close();
        $conn->close();
        header("refresh:1; url=dashboard.php");
        exit;
        }
        $result_teachers = $stmt_teachers->get_result();

        if ($result_teachers->num_rows > 0) {
            echo '<table class="table table-striped">';
            echo '<thead><tr><th>Teacher Initial</th><th>Action</th></tr></thead><tbody>';
            while ($row = $result_teachers->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['initial']) . '</td>';
                echo '<td><form method="post" action=""><input type="hidden" name="delete_teacher_info" value="' . htmlspecialchars($row['initial']) . '"><button type="submit" class="btn btn-danger btn-sm">Delete</button></form></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p class="text-muted">No queue for teachers.</p>';
        }
        $stmt_teachers->close();
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