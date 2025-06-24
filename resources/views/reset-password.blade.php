<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(90deg, rgb(255, 255, 255) 0%, rgb(255, 255, 255) 50%);
      font-family: "Roboto", sans-serif;
    }
    .reset-page {
      width: 100%;
      padding: 8% 0 0;
      margin: auto;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .form-container {
      position: relative;
      z-index: 1;
      background: #FFFFFF;
      max-width: 360px;
      padding: 45px;
      text-align: center;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2), 0 5px 5px rgba(0, 0, 0, 0.24);
      border-radius: 1rem;
    }
    .form-container input {
      font-family: "Roboto", sans-serif;
      outline: 0;
      background: #f2f2f2;
      width: 100%;
      border: 0;
      margin: 0 0 15px;
      padding: 15px;
      box-sizing: border-box;
      font-size: 14px;
    }
    .form-container button {
      font-family: "Roboto", sans-serif;
      text-transform: uppercase;
      outline: 0;
      background: #4CAF50;
      width: 100%;
      border: 0;
      padding: 15px;
      color: #FFFFFF;
      font-size: 14px;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .form-container button:hover {
      background: #43A047;
    }
  </style>
</head>
<body>
  <div class="reset-page">
    <div class="form-container">
      <!-- Reset Password Form -->
      <form id="reset-password-form" action="/api/reset-password" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" id="email" class="form-control" value="{{ request('email') }}" />
        <div class="mb-3">
          <label for="password" class="form-label">New Password</label>
          <input type="password" name="password" id="password" class="form-control" required />
        </div>
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#reset-password-form').on('submit', function(e) {
        var password = $('#password').val();
        var passwordConfirmation = $('#password_confirmation').val();

        if (password !== passwordConfirmation) {
          e.preventDefault();
          alert('Passwords do not match. Please make sure both passwords are the same.');
        }
      });
    });
  </script>
</body>
</html>
