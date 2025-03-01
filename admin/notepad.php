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
        $rl='admin';
        
        $stmt_note=$conn->prepare("SELECT notes FROM drafts WHERE user_id=? and role=?");
        $stmt_note->bind_param("is", $user_id,$rl);
        
        try{
            $stmt_note->execute();
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
            $stmt_note->close();
            $conn->close();
            header("refresh:1; url=dashbaord.php");
            exit;
        }
        $note_result = $stmt_note->get_result();
        $note = $note_result->fetch_assoc();
        $current_note = $note['notes']??''; 
        
        $stmt_note->close();
    }
    else{
        session_destroy();
        $conn->close();
        echo '<div class="alert alert-danger">You are not logged in. Redirecting...</div>';
        header("refresh:1; url=../index.php");
        exit;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_note'])) {
        $updated_note = $_POST['note'];

        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM drafts WHERE user_id = ? and role=?");
        $stmt_check->bind_param("is", $user_id,$rl);
        
        try{
            $stmt_check->execute();
            $stmt_check->bind_result($exists);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($exists > 0) {
            $stmt_update = $conn->prepare("UPDATE drafts SET notes=? WHERE user_id=? and role=?");
            $stmt_update->bind_param("sis", $updated_note, $user_id,$rl);
        } else {

            $stmt_update = $conn->prepare("INSERT INTO drafts (user_id, notes,role) VALUES (?, ?,?)");
            $stmt_update->bind_param("iss", $user_id, $updated_note,$rl);
        }
        
        try{
            $stmt_update->execute();
            echo '<div class="alert alert-success">Note saved successfully.</div>';
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }

        $stmt_update->close();
        header("refresh:1");
        exit;
        }
        catch(Exception $e){
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            $stmt_check->close();
            header("refresh:1");
            exit;
        }
        
    }

    $conn->close();
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
            <h1 class="text-center mb-4">Notepad</h1>
            <p class="text-center">Space to write and store short note and to keep track of thoughts, ideas, and reminders here for easy reference</p>
            
            <form action="notepad.php" method="post">
                <div class="mb-3">
                    <label for="note" class="form-label">Note</label>
                    <textarea class="form-control" id="note" name="note" rows="10" maxlength="500"><?php echo htmlspecialchars($current_note); ?></textarea>
                    <small class="form-text text-muted">Maximum 500 characters.</small>
                </div>
                <button type="submit" name="save_note" class="btn btn-primary">Save</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>