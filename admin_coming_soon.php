<?php session_start();
include "db_connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaMax - admin comig soon </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="common.css">
    <style>
        /* Add page-specific styles here */
    </style>
</head>
<body>
    <header>
        <div class="header-container">
    
            <h1><span class="cinema">CINEMA</span><span class="max">MAX</span></h1>
           
        </div>
    </header>

    <nav>
        <a href="Adminhome.php">Now Showing</a>
        <a href="admin_coming_soon.php">Coming Soon</a>
        <a href="offers.html">Offers</a>
        <a href="f&b.html">Food & Beverages</a>
        <a href="location.html">Our Locations</a>
        <a href="#footer">Contact</a>
    </nav>

    <!-- Page-specific content goes here -->

    <footer id="footer">
        <div>
            <h3>Contact Us</h3>
            <p>HQ: Office 304 District 5, New Cairo City</p>
            <p>Hotline: 161676</p>
            <p>Email: info@cinemamax.com</p>
        </div>
        <div style="margin-top: 1rem;">
            <p>&copy; 2025 CinemaMax. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 
