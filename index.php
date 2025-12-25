<?php session_start(); ?>
<!doctype html>
<html lang="id">
<head>
<title>Login SIRS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
<div class="col-md-4 mx-auto">

<div class="card shadow p-4">

    <!-- LOGO -->
    <div class="text-center mb-3">
        <img src="assets/img/logo_rs.jpg"
             class="img-fluid"
             style="max-height:200px"
             alt="Logo Rumah Sakit">
    </div>

    <h4 class="text-center mb-3">Selamat Datang!</h4>

    <form action="auth/login.php" method="POST">
        <input type="text" name="username" class="form-control mb-2"
               placeholder="Username" required>

        <input type="password" name="password" class="form-control mb-3"
               placeholder="Password" required>

        <button class="btn btn-primary w-100">
            Login
        </button>
    </form>

</div>

</div>
</div>

</body>
</html>
