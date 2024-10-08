<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
      body {
        background-image: url("../images/scoo12.jpg");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        font-family: "Arial", sans-serif;
      }

      .container {
        margin-left: -1%;
      }

      .card {
        border-radius: 1rem;
        background-color: rgba(255, 255, 255, 0.5); 
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
    </style>
</head>
<body>
    <section class="vh-100">
      <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-12 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-2-strong">
              <div class="card-body">

                <h3 class="mb-5">Admin Login</h3>
                <form action="admin_login.php" method="POST">
                  <div class="form-outline mb-4">
                    <input type="text" id="username" name="username" class="form-control form-control-lg" required />
                    <label class="form-label" for="username">Username</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                    <label class="form-label" for="password">Password</label>
                  </div>

                  <button class="btn btn-primary btn-lg btn-block" type="submit">Login</button>
                </form>
                <hr class="my-4">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
</body>
</html>
