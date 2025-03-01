<!doctype html>
<html lang="en">
    <head>
    <link rel="icon" type="image/webp" href="pcs/icon.webp">
    <meta name="title" content="MyNSU - For NSUers">
    <meta name="description" content="MyNSU is a student-centric platform designed for NSU students to manage academic stats, track grades, rate teachers, and visualize performance insights.">
    <meta name="keywords" content="NSU, North South University, MyNSU, NSU academic management, NSU grade tracking, NSU academic performance insights, NSU teacher rating, NSU student dashboard, NSU academic stats, NSU CGPA calculator, NSU university platform, NSU academic tools, NSU courses manager, NSU faculty review, NSU faculty rating, NSU student portal, NSU cgpa tracker, NSU academic companion">
    <meta name="robots" content="index, nofollow">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="English">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="MyNSU - For NSUers">
    <meta property="og:description" content="MyNSU is a student-centric platform designed for NSU students to manage academic stats, track grades, rate teachers, and visualize performance insights.">
    <meta property="og:url" content="https://www.mynsu.xyz/">
    <meta property="og:type" content="website">
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "MyNSU - For NSUers",
    "url": "https://www.mynsu.xyz/",
    "description": "MyNSU is a student-centric platform designed for NSU students to manage academic stats, track grades, rate teachers, and visualize performance insights."
    }
    </script>
    <title>MyNSU - For NSUers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body>
    <?php

        $maintenance=false;
        //signup login forgot
    ?>
    <script>
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="">
                
                    MyNSU</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <button class="btn btn-primary me-2 mb-3 mb-lg-0" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                        </li>
                        <li class="nav-item">
                        <button class="btn btn-outline-primary mb-3 mb-lg-0" data-bs-toggle="modal" data-bs-target="#signupModal">Sign up</button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <section class="hero bg-primary text-white text-center py-5">
            <div class="container">
                <h1>Welcome to MyNSU</h1>
                <p class="lead fs-3">An academic tool for NSUers</p>

<?php if ($maintenance): ?>
<div class="text-center text-light p-2 rounded" style="background-color: #d32f2f;">
    <span class="fw-bold">MyNSU UNDER MAINTENANCE!</span> Check back later.
</div>
<?php endif; ?>
                <div id="featureCarousel" class="carousel slide mt-4" data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                        <p class="display-4 py-5">Feature packed tool</p>
                        </div>
                        <div class="carousel-item">
                        <p class="display-4 py-5">Course manager</p>
                        </div>
                        <div class="carousel-item">
                        <p class="display-4 py-5">Current semester manager</p>
                        </div>
                        <div class="carousel-item">
                        <p class="display-4 py-5">Routine maker</p>
                        </div>
                        <div class="carousel-item">
                        <p class="display-4 py-5">Teacher ratings</p>
                        </div>
                        <div class="carousel-item">
                        <p class="display-4 py-5">Academic insights</p>
                        </div>
                    </div>
                </div>
<div class="mb-3">
<button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#signupModal">Get started</button>
</div>

            </div>
        </section>

        <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="signupModalLabel">Sign up</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="auth/signup.php" method="post"> <!---->
                            <div class="mb-3">
                                <label for="signupFirstName" class="form-label">First name</label>
                                <input type="text" class="form-control" id="signupFirstName" name="first_name" maxlength="50" required>
                            </div>
                            <div class="mb-3">
                                <label for="signupLastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="signupLastName" name="last_name" maxlength="50" required>
                            </div>
                            <div class="mb-3">
                                <label for="studentId" class="form-label">NSU ID</label>
                                <input type="number" class="form-control" id="studentId" name="student_id" min="0" max="9999999999" title="Must be 10 digits" required>
                            </div>
                            <div class="mb-3">
                                <label for="nsuEmail" class="form-label">NSU email</label>
                                <input type="email" class="form-control" id="nsuEmail" name="nsu_email" pattern=".+@northsouth\.edu$" title="Must be your NSU format email" maxlength="100" required>
                            </div>
                            <div class="mb-3">
                                <label for="signupEmail" class="form-label">Primary email (Gmail)</label>
                                <input type="email" class="form-control" id="signupEmail" name="email" pattern=".+@gmail\.com$" title="Must be your Google format email" maxlength="100" required>
                            </div>
                            <div class="mb-3">
                                <label for="signupPassword" class="form-label">Set password</label>
                                <input type="password" class="form-control" id="signupPassword" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,18}$" title="Length must be 6 to 18 including letter, number, and special character(@$!%*#?&)" maxlength="18" required>
                            </div>
                            <div class="mb-3">
                                <label for="securityVariable" class="form-label">Set security key</label>
                                <input type="text" class="form-control" id="securityVariable" name="security_variable" title="Can be any of your memorable words" maxlength="50" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="signup">Sign up</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Login</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="auth/login.php" method="post"> <!---->
                            <div class="mb-3">
                                <label for="loginEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="loginEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="loginPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="loginPassword" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="login">Login</button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password?</a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Recover account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="auth/recover.php" method="post"> <!---->
                            <p>
                                Select verification method below
                            </p>
                            <div class="form-check">
                                    <input type="radio" class="form-check-input" name="verification_method" id="securityVariableOption" value="security_variable" required>
                                    <label for="securityVariableOption" class="form-check-label">Verify by security key</label>
                            </div>
                            <div class="form-check">
                                    <input type="radio" class="form-check-input" name="verification_method" id="emailCodeOption" value="email_code" required>
                                    <label for="emailCodeOption" class="form-check-label">Verify by code via email</label>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="forgotEmail" class="form-label">Registered email</label>
                                <input type="email" class="form-control" id="forgotEmail" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100" name="forgot">Recover</button>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
        <footer class="bg-light text-black text-center py-4 mt-5">
            <div class="container">
                <p>&copy; MyNSU. All rights reserved.</p>
                <p>
                    <!--url-->
                    <a href="mailto:" class="text-decoration-none">Contact</a>
                    |
                    <a href="" class="text-decoration-none">Facebook</a>
                </p>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    </body>
</html>