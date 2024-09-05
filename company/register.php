<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vendor Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
      body {
        background-image: url("../images/scoo12.jpg");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Arial', sans-serif;
      }

      .container {
        margin-left: -1%;
      }

      .card {
        border-radius: 1rem;
        background-color: rgba(255, 255, 255, 0.5); /* Transparent white background */
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

      .form-outline input {
        border-radius: 0.5rem;
        border: 1px solid #ddd;
      }

      .form-outline label {
        font-weight: 500;
      }

      .password-container {
        position: relative;
      }

      .password-container input {
        width: calc(100% - 40px);
      }

      .password-container .toggle-visibility {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #555;
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
                <h3 class="mb-5">Vendor Registration</h3>

                <form action="regvendor.php" method="post">
                  <div class="form-outline mb-4">
                    <input type="text" id="company_name" name="company_name" class="form-control form-control-lg" required />
                    <label class="form-label" for="company_name">Company Name</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="email" id="email" name="email" class="form-control form-control-lg" required />
                    <label class="form-label" for="email">Email</label>
                  </div>

                  <div class="form-outline mb-4">
                    <input type="number" id="phone" name="phone" class="form-control form-control-lg" pattern="[0-9]{10}" maxlength="10" required />
                    <label class="form-label" for="phone">Phone</label>
                  </div>

                  <div class="form-outline mb-4 password-container">
                    <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                    <label class="form-label" for="password">Password</label>
                    <i class="fas fa-eye toggle-visibility" id="togglePassword"></i>
                  </div>

                  <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
                </form>

                <hr class="my-4">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.js"></script>
    <script>
      // Toggle password visibility
      document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
      });
    </script>
</body>
</html>
