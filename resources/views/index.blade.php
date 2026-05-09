<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: url('BCpic.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
        }

        /* Navbar Styles */
        .navbar {
            padding: 0.5rem 1rem;
        }

        .navbar-brand {
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar img {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .navbar .logo-inventory {
            height: 40px;
            width: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .navbar .logo-bc {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .navbar-toggler {
            border: 2px solid rgba(255,255,255,0.5);
            padding: 0.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        }

        .navbar-toggler-icon {
            width: 1.5em;
            height: 1.5em;
        }

        /* Mobile menu */
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: rgba(33, 37, 41, 0.95);
                padding: 1rem;
                margin-top: 0.5rem;
                border-radius: 8px;
            }

            .navbar-nav {
                gap: 0.5rem !important;
            }

            .nav-item {
                text-align: center;
                padding: 0.5rem 0;
            }
        }

        .welcome-container {
            position: relative;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.8);
            animation: fadeIn 1.5s ease-in-out;
            max-width: 90%;
            margin: 1rem;
        }

        .welcome-text {
            font-size: clamp(1.5rem, 5vw, 3rem);
            font-weight: bold;
            margin: 0 0 1rem 0;
            animation: textSlideIn 2s ease forwards;
        }

        .welcome-container p {
            font-size: clamp(0.9rem, 2.5vw, 1.2rem);
            margin-bottom: 1.5rem;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: clamp(1rem, 2vw, 1.2rem);
            border-radius: 8px;
            animation: fadeIn 1.1s ease-in-out forwards;
        }

        #loginModal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            padding: 1rem;
            overflow-y: auto;
        }

        .login-container {
            background-color: #ffff;
            color: black;
            padding: 2rem;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            margin: auto;
        }

        .login-container h1 {
            font-size: clamp(1.8rem, 5vw, 2.5rem);
            margin-bottom: 20px;
            color: #1877F2;
        }

        .login-container .form-control {
            font-size: 1rem;
            padding: 0.75rem;
        }

        .btn-login {
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 1.2rem;
            background-color: #1877F2;
            width: 100%;
            margin-top: 12px;
            border: none;
            color: white;
        }

        .btn-login:hover {
            background-color: #1565c0;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes textSlideIn {
            0% {
                transform: translateY(-50px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 0.5em;
            min-height: 20px;
        }

        /* Mobile specific adjustments */
        @media (max-width: 576px) {
            .welcome-container {
                padding: 1.5rem;
            }

            .login-container {
                padding: 1.5rem;
                max-width: 95%;
            }

            .navbar img {
                height: 35px;
                width: 35px;
            }

            .navbar .logo-inventory {
                height: 35px;
                width: 35px;
            }

            .navbar .logo-bc {
                height: 35px;
                width: 35px;
            }

            .navbar-brand {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <img src="bc.png" alt="BC Logo" class="logo-inventory"/>
            <a class="navbar-brand" href="#">Inventory System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('index') }}">Home</a>
                    </li> 
                    <li class="nav-item">
                        <a class="nav-link" href="contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="login-btn">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="welcome-container">
        <h1 class="welcome-text">Welcome to the Inventory System</h1>
        <p>Manage your inventory efficiently and effortlessly</p>
        <button class="btn btn-light btn-lg" id="dashboard-btn">Go to Dashboard</button>
    </div>

    <div id="loginModal">
        <div class="login-container">
            <h1>Login</h1>
            <form id="loginForm" action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
        <div id="username-error" class="error-message"></div>
    </div>

    <div class="mb-3">
        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        <div id="password-error" class="error-message"></div>
    </div>

    <button type="submit" class="btn btn-dark btn-login">Login</button>
</form>

<div class="mt-3">
            <a class="text-decoration-none" style="color: #1877F2;">Forgot Password?</a>
        </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
     var modal = document.getElementById("loginModal");
        var dashboardBtn = document.getElementById("dashboard-btn");
        var loginBtn = document.getElementById("login-btn");

        // Open modal when clicking dashboard or login button
        dashboardBtn.onclick = loginBtn.onclick = function(e) {
            e.preventDefault();
            modal.style.display = "flex";
            // Close mobile menu if open
            var navbarCollapse = document.getElementById('navbarNav');
            if (navbarCollapse.classList.contains('show')) {
                var bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                    toggle: true
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Login form submission
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                $('#username-error').text('');
                $('#password-error').text('');

                $.ajax({
                    url: "{{ route('login') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.errors) {
                            let errors = response.responseJSON.errors;

                            if (errors.username) {
                                $('#username-error').text(errors.username[0]);
                            }
                            if (errors.password) {
                                $('#password-error').text(errors.password[0]);
                            }
                        } else {
                            $('#username-error').text('Login failed. Please try again.');
                        }
                    }
                });
            });
        });
</script>
</body>
</html>
