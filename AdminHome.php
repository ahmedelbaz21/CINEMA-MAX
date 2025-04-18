<?php session_start();
include "db_connection.php";
// Handle GET request: fetch movies
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $conn->query("SELECT * FROM movies");
    $movies = [];

    while ($row = $result->fetch_assoc()) {
        $movies[] = $row;
    }

    echo json_encode($movies);
    exit;
}

// Handle POST request: add new movie
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["title"], $data["genre"], $data["duration"], $data["rating"], $data["description"],$data["cast"])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
        exit;
    }

    $title = $conn->real_escape_string($data["title"]);
    $genre = $conn->real_escape_string($data["genre"]);
    $duration = $conn->real_escape_string($data["duration"]);
    $rating = $conn->real_escape_string($data["rating"]);

    $stmt = $conn->prepare("INSERT INTO movies (title, genre, duration, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $genre, $duration, $rating);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    exit;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manage Movies</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="common.css">
    <style>
        .movie-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .movie-card {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 100%;
            transition: transform 0.3s ease-in-out;
        }

        .movie-poster img {
            width: 100%;
            height: auto;
        }

        .movie-info {
            padding: 1rem;
        }

        .edit-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px;
            border-radius: 50%;
            cursor: pointer;
        }

        .add-movie-card {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 250px;
            border: 2px dashed #ddd;
            cursor: pointer;
            font-size: 2rem;
            color: #555;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            margin: 10% auto;
            width: 50%;
            border-radius: 10px;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #d21515;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #b10e0e;
        }
    </style>
</head>
<body>
<header>
    <div class="header-container">
        <h1><span class="cinema">CINEMA</span><span class="max">MAX</span></h1>
    </div>
</header>

<nav>
    <a href="AdminHome.html">Now Showing</a>
    <a href="Admin_coming_soon.html">Coming Soon</a>
    <a href="Adminoffers.html">Offers</a>
    <a href="AdminF&B.php">Food & Beverages</a>
    <a href="Adminlocation.html">Our Locations</a>
    <a href="#footer">Contact</a>
</nav>

<div class="movie-container" id="movies">
    <!-- Add Movie Card -->
    <div class="movie-card" id="addMovie" onclick="openModal()">
        <div class="add-movie-card">+</div>
    </div>
</div>

<!-- Add Movie Modal -->
<div id="movieModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2>Add Movie</h2>
        <form id="movieForm">
            <input type="text" id="title" placeholder="Enter Movie Title" required>
            <input type="text" id="genre" placeholder="Enter Genre" required>
            <input type="text" id="duration" placeholder="Enter Duration (e.g. 2h 15min)" required>
            <input type="text" id="rating" placeholder="Enter Age Rating (e.g. PG-13)" required>
            <button type="button" onclick="saveMovie()">Add Movie</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", fetchMovies);

    function openModal() {
        document.getElementById("movieModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("movieModal").style.display = "none";
        document.getElementById("movieForm").reset();
    }

    function fetchMovies() {
        fetch("movies.php")
            .then(res => res.json())
            .then(movies => {
                const movieList = document.getElementById("movies");

                movies.forEach(movie => {
                    const movieCard = document.createElement("div");
                    movieCard.classList.add("movie-card");
                    movieCard.innerHTML = `
                        <div class="movie-poster">
                            <img src="movie%20posters/${movie.title}.jpg" alt="${movie.title}">
                        </div>
                        <div class="movie-info">
                            <h3>${movie.title}</h3>
                            <p>Genre: ${movie.genre}</p>
                            <p>Duration: ${movie.duration}</p>
                            <p>Rating: ${movie.rating}</p>
                        </div>
                        <div class="edit-icon" onclick="editMovie(${movie.id})">✎</div>
                    `;
                    movieList.insertBefore(movieCard, document.getElementById("addMovie"));
                });
            })
            .catch(err => {
                alert("Request failed.");
                console.error("Fetch error:", err);
            });

    }

    function saveMovie() {
        const title = document.getElementById("title").value;
        const genre = document.getElementById("genre").value;
        const duration = document.getElementById("duration").value;
        const rating = document.getElementById("rating").value;

        if (title && genre && duration && rating) {
            fetch("movies.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ title, genre, duration, rating })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert("Movie added successfully!");
                    closeModal();
                    document.getElementById("movies").innerHTML = `
                        <div class="movie-card" id="addMovie" onclick="openModal()">
                            <div class="add-movie-card">+</div>
                        </div>`;
                    fetchMovies(); // Refresh movie list
                } else {
                    alert("Error adding movie: " + data.message);
                }
            })
            .catch(err => {
                alert("Request failed.");
                console.error(err);
            });
        } else {
            alert("Please fill all fields!");
        }
    }

    function editMovie(id) {
        alert("Edit feature coming soon! (Movie ID: " + id + ")");
    }
</script>

</body>
</html>
