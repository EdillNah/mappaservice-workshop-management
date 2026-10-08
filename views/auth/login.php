<?php

session_start();
require_once "../../config/koneksi.php";

$error = "";

if (isset($_POST["login"])) {

  $username = $_POST["username"];
  $password = $_POST["password"];

  $sql = "SELECT * FROM users WHERE username = ?";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$username]);

  $user = $stmt->fetch();

  if ($user && $password === $user["password"]) {
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    if ($user["role"] === "owner") {
      header("Location: ../../dashboard/owner.php");
      exit;
    }

    if ($user["role"] === "kasir") {
      header("Location: ../../dashboard/kasir.php");
      exit;
    }
  } else {
    $error = "Username atau password salah.";
  }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Login MappaService</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>
  <h1 style="font-family: ariel">MappaService</h1>
  <h2 style="font-family: ariel">Login</h2>
  <?php if ($error): ?>
  <p><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" >
    <label>Username</label>
    <br>
    <input type="text" name="username" required>
    <br><br>

    <label>Password</label>
    <br>
    <input type="password" name="password" required>
    <br><br>
    <button type="submit" name="login" type="button" class="btn btn-outline-primary">Login</button>
  </form>
</body>

</html>