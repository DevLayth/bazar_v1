<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset Failed</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
    <div class="text-center">
      <div class="alert alert-danger" role="alert">
        <h4 class="alert-heading">Password Reset Failed</h4>
        <p>There was a problem resetting your password. Please try again or request another password reset link.</p>
        <hr>
        {{-- <p class="mb-0">
          If you continue to experience issues, please <a href="/contact" class="btn btn-link">contact support</a>.
        </p> --}}
        {{-- <p class="mt-3">
          <a href="/password/reset" class="btn btn-primary">Request Another Reset Link</a>
        </p> --}}
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
