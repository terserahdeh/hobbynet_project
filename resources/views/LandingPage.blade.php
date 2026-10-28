
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFF8F3">
    <meta name="description" content="Temukan hobi baru, belajar bertahap, dan bagikan hasil karyamu bersama HobbyNet.">

    <title>HobbyNet — Mulai Hobimu, Ciptakan Ceritamu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&display=swap" rel="stylesheet">

    @php
        $loggedIn = auth()->check();

        $startUrl = $loggedIn
            ? route('home')
            : route('onboarding.account');

        $startLabel = $loggedIn
            ? 'Buka Beranda'
            : 'Mulai Gratis';

        $exploreUrl = $loggedIn
            ? route('explore')
            : route('onboarding.account');

        $courses = [
            [
                'title' => 'Crochet untuk Pemula',
                'category' => 'CRAFT & DIY',
                'description' => 'Belajar merajut dari simpul pertama sampai membuat alas gelas.',
                'image' => 'images/landing/path-1.png',
                'label' => 'Favorit Pemula',
                'icon' => '🧶',
            ],
            [
                'title' => 'Belajar Melukis',
                'category' => 'ART & PAINTING',
                'description' => 'Eksplorasi warna, kuas, dan kreativitas melalui karya pertamamu.',
                'image' => 'images/landing/path-2.png',
                'label' => 'Kreatif',
                'icon' => '🎨',
            ],
            [
                'title' => 'Fotografi Dasar',
                'category' => 'PHOTOGRAPHY',
                'description' => 'Kenali komposisi, pencahayaan, dan cara menghasilkan foto menarik.',
                'image' => 'images/landing/path-3.png',
                'label' => 'Eksplorasi',
                'icon' => '📷',
            ],
        ];
    @endphp

    <style>
        :root {
            --bg: #FFF8F3;
            --ink: #483B42;
            --muted: #81737B;

            --pink: #F4AFC3;
            --pink-light: #FFE5EC;
            --pink-deep: #B95073;

            --green: #B7D8AE;
            --green-light: #E7F3E1;
            --green-deep: #50774B;

            --purple: #C7C5FA;
            --purple-light: #EDEBFF;
            --purple-deep: #7067AC;

            --yellow: #FFE1A0;
            --yellow-light: #FFF2D4;

            --white: #FFFFFF;
            --shadow: 0 12px 40px rgba(80, 55, 75, .08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.65;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            font: inherit;
        }

        img {
            display: block;
            max-width: 100%;
        }

        h1, h2, h3 {
            font-family: 'Fraunces', Georgia, serif;
            line-height: 1.2;
        }

        .container {
            width: min(1180px, calc(100% - 48px));
            margin: auto;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .11em;
            text-transform: uppercase;
            color: var(--pink-deep);
        }

        .section-header {
            max-width: 650px;
            margin: 0 auto 40px;
            text-align: center;
        }

        .section-header h2 {
            font-size: clamp(1.8rem, 3.1vw, 2.6rem);
            margin: 12px 0 13px;
        }

        .section-header p {
            color: var(--muted);
            font-size: .91rem;
            line-height: 1.75;
        }

        .highlight-pink {
            color: var(--pink-deep);
        }

        .highlight-green {
            color: var(--green-deep);
        }

        /* =========================
           NAVBAR
           ========================= */

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 248, 243, .93);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(90, 60, 80, .06);
        }

        .nav-content {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -.035em;
            white-space: nowrap;
        }

        .logo-mark {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            flex: none;
            background: var(--green);
            border-radius: 12px;
            color: #57423A;
        }

        .logo-mark svg {
            width: 22px;
            height: 22px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 26px;
            font-size: .83rem;
            font-weight: 600;
        }

        .nav-links > a:not(.btn) {
            transition: color .2s;
        }

        .nav-links > a:not(.btn):hover {
            color: var(--pink-deep);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* =========================
           BUTTONS
           ========================= */

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 23px;
            border-radius: 100px;
            border: 2px solid transparent;
            font-size: .84rem;
            font-weight: 800;
            text-align: center;
            transition: transform .25s, box-shadow .25s, background .25s;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(80, 50, 70, .12);
        }

        .btn-primary {
            background: var(--pink);
            color: var(--ink);
        }

        .btn-primary:hover {
            background: #F29DB8;
        }

        .btn-white {
            background: white;
            color: var(--ink);
        }

        .btn-outline {
            background: transparent;
            border-color: #D9CBD0;
            color: var(--ink);
        }

        .btn-dark {
            background: var(--ink);
            color: white;
        }

        .btn-sm {
            padding: 10px 18px;
            font-size: .78rem;
        }

        /* =========================
           HERO SECTION
           ========================= */

        .hero {
            position: relative;
            overflow: hidden;
            padding: 76px 0 86px;
            background:
                radial-gradient(circle at 93% 15%, #FCE0E9 0, transparent 33%),
                radial-gradient(circle at 4% 85%, #E7EDFF 0, transparent 35%),
                var(--bg);
        }

        .hero::before {
            content: '✳';
            position: absolute;
            left: 3%;
            top: 12%;
            font-size: 80px;
            color: var(--pink);
            opacity: .38;
            pointer-events: none;
        }

        .hero::after {
            content: '✦';
            position: absolute;
            right: 5%;
            bottom: 9%;
            font-size: 100px;
            color: #A4C79B;
            opacity: .45;
            pointer-events: none;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.08fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            background: white;
            border: 1px solid #F6DAE4;
            border-radius: 100px;
            font-size: .69rem;
            font-weight: 800;
            color: var(--pink-deep);
            margin-bottom: 22px;
            box-shadow: 0 6px 18px rgba(150, 90, 110, .06);
        }

        .hero h1 {
            font-size: clamp(2.65rem, 4.35vw, 3.85rem);
            letter-spacing: -.045em;
            line-height: 1.19;
            margin-bottom: 23px;
        }

        .hero h1 .accent {
            position: relative;
            color: var(--pink-deep);
        }

     

        .hero-desc {
            max-width: 475px;
            font-size: .95rem;
            line-height: 1.8;
            color: var(--muted);
            margin-bottom: 27px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-note {
            display: flex;
            gap: 9px;
            align-items: center;
            margin-top: 21px;
            color: var(--muted);
            font-size: .76rem;
        }

        /* =========================
           HERO VISUAL
           ========================= */

        .hero-visual {
            position: relative;
            min-height: 440px;
        }

        .visual-blob {
            position: absolute;
            inset: 45px 25px 25px 35px;
            background: #F7CCD9;
            border-radius: 42% 58% 62% 38% / 40% 41% 59% 60%;
            transform: rotate(-8deg);
        }

        .hero-photo {
            position: absolute;
            overflow: hidden;
            border: 7px solid white;
            box-shadow: 0 18px 35px rgba(78, 50, 66, .17);
        }

        .hero-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-one {
            width: 58%;
            height: 235px;
            left: 4%;
            top: 14px;
            border-radius: 30px;
            transform: rotate(-9deg);
        }

        .photo-two {
            width: 58%;
            height: 250px;
            right: 0;
            top: 120px;
            border-radius: 30px;
            transform: rotate(8deg);
        }

        .photo-three {
            width: 42%;
            height: 160px;
            left: 5%;
            bottom: 0;
            border-radius: 25px;
            transform: rotate(-4deg);
        }

        .floating-note {
            position: absolute;
            z-index: 4;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 11px 15px;
            border-radius: 18px;
            box-shadow: 0 15px 30px rgba(80, 55, 75, .12);
            font-size: .75rem;
            font-weight: 800;
        }

        .floating-note .emoji {
            font-size: 1.35rem;
        }

        .note-top {
            right: -12px;
            top: 25px;
            transform: rotate(7deg);
        }

        .note-bottom {
            left: 16%;
            bottom: 18px;
            transform: rotate(-5deg);
        }

        /* =========================
           FEATURE STRIP
           ========================= */

        .feature-strip {
            background: white;
            padding: 30px 0;
            border-block: 1px solid #F3EBEA;
        }

        .feature-strip-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .strip-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: .78rem;
            font-weight: 800;
        }

        .strip-icon {
            display: grid;
            place-items: center;
            width: 39px;
            height: 39px;
            border-radius: 14px;
            font-size: 1.2rem;
        }

        .strip-item:nth-child(1) .strip-icon {
            background: var(--pink-light);
        }

        .strip-item:nth-child(2) .strip-icon {
            background: var(--green-light);
        }

        .strip-item:nth-child(3) .strip-icon {
            background: var(--purple-light);
        }

        .strip-item:nth-child(4) .strip-icon {
            background: var(--yellow-light);
        }

        /* =========================
           HOW IT WORKS
           ========================= */

        .how {
            padding: 85px 0;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .step-card {
            position: relative;
            padding: 30px 27px;
            border-radius: 30px;
            transition: transform .25s;
        }

        .step-card:hover {
            transform: translateY(-6px);
        }

        .step-card:nth-child(1) {
            background: #FCE3EB;
        }

        .step-card:nth-child(2) {
            background: #E7F2DD;
        }

        .step-card:nth-child(3) {
            background: #E9E7FC;
        }

        .step-number {
            display: inline-block;
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .08em;
            margin-bottom: 20px;
            opacity: .65;
        }

        .step-emoji {
            font-size: 2.9rem;
            margin-bottom: 18px;
            line-height: 1;
        }

        .step-card h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .step-card p {
            font-size: .84rem;
            color: #73666D;
        }

        /* =========================
           COURSE CARDS
           ========================= */

        .courses {
            padding: 85px 0;
            background: #F2F1FF;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .course-card {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border-radius: 26px;
            background: white;
            box-shadow: var(--shadow);
            transition: transform .25s, box-shadow .25s;
        }

        .course-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 20px 45px rgba(70, 52, 90, .14);
        }

        .course-image {
            position: relative;
            height: 210px;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .45s;
        }

        .course-card:hover img {
            transform: scale(1.06);
        }

        .course-label {
            position: absolute;
            top: 17px;
            left: 17px;
            background: white;
            border-radius: 100px;
            padding: 7px 13px;
            font-size: .7rem;
            font-weight: 800;
        }

        .course-body {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 23px;
        }

        .course-category {
            font-size: .64rem;
            font-weight: 800;
            letter-spacing: .09em;
            color: var(--pink-deep);
            margin-bottom: 9px;
        }

        .course-card h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        .course-card p {
            color: var(--muted);
            font-size: .82rem;
            flex: 1;
        }

        .course-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #F0EBEF;
            margin-top: 20px;
            padding-top: 18px;
            font-size: .81rem;
            font-weight: 800;
        }

        .course-footer span:last-child {
            color: var(--pink-deep);
        }

        /* =========================
           CREATIVE JOURNEY
           ========================= */

        .journey {
            padding: 90px 0;
        }

        .journey-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 55px;
            align-items: center;
        }

        .journey-art {
            position: relative;
            background: #E4EDD9;
            border-radius: 45px;
            padding: 36px;
            min-height: 370px;
            display: grid;
            place-items: center;
        }

        .journey-art img {
            width: 100%;
            max-height: 350px;
            object-fit: contain;
            border-radius: 24px;
        }

        .journey-content h2 {
            font-size: clamp(1.9rem, 3vw, 2.6rem);
            margin: 15px 0 20px;
        }

        .journey-content > p {
            color: var(--muted);
            font-size: .92rem;
            margin-bottom: 28px;
        }

        .journey-list {
            display: grid;
            gap: 20px;
        }

        .journey-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .journey-icon {
            flex: none;
            width: 45px;
            height: 45px;
            border-radius: 15px;
            display: grid;
            place-items: center;
            background: var(--pink-light);
            font-size: 1.3rem;
        }

        .journey-item:nth-child(2) .journey-icon {
            background: var(--purple-light);
        }

        .journey-item:nth-child(3) .journey-icon {
            background: var(--green-light);
        }

        .journey-item strong {
            display: block;
            margin-bottom: 4px;
            font-size: .91rem;
        }

        .journey-item p {
            color: var(--muted);
            font-size: .82rem;
        }

        /* =========================
           AI COACH
           ========================= */

        .ai-section {
            padding: 85px 0;
            background: #FFF0F3;
        }

        .ai-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 45px;
            align-items: center;
            background: white;
            border-radius: 36px;
            padding: 44px;
            box-shadow: var(--shadow);
        }

        .ai-content h2 {
            font-size: clamp(1.9rem, 3vw, 2.6rem);
            margin: 15px 0 20px;
        }

        .ai-content p {
            color: var(--muted);
            font-size: .92rem;
            margin-bottom: 26px;
        }

        .ai-demo {
            background: #F7F5FF;
            padding: 26px;
            border-radius: 28px;
        }

        .ai-demo-title {
            font-size: .78rem;
            font-weight: 800;
            margin-bottom: 23px;
        }

        .chat-bubble {
            max-width: 86%;
            padding: 15px 18px;
            border-radius: 18px;
            font-size: .81rem;
            margin-bottom: 16px;
            line-height: 1.65;
        }

        .chat-user {
            background: #E5DFFF;
            margin-left: auto;
            border-bottom-right-radius: 5px;
        }

        .chat-ai {
            background: white;
            border: 1px solid #E6E1F6;
            border-bottom-left-radius: 5px;
        }

        /* =========================
           HOBBYNET PLUS
           ========================= */

        .plus-section {
            padding: 85px 0;
        }

        .plus-box {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 60px;
            align-items: center;
            background: linear-gradient(120deg, #E8D9FF, #FFE0EA, #FFF0D1);
            border-radius: 40px;
            padding: 52px;
            overflow: hidden;
            position: relative;
        }

        .plus-box h2 {
            font-size: clamp(1.9rem, 3vw, 2.6rem);
            margin: 15px 0 20px;
        }

        .plus-box p {
            color: #71616B;
            font-size: .92rem;
            margin-bottom: 27px;
        }

        .plus-art {
            display: grid;
            place-items: center;
            min-height: 215px;
            border-radius: 50%;
            background: rgba(255,255,255,.35);
            font-size: 7rem;
            transform: rotate(10deg);
        }

        /* =========================
           FINAL CTA
           ========================= */

        .final-cta {
            padding: 75px 0;
        }

        .cta-card {
            position: relative;
            overflow: hidden;
            background: #B9D9AE;
            padding: 63px 30px;
            border-radius: 40px;
            text-align: center;
        }

        .cta-card::before,
        .cta-card::after {
            position: absolute;
            font-size: 150px;
            line-height: 1;
            opacity: .18;
            pointer-events: none;
        }

        .cta-card::before {
            content: '✿';
            left: 3%;
            top: 15%;
        }

        .cta-card::after {
            content: '✦';
            right: 5%;
            bottom: 5%;
        }

        .cta-card h2 {
            font-size: clamp(1.9rem, 3.2vw, 2.8rem);
            margin-bottom: 18px;
        }

        .cta-card p {
            max-width: 470px;
            margin: 0 auto 28px;
            color: #58704F;
            font-size: .9rem;
        }

        /* =========================
           FOOTER
           ========================= */

        .footer {
            background: #483B42;
            color: white;
            padding: 55px 0 22px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 55px;
        }

        .footer .logo {
            color: white;
        }

        .footer-brand p {
            color: #CFBDC7;
            font-size: .81rem;
            max-width: 330px;
            margin-top: 20px;
        }

        .footer h4 {
            font-family: 'DM Sans', sans-serif;
            font-size: .85rem;
            margin-bottom: 20px;
        }

        .footer-links {
            display: grid;
            gap: 13px;
        }

        .footer-links a {
            color: #CFBDC7;
            font-size: .81rem;
        }

        .footer-links a:hover {
            color: var(--pink);
        }

        .footer-bottom {
            padding-top: 25px;
            border-top: 1px solid rgba(255,255,255,.13);
            color: #B9A9B3;
            font-size: .78rem;
            text-align: center;
        }

        /* =========================
           RESPONSIVE
           ========================= */

        @media (max-width: 1000px) {
            .hero-grid {
                gap: 30px;
            }

            .hero h1 {
                font-size: clamp(2.4rem, 4.5vw, 3.2rem);
            }

            .journey-grid,
            .ai-box,
            .plus-box {
                gap: 35px;
            }
        }

        @media (max-width: 820px) {
            .nav-links {
                display: none;
            }

            .hero-grid,
            .journey-grid,
            .ai-box,
            .plus-box {
                grid-template-columns: 1fr;
            }

            .hero-visual {
                min-height: 430px;
                max-width: 520px;
                width: 100%;
                margin: auto;
            }

            .steps-grid,
            .course-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .feature-strip-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .container {
                width: min(100% - 32px, 1180px);
            }

            .nav-content {
                min-height: 68px;
            }

            .nav-actions .btn-outline {
                display: none;
            }

            .hero {
                padding: 55px 0 70px;
            }

            .hero h1 {
                font-size: clamp(2.25rem, 9vw, 2.8rem);
                line-height: 1.17;
            }

            .hero-desc {
                font-size: .88rem;
            }

            .hero-visual {
                min-height: 340px;
            }

            .photo-one {
                height: 185px;
            }

            .photo-two {
                top: 95px;
                height: 205px;
            }

            .photo-three {
                height: 125px;
            }

            .floating-note {
                padding: 9px 12px;
                font-size: .67rem;
            }

            .steps-grid,
            .course-grid,
            .feature-strip-grid,
            .footer-grid {
                grid-template-columns: 1fr;
            }

            .strip-item {
                justify-content: flex-start;
            }

            .how,
            .courses,
            .journey,
            .ai-section,
            .plus-section {
                padding: 65px 0;
            }

            .section-header h2 {
                font-size: 1.95rem;
            }

            .ai-box,
            .plus-box {
                padding: 28px 22px;
                border-radius: 26px;
            }

            .plus-art {
                min-height: 145px;
                font-size: 5rem;
            }

            .journey-art {
                min-height: 280px;
            }

            .cta-card {
                padding: 48px 22px;
                border-radius: 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVBAR
         ========================= -->

    <header class="navbar">
        <div class="container nav-content">

            <a href="{{ route('landing') }}" class="logo">
                <span class="logo-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M12 3C7.03 3 3 7.03 3 12s4.03 9 9 9c1.5 0 2.4-.9 2.4-2.1 0-.8-.3-1.2-.3-1.8 0-.8.6-1.3 1.4-1.3h1.2c2.6 0 4.3-1.8 4.3-4.3C21 6.78 17 3 12 3Z"/>
                        <circle cx="7.7" cy="11.3" r="1"/>
                        <circle cx="10.3" cy="7.5" r="1"/>
                        <circle cx="15" cy="7.7" r="1"/>
                        <circle cx="17.2" cy="11.5" r="1"/>
                    </svg>
                </span>
                HobbyNet
            </a>

            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="#how">Cara Kerja</a>
                <a href="#courses">Jelajah Hobi</a>
                <a href="#journey">Roadmap</a>
                <a href="{{ route('plus') }}">HobbyNet Plus</a>
            </nav>

            <div class="nav-actions">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                        Masuk
                    </a>
                @endguest

                <a href="{{ $startUrl }}" class="btn btn-primary btn-sm">
                    {{ $startLabel }} ↗
                </a>
            </div>

        </div>
    </header>

    <main>

        <!-- =========================
             HERO
             ========================= -->

        <section class="hero">
            <div class="container hero-grid">

                <div class="hero-content">

                    <span class="hero-badge">
                        Your creative journey starts here
                    </span>

                    <h1>
                        Hobi Baru,<br>
                        Cerita Baru,<br>
                        <span class="accent">Versi Baru Kamu.</span>
                    </h1>

                    <p class="hero-desc">
                        Nggak perlu jago untuk mulai berkarya.
                        Temukan hobi yang kamu suka, ikuti panduan
                        langkah demi langkah, dan rayakan setiap
                        hasil karya pertamamu bersama HobbyNet.
                    </p>

                    <div class="hero-actions">
                        <a href="{{ $startUrl }}" class="btn btn-primary">
                            {{ $startLabel }} ↗
                        </a>

                        <a href="#how" class="btn btn-outline">
                            Lihat Cara Kerja ↓
                        </a>
                    </div>

                    <div class="hero-note">
                        <span>
                            Mulai dari langkah kecil,
                            berkembang dengan caramu sendiri.
                        </span>
                    </div>

                </div>

                <div class="hero-visual"
                     aria-label="Berbagai aktivitas hobi kreatif">

                    <div class="visual-blob"></div>

                    <div class="hero-photo photo-one">
                        <img
                            src="{{ asset('images/landing/hero-1.png') }}"
                            alt="Aktivitas melukis dan berkarya"
                        >
                    </div>

                    <div class="hero-photo photo-two">
                        <img
                            src="{{ asset('images/landing/hero-2.png') }}"
                            alt="Aktivitas merajut"
                        >
                    </div>

                    <div class="hero-photo photo-three">
                        <img
                            src="{{ asset('images/landing/hero-3.png') }}"
                            alt="Kamera dan perlengkapan fotografi"
                        >
                    </div>

                </div>

            </div>
        </section>

        <!-- =========================
             FEATURE STRIP
             ========================= -->

        
<section class="feature-strip">
    <div class="container feature-strip-grid">

        <div class="strip-item">
            <span class="strip-icon">🌈</span>
            <span>Eksplorasi Berbagai Hobi</span>
        </div>

        <div class="strip-item">
            <span class="strip-icon">🗺️</span>
            <span>Panduan Bertahap</span>
        </div>

        <div class="strip-item">
            <span class="strip-icon">📸</span>
            <span>Portofolio Karya</span>
        </div>

        <div class="strip-item">
            <span class="strip-icon">🏆</span>
            <span>Lencana Pencapaian</span>
        </div>

    </div>
</section>


        <!-- =========================
             HOW IT WORKS
             ========================= -->

        <section class="how" id="how">
            <div class="container">

                <div class="section-header">
                    <span class="eyebrow">✦ HOW IT WORKS</span>

                    <h2>
                        Mulai Berkarya Itu
                        <span class="highlight-pink">Nggak Ribet!</span>
                    </h2>

                    <p>
                        Tiga langkah sederhana untuk memulai
                        perjalanan hobimu. Nggak harus sempurna,
                        yang penting berani mencoba.
                    </p>
                </div>

                <div class="steps-grid">

                    <article class="step-card">
                        <span class="step-number">STEP 01</span>
                        <div class="step-emoji">🔎</div>

                        <h3>Temukan Hobimu</h3>

                        <p>
                            Jelajahi berbagai aktivitas kreatif
                            dan pilih hobi yang paling bikin
                            kamu penasaran.
                        </p>
                    </article>

                    <article class="step-card">
                        <span class="step-number">STEP 02</span>
                        <div class="step-emoji">🧩</div>

                        <h3>Belajar Bertahap</h3>

                        <p>
                            Ikuti roadmap, siapkan bahan,
                            dan kerjakan tantangan kecil
                            satu per satu.
                        </p>
                    </article>

                    <article class="step-card">
                        <span class="step-number">STEP 03</span>
                        <div class="step-emoji">🏆</div>

                        <h3>Rayakan Karyamu</h3>

                        <p>
                            Unggah hasil karya, simpan di profil,
                            dan kumpulkan lencana pencapaianmu.
                        </p>
                    </article>

                </div>

            </div>
        </section>

        <!-- =========================
             EXPLORE COURSES
             ========================= -->

        <section class="courses" id="courses">
            <div class="container">

                <div class="section-header">
                    <span class="eyebrow">✦ EXPLORE YOUR PASSION</span>

                    <h2>
                        Banyak Hobi,
                        <span class="highlight-pink">Banyak Cerita.</span>
                    </h2>

                    <p>
                        Dari benang rajut sampai kamera,
                        setiap hobi punya keseruannya sendiri.
                    </p>
                </div>

                <div class="course-grid">

                    @foreach ($courses as $course)

                        <a href="{{ $exploreUrl }}" class="course-card">

                            <div class="course-image">
                                <img
                                    src="{{ asset($course['image']) }}"
                                    alt="{{ $course['title'] }}"
                                    loading="lazy"
                                >

                                <span class="course-label">
                                    {{ $course['icon'] }}
                                    {{ $course['label'] }}
                                </span>
                            </div>

                            <div class="course-body">
                                <span class="course-category">
                                    {{ $course['category'] }}
                                </span>

                                <h3>{{ $course['title'] }}</h3>

                                <p>{{ $course['description'] }}</p>

                                <div class="course-footer">
                                    <span>Jelajahi Course</span>
                                    <span>Mulai ↗</span>
                                </div>
                            </div>

                        </a>

                    @endforeach

                </div>

                <div style="text-align:center;margin-top:42px">
                    <a href="{{ $exploreUrl }}" class="btn btn-dark">
                        Lihat Semua Hobi ↗
                    </a>
                </div>

            </div>
        </section>

        <!-- =========================
             CREATIVE JOURNEY
             ========================= -->

        <section class="journey" id="journey">
            <div class="container journey-grid">

                <div class="journey-art">
                    <img
                        src="{{ asset('images/landing/win-1.png') }}"
                        alt="Ilustrasi karya kreatif"
                        loading="lazy"
                    >
                </div>

                <div class="journey-content">

                    <span class="eyebrow">✦ YOUR CREATIVE JOURNEY</span>

                    <h2>
                        Setiap Langkah
                        <span class="highlight-green">Punya Arti.</span>
                    </h2>

                    <p>
                        HobbyNet bukan cuma tempat mencari tutorial.
                        Kami membantu kamu membangun kebiasaan belajar
                        sampai menghasilkan karya yang bisa dibanggakan.
                    </p>

                    <div class="journey-list">

                        <div class="journey-item">
                            <span class="journey-icon">🗺️</span>

                            <div>
                                <strong>Roadmap yang Jelas</strong>
                                <p>
                                    Belajar lewat langkah yang terstruktur,
                                    dari pemula sampai menyelesaikan proyek.
                                </p>
                            </div>
                        </div>

                        <div class="journey-item">
                            <span class="journey-icon">📸</span>

                            <div>
                                <strong>Portofolio Karyamu</strong>
                                <p>
                                    Simpan foto, judul, dan cerita dari
                                    setiap karya yang kamu selesaikan.
                                </p>
                            </div>
                        </div>

                        <div class="journey-item">
                            <span class="journey-icon">🏅</span>

                            <div>
                                <strong>Lencana Pencapaian</strong>
                                <p>
                                    Rayakan progres belajarmu dengan
                                    lencana yang bisa dikumpulkan.
                                </p>
                            </div>
                        </div>

                    </div>

                    <div style="margin-top:32px">
                        <a href="{{ $startUrl }}" class="btn btn-primary">
                            Mulai Perjalananmu ↗
                        </a>
                    </div>

                </div>

            </div>
        </section>

        <!-- =========================
             AI COACH
             ========================= -->

        
<section class="ai-section" id="showcase">
    <div class="container">
        <div class="ai-box">

            <div class="ai-content">
                <span class="eyebrow">
                    ✦ SHOW YOUR CREATION
                </span>

                <h2>
                    Dari Belajar,
                    <span class="highlight-pink">
                        Jadi Karya Nyata.
                    </span>
                </h2>

                <p>
                    Selesaikan course, abadikan hasil
                    karyamu, dan simpan setiap pencapaian
                    di portofolio HobbyNet.

                    Setiap karya punya cerita,
                    dan setiap perjalanan layak dirayakan.
                </p>

                <a href="{{ $startUrl }}" class="btn btn-dark">
                    Mulai Berkarya ↗
                </a>
            </div>

            <div class="ai-demo"
                 style="background:#F7F0FF;
                        padding:22px;
                        border-radius:28px;">

                <div style="border-radius:20px;overflow:hidden;">
                    <img
                        src="{{ asset('images/landing/win-1.png') }}"
                        alt="Contoh karya kreatif HobbyNet"
                        style="width:100%;max-height:280px;object-fit:cover;"
                        loading="lazy"
                    >
                </div>

                <div style="padding:18px 4px 0;">
                    <span class="eyebrow">✦ CREATIVE PORTFOLIO</span>

                    <h3 style="font-size:1.3rem;margin:8px 0;">
                        Karya Pertamamu Dimulai di Sini
                    </h3>

                    <p style="font-size:.85rem;color:#81737B;">
                        Belajar, berkreasi, unggah karya,
                        dan kumpulkan lencana.
                    </p>
                </div>

            </div>

        </div>
    </div>
</section>


        <!-- =========================
             HOBBYNET PLUS
             ========================= -->

        <section class="plus-section">
            <div class="container">

                <div class="plus-box">

                    <div>
                        <span class="eyebrow">✦ HOBBYNET PLUS</span>

                        <h2>
                            Your Passion,
                            <br>
                            <span class="highlight-pink">
                                Without Limits.
                            </span>
                        </h2>

                        <p>
                            Ingin eksplorasi lebih jauh?
                            HobbyNet Plus membuka akses ke
                            course premium dan pengalaman
                            belajar yang lebih lengkap.
                        </p>

                        <a href="{{ route('plus') }}" class="btn btn-dark">
                            Jelajahi HobbyNet Plus ↗
                        </a>
                    </div>

                    <div class="plus-art" aria-hidden="true">
                        👑
                    </div>

                </div>

            </div>
        </section>

        <!-- =========================
             FINAL CALL TO ACTION
             ========================= -->

        <section class="final-cta">
            <div class="container">

                <div class="cta-card">

                    <span class="eyebrow" style="color:#50774B">
                        ✦ IT'S YOUR TURN
                    </span>

                    <h2>
                        Karya Pertamamu
                        <br>
                        Menunggu untuk Dibuat!
                    </h2>

                    <p>
                        Nggak perlu menunggu sampai jago.
                        Setiap kreator hebat pernah menjadi pemula.
                        Yuk, mulai cerita hobimu hari ini!
                    </p>

                    <a href="{{ $startUrl }}" class="btn btn-white">
                        {{ $startLabel }} 
                    </a>

                </div>

            </div>
        </section>

    </main>

    <!-- =========================
         FOOTER
         ========================= -->

    <footer class="footer">
        <div class="container">

            <div class="footer-grid">

                <div class="footer-brand">
                    <a href="{{ route('landing') }}" class="logo">
                        <span class="logo-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="1.8"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M12 3C7.03 3 3 7.03 3 12s4.03 9 9 9c1.5 0 2.4-.9 2.4-2.1 0-.8-.3-1.2-.3-1.8 0-.8.6-1.3 1.4-1.3h1.2c2.6 0 4.3-1.8 4.3-4.3C21 6.78 17 3 12 3Z"/>
                                <circle cx="7.7" cy="11.3" r="1"/>
                                <circle cx="10.3" cy="7.5" r="1"/>
                                <circle cx="15" cy="7.7" r="1"/>
                                <circle cx="17.2" cy="11.5" r="1"/>
                            </svg>
                        </span>
                        HobbyNet
                    </a>

                    <p>
                        Tempat untuk menemukan passion,
                        mengeksplorasi ide, dan merayakan
                        setiap karya kecil yang kamu buat.
                    </p>
                </div>

                <div>
                    <h4>Explore</h4>

                    <div class="footer-links">
                        <a href="#courses">Jelajah Hobi</a>
                        <a href="#how">Cara Kerja</a>
                        <a href="#journey">Creative Journey</a>
                        <a href="{{ route('plus') }}">HobbyNet Plus</a>
                    </div>
                </div>

                <div>
                    <h4>Account</h4>

                    <div class="footer-links">
                        @auth
                            <a href="{{ route('home') }}">Beranda</a>
                            <a href="{{ route('profile') }}">Profil</a>
                            <a href="{{ route('explore') }}">Jelajah</a>
                        @else
                            <a href="{{ route('login') }}">Masuk</a>
                            <a href="{{ route('onboarding.account') }}">
                                Daftar Gratis
                            </a>
                        @endauth
                    </div>
                </div>

            </div>

            <div class="footer-bottom">
                © {{ date('Y') }} HobbyNet.
                Made with ♡ for every creative soul.
            </div>

        </div>
    </footer>

</body>
</html>
