<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>WAPH-Team30 Login page</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #bedf;
      margin: 0;
      padding: 0;
    }
    h1, h2 {
      text-align: center;
      color: #333;
    }
    #digit-clock {
      text-align: center;
      margin-bottom: 20px;
    }
    .form {
      max-width: 300px;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .text_field {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }
    .button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .button:hover {
      background-color: #0056b3;
    }
  </style>
  <script type="text/javascript">
      function displayTime() {
        document.getElementById('digit-clock').innerHTML = "Current time: " + new Date();
      }
      setInterval(displayTime, 500);
  </script>
</head>
<body>
  <h1>A Simple login form, WAPH-team30</h1>
  <h2>WAPH_TEAM_30</h2>
  <div id="digit-clock"></div>  
<?php
  echo "<p style='text-align: center;'>Visited time: " . htmlspecialchars(date("Y-m-d h:i:sa")) . "</p>";
?>
  <form action="index.php" method="POST" class="form login">
    <label for="username">Username:</label><br>
    <input type="text" id="username" class="text_field" name="username" /><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" class="text_field" name="password" /><br>
    <button class="button" type="submit">Login</button>
  </form> 
  <form action="superuser_login.php" method="GET" class="form">
    <button class="button" type="submit">Superuser-Login</button>
  </form>
  <h2 style="text-align: center;">New User? Register Here!</h2>
  <form action="registrationform.php" method="GET" class="form">
    <button class="button" type="submit">Register</button>
  </form>
</body>
</html>

