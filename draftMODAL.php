<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .kebab-container {
            position: relative;
        }

        .kebab-menu {
            cursor: pointer;
        }

        .modal-container {
            display: none;
            position: absolute;
            top: 40px;
            right: 0;
            background: white;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .modal-content {
            padding: 10px;
        }

        a {
            display: block;
            padding: 8px;
            text-decoration: none;
            color: #333;
        }

        a:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

<div class="kebab-container">
    <span class="kebab-menu" onclick="toggleModal()">
        <a href="#"><img src="images/kebabMenu.png" alt="options-icon" height="30px"></a>
    </span>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="top">
                    <h3>Invest in this project</h3>
                    <span class="close">&times;</span>
                </div>
                <p>Name:</p>
                <input type="text" name="investor-name" required>
                <p>Email:</p>
                <input type="text" name="investor-email" required>
                <p>Source of Income:</p>
                <input type="text" name="source-income" required>
                <p>Identity proof:</p>
                <input type="file" name="proof" required>
                <p>Request documents:</p>
                <input type="checkbox" name="reqDocs[]" id="canvas" value="Startup Model Canvas"><label for="canvas">Startup Model Canvas</label><br>
                <input type="checkbox" name="reqDocs[]" id="video-pitch" value="Video Pitch"><label for="video-pitch">Video Pitch</label><br>
                <input type="checkbox" name="reqDocs[]" id="pitch-deck" value="Pitch Deck"><label for="pitch-deck">Pitch Deck</label><br>
                <p>Others:</p>
                <input type="text" name="other-docs">
                <input type="submit" value="Submit" name="submit">
            </form>
        </div>
    </div>
</div>

<script>
    const modalContainer = document.querySelector('.modal-container');

    function toggleModal() {
        modalContainer.style.display = (modalContainer.style.display === 'none' || modalContainer.style.display === '') ? 'block' : 'none';
    }

    function closeModal() {
        modalContainer.style.display = 'none';
    }
</script>

</body>
</html>
