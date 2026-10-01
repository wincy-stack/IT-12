<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aqua De Smiley – Pure & Clean Water</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #0ea5e9;
            --accent: #38bdf8;
            --dark-bg: #0a1628;
            --text: #e2e8f0;
            --text-muted: #94a3b8;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--dark-bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(14,165,233,0.15) 0%, transparent 70%);
            z-index: 0;
            pointer-events: none;
        }
        .hero-content {
            z-index: 1;
            max-width: 800px;
        }
        h1 {
            font-size: clamp(3rem, 8vw, 5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--accent), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        p {
            font-size: clamp(1.1rem, 3vw, 1.3rem);
            color: var(--text-muted);
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-inline: auto;
            line-height: 1.6;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border-radius: 99px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4);
        }
        .btn-primary:hover {
            background: #0284c7;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.6);
        }
        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="hero-content">
            <h1><i class="fa-solid fa-droplet" style="-webkit-text-fill-color:var(--accent);"></i> Aqua De Smiley</h1>
            <p>Experience the purest and cleanest water refilling station in town. Fast delivery, pristine quality, and reliable service.</p>
            <div class="btn-group">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Go to Dashboard <i class="fa-solid fa-arrow-right"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Log In <i class="fa-solid fa-right-to-bracket"></i></a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
