<?php 
include '../includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up Form</title>
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
            min-height: 500px;
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

        .form-card-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-input,
        .form-textarea {
            padding: 20px 25px 15px;
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
        }

        .form-label {
            position: absolute;
            left: 25px;
            top: 60%;
            transform: translateY(-90%);
            pointer-events: none;
            transition: 0.3s;
            margin: 0;
            font-size: 14px; 
            line-height: 20px;
            font-weight: 500;
            color: #fff;
        }

        .form-input:valid ~ .form-label,
        .form-input:focus ~ .form-label {
            top: 1%;
            transform: translateY(-90%);
            font-size: 10px;
            line-height: 16px;
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
            background-color: #A4ABA6;
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

        @media screen and (max-width: 767px) {
            .form-card-group {
                grid-template-columns: 1fr; 
                gap: 20px;
            }
        }

        .alert {
    padding: 5px;
    margin-bottom: 30px;
    border-radius: 5px;
    font-size: 16px;
    line-height: 1.4;
    width: 100%;
    text-align: center;
    display: flex;
    align-items: center;
    gap: 10px;
    height: auto; 
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-icon {
    font-size: 18px; 
    color: inherit;
}


        .password-container {
            position: relative;
        }

        .fa-eye, .fa-eye-slash {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="form-main">
        <div class="form-container">
            <div class="main-wrapper">
                <h2 class="form-head">REGISTER</h2>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?>">
                        <i class="fas fa-<?php echo $_SESSION['msg_type'] === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> alert-icon"></i>
                        <?php 
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                            unset($_SESSION['msg_type']);
                        ?>
                    </div>
                <?php endif; ?>

                <form class="form-wrapper" method="post" action="fn.php" id="signupForm">
                    <div class="form-card">
                        <input
                            class="form-input"
                            type="text"
                            name="full_name"
                            id="full_name"
                            required="required"
                        />
                        <label class="form-label" for="full_name">Full Name</label>
                    </div>

                    <div class="form-card">
                        <input
                            class="form-input"
                            type="email"
                            name="email"
                            id="email"
                            required="required"
                        />
                        <label class="form-label" for="email">Email</label>
                    </div>

                    <div class="form-card">
                        <input
                            class="form-input"
                            type="text"
                            name="phone_number"
                            id="phone_number"
                            pattern="\d{10,}"
                            title="Phone number must be at least 10 digits"
                            required="required"
                        />
                        <label class="form-label" for="phone_number">Phone Number</label>
                    </div>

                    <div class="form-card-group">
                        <div class="form-card password-container">
                            <input
                                class="form-input"
                                type="password"
                                name="password"
                                id="password"
                                required="required"
                            />
                            <label class="form-label" for="password">Password</label>
                            <i class="fas fa-eye" id="togglePassword"></i>
                        </div>

                        <div class="form-card password-container">
                            <input
                                class="form-input"
                                type="password"
                                name="confirm_password"
                                id="confirm_password"
                                required="required"
                            />
                            <label class="form-label" for="confirm_password">Confirm Password</label>
                            <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                        </div>
                    </div>

                    <div class="btn-wrap">
                        <button type="submit">Sign Up</button>
                    </div>

                    <div class="account-prompt">
                        <p>Already have an account? <a href="../Auth/login.php">Login here</a></p>
                    </div>
                </form>
            </div>

            <div class="description-wrapper">
                <p>
                    Welcome! Please fill out the form to create an account. If you already have an account, you can log in by clicking the login link below. We’re excited to have you join us!
                </p>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script>
        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordField = document.getElementById('confirm_password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.classList.toggle('fa-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPasswordField.type === 'password' ? 'text' : 'password';
            confirmPasswordField.type = type;
            this.classList.toggle('fa-eye-slash');
        });

        // Validate Passwords Match
        document.getElementById('signupForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
            }
        });

        // Display popup message on successful registration
        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_SESSION['popup_message'])): ?>
                alert("<?php echo $_SESSION['popup_message']; ?>");
                <?php unset($_SESSION['popup_message']); ?>
                setTimeout(function() {
                    window.location.href = "../Auth/login.php";
                }, 1000); // Delay redirect for 1 second to allow alert to be seen
            <?php endif; ?>
        });
    </script>
</body>
</html>
