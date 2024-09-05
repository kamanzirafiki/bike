<?php include '../userdash/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Arial", sans-serif;
        }

        .form-main {
            background: linear-gradient(to bottom, #00000024, #00000024),
                url(../images/bike2.jpg) no-repeat center;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 30px;
        }

        .main-wrapper {
            border-radius: 10px;
            padding: 45px;
            width: 33%;
            min-height: 400px;
            backdrop-filter: blur(8px);
            background-color: #ffffff85;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .description-wrapper {
            width: 40%;
            background-color: transparent;
            border-radius: 5px;
            padding: 20px;
            color: #fff;
            font-size: 16px;
            line-height: 1.6;
            box-shadow: none;
        }

        @media screen and (max-width: 991px) {
            .main-wrapper,
            .description-wrapper {
                width: 100%;
            }
        }

        @media screen and (max-width: 767px) {
            .form-container {
                flex-direction: column;
                gap: 20px;
            }
        }

        .form-head {
            font-size: 30px;
            line-height: 40px;
            font-weight: 600;
            text-align: left;
            margin: 0px 0 50px;
            color: #fff;
            position: relative;
            padding-bottom: 15px;
        }

        .form-head::after {
            content: "";
            display: block;
            width: 90%;
            height: 4px;
            background: #fff;
            position: absolute;
            left: 43%;
            bottom: 0;
            transform: translateX(-50%);
        }

        .form-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 15px;
        }

        .form-card {
            position: relative;
            width: 100%;
        }

        .form-input {
            padding: 20px 25px;
            width: 100%;
            border: 1px solid #fff;
            border-radius: 5px;
            background: transparent;
            outline: none;
            font-size: 20px;
            line-height: 30px;
            font-weight: 400;
            box-sizing: border-box;
            color: #fff;
            position: relative;
        }

        .form-input:-webkit-autofill,
        .form-input:-webkit-autofill:hover,
        .form-input:-webkit-autofill:focus,
        .form-input:-webkit-autofill:active {
            transition: background-color 9999s ease-in-out 0s;
        }

        .form-input::-webkit-outer-spin-button,
        .form-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .form-label {
            position: absolute;
            left: 25px;
            top: 25px;
            pointer-events: none;
            transition: 0.3s;
            font-size: 15px;
            line-height: 28px;
            font-weight: 500;
            color: #fff;
            background: transparent;
            padding: 0 5px;
        }

        .form-input:focus ~ .form-label,
        .form-input:not(:placeholder-shown) ~ .form-label {
            top: -20px;
            left: 20px;
            font-size: 13px;
            line-height: 23px;
            color: #fff;
        }

        .btn-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px 0 0;
        }

        .btn-wrap button {
            padding: 0 32px;
            font-size: 18px;
            line-height: 48px;
            border: 1px solid transparent;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.5s ease;
            background-color:#A4ABA6;
            cursor: pointer;
            box-shadow: 0 0 5px 5px #00000020;
            color: #fff;
        }

        .btn-wrap button:hover {
            border: 1px solid #000;
            background: transparent;
        }

        .account-prompt {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

        .account-prompt a {
            color: #fff; 
            text-decoration: none;
            font-weight: 600;
        }

        .account-prompt a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
            font-size: 16px;
        }

        .forgot-password a {
            color: #fff; 
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #fff;
            font-size: 20px;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #ccc;
        }

        .alert {
            background-color: #f44336; /* Red background for error */
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Set width to fit within the form */
        }

        .alert-success {
            background-color: #4caf50;
        }

        .alert-error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <div class="form-main">
        <div class="form-container">
            <div class="main-wrapper">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; ?>
                        <script>
                            setTimeout(function() {
                                document.querySelector('.alert').style.display = 'none';
                            }, 3000); 
                        </script>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <h2 class="form-head">LOGIN</h2>

                <form class="form-wrapper" method="post" action="fn.php">
                    <div class="form-card">
                        <input class="form-input" type="email" name="email" id="email" placeholder=" " required="required" />
                        <label class="form-label" for="email">Email</label>
                    </div>

                    <div class="form-card">
                        <div class="form-group">
                            <input class="form-input" type="password" name="password" id="password" placeholder=" " required="required" />
                            <label class="form-label" for="password">Password</label>
                            <i id="password-toggle" class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                        </div>
                    </div>

                    <div class="btn-wrap">
                        <button type="submit">Login</button>
                    </div>

                    <div class="forgot-password">
                        <p><a href="forgot-password.php">Forgot Password?</a></p>
                    </div>

                    <div class="account-prompt">
                        <p>Don't have an account? <a href="../register/">Sign up here</a></p>
                    </div>
                </form>
            </div>

            <div class="description-wrapper">
                <p>
                    Welcome back! Please log in to access your account. If you don’t have an account yet, you can create one by clicking the sign-up link. 
                </p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            var password = document.getElementById("password");
            var icon = document.getElementById("password-toggle");
            if (password.type === "password") {
                password.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                password.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
    <?php include '../includes/footer.php' ?>
</body>
</html>
