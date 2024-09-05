<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Form</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css" />

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
        padding: 20px; /* Padding to account for description spacing */
      }

      .form-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 30px; /* Space between the form and description */
      }

      .main-wrapper {
        border-radius: 10px;
        padding: 45px;
        width: 33%; /* Adjust width for better layout */
        min-height: 400px; /* Adjust height of the form */
        backdrop-filter: blur(8px);
        background-color: #ffffff85;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Optional box shadow for better separation */
      }

      .description-wrapper {
        width: 40%;
        background-color: transparent;
        border-radius: 5px;
        padding: 20px;
        color: #fff;
        font-size: 16px;
        line-height: 1.6;
        margin-top: 10%;
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
        color: #fff;
        line-height: 40px;
        font-weight: 600;
        text-align: left;
        margin: 0px 0 25px;
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
        left: 45%;
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

      /*
      .form-input:valid,
      .form-input:focus,
      .form-textarea:valid,
      .form-textarea:focus {

      } */

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

      .form-textarea {
        resize: vertical; /* Allows the user to resize the textarea vertically */
        min-height: 120px; /* Set a minimum height */
        line-height: 1.5;
      }

      .form-label,
      .form-textarea-label {
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

      .form-textarea-label {
        top: 20%;
        transform: translateY(-75%);
      }

      .form-input:valid ~ .form-label,
      .form-input:focus ~ .form-label,
      .form-textarea:valid ~ .form-textarea-label,
      .form-textarea:focus ~ .form-textarea-label {
        top: 1%;
        transform: translateY(-90%);
        font-size: 13px;
        line-height: 23px;
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

      /* Alert message styling */
      .alert {
        padding: 15px;
        background-color: #4CAF50; /* Green */
        color: white;
        margin-bottom: 15px;
        border-radius: 5px;
        text-align: center;
      }

      .alert.error {
        background-color: #f44336; /* Red */
      }
      #feedback-section {
    position: relative; /* Ensure it's positioned relative */
    z-index: 10; /* Higher value to bring it above other elements */
  }
  /* Modal Styles */
  .modal {
    display: none; /* Hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6); /* Slightly darker background for the modal overlay */
    backdrop-filter: blur(5px); /* Optional: Add a blur effect to the background */
  }
  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 40%; /* Reduced width */
    max-width: 500px; /* Optional: Set a maximum width */
    animation: shake 0.5s;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Optional: Add a shadow for a cool effect */
  }
  @keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    50% { transform: translateX(10px); }
    75% { transform: translateX(-10px); }
    100% { transform: translateX(0); }
  }
  .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }
  .close:hover,
  .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
  .modal-buttons {
    text-align: center;
    margin-top: 20px;
  }
  .modal-buttons button {
    background-color: #4CAF50; /* Green background */
    border: none;
    color: white;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 5px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s ease;
  }
  .modal-buttons button.cancel {
    background-color: #f44336; /* Red background for cancel */
  }
  .modal-buttons button:hover {
    background-color: #45a049; /* Darker green */
  }
  .modal-buttons button.cancel:hover {
    background-color: #c62828; /* Darker red for cancel */
  }
    </style>
  </head>
  <body>
    <div class="form-main">
      <div class="form-container">
        <div class="main-wrapper">
          <h2 class="form-head">Contact Form</h2>

          <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] == 'success'): ?>
              <div class="alert">Thank you for contacting us. We will get back to you soon!</div>
            <?php elseif ($_GET['status'] == 'error'): ?>
              <div class="alert error">There was an error submitting your query. Please try again later.</div>
            <?php endif; ?>
          <?php endif; ?>

          <form class="form-wrapper" action="contactfn.php" method="POST">
            <div class="form-card">
              <input
                class="form-input"
                type="text"
                name="full_name"
                required="required"
              />
              <label class="form-label" for="full_name">Full Name</label>
            </div>

            <div class="form-card">
              <input
                class="form-input"
                type="email"
                name="email"
                required="required"
              />
              <label class="form-label" for="email">Email Address</label>
            </div>

            <div class="form-card">
              <input
                class="form-input"
                type="tel"
                name="phone_number"
                required="required"
              />
              <label class="form-label" for="phone_number">Phone Number</label>
            </div>

            <div class="form-card">
              <textarea
                class="form-textarea"
                name="message"
                required="required"
              ></textarea>
              <label class="form-textarea-label" for="message">Your Message</label>
            </div>

            <div class="btn-wrap">
              <button type="submit">Send Message</button>
            </div>
          </form>
        </div>

        <div class="description-wrapper">
          <h3>Get in Touch with Us</h3>
          <p>
            Feel free to reach out to us with any questions, suggestions, or
            feedback. We are here to help you and ensure your experience with
            our service is as smooth as possible.
          </p>
          <p>
            Whether you're looking to rent a bike or scooter, have queries
            about our services, or simply want to share your thoughts, our team
            is ready to assist you.
          </p>
        </div>
      </div>
    </div>

    <script>
      // Automatically hide alert messages after 3 seconds
      setTimeout(function() {
        const alert = document.querySelector('.alert');
        if (alert) {
          alert.style.display = 'none';
        }
      }, 3000);
    </script>
    <?php include '../includes/footer.php'; ?>
  </body>
</html>
