<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HobbyNet — Temukan Hobi Pertamamu</title>
    <meta name="description" content="Temukan hobi yang kamu suka, siapkan perlengkapannya, dan mulai proyek pertamamu satu langkah kecil demi satu langkah.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    @php
        // Fallback jika route bernama belum ada (mis. belum pasang Breeze/Fortify)
        $loginUrl    = url('/login');
        $registerUrl = url('/login');
    @endphp

    <style>
        :root {
            --ink: 87, 66, 58;               /* #57423A */
            --text: rgb(var(--ink));
            --text-80: rgba(var(--ink), .8);
            --text-60: rgba(var(--ink), .6);
            --text-45: rgba(var(--ink), .45);
            --text-25: rgba(var(--ink), .25);
            --bg: #FFF9F5;
            --bg-alt: #F5F8F0;
            --cream: #FDF3EE;
            --pink: #F1C6CA;
            --pink-soft: #FAECED;
            --sage: #C8D8B9;
            --sage-soft: #EEF3E9;
            --lav: #C6D3F5;
            --lav-soft: #EDF1FC;
            --radius: 20px;
            /* Palet: Warm White #FFF9F5 | Dusty Pink #F1C6CA | Soft Sage #C8D8B9 | Periwinkle #C6D3F5 */
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        h1, h2, h3, .logo { font-family: 'Fraunces', serif; color: var(--text); }

        .container { max-width: 1120px; margin: 0 auto; padding: 0 24px; }

        /* ===== Buttons ===== */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 12px 24px; border-radius: 999px;
            font: 500 .875rem 'Outfit', sans-serif;
            border: 1.5px solid transparent; cursor: pointer;
            transition: transform .2s, box-shadow .2s, background .2s;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(var(--ink), .18); }
        .btn:active { transform: translateY(0); box-shadow: inset 0 3px 8px rgba(var(--ink), .25); }
        .btn-pink    { background: var(--pink); color: var(--text); }
        .btn-outline { border-color: var(--text); color: var(--text); background: transparent; }
        .btn-dark    { background: var(--text); color: #fff; }
        .btn-sm      { padding: 9px 18px; font-size: .78rem; }

        /* ===== Navbar ===== */
        :root { --nav-h: 76px; }
        .navbar { height: var(--nav-h); position: sticky; top: 0; z-index: 50; background: rgba(255,249,245,.9); backdrop-filter: blur(8px); }
        .navbar .container { height: 100%; display: flex; align-items: center; justify-content: space-between; }
        .logo { display: flex; align-items: center; gap: 10px; font-size: 1.05rem; font-weight: 700; }
        .logo-mark { width: 22px; height: 22px; border-radius: 7px; background: var(--pink); display: grid; place-items: center; }
        .logo-mark::after { content: ''; width: 8px; height: 8px; border-radius: 50%; background: #fff; }
        .nav-links { display: flex; align-items: center; gap: 10px; font-size: .78rem; }
        .nav-links a:not(.btn) {
            color: var(--text-80);
            padding: 9px 16px; border-radius: 999px;
            transition: color .2s, background .2s, box-shadow .2s, transform .2s;
        }
        /* Hover: muncul bayangan lembut seperti tombol */
        .nav-links a:not(.btn):hover {
            color: var(--text); background: #fff;
            box-shadow: 0 6px 16px rgba(var(--ink), .14);
            transform: translateY(-2px);
        }
        /* Saat ditekan: bayangan masuk ke dalam */
        .nav-links a:not(.btn):active {
            transform: translateY(0);
            background: var(--pink-soft);
            box-shadow: inset 0 2px 6px rgba(var(--ink), .18);
        }
        .nav-links a:focus-visible, .btn:focus-visible { outline: 2px solid var(--pink); outline-offset: 3px; }

        /* ===== Hero ===== */
        .hero {
            /* Hero memenuhi layar: tinggi layar dikurangi tinggi navbar */
            min-height: calc(100vh - var(--nav-h));
            min-height: calc(100svh - var(--nav-h));
            display: flex; align-items: center;
            padding: 24px 0 48px;
        }
        .hero .container { width: 100%; display: grid; grid-template-columns: 1.1fr 1fr; gap: 40px; align-items: center; }
        .hero h1 { font-size: clamp(2.2rem, 4.6vw, 3.1rem); line-height: 1.15; font-weight: 700; margin-bottom: 20px; }
        .hero p { color: var(--text-60); font-size: .9rem; max-width: 300px; margin-bottom: 28px; }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

        .hero-visual { position: relative; height: 280px; }
        .photo {
            position: absolute; border: 4px solid #fff; border-radius: 22px;
            background: var(--pink-soft); overflow: hidden;
            box-shadow: 0 14px 30px rgba(var(--ink), .15);
        }
        .photo img { width: 100%; height: 100%; object-fit: cover; }
        .photo.p1 { width: 142px; height: 138px; left: 0;     top: 100px; }
        .photo.p2 { width: 108px; height: 140px; left: 154px; top: 50px;  }
        .photo.p3 { width: 124px; height: 154px; left: 270px; top: 0;     }

        /* ===== Section heading ===== */
        section { position: relative; }
        .section-head { text-align: center; margin-bottom: 56px; }
        .section-head h2 { font-size: 1.35rem; margin-bottom: 6px; }
        .section-head p { color: var(--text-60); font-size: .75rem; }

        /* ===== How it works ===== */
        .how { padding: 88px 0 80px; background: var(--bg); }
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; text-align: center; }
        .step-icon { width: 96px; height: 96px; border-radius: 50%; margin: 0 auto 22px; display: grid; place-items: center; }
        .step-icon img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; }
        .step-icon svg { width: 40px; height: 40px; stroke: var(--text); fill: none; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; opacity: .75; }
        .step:nth-child(1) .step-icon { background: var(--pink-soft); }
        .step:nth-child(2) .step-icon { background: var(--sage-soft); }
        .step:nth-child(3) .step-icon { background: var(--lav-soft); }
        .step h3 { font-size: .95rem; margin-bottom: 8px; }
        .step p { color: var(--text-60); font-size: .72rem; max-width: 250px; margin: 0 auto; }

        /* ===== Popular paths ===== */
        .paths { padding: 72px 0 80px; background: var(--cream); }
        .paths-head { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; }
        .paths-head h2 { font-size: 1.35rem; }
        .paths-head p  { color: var(--text-60); font-size: .75rem; }
        .link-underline { font-size: .72rem; border-bottom: 1px solid var(--text-45); padding-bottom: 2px; color: var(--text-80); }
        .paths-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .path-img { height: 200px; border-radius: 18px; overflow: hidden; background: var(--pink-soft); margin-bottom: 14px; }
        .path-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
        .path:hover .path-img img { transform: scale(1.05); }
        .path h3 { font-size: .95rem; margin-bottom: 4px; }
        .path p { color: var(--text-60); font-size: .7rem; margin-bottom: 10px; }
        .path .more { font-size: .68rem; font-weight: 500; }
        .path:nth-child(1) .more { color: #D4909B; }
        .path:nth-child(2) .more { color: #84A06F; }
        .path:nth-child(3) .more { color: #8798D6; }

        /* ===== Quick wins ===== */
        .wins { padding: 72px 0 88px; background: var(--bg-alt); }
        .wins .section-head { margin-bottom: 36px; }
        .wins-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .win { background: #fff; border-radius: 18px; padding: 8px; box-shadow: 0 4px 14px rgba(var(--ink), .05); transition: transform .25s, box-shadow .25s; }
        .win:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(var(--ink), .1); }
        .win-img { height: 96px; border-radius: 12px; overflow: hidden; background: var(--sage-soft); }
        .win-img img { width: 100%; height: 100%; object-fit: cover; }
        .win-body { padding: 10px 6px 6px; }
        .tag { font-size: .55rem; letter-spacing: .08em; text-transform: uppercase; font-weight: 500; }
        .win:nth-child(1) .tag { color: #D4909B; }
        .win:nth-child(2) .tag { color: #D4909B; }
        .win:nth-child(3) .tag { color: #8798D6; }
        .win:nth-child(4) .tag { color: #D4909B; }
        .win h4 { font: 500 .8rem 'Outfit', sans-serif; margin: 3px 0 4px; }
        .meta { display: flex; gap: 12px; font-size: .6rem; color: var(--text-45); }

        /* ===== CTA ===== */
        .cta { padding: 64px 0 72px; background: var(--bg); }
        .cta-box { background: var(--lav-soft); border-radius: 32px; padding: 64px 24px; text-align: center; position: relative; overflow: hidden; }
        .cta-box h2 { font-size: 1.6rem; margin-bottom: 12px; }
        .cta-box p { color: var(--text-60); font-size: .8rem; max-width: 320px; margin: 0 auto 26px; }
        .deco { position: absolute; width: 56px; height: 56px; stroke: rgba(var(--ink), .15); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .deco.d1 { top: 24px; left: 20px; }
        .deco.d2 { bottom: 20px; right: 20px; }

        /* ===== Footer ===== */
        footer { border-top: 1px solid rgba(var(--ink), .08); padding: 40px 0 28px; }
        .footer-top { display: flex; justify-content: space-between; gap: 40px; flex-wrap: wrap; }
        .footer-brand p { color: var(--text-60); font-size: .68rem; max-width: 300px; margin-top: 10px; }
        .footer-cols { display: flex; gap: 56px; }
        .footer-col h5 { font: 500 .7rem 'Outfit', sans-serif; margin-bottom: 10px; }
        .footer-col a { display: block; font-size: .66rem; color: var(--text-45); margin-bottom: 6px; transition: color .2s; }
        .footer-col a:hover { color: var(--text); }
        .socials { display: flex; gap: 10px; }
        .socials a { width: 16px; height: 16px; }
        .socials svg { width: 100%; height: 100%; fill: none; stroke: var(--text); stroke-width: 1.8; }
        .copyright { text-align: center; font-size: .6rem; color: var(--text-25); margin-top: 48px; }

        /* ===== Responsive ===== */
        @media (max-width: 860px) {
            .hero .container { grid-template-columns: 1fr; text-align: center; gap: 28px; }
            .hero-actions { justify-content: center; }
            .hero-visual { height: 300px; max-width: 400px; margin: 0 auto; }
            .steps, .paths-grid { grid-template-columns: 1fr; }
            .wins-grid { grid-template-columns: repeat(2, 1fr); }
            .nav-links a:first-child { display: none; }
        }

        @media (max-width: 480px) {
            .wins-grid { grid-template-columns: 1fr; }
            .footer-cols { gap: 28px; flex-wrap: wrap; }

            /* Penyesuaian gambar hero agar tidak kepotong di HP */
            .hero-visual { height: 210px; max-width: 310px; }
            .photo.p1 { width: 110px; height: 108px; left: 0; top: 70px; }
            .photo.p2 { width: 85px; height: 110px; left: 118px; top: 35px; }
            .photo.p3 { width: 98px; height: 120px; left: 208px; top: 0; }
        }
    </style>
</head>
<body>

    {{-- ================= NAVBAR ================= --}}
    <header class="navbar">
        <div class="container">
            <a href="{{ url('/') }}" class="logo"><span class="logo-mark"></span> HobbyNet</a>
            <nav class="nav-links">
                <a href="#paths">Explore Hobbies</a>
                <a href="{{ $loginUrl }}">Log In</a>
                <a href="{{ $registerUrl }}" class="btn btn-dark btn-sm">Sign Up</a>
            </nav>
        </div>
    </header>

    {{-- ================= HERO ================= --}}
    <section class="hero">
        <div class="container">
            <div>
                <h1>Your next favorite hobby starts here.</h1>
                <p>Find a hobby you love, gather what you need, and create your very first project — one small step at a time.</p>
                <div class="hero-actions">
                    <a href="{{ $registerUrl }}" class="btn btn-pink">Find My Hobby</a>
                    <a href="#quick-wins" class="btn btn-outline">Explore Projects</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="photo p1"><img src="{{ asset('images/landing/hero-1.png') }}" alt="Alat lukis" onerror="this.style.display='none'"></div>
                <div class="photo p2"><img src="{{ asset('images/landing/hero-2.png') }}" alt="Kamera dan kopi" onerror="this.style.display='none'"></div>
                <div class="photo p3"><img src="{{ asset('images/landing/hero-3.png') }}" alt="Merajut" onerror="this.style.display='none'"></div>
            </div>
        </div>
    </section>

    {{-- ================= HOW IT WORKS ================= --}}
    <section class="how" id="how-it-works">
        <div class="container">
            <div class="section-head">
                <h2>How It Works</h2>
                <p>A gentle path from curiosity to creation.</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">
                        <img src="{{ asset('images/landing/how-1.png') }}" alt="" onerror="this.style.display='none'">
                    </div>
                    <h3>1. Choose Your Hobby</h3>
                    <p>Take our simple quiz or browse categories to find what speaks to your heart.</p>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <img src="{{ asset('images/landing/how-2.png') }}" alt="" onerror="this.style.display='none'">
                    </div>
                    <h3>2. Get Ready</h3>
                    <p>We'll give you a curated list of exactly what you need—nothing more, nothing less.</p>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <img src="{{ asset('images/landing/how-3.png') }}" alt="" onerror="this.style.display='none'">
                    </div>
                    <h3>3. Make Your First Project</h3>
                    <p>Follow our beginner-friendly guides to complete your very first masterpiece today.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= POPULAR PATHS ================= --}}
    <section class="paths" id="paths">
        <div class="container">
            <div class="paths-head">
                <div>
                    <h2>Explore Popular Paths</h2>
                    <p>Discover the joy in these favorite activities.</p>
                </div>
                <a href="{{ $registerUrl }}" class="link-underline">View All Hobbies</a>
            </div>

            <div class="paths-grid">
                <a href="{{ $registerUrl }}" class="path">
                    <div class="path-img"><img src="{{ asset('images/landing/path-1.png') }}" alt="Crochet" onerror="this.style.display='none'"></div>
                    <h3>Crochet</h3>
                    <p>The meditative art of yarn and hook. Perfect for cozy evenings.</p>
                    <span class="more">Learn More →</span>
                </a>
                <a href="{{ $registerUrl }}" class="path">
                    <div class="path-img"><img src="{{ asset('images/landing/path-2.png') }}" alt="Painting" onerror="this.style.display='none'"></div>
                    <h3>Painting</h3>
                    <p>Express yourself through colors and textures. No experience needed.</p>
                    <span class="more">Learn More →</span>
                </a>
                <a href="{{ $registerUrl }}" class="path">
                    <div class="path-img"><img src="{{ asset('images/landing/path-3.png') }}" alt="Photography" onerror="this.style.display='none'"></div>
                    <h3>Photography</h3>
                    <p>Capture the world through your unique lens. Start with your phone.</p>
                    <span class="more">Learn More →</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ================= QUICK WINS ================= --}}
    <section class="wins" id="quick-wins">
        <div class="container">
            <div class="section-head">
                <h2>Quick Wins</h2>
                <p>Start and finish these beginner projects in under 2 hours.</p>
            </div>

            <div class="wins-grid">
                @php
                    $wins = [
                        ['img' => 'win-1.png',  'tag' => 'Crochet • Beginner',  'title' => 'Cotton Crochet Coaster', 'time' => '45m', 'level' => 'Easy'],
                        ['img' => 'win-2.png','tag' => 'Painting • Beginner', 'title' => 'Sunset Watercolor',      'time' => '60m', 'level' => 'Easy'],
                        ['img' => 'win-3.png','tag' => 'Photo • Intro',      'title' => 'Composition 101',        'time' => '30m', 'level' => 'Fundamental'],
                        ['img' => 'win-4.png',   'tag' => 'Craft • Beginner',    'title' => 'Soy Wax Candle',         'time' => '90m', 'level' => 'Moderate'],
                    ];
                @endphp

                @foreach ($wins as $w)
                    <a href="{{ $registerUrl }}" class="win">
                        <div class="win-img"><img src="{{ asset('images/landing/'.$w['img']) }}" alt="{{ $w['title'] }}" onerror="this.style.display='none'"></div>
                        <div class="win-body">
                            <span class="tag">{{ $w['tag'] }}</span>
                            <h4>{{ $w['title'] }}</h4>
                            <div class="meta"><span>⏱ {{ $w['time'] }}</span><span>▮ {{ $w['level'] }}</span></div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="cta">
        <div class="container">
            <div class="cta-box">
                <svg class="deco d1" viewBox="0 0 24 24"><path d="M20 4c-8 0-14 5-14 12v4h4c7 0 10-6 10-16z"/><path d="M6 20c2-5 5-8 9-10"/></svg>
                <h2>Ready to start something new?</h2>
                <p>Join our community of over 10,000 hobbyists who found their passion with us. No pressure, just joy.</p>
                <a href="{{ $registerUrl }}" class="btn btn-dark">Sign Up for Free</a>
                <svg class="deco d2" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8.100 15.900M14.500 14.500 20 20M8.100 8.100 12 12"/></svg>
            </div>
        </div>
    </section>

    {{-- ================= FOOTER ================= --}}
    <footer>
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="{{ url('/') }}" class="logo"><span class="logo-mark"></span> HobbyNet</a>
                    <p>Supporting your creative journey, one small project at a time. Designed for the curious heart.</p>
                </div>
                <div class="footer-cols">
                    <div class="footer-col">
                        <h5>Explore</h5>
                        <a href="#paths">All Hobbies</a>
                        <a href="#quick-wins">Popular Projects</a>
                        <a href="#quick-wins">Starter Kits</a>
                    </div>
                    <div class="footer-col">
                        <h5>Community</h5>
                        <a href="#">Showcase</a>
                        <a href="#">Forums</a>
                        <a href="#">Workshops</a>
                    </div>
                    <div class="footer-col">
                        <h5>Connect</h5>
                        <div class="socials">
                            <a href="#" aria-label="Instagram"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.500" cy="6.500" r=".6"/></svg></a>
                            <a href="#" aria-label="Pinterest"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M11 8c-3 1-3 5 0 5s3-3 1-5M10 16l1.500-8"/></svg></a>
                            <a href="#" aria-label="TikTok"><svg viewBox="0 0 24 24"><path d="M14 3v11a4 4 0 1 1-4-4M14 3c0 3 2 5 5 5"/></svg></a>
                        </div>
                    </div>
                </div>
            </div>
            <p class="copyright">© {{ date('Y') }} HobbyNet. Crafted with love for creators everywhere.</p>
        </div>
    </footer>

</body>
</html>