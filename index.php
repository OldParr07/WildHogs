<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WildHogs MC Klubb - Din Lokala Motorcykelklubb</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/nav.php'; ?>
    
    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h1>🏍️ WildHogs MC Klubb</h1>
                <p class="hero-tagline">En familj av motorcykelälskare och vildsvinsentusiaster</p>
                <div class="hero-buttons">
                    <a href="webshop.php" class="btn-primary">Besök Webbshop</a>
                    <a href="#about" class="btn-secondary">Läs Mer Om Oss</a>
                </div>
            </div>
        </section>
        
        <!-- Features Section -->
        <section id="about" class="features-section">
            <div class="container">
                <h2>Om WildHogs</h2>
            </div>
            <div class="features-scroll">
                <div class="feature-card">
                    <h3>🏍️ Passion för MC</h3>
                    <p>Vi är en grupp dedikerade motorcykelpojkar som älskar vägarna, vitskorna och det fria livet på två hjul. Motorcykling är inte bara transport - det är en livsstil!</p>
                </div>
                <div class="feature-card">
                    <h3>🐗 Vildsvin Tradition</h3>
                    <p>Vildsvin är vår symbol - starkt, frimodigt och obetäckt. Precis som vi och våra maskiner. Vi bär vildsvin-traditioner med stolthet!</p>
                </div>
                <div class="feature-card">
                    <h3>👥 Samhälle & Familj</h3>
                    <p>Vi ser oss själva som en familj. Tillsammans är vi starkare och ha roligare på vägen. Varje medlem är viktig för klubben.</p>
                </div>
                <div class="feature-card">
                    <h3>🛣️ Vägäventyr</h3>
                    <p>Vi älskar att utforska nya vägar tillsammans. Från de svenska fjällen till kusterna - äventyret väntar alltid runt nästa kurva!</p>
                </div>
                <div class="feature-card">
                    <h3>🛍️ Webbshop</h3>
                    <p>Köp officiell WildHogs merch, läderutrustning och motorcykelkläder direkt från klubben. Visa dina MC-kollegor vilken klubb du tillhör!</p>
                </div>
                <div class="feature-card">
                    <h3>⚙️ Mekanik & Teknik</h3>
                    <p>Vi hjälps åt med reparationer och underhåll. Från enkla tjänster till större byggprojekt - tillsammans fixar vi allt!</p>
                </div>
                <div class="feature-card">
                    <h3>🔧 Supportörer</h3>
                    <p>Vi är en MC-klubb för män och kvinnor som älskar motorcyklar och äventyret. Alla är välkomna - erfarenhet är inte ett krav!</p>
                </div>
                <div class="feature-card">
                    <h3>🌍 Nätverk</h3>
                    <p>Vi är del av ett större MC-nätverk. Genom WildHogs träffar du MC-entusiaster från hela Värmland!</p>
                </div>
            </div>
        </section>
        
        <!-- News/Events Section -->
        <section class="news-section">
            <div class="container">
                <h2>Kommande Händelser</h2>
            </div>
            <div class="news-scroll">
                <div class="news-card">
                    <h3>🏁 Vårrundan 2025</h3>
                    <p><strong>April 20-22</strong> - Två dagar med öppna vägar och bra sällskap. Vi samlas vid Backgården och åker genom Värmlands vackraste vägar.</p>
                </div>
                <div class="news-card">
                    <h3>🍖 Grillfest</h3>
                    <p><strong>Juni 15</strong> - Klubbgrillfest med grillat vildsvin, kall öl och motorcykelsamtal fram till midnatt vid klubbhuset.</p>
                </div>
                <div class="news-card">
                    <h3>☀️ Sommarfest</h3>
                    <p><strong>Juni 28-29</strong> - Två dagars festivaler med live-musik, bra mat och trevligt sällskap. Bästa MC-festen på året!</p>
                </div>
                <div class="news-card">
                    <h3>🏖️ Längdtur till Östersjön</h3>
                    <p><strong>Juli 5-12</strong> - En veckas äventyrsresa längs västkusten. Vi åker tillsammans och stannar på olika MC-vänliga ställen.</p>
                </div>
                <div class="news-card">
                    <h3>🌲 Höstträff i Norr</h3>
                    <p><strong>September 7</strong> - Långweekend i norr. Varma täcken, brasa och de bästa vägar du kan föreställa dig. Perfekt höstväder!</p>
                </div>
                <div class="news-card">
                    <h3>🏁 Värmland MC Rally</h3>
                    <p><strong>September 20-21</strong> - Stort MC-möte med klubbar från hela Sverige. Tävlingar, utställningar och fest!</p>
                </div>
                <div class="news-card">
                    <h3>🎃 Höstgrill</h3>
                    <p><strong>Oktober 18</strong> - Traditionell höstgrill innan vinterförhållandena kommer. Varma drycker och mysig stämning.</p>
                </div>
                <div class="news-card">
                    <h3>❄️ Vinterförberedelser</h3>
                    <p><strong>November 1-30</strong> - Gemensamt servicing av motorcyklarna. Vi hjälps åt med vinterparkering och underhållsschemaläggning.</p>
                </div>
                <div class="news-card">
                    <h3>🎄 Julbord</h3>
                    <p><strong>December 14</strong> - Klassiskt julbord och julfest för alla MC-fans. Två högsta gången och mysig stämning!</p>
                </div>
            </div>
        </section>
        
        <!-- WebShop Preview -->
        <section class="container webshop-preview">
            <h2>Aktuell Merch</h2>
            <div class="products-preview">
                <?php
                $stmt = $pdo->query("SELECT * FROM products LIMIT 3");
                $preview_products = $stmt->fetchAll();
                foreach ($preview_products as $product):
                ?>
                    <div class="product-preview" tabindex="0">
                        <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                        <p><?php echo number_format($product['price'], 2, ',', ' '); ?> kr</p>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="webshop.php" class="btn-primary" style="display: inline-block; margin-top: 20px;">Se Allt I Webbshop</a>
        </section>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Infinite, seamless scroll loop for both directions.
        function setupInfiniteScroll(containerId) {
            const container = document.querySelector('.' + containerId);
            if (!container) return;

            const originalCards = Array.from(container.querySelectorAll('[class$="-card"]'));
            if (originalCards.length === 0) return;

            // Append a copy and prepend a copy so we can loop seamlessly both directions
            originalCards.forEach(c => container.appendChild(c.cloneNode(true)));
            for (let i = originalCards.length - 1; i >= 0; i--) {
                container.insertBefore(originalCards[i].cloneNode(true), container.firstChild);
            }

            // Calculate card width including gap between cards
            const first = container.querySelector('[class$="-card"]');
            const second = container.querySelectorAll('[class$="-card"]')[1];
            const r1 = first.getBoundingClientRect();
            const r2 = second.getBoundingClientRect();
            const gap = Math.max(0, r2.left - (r1.left + r1.width));
            const cardWidth = r1.width + gap;

            const originalCount = originalCards.length;
            const originalWidth = cardWidth * originalCount;

            // Start in the middle copy so user can scroll both ways
            container.scrollLeft = originalWidth;

            // On scroll, jump by originalWidth when crossing boundaries (instant reset)
            container.addEventListener('scroll', () => {
                if (container.scrollLeft >= originalWidth * 2) {
                    container.scrollLeft -= originalWidth;
                } else if (container.scrollLeft <= 0) {
                    container.scrollLeft += originalWidth;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupInfiniteScroll('features-scroll');
            setupInfiniteScroll('news-scroll');
        });
    </script>
</body>
</html>