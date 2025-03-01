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
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact']);
        $level = trim($_POST['level']);
    
        
            
            $query = "SELECT user_id FROM users WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            
            try{
                $stmt->execute();
            }
            catch(Exception $e){
                $stmt->close();
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            
            header("refresh:1");
            exit;
            }
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                
                $row = $result->fetch_assoc();
                $new_user_id = $row['user_id'];

            $query = "SELECT user_id FROM admins WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $new_user_id);
            
            try{
                $stmt->execute();
            }
            catch(Exception $e){
                $stmt->close();
            echo '<div class="alert alert-danger">Something went wrong.</div>';
            
            header("refresh:1");
            exit;
            }
            $resultu = $stmt->get_result();
            if ($resultu->num_rows == 0) {

                $insert_query = "INSERT INTO admins (user_id, contact, level) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("isi", $new_user_id, $contact, $level);
                try{
                    $insert_stmt->execute();
                    echo '<div class="alert alert-success">Admin added successfully.</div>';
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong.</div>';
                }
                $stmt->close();
                $insert_stmt->close();
                header("refresh:1");
            exit;
            }
            else{
                echo '<div class="alert alert-danger">Admin already exist.</div>';
            }
        }
             else {
                echo '<div class="alert alert-danger">User with this email does not exist.</div>';
            }
    
            $stmt->close();
            header("refresh:1");
            exit;
        
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        $delete_id = intval($_POST['delete_id']); 
    
        try {
            
            $delete_query = "DELETE FROM admins WHERE user_id = ?";
            $delete_stmt = $conn->prepare($delete_query);
            $delete_stmt->bind_param("i", $delete_id);
            $delete_stmt->execute();
    
            echo '<div class="alert alert-success">Admin deleted successfully.</div>';
            
        } catch (Exception $e) { 
            echo '<div class="alert alert-danger">Something went wrong.</div>';
        }
        $delete_stmt->close(); 
        header("refresh:1"); 
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
            <h1 class="text-center mb-4">Admins</h1>
            <p class="text-center">Manage admins</p>
            
            <div class="mb-5 border p-3 rounded">
        <h4 class="text">Add New Admin</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" pattern=".+@gmail\.com$" title="Must be Google format email" maxlength="100" required>
            </div>
            <div class="mb-3">
                <label for="contact" class="form-label">Contact</label>
                <input type="number" min="0" max="99999999999" class="form-control" id="contact" name="contact" required>
            </div>
            <div class="mb-3">
                <label for="level" class="form-label">Level</label>
                <input type="number" min="1" max="10" class="form-control" id="level" name="level" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary" name="add">Add</button>
            </div>
        </form>
    </div>
    <div class="mb-5 border p-3 rounded">
        <h4 class="text">Admin List</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">First name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Level</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $query = "SELECT u.first_name, u.email, a.contact, a.level, a.user_id 
                          FROM admins a 
                          JOIN users u ON a.user_id = u.user_id 
                          WHERE a.user_id != ? && a.level!=0"; 
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                
                try{
                    $stmt->execute();
                }
                catch(Exception $e){
                    echo '<div class="alert alert-danger">Something went wrong. Redirecting...</div>';
                    $stmt->close();
                    $conn->close();
                    header("refresh:1; url=dashbaord.php");
                    exit;
                }
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['first_name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['contact']}</td>
                                <td>{$row['level']}</td>
                                <td>
                        <form method='POST' action=''>
                            <input type='hidden' name='delete_id' value='{$row['user_id']}'>
                            <button type='submit' class='btn btn-danger btn-sm' name='delete'>Delete</button>
                        </form>
                    </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No admins found.</td></tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
        </div>
        <?php
            $conn->close();
        ?>

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>