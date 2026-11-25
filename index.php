<?php require_once 'config/functions.php'; if(!empty($_SESSION['user_id']))redirect('dashboard.php');redirect('login.php');
