<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
      body {
        background-image: url("../images/scoo12.jpg");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
      }

      .container {
        margin-left: -1%;
      }

      .card {
        border-radius: 1rem;
        background-color: rgba(255, 255, 255, 0.5); /* More transparent white background */
      }

      .card-body {
        padding: 5rem;
        text-align: center;
      }

      .btn-primary {
        background-color: #508bfc;
        border-color: #508bfc;
      }

      .btn-primary:hover {
        background-color: #4178d3;
        border-color: #4178d3;
      }

      .btn-google {
        background-color: #dd4b39;
      }

      .btn-google:hover {
        background-color: #c1351d;
      }

      .btn-facebook {
        background-color: #3b5998;
      }

      .btn-facebook:hover {
        background-color: #2d4373;
      }

      .alert {
        margin-top: 15px;
        padding: 10px; /* Reduced padding */
        font-size: 0.9rem; /* Reduced font size */
        border-radius: 0.5rem; /* Adjust border radius if needed */
      }

      .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
      }
    </style>
</head>
<body>
    <section class="vh-100">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-2-strong">
              <div class="card-body">
                <h3 class="mb-5">Login</h3>

                <!-- Display session message -->
                <?php
                session_start();
                if (isset($_SESSION['message']) && $_SESSION['msg_type'] === 'danger') {
                    echo '<div class="alert alert-danger">' . $_SESSION['message'] . '</div>';
                    unset($_SESSION['message']); // Clear message after display
                }
                ?>

                <form action="logvendor.php" method="post">
                  <div class="form-outline mb-4">
                    <input type="email" id="typeEmailX-2" name="email" class="form-control form-control-lg" required />
                    <label class="form-label" for="typeEmailX-2">Email</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" id="typePasswordX-2" name="password" class="form-control form-control-lg" required />
                    <label class="form-label" for="typePasswordX-2">Password</label>
                  </div>

                  <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>

                  <hr class="my-4">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
</body>
</html>
