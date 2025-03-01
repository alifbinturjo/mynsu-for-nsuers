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

                $stmt_st=$conn->prepare("select contact,registration_date from admins where user_id=?");
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
                $contact=$user['contact'];
                $registration_date=$user['registration_date'];
                $stmt_st->close();

    
            }
            else{
                session_destroy();
                $conn->close();
                echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
                header("refresh:1; url=../index.php");
                exit;
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
                
                $new_contact = $_POST['contact'];   
                
                $stmt_update_student = $conn->prepare("UPDATE admins SET contact=? WHERE user_id=?");
                $stmt_update_student->bind_param("si", $new_contact, $user_id);  
                
            
                
                try{
                    
                    $stmt_update_student->execute();
                    
                    echo '<div class="alert alert-success">Profile updated successfully.</div>';
                }
                catch(Exception $e){
                    
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                
                $stmt_update_student->close();
                
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
<h1 class="text-center mb-4">Profile</h1>
<p class="text-center">Manage profile informations</p>
            <form method="POST">
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="number" class="form-control" id="contact" min="0" max="99999999999" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="registration_date" class="form-label">Admin since</label>
                    <input type="text" class="form-control" id="registration_date" value="<?php echo htmlspecialchars($registration_date); ?>" disabled>
                </div>
                
                <button type="submit" class="btn btn-primary" name="update_info">Update</button>
            </form>
 </div>

 <?php
$conn->close();
 ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>