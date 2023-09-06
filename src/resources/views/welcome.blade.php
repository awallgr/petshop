<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(0deg, #00ccff, #0099cc);
            animation: backgroundAnimation 5s infinite alternate;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }
        h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: white;
            margin-bottom:40px;
        }
        a {
            display: inline-block;
            padding: 15px 30px;
            font-size: 1.5rem;
            background-color: #003366;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }
        a:active {
            transform: scale(0.9);
        }
        @keyframes backgroundAnimation {
            0% {
                background: #00ccff;
            }
            100% {
                background: #0099cc;
            }
        }
    </style>
</head>
<body>
    <h1>Hello...World!</h1>
    <a href="/api/documentation">API</a>
</body>
</html>


