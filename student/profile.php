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
                $stmt_name=$conn->prepare("select first_name,last_name,registration_date,email from users where user_id=?");
                $stmt_name->bind_param("i",$user_id);
                
                try{
                    $stmt_name->execute();
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                    $stmt_name->close();
                    $conn->close();
                    header("refresh:1; url=dashboard.php");
                    exit;
                }
                $resutl=$stmt_name->get_result();
                $user=$resutl->fetch_assoc();
                $first_name=$user['first_name'];
                $_SESSION['first_name']=$first_name;
                $last_name=$user['last_name'];
                $registration_date=$user['registration_date'];
                $email=$user['email'];
                $_SESSION['email']=$email;
                $stmt_name->close();
                
                $stmt_st=$conn->prepare("select student_id,nsu_email from students where user_id=?");
                $stmt_st->bind_param("i",$user_id);
                
                try{
                    $stmt_st->execute();
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                    $stmt_st->close();
                    $conn->close();
                    header("refresh:1; url=dashboard.php");
                    exit;
                }
                $resutl=$stmt_st->get_result();
                $user=$resutl->fetch_assoc();
                $student_id=$user['student_id'];
                $nsuEmail=$user['nsu_email'];
                $stmt_st->close();

                $ui_rating = $func_rating = null;
                $stmt = $conn->prepare("SELECT user_interface, user_functionalities FROM platform_rate WHERE user_id = ?");
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
                $rating_data = $result->fetch_assoc();
                if ($rating_data) {
                    $ui_rating = $rating_data['user_interface'];
                    $func_rating = $rating_data['user_functionalities'];
                }
                $stmt->close();
    
            }
            else{
                session_destroy();
                $conn->close();
                echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
                header("refresh:1; url=../index.php");
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
                
                $new_first_name = $_POST['first_name'];
                $_SESSION['first_name']=$new_first_name;
                $new_last_name = $_POST['last_name'];
                $new_student_id = $_POST['student_id'];

                $stmt_update_user = $conn->prepare("UPDATE users SET first_name=?, last_name=? WHERE user_id=?");
                $stmt_update_user->bind_param("ssi", $new_first_name, $new_last_name, $user_id);

                $stmt_update_student = $conn->prepare("UPDATE students SET student_id=? WHERE user_id=?");
                $stmt_update_student->bind_param("ii", $new_student_id, $user_id);
                
                
                $conn->begin_transaction();
                try{
                    $stmt_update_user->execute();
                    $stmt_update_student->execute();
                    $conn->commit();
                    echo '<div class="alert alert-success">Profile updated successfully.</div>';
                }
                catch(Exception $e){
                    $conn->rollback();
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt_update_user->close();
                $stmt_update_student->close();
                
                header("Refresh:1");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rate_platform'])) {
                $new_ui_rating = $_POST['ui_rating'];
                $new_func_rating = $_POST['func_rating'];

                $stmt = $conn->prepare("REPLACE INTO platform_rate (user_id, user_interface, user_functionalities) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $user_id, $new_ui_rating, $new_func_rating);
                
                try{
                    $stmt->execute();
                    echo '<div class="alert alert-success">Thank you for rating.</div>';
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt->close();

                
                header("Refresh:1");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_security_variable'])) {
                $new_security_variable = $_POST['new_security_variable'];
            
                $hashed_security_variable = password_hash($new_security_variable, PASSWORD_DEFAULT);
            
                $stmt = $conn->prepare("UPDATE info SET security_variable=? WHERE user_id=?");
                $stmt->bind_param("si", $hashed_security_variable, $user_id);
                try{
                    $stmt->execute();
                    echo '<div class="alert alert-success">Security Key updated successfully.</div>';
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt->close();
            
                
                header("Refresh:1");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
                $new_password = $_POST['new_password'];
            
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
                $stmt = $conn->prepare("UPDATE info SET password=? WHERE user_id=?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                try{
                    $stmt->execute();
                    echo '<div class="alert alert-success">Password updated successfully.</div>';
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt->close();
            
                
                header("Refresh:1");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_acc'])) {
                
                $stmt_delete_user = $conn->prepare("DELETE FROM users WHERE user_id=?");
                
                
                
                try {
                    $stmt_delete_user->bind_param("i", $user_id);
                    
            
                    $stmt_delete_user->execute();
                    
            
                    
                    echo '<div class="alert alert-success">Your account has been deleted successfully. Redirecting...</div>';
                    
                    $stmt_delete_user->close();
                    $conn->close();
                    session_destroy();
                    header("refresh:1; url=../index.php");
                    exit;
                } catch (Exception $e) {
                    
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt_delete_user->close();
                header("Refresh:1");
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
<h1 class="text-center mb-4">Profile</h1>
<p class="text-center">Manage profile info</p>
            <form method="POST">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" maxlength="50" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" maxlength="50" required>
                </div>
                <div class="mb-3">
                    <label for="student_id" class="form-label">NSU ID</label>
                    <input type="number" class="form-control" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id); ?>" min="0" max="9999999999" title="Must be 10 digits" required>
                </div>
                <div class="mb-3">
                    <label for="nsu_email" class="form-label">NSU email</label>
                    <input type="text" class="form-control" id="nsu_email" name="nsu_email" value="<?php echo htmlspecialchars($nsuEmail); ?>"  disabled>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="registration_date" class="form-label">User since</label>
                    <input type="text" class="form-control" id="registration_date" value="<?php echo htmlspecialchars($registration_date); ?>" disabled>
                </div>
                <button type="submit" name="update_info" class="btn btn-primary">Update profile</button>
            </form>
        </div>

            <div class="container mt-5">
            <h3>Rate the Platform</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="ui_rating" class="form-label">Interface</label>
                    <select class="form-control" id="ui_rating" name="ui_rating" required>
                        <option value="" <?php echo is_null($ui_rating) ? 'selected' : ''; ?>>Not selected</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $ui_rating == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="func_rating" class="form-label">Functionalities</label>
                    <select class="form-control" id="func_rating" name="func_rating" required>
                        <option value="" <?php echo is_null($func_rating) ? 'selected' : ''; ?>>Not selected</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $func_rating == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" name="rate_platform" class="btn btn-primary">Rate</button>
            </form>
        </div>

    <div class="d-flex justify-content-center mt-5 mb-5">
        <button type="button" class="btn btn-warning me-5" data-bs-toggle="modal" data-bs-target="#changeSecurityVariableModal">
            Change Security Key
        </button>
        <button type="button" class="btn btn-warning me-5" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            Change Password
        </button>
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#changeEmailModal">
            Change Email
        </button>
    </div>
    <div class="d-flex justify-content-center mb-5">
    <form action="" method="POST" class="w-50">
        <button type="submit" name="delete_acc" class="btn btn-danger w-100">Delete Account (One-click-operation)</button>
    </form>
</div>
    
 </div>
 <div class="modal fade" id="changeEmailModal" tabindex="-1" aria-labelledby="changeEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changeEmailModalLabel">Change Email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="../auth/verify.php">
           <div class="mb-3">
                    <label for="email" class="form-label">Current Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" disabled>
                    <p class="text-muted small">*A verification code will be sent to this email.</p>
          </div>
          <div class="mb-3">
            <label for="newEmail" class="form-label">New Email (Gmail)</label>
            <input type="email" class="form-control" id="newEmail" name="newEmail" pattern=".+@gmail\.com$" title="Must be your Google format email" maxlength="100" required>
          </div>
          <button type="submit" class="btn btn-primary" name="change_email">Proceed</button>
        </form>
      </div>
    </div>
  </div>
</div>
 <div class="modal fade" id="changeSecurityVariableModal" tabindex="-1" aria-labelledby="changeSecurityVariableModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changeSecurityVariableModalLabel">Change Security Key</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
            <div class="mb-3">
                <label for="new_security_variable" class="form-label">New Security Key</label>
                <input type="text" class="form-control" id="new_security_variable" name="new_security_variable" title="Can be any of your memorable words" maxlength="50" required>
            </div>
            <button type="submit" name="change_security_variable" class="btn btn-primary">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST">
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,18}$" title="Length must be 6 to 18 including letter, number, and special character(@$!%*#?&)" maxlength="18" required>
                
            </div>
            <button type="submit" name="change_password" class="btn btn-primary">Update</button>
        </form>
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