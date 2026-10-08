<?php

function sudahLogin() {
  return isset($_SESSION["user_id"]);
}

function harusLogin() {
  if (!sudahLogin()) {
    header("Location: ../views/auth/login.php");
    exit;
  }
}

function harusKasir() {
  harusLogin();

  if ($_SESSION["role"] !== "kasir") {
    header("Location: ../dashboard/owner.php");
    exit;
  }
}

function harusOwner()
{
  harusLogin();

  if ($_SESSION["role"] !== "owner") {
    header("Location: ../dashboard/kasir.php");
    exit;
  }
}