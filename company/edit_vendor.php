<?php
// Include your database connection and header
include '../db_connection.php';
include 'header.php'; // Include the header file

// Start session to access user information
session_start();

// Check if vendor is logged in (adjust according to your authentication logic)
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$vendor = null;

try {
    // Fetch the vendor information based on the session vendor_id
    $stmt = $pdo->prepare("SELECT vendor_id, company_name, email, phone FROM vendors WHERE vendor_id = :vendor_id");
    $stmt->execute(['vendor_id' => $vendor_id]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Validate input
    if (empty($company_name) || empty($email) || empty($phone)) {
        $error = "All fields are required.";
    } else {
        // Check if any changes were made
        if ($company_name === $vendor['company_name'] && $email === $vendor['email'] && $phone === $vendor['phone']) {
            $error = "No changes made to the information.";
        } else {
            // Update vendor information
            try {
                $stmt = $pdo->prepare("UPDATE vendors SET company_name = :company_name, email = :email, phone = :phone, updated_at = NOW() WHERE vendor_id = :vendor_id");
                $stmt->execute([
                    'company_name' => $company_name,
                    'email' => $email,
                    'phone' => $phone,
                    'vendor_id' => $vendor_id,
                ]);
                $success = "Information updated successfully.";
                // Refresh the vendor info in the session or page
                $vendor['company_name'] = $company_name;
                $vendor['email'] = $email;
                $vendor['phone'] = $phone;
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vendor Information</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Barlow', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
        }

        .form-control {
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        .alert {
            margin-top: 20px;
            width: 50%; 
            margin-left: 25%;
        }

        /* Sidebar and Header Styles */
        .sidebar {
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            overflow-y: auto;
            transform: translateX(0);
            transition: transform 0.4s ease-out;
            background-color: #343a40;
        }

        .sidebar .list-group-item {
            border-radius: 0;
            border: 1px solid #495057;
            background-color: #343a40;
            color: #f8f9fa;
            transition: background-color 0.3s, border-color 0.3s, color 0.3s;
        }

        .sidebar .list-group-item:hover {
            background-color: #495057 !important;
            border-color: #6c757d;
        }

        .sidebar .list-group-item.active {
            background-color: #007bff !important;
            border-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="row no-gutters">
        <?php include 'header.php'?>
        <?php include 'sidebar.php' ?>

        <!-- Main Content -->
        <div class="col-md-9 offset-md-3 col-lg-10 offset-lg-2">
            <div class="container">
                <h2>Company Information</h2>

                <!-- Display error message if any -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Display success message if any -->
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($vendor): ?>
                    <!-- Move the alert display before the form -->
                    <form method="POST" class="mx-auto" style="width: 50%;">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="company_name">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($vendor['company_name']); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($vendor['email']); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($vendor['phone']); ?>" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Information</button>
                    </form>
                <?php else: ?>
                    <p>Vendor information not found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add auto-dismiss and close button functionality -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>
</html>
