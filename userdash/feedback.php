<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins&display=swap">

    <!-- FontAwesome for Stars and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Bootstrap CSS for Modal -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            display: grid;
            height: 100%;
            place-items: center;
            text-align: center;
            background: #000;
            height: 100vh;
            background-image: url(https://ibb.co/vHTWnbX);
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            opacity: 1;
        }
        .star-widget input {
            display: none;
        }
        .star-widget label {
            font-size: 35px;
            color: #444;
            padding: 10px;
            float: right;
            transition: all .2s ease;
        }
        input:not(:checked) ~ label:hover,
        input:not(:checked) ~ label:hover ~ label {
            color: #fd4;
        }

        input:checked ~ label {
            color: #fd4;
        }

        #rate-1:checked ~ .rating-desc:before {
            content: "Poor😋";
        }
        #rate-2:checked ~ .rating-desc:before {
            content: "Not bad";
        }
        #rate-3:checked ~ .rating-desc:before {
            content: "Average";
        }
        #rate-4:checked ~ .rating-desc:before {
            content: "Good😋";
        }
        #rate-5:checked ~ .rating-desc:before {
            content: "Excellent😋";
        }

        .rating-desc {
            width: 100%;
            font-size: 20px;
            font-weight: 600;
            margin: 5px 0 20px 0;
            text-align: center;
            transition: all .2s ease;
        }
        .textarea textarea {
            border: 1px solid #e4e5e7;
            background: white;
            color: #6C6C6E;
            padding: 22px;
            font-size: 16px;
            margin-top: 15px;
            letter-spacing: -0.011em;
            border-radius: 10px;
            resize: none;
        }
        .textarea textarea:focus {
            border-color: #36bb91 !important;
            background: white;
            color: #1a1a1a;
            outline: none;
        }
        .btn {
            height: 45px;
            width: 100%;
            margin: 15px 0;
        }
        .btn button {
            height: 100%;
            width: 60%;
            outline: none;
            background: #36bb91;
            color: #fff;
            font-size: 17px;
            font-weight: 600;
            border-radius: 15px;
            cursor: pointer;
            border: none;
        }

        .btn button:hover {
            background: #1a5e49;
        }
        .star-rating-bx {
            background-color: #fff;
            box-shadow: 0px 4px 40px 0px rgb(0 0 0 / 5%);
            border-radius: 10px;
            padding: 40px;
        }
        @media (max-width:576px) {
            .star-rating-bx {
                padding:20px 15px;
            }
        }

        .modal-title {
            font-weight: 600;
        }
        #error-comment,
        #error-rating {
            color: red;
        }

        .alert-icon {
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .alert {
            display: none;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            position: relative;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<section class="section">
    <div class="star-rating-bx">
        <h2 class="text-center">Feedback</h2>
        <div class="star-widget">
        <?php
        
        $current_url = "/bike_scooters/userdash/";  
        ?>
            <form method="POST" name="feedback" action="feedbackfn.php" onsubmit="return feedBack();">
                <input type="radio" name="rate" id="rate-5" value="5">
                <label for="rate-5" class="fas fa-star"></label>
                <input type="radio" name="rate" id="rate-4" value="4">
                <label for="rate-4" class="fas fa-star"></label>
                <input type="radio" name="rate" id="rate-3" value="3">
                <label for="rate-3" class="fas fa-star"></label>
                <input type="radio" name="rate" id="rate-2" value="2">
                <label for="rate-2" class="fas fa-star"></label>
                <input type="radio" name="rate" id="rate-1" value="1">
                <label for="rate-1" class="fas fa-star"></label>
                <p id="error-rating"></p>
                <p class="rating-desc"></p>
                
                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($current_url, ENT_QUOTES, 'UTF-8'); ?>">
    
                <div class="textarea">
                    <textarea cols="30" id="comment" name="feedbackText" placeholder="Describe your comment"></textarea>
                    <p id="error-comment"></p>
                </div>
                <div class="btn">
                    <button type="submit">Submit</button>
                </div>
            </form>

            <div id="success-message" class="alert alert-success">
                <span class="alert-icon"><i class="fas fa-check-circle"></i></span> 
                Feedback submitted successfully!
            </div>
            <div id="error-message" class="alert alert-error">
                <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                Please fill out the form completely.
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap JS for Modal functionality -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script>
    function feedBack() {
        var comment = document.getElementById('comment').value;
        var rating = document.querySelector("input[name=rate]:checked");

        // Reset error messages
        document.getElementById('error-comment').innerHTML = "";
        document.getElementById('error-rating').innerHTML = "";

        if (comment === '') {
            document.getElementById('error-comment').innerHTML = "* Please enter a comment";
            showErrorAlert();
            return false; 
        }

        if (!rating) {
            document.getElementById('error-rating').innerHTML = "* Please choose a star rating";
            showErrorAlert();
            return false; 
        }

        showSuccessAlert();
        return true;  // Allow form submission
    }

    function showSuccessAlert() {
        var successMessage = document.getElementById('success-message');
        successMessage.style.display = 'block';
        setTimeout(function() {
            successMessage.style.display = 'none';
        }, 3000);  // Alert disappears after 3 seconds
    }

    function showErrorAlert() {
        var errorMessage = document.getElementById('error-message');
        errorMessage.style.display = 'block';
        setTimeout(function() {
            errorMessage.style.display = 'none';
        }, 3000);  // Alert disappears after 3 seconds
    }
</script>

</body>
</html>
