<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>São Carlos | Linguiça Mineira Artesanal</title>
    <meta name="description" content="Linguiça mineira artesanal com entrega em todo o estado do Rio de Janeiro.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --cream: #f8f3ea;
            --sand: #efe4d3;
            --charcoal: #27241f;
            --green: #00683f;
            --deep-green: #004a2f;
            --terracotta: #c06349;
            --wine: #5d2925;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: "Nunito", sans-serif;
            color: var(--charcoal);
            background: radial-gradient(circle at 80% 10%, #fef9ef 0%, #f4ebdd 40%, #eee0cc 100%);
            scroll-behavior: smooth;
        }

        img {
            display: block;
            max-width: 100%;
            height: auto;
        }

        .container {
            width: min(1120px, calc(100% - 2rem));
            margin-inline: auto;
        }

        .hero {
            min-height: 96vh;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
            padding: 2rem 0 4rem;
        }

        .hero::before,
        .hero::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(2px);
            pointer-events: none;
        }

        .hero::before {
            width: 460px;
            height: 460px;
            background: linear-gradient(180deg, #d1886f 0%, #b95f47 100%);
            top: -200px;
            right: -120px;
            opacity: 0.25;
            animation: drift 10s ease-in-out infinite alternate;
        }

        .hero::after {
            width: 360px;
            height: 360px;
            background: linear-gradient(180deg, #267757 0%, #0d5f40 100%);
            bottom: -160px;
            left: -100px;
            opacity: 0.22;
            animation: drift 8s ease-in-out infinite alternate-reverse;
        }

        .hero-grid {
            display: grid;
            gap: 2.2rem;
            align-items: center;
            grid-template-columns: 1fr;
        }

        .logo-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(39, 36, 31, 0.12);
            border-radius: 999px;
            padding: 0.45rem 0.9rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            font-size: 0.9rem;
            backdrop-filter: blur(5px);
            animation: rise 0.8s ease forwards;
        }

        .logo-dot {
            width: 9px;
            height: 9px;
            background: var(--green);
            border-radius: 999px;
        }

        h1 {
            margin: 1rem 0 0.9rem;
            font-family: "Bebas Neue", sans-serif;
            font-size: clamp(2.3rem, 9vw, 5.6rem);
            line-height: 0.95;
            letter-spacing: 0.03em;
            color: #1f1b16;
            text-wrap: balance;
            animation: rise 0.95s ease forwards;
        }

        .headline-emphasis {
            color: var(--green);
        }

        .hero p {
            margin: 0;
            max-width: 58ch;
            font-size: clamp(1rem, 2.5vw, 1.22rem);
            line-height: 1.55;
            color: #413a33;
            animation: rise 1.1s ease forwards;
        }

        .cta-row {
            margin-top: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            animation: rise 1.25s ease forwards;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.25rem;
            border-radius: 0.8rem;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--green), var(--deep-green));
            color: var(--white);
            box-shadow: 0 14px 28px rgba(0, 74, 47, 0.28);
        }

        .btn-secondary {
            border: 1px solid rgba(39, 36, 31, 0.2);
            color: var(--charcoal);
            background: rgba(255, 255, 255, 0.75);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(39, 36, 31, 0.12);
            border-radius: 1.4rem;
            padding: 1rem;
            backdrop-filter: blur(4px);
            box-shadow: 0 16px 35px rgba(35, 31, 24, 0.13);
            animation: rise 1.1s ease forwards;
        }

        .hero-card-grid {
            display: grid;
            gap: 0.9rem;
            grid-template-columns: 1fr;
        }

        .hero-photo {
            border-radius: 1.1rem;
            overflow: hidden;
            min-height: 230px;
            background: #ebe0cf;
        }

        .hero-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .pill {
            padding: 0.5rem 0.75rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.82rem;
            background: #f6efe3;
            border: 1px solid rgba(39, 36, 31, 0.12);
        }

        .logo-center {
            width: min(310px, 88%);
            margin-inline: auto;
            border-radius: 999px;
            border: 10px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
        }

        section {
            padding: 4.2rem 0;
        }

        .features {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.66) 0%, rgba(245, 236, 220, 0.86) 100%);
            border-top: 1px solid rgba(39, 36, 31, 0.08);
            border-bottom: 1px solid rgba(39, 36, 31, 0.08);
        }

        .section-title {
            margin: 0 0 0.45rem;
            font-family: "Bebas Neue", sans-serif;
            letter-spacing: 0.04em;
            font-size: clamp(2rem, 4.5vw, 3.2rem);
            color: #1f1a15;
            text-align: center;
        }

        .section-sub {
            margin: 0 auto 2rem;
            text-align: center;
            max-width: 62ch;
            color: #4b433b;
        }

        .cards {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .feature-card {
            background: var(--white);
            border: 1px solid rgba(39, 36, 31, 0.1);
            border-radius: 1rem;
            padding: 1.15rem;
            box-shadow: 0 8px 20px rgba(37, 30, 21, 0.07);
        }

        .feature-card h3 {
            margin: 0 0 0.45rem;
            color: var(--deep-green);
        }

        .feature-card p {
            margin: 0;
            color: #564d45;
            line-height: 1.55;
        }

        .delivery {
            background: linear-gradient(130deg, #174f38 0%, #0f3d2d 65%, #082b20 100%);
            color: #fbf5ea;
        }

        .delivery-grid {
            display: grid;
            gap: 1.2rem;
            grid-template-columns: 1fr;
            align-items: center;
        }

        .delivery h2 {
            margin: 0 0 0.6rem;
            font-family: "Bebas Neue", sans-serif;
            letter-spacing: 0.03em;
            font-size: clamp(2rem, 4.5vw, 3.3rem);
            line-height: 0.95;
        }

        .delivery p {
            margin: 0;
            color: rgba(251, 245, 234, 0.9);
            line-height: 1.6;
        }

        .delivery-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1rem;
        }

        .delivery-list {
            display: grid;
            gap: 0.5rem;
            margin-top: 0.8rem;
        }

        .delivery-item {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 0.8rem;
            padding: 0.55rem 0.75rem;
            font-weight: 700;
        }

        .floating-contact {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 100;
        }

        .floating-contact a {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #1f8f4e;
            color: white;
            text-decoration: none;
            border-radius: 999px;
            padding: 0.8rem 1rem;
            font-weight: 800;
            box-shadow: 0 12px 20px rgba(11, 54, 31, 0.35);
        }

        footer {
            padding: 2rem 0 2.8rem;
            text-align: center;
            color: #5f564f;
            font-size: 0.95rem;
        }

        @media (min-width: 900px) {
            .hero-grid {
                grid-template-columns: 1.05fr 0.95fr;
            }

            .hero-card-grid {
                grid-template-columns: 1.1fr 0.9fr;
            }

            .delivery-grid {
                grid-template-columns: 1.2fr 0.8fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes drift {
            from {
                transform: translate3d(0, 0, 0);
            }

            to {
                transform: translate3d(-18px, 16px, 0);
            }
        }
    </style>
</head>
<body>
    <header class="hero" id="inicio">
        <div class="container hero-grid">
            <div>
                <div class="logo-chip">
                    <span class="logo-dot"></span>
                    Nova Iguaçu - RJ | Frigorífico Artesanal
                </div>
                <h1>
                    Linguiça mineira artesanal <span class="headline-emphasis">de verdade</span>,
                    da nossa fábrica para a sua mesa.
                </h1>
                <p>
                    Sabor caseiro, tempero equilibrado e qualidade em cada gomo.
                    Atendemos varejo e atacado com entrega rápida em todo o estado do Rio de Janeiro.
                </p>
                <div class="cta-row">
                    <a class="btn btn-primary" href="https://wa.me/5521964178687" target="_blank" rel="noopener">Pedir pelo WhatsApp</a>
                    <a class="btn btn-secondary" href="#entrega">Ver áreas de entrega</a>
                </div>
            </div>

            <div class="hero-card">
                <div class="hero-card-grid">
                    <div class="hero-photo">
                        <img src="/images/linguica-ilustrativa.avif" alt="Linguiça artesanal Nova Iguaçu" loading="eager" onerror="this.onerror=null;this.src='/images/logo.jpg';">
                    </div>
                    <div>
                        <img class="logo-center" src="/images/logo.jpg" alt="Logo Nova Iguaçu" loading="eager">
                        <div class="info-pills" style="margin-top: 0.9rem;">
                            <span class="pill">100% artesanal</span>
                            <span class="pill">Selo de qualidade</span>
                            <span class="pill">Entrega no RJ inteiro</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="features" id="produtos">
        <div class="container">
            <h2 class="section-title">Nosso Diferencial</h2>
            <p class="section-sub">
                Produzimos linguiça mineira com processo artesanal, padrão de higiene rigoroso e seleção de carnes de alta qualidade.
            </p>

            <div class="cards">
                <article class="feature-card">
                    <h3>Receita Tradicional</h3>
                    <p>Tempero equilibrado, textura suculenta e sabor marcante inspirado na tradição mineira.</p>
                </article>
                <article class="feature-card">
                    <h3>Fabrico Diário</h3>
                    <p>Lotes frescos preparados diariamente para manter padrão e consistência em cada entrega.</p>
                </article>
                <article class="feature-card">
                    <h3>Atendimento B2B e B2C</h3>
                    <p>Atendemos restaurantes, mercados, casas de carnes e consumidores finais no mesmo nível de cuidado.</p>
                </article>
                <article class="feature-card">
                    <h3>Logística Inteligente</h3>
                    <p>Roteiros de entrega organizados para chegar rápido em todas as regiões do estado do Rio de Janeiro.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="delivery" id="entrega">
        <div class="container delivery-grid">
            <div>
                <h2>Entrega em Todo o Estado do Rio de Janeiro</h2>
                <p>
                    Capital, baixada, região serrana, região dos lagos, sul fluminense e norte fluminense.
                    Seu pedido sai com agilidade e com acondicionamento adequado para preservar qualidade e sabor.
                </p>
            </div>
            <aside class="delivery-box">
                <strong>Regiões com cobertura</strong>
                <div class="delivery-list">
                    <div class="delivery-item">Rio Capital e Zona Oeste</div>
                    <div class="delivery-item">Niterói e São Gonçalo</div>
                    <div class="delivery-item">Baixada Fluminense</div>
                    <div class="delivery-item">Região Serrana</div>
                    <div class="delivery-item">Região dos Lagos</div>
                    <div class="delivery-item">Sul e Norte Fluminense</div>
                </div>
            </aside>
        </div>
    </section>

    <footer>
        Nova Iguaçu - RJ | Produtos cárneos artesanais | Contato comercial: (21) 96417-8687
    </footer>

    <div class="floating-contact">
        <a href="https://wa.me/5521964178687" target="_blank" rel="noopener">WhatsApp Comercial</a>
    </div>
</body>
</html>
