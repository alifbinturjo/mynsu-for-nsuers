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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_aim'])) {
        $new_credit = $_POST['total_credit'];
        $new_cgpa = $_POST['target_cgpa'];
        $waived = $_POST['waived'];

        $stmt_aim = $conn->prepare("SELECT credit, cgpa, waived FROM aims WHERE user_id = ?");
                    $stmt_aim->bind_param("i", $user_id); 
                    
                    try{
                        $stmt_aim->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_aim->close();
                            $conn->close();
                            
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_aim = $stmt_aim->get_result();
                    $aim = $result_aim->fetch_assoc();

        if ($aim) {
            
            $stmt_update = $conn->prepare("UPDATE aims SET credit = ?, cgpa = ?, waived=? WHERE user_id = ?");
            $stmt_update->bind_param("dddi", $new_credit, $new_cgpa, $waived, $user_id); 
            
            try{
                $stmt_update->execute();
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                    $stmt_update->close();
                    $stmt_aim->close();
                    header("refresh:1");
                    exit;
                }
            $stmt_update->close();
        } else {
            
            $stmt_insert = $conn->prepare("INSERT INTO aims (user_id, credit, waived, cgpa) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("iddd", $user_id, $new_credit, $waived, $new_cgpa);
            
            try{
                $stmt_insert->execute();
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                    $stmt_insert->close();
                    $stmt_aim->close();
                    header("refresh:1");
                    exit;
                }
            $stmt_insert->close();
        }
        $stmt_aim->close();
        header("refresh:1");
        echo '<div class="alert alert-success">Saved successfully.</div>';
        exit;
    }

    if (isset($_POST['save_threshold'])) {
        $threshold_grade = $_POST['threshold_grade'];
        $stmt_save_threshold = $conn->prepare("
            INSERT INTO aims (user_id, grade) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE grade = VALUES(grade)
        ");
        $stmt_save_threshold->bind_param("is", $user_id, $threshold_grade);
        try{
            $stmt_save_threshold->execute();
            echo '<div class="alert alert-success">Saved successfully.</div>';
            }
            catch(Exception $e){
                echo '<div class="alert alert-danger">Something went wrong.</div>';

            }
            $stmt_save_threshold->close();
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
            <h1 class="text-center mb-4">Overview</h1>
            <p class="text-center">Insights over provided stats</p>
            
    <div class="row mt-4">
        <div class="col-12">
            <div class="card ">
                <div class="card-body">
                    <h4 class="card-title">General</h4>
                    <p class="card-text">
                    <?php
                    $stmt_aim = $conn->prepare("SELECT credit, cgpa, waived FROM aims WHERE user_id = ?");
                    $stmt_aim->bind_param("i", $user_id); 
                    
                    try{
                        $stmt_aim->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_aim->close();
                            $conn->close();
                            
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_aim = $stmt_aim->get_result();
                    $aim = $result_aim->fetch_assoc();

                    if ($aim) {
                        $total_credit = $aim['credit'];
                        $target_cgpa = $aim['cgpa'];
                        $waived=$aim['waived'];
                    }
                    $stmt_aim->close();
                    
                    $grade_points = [
                        'A' => 4.0,
                        'A-' => 3.7,
                        'B+' => 3.3,
                        'B' => 3.0,
                        'B-' => 2.7,
                        'C+' => 2.3,
                        'C' => 2.0,
                        'C-' => 1.7,
                        'D+' => 1.3,
                        'D' => 1.0,
                        'F' => 0.0
                    ];

                    $stmt = $conn->prepare("
                        SELECT c.credit, e.grade, e.semester
                        FROM enrolled e
                        JOIN courses c ON e.code = c.code
                        WHERE e.user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    try{
                    $stmt->execute();
                    }
                    catch(Exception $e){
                        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                        $stmt->close();
                        $conn->close();
                        header("refresh:1; url=dashboard.php");
                        exit;
                    }
                    $result = $stmt->get_result();

                    $total_credits = 0;
                    $total_grade_points = 0;
                    $completed_credit = 0;
                    $completed_course=0;
                    $semesters = [];

                    while ($row = $result->fetch_assoc()) {
                        $credit = $row['credit'];
                        $grade = $row['grade'];
                        $semester = $row['semester'];
                        
                        $total_credits += $credit;

                        if ($grade !== 'F') {
                            $completed_credit += $credit; 
                            $completed_course+=1;
                        }

                        if (isset($grade_points[$grade])) {
                            $total_grade_points += $credit * $grade_points[$grade];
                        }

                        $semesters[] = $semester;
                    }
                    $query_retakes = "SELECT DISTINCT semester FROM retakes WHERE user_id = ?";
                    $stmt_retakes = $conn->prepare($query_retakes);
                    $stmt_retakes->bind_param('i', $user_id);
                    
                    try{
                        $stmt_retakes->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt->close();
                            $conn->close();
                            $stmt_retakes->close();
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_retakes = $stmt_retakes->get_result();

                    while ($row_retakes = $result_retakes->fetch_assoc()) {
                        $semester = $row_retakes['semester'];
                        
                            $semesters[] = $semester; 
                        
                    }

                    $distinct_semesters = count(array_unique($semesters));

                    if ($total_credits > 0) {
                        
                        $cgpa = round($total_grade_points / $total_credits, 2);
                        $completed_credit+=$waived;

                        echo "<br>Current CGPA: $cgpa <br>";
                        echo "<br>Completed credits: $completed_credit <br>";
                        echo "<br>Completed courses: $completed_course <br>";
                        echo "<br>Total semesters: $distinct_semesters <br>";
                        $completed_credit-=$waived;
                    } else {
                        $cgpa=0;
                        echo "No past courses.";
                    }

                    $stmt->close();
                    $stmt_retakes->close();
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
                <h4 class="card-title">Goal</h4>
                <p class="card-text">
                    <?php
                    
                    

                    if ($aim) {
                        $total_credit = $aim['credit'];
                        $target_cgpa = $aim['cgpa'];
                        $waived=$aim['waived'];

                        $remaining_credit = $total_credit - $completed_credit - $waived; 
                        $remaining_credit_text = $remaining_credit <= 0 ? "Completed" : $remaining_credit;
                        
                        if ($remaining_credit > 0) {
                            $needed_cgpa = ((($total_credit-$waived )* $target_cgpa) - ($total_credits * $cgpa)) / $remaining_credit;
                            $needed_cgpa_text = $needed_cgpa <= $cgpa ? "Achieved" : round($needed_cgpa, 2);
                        } else {
                            $needed_cgpa_text = "Completed";
                        }

                        
                        echo "<br>Remaining credits: $remaining_credit_text <br>";
                        echo "<br>Needed CGPA/Course: $needed_cgpa_text <br>";
                    } else {
                        echo "No saved data.";
                    }

                    
                    ?>
                </p>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="total_credit" class="form-label">Total credits</label>
                        <input type="number" class="form-control" min="0" max="500" step="1" id="total_credit" name="total_credit" value="<?php echo $aim['credit'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="target_cgpa" class="form-label">CGPA Goal</label>
                        <input type="number" min="0" max="4" step="0.01" class="form-control" id="target_cgpa" name="target_cgpa" value="<?php echo $aim['cgpa'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="waived" class="form-label">Waived credits</label>
                        <input type="number" min="0" max="20" class="form-control" id="waived" name="waived" value="<?php echo $aim['waived'] ?? ''; ?>" required>
                    </div>
                    <button type="submit" name="save_aim" class="btn btn-primary">Save</button>
                </form>
                
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <div class="card ">
            <div class="card-body">
                <h4 class="card-title">Current Semester</h4>
                <p class="card-text">
                    <?php
                    
                    $grade_points = [
                        'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
                        'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
                        'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
                        'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
                    ];
                    
                    $course_count = 0;
                    $credit_count = 0;
                    $total_grade_points = 0;
                    $best_grades = []; 
                    $total_credits2 = 0;
                    $total_grade_points2 = 0;
                    
                    $stmt_grades = $conn->prepare("SELECT e.code, e.grade FROM enrolls e WHERE e.user_id = ?");
                    $stmt_grades->bind_param("i", $user_id);
                    
                    try{
                        $stmt_grades->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_grades->close();
                            $conn->close();
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_grades = $stmt_grades->get_result();

                    while ($row = $result_grades->fetch_assoc()) {
                        $grade = $row['grade'];
                        $code = $row['code'];

                        $stmt_credit = $conn->prepare("SELECT credit FROM courses WHERE code = ?");
                        $stmt_credit->bind_param("s", $code);
                        
                        try{
                            $stmt_credit->execute();
                            }
                            catch(Exception $e){
                                echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                $stmt_grades->close();
                                $stmt_credit->close();
                                $conn->close();
                                header("refresh:1; url=dashboard.php");
                                exit;
                            }
                        $result_credit = $stmt_credit->get_result();
                        $credit_data = $result_credit->fetch_assoc();
                        $credit = $credit_data['credit'] ?? 0;

                        $course_count++;
                        $credit_count += $credit;
                        
                        if (isset($grade_points[$grade])) {
                            $total_grade_points += $credit * $grade_points[$grade];
                        }
                        if (!isset($best_grades[$code]) || $grade_points[$best_grades[$code]] < $grade_points[$grade]) {
                            $best_grades[$code] = $grade;
                        }
                    }
                    
                    $cgpa = $credit_count > 0 ? round($total_grade_points / $credit_count, 2) : 'N/A';
                    $stmt_grades->close();
                                

                    $stmt_enrolled = $conn->prepare("SELECT e.code, e.grade FROM enrolled e WHERE e.user_id = ?");
                    $stmt_enrolled->bind_param("i", $user_id);
                    
                    try{
                        $stmt_enrolled->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            
                            $stmt_enrolled->close();
                            $conn->close();
                            
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_enrolled = $stmt_enrolled->get_result();

                    while ($row = $result_enrolled->fetch_assoc()) {
                        $code = $row['code'];
                        $grade = $row['grade'];

                        
                        $stmt_credit = $conn->prepare("SELECT credit FROM courses WHERE code = ?");
                        $stmt_credit->bind_param("s", $code);
                        
                        try{
                            $stmt_credit->execute();
                            }
                            catch(Exception $e){
                                echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                
                                $stmt_credit->close();

                                $stmt_enrolled->close();
                                $conn->close();
                                
                                header("refresh:1; url=dashboard.php");
                                exit;
                            }
                        $result_credit = $stmt_credit->get_result();
                        $credit_data = $result_credit->fetch_assoc();
                        $credit = $credit_data['credit'] ?? 0;

                        
                        if (!isset($best_grades[$code]) || $grade_points[$best_grades[$code]] < $grade_points[$grade]) {
                            $best_grades[$code] = $grade;
                        }
                    }
                    

                                $stmt_enrolled->close();

                    foreach ($best_grades as $code => $best_grade) {
                        
                        $stmt_credit = $conn->prepare("SELECT credit FROM courses WHERE code = ?");
                        $stmt_credit->bind_param("s", $code);
                        $stmt_credit->execute();
                        try{
                            $stmt_credit->execute();
                            }
                            catch(Exception $e){
                                echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                
                                $stmt_credit->close();
                                
                                $conn->close();
                                
                                header("refresh:1; url=dashboard.php");
                                exit;
                            }
                        $result_credit = $stmt_credit->get_result();
                        $credit_data = $result_credit->fetch_assoc();
                        $credit = $credit_data['credit'] ?? 0;

                        
                        if (isset($grade_points[$best_grade])) {
                            $total_grade_points2 += $credit * $grade_points[$best_grade];
                        }

                        
                        $total_credits2 += $credit;
                        $stmt_credit->close();
                    }

                    $next_cgpa = $total_credits2 > 0 ? round($total_grade_points2 / $total_credits2, 2) : 'N/A';
                    
                    if($course_count!="0"){
                    echo "<br>Courses taken: $course_count <br>";
                    echo "<br>Credits taken: $credit_count <br>";
                    echo "<br>Expecting Semester CGPA: $cgpa <br>";
                    echo "<br>Expecting Overall CGPA: $next_cgpa <br>";
                    }
                    else{
                        echo"Current semester data unavailable";
                    }
                    
                    
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
                <h4 class="card-title">Individual Semesters</h4>
                <p class="card-text">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Semester</th>
                                <th>Credits taken</th>
                                <th>Semester CGPA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            
                            $grade_points = [
                                
                                'A' => 4.0,
                                'A-' => 3.7,
                                'B+' => 3.3,
                                'B' => 3.0,
                                'B-' => 2.7,
                                'C+' => 2.3,
                                'C' => 2.0,
                                'C-' => 1.7,
                                'D+' => 1.3,
                                'D' => 1.0,
                                'F' => 0.0,
                            ];

                            
                            $stmt_semesters = $conn->prepare("
    SELECT semester FROM (
        SELECT DISTINCT semester FROM enrolled WHERE user_id = ?
        UNION
        SELECT DISTINCT semester FROM retakes WHERE user_id = ?
    ) AS semesters
    ORDER BY semester ASC
");
$stmt_semesters->bind_param("ii", $user_id, $user_id);
                            
                            try{
                                $stmt_semesters->execute();
                                }
                                catch(Exception $e){
                                    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                    $stmt_semesters->close();
                                    $conn->close();
                                    header("refresh:1; url=dashboard.php");
                                    exit;
                                }
                            $result_semesters = $stmt_semesters->get_result();

                            $semester_count=0;
                            while ($semester = $result_semesters->fetch_assoc()) {
                                $semester_no = $semester['semester'];
                                $total_credit = 0;
                                $total_grade_points = 0;
                                $semester_count++;
                                
                                $stmt_courses = $conn->prepare("
    SELECT c.code, c.credit, combined.grade 
    FROM (
        SELECT code, grade FROM enrolled WHERE semester = ? AND user_id = ?
        UNION ALL
        SELECT code, grade FROM retakes WHERE semester = ? AND user_id = ?
    ) AS combined
    INNER JOIN courses c ON combined.code = c.code
");
$stmt_courses->bind_param("iiii", $semester_no, $user_id, $semester_no, $user_id);
                                
                                try{
                                    $stmt_courses->execute();
                                    }
                                    catch(Exception $e){
                                        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                        $stmt_courses->close();
                                        $stmt_semesters->close();
                                        $conn->close();
                                        header("refresh:1; url=dashboard.php");
                                        exit;
                                    }
                                $result_courses = $stmt_courses->get_result();

                                
                                while ($row = $result_courses->fetch_assoc()) {
                                    $credit = $row['credit'];
                                    $grade = $row['grade'];

                                    $total_credit += $credit;
                                    if (isset($grade_points[$grade])) {
                                        $total_grade_points += $credit * $grade_points[$grade];
                                    }
                                }

                                $stmt_courses->close();

                                
                                $cgpa = $total_credit > 0 ? round($total_grade_points / $total_credit, 2) : 'N/A';

                                
                                echo "<tr>
                                        <td>$semester_no</td>
                                        <td>$total_credit</td>
                                        <td>$cgpa</td>
                                    </tr>";
                            }
                            if($semester_count==0){
                                echo"No past course data.";
                            }

                            $stmt_semesters->close();
                            ?>
                        </tbody>
                    </table>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4">
    <div class="col-12">
        <div class="card ">
            <div class="card-body">
                <h4 class="card-title">Retakes</h4>
                
                    <?php
                    
                    $stmt_retakes_count = $conn->prepare("SELECT COUNT(DISTINCT code) AS retaken_count FROM retakes WHERE user_id = ?");
                    $stmt_retakes_count->bind_param("i", $user_id);
                    
                    try{
                        $stmt_retakes_count->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_retakes_count->close();
                            $conn->close();
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_retakes_count = $stmt_retakes_count->get_result();
                    $data_retakes_count = $result_retakes_count->fetch_assoc();
                    $retaken_count = $data_retakes_count['retaken_count'] ?? 0;
                    $stmt_retakes_count->close();
                    
                    $stmt_current_retakes = $conn->prepare("
                        SELECT COUNT(DISTINCT e.code) AS current_retakes 
                        FROM enrolls e 
                        INNER JOIN enrolled en ON e.code = en.code 
                        WHERE e.user_id = ?
                    ");
                    $stmt_current_retakes->bind_param("i", $user_id);
                    
                    try{
                        $stmt_current_retakes->execute();
                        }
                        catch(Exception $e){
                            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                            $stmt_current_retakes->close();
                            $conn->close();
                            header("refresh:1; url=dashboard.php");
                            exit;
                        }
                    $result_current_retakes = $stmt_current_retakes->get_result();
                    $data_current_retakes = $result_current_retakes->fetch_assoc();
                    $current_retakes = $data_current_retakes['current_retakes'] ?? 0;
                    $stmt_current_retakes->close();

                    echo "<br>Past retaken courses: $retaken_count <br>";
                    echo "<br>Current retaken courses: $current_retakes <br>";
                
                $grade_points = [
                    'B' => 3.0, 'B-' => 2.7,
                    'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
                    'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
                ];

                
                $stmt_fetch_threshold = $conn->prepare("SELECT grade FROM aims WHERE user_id = ?");
                $stmt_fetch_threshold->bind_param("i", $user_id);
                
                try{
                    $stmt_fetch_threshold->execute();
                    }
                    catch(Exception $e){
                        echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                        $stmt_fetch_threshold->close();
                        $conn->close();
                        header("refresh:1; url=dashboard.php");
                        exit;
                    }
                $result_threshold = $stmt_fetch_threshold->get_result();
                $threshold_data = $result_threshold->fetch_assoc();
                $threshold_grade = $threshold_data['grade'] ?? null;
                $stmt_fetch_threshold->close();

                echo '<form method="POST" class="mt-3">';
                echo '<div class="mb-3">';
                echo '<label for="threshold_grade">Grade threshold</label>';
                echo '<select id="threshold_grade" name="threshold_grade" class="form-control">';
                foreach ($grade_points as $grade => $point) {
                    $selected = ($threshold_grade === $grade) ? "selected" : "";
                    echo "<option value='$grade' $selected>$grade</option>";
                }
                echo '</select>';
                echo '</div>';
                
                echo '<button type="submit" name="save_threshold" class="btn btn-primary">Save</button>';
                echo '</form>';
                ?>

                
                <h6 class="mt-4">Courses to Retake</h6>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Current Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!is_null($threshold_grade)) {
                            $threshold_point = $grade_points[$threshold_grade];

                            
                            $stmt_retake_courses = $conn->prepare("
                                SELECT en.code, en.grade 
                                FROM enrolled en 
                                WHERE en.user_id = ?
                            ");
                            $stmt_retake_courses->bind_param("i", $user_id);
                            
                            try{
                                $stmt_retake_courses->execute();
                                }
                                catch(Exception $e){
                                    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                    $stmt_retake_courses->close();
                                    $conn->close();
                                    header("refresh:1; url=dashboard.php");
                                    exit;
                                }
                            $result_retake_courses = $stmt_retake_courses->get_result();
                            if ($result_retake_courses->num_rows > 0) {
                            while ($row = $result_retake_courses->fetch_assoc()) {
                                $course_code = $row['code'];
                                $current_grade = $row['grade'];

                                
                                if (isset($grade_points[$current_grade]) && $grade_points[$current_grade] <= $threshold_point) {
                                    echo "<tr><td>$course_code</td><td>$current_grade</td></tr>";
                                }
                            }}
                            $stmt_retake_courses->close();
                        } else {
                            echo "<tr><td colspan='2'>Set grade threshold to view the courses needs to retake.</td></tr>";
                        }
                        
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row mt-4 mb-5">
    <div class="col-12">
        <div class="card ">
            <div class="card-body">
                <h4 class="card-title">Filter</h4>

                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="grade">Grade</label>
                        <select id="grade" name="grade" class="form-control">
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
                    <button type="submit" name="filter_grade" class="btn btn-primary">Filter courses by grade</button>
                </form>

                
                <?php
                if (isset($_POST['filter_grade'])) {
                    $selected_grade = $_POST['grade'];

                    
                    $grade_points = [
                        'A' => 4.0, 'A-' => 3.7,
                        'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
                        'C+' => 2.3, 'C' => 2.0, 'C-' => 1.7,
                        'D+' => 1.3, 'D' => 1.0, 'F' => 0.0
                    ];

                    if ($selected_grade != '') {
                        $selected_grade_point = $grade_points[$selected_grade] ?? 0;

                        
                        $stmt_grade_filter = $conn->prepare("
                            SELECT code 
                            FROM enrolled 
                            WHERE user_id = ? AND grade = ?
                        ");
                        $stmt_grade_filter->bind_param("is", $user_id, $selected_grade);
                        
                        try{
                            $stmt_grade_filter->execute();
                            }
                            catch(Exception $e){
                                echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                                $stmt_grade_filter->close();
                                $conn->close();
                                header("refresh:1; url=dashboard.php");
                                exit;
                            }
                        $result_grade_filter = $stmt_grade_filter->get_result();

                        echo '<h6 class="mt-4">Courses with Grade: ' . $selected_grade . '</h6>';
                        echo '<table class="table table-striped">';
                        echo '<thead><tr><th>Course Code</th></tr></thead>';
                        echo '<tbody>';
                        if ($result_grade_filter->num_rows > 0) {
                        while ($row = $result_grade_filter->fetch_assoc()) {
                            echo "<tr><td>{$row['code']}</td></tr>";
                        }
                    }
                    else{
                        echo '<tr><td>No courses found with this grade.</td></tr>';
                    }

                        echo '</tbody></table>';
                        $stmt_grade_filter->close();
                    }
                }
                ?>

  
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