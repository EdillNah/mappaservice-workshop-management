<?php

function sudahLogin()
{
    return isset($_SESSION["user_id"]);
}

function harusLogin()
{
    if (!sudahLogin()) {
        // Redirect selalu mengarah tepat ke login.php di folder views/auth/
        header("Location: ../views/auth/login.php");
        exit;
    }
}

function harusKasir()
{
    harusLogin();

    if ($_SESSION["role"] !== "kasir") {
        // Jika bukan kasir, lempar ke dashboard owner
        header("Location: ../dashboard/owner.php");
        exit;
    }
}

function harusOwner()
{
    harusLogin();

    if ($_SESSION["role"] !== "owner") {
        // Jika bukan owner, lempar ke dashboard kasir
        header("Location: ../dashboard/kasir.php");
        exit;
    }
}