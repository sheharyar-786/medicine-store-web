<?php
$pageTitle = 'Online Medicine Delivery in Lahore';
$pageStyles = ['home']; // Reuse clean home styling
include 'includes/db_connect.php';
include 'includes/header.php';
?>

<!-- JSON-LD Local SEO Schema Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Pharmacy",
  "name": "HealthCare Store",
  "image": "<?php echo 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . basePath(); ?>/assets/images/logo.png",
  "@id": "<?php echo 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . basePath(); ?>",
  "url": "<?php echo 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . basePath(); ?>",
  "telephone": "+923001234567",
  "priceRange": "$$",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "123 Main Boulevard, Gulberg III",
    "addressLocality": "Lahore",
    "addressRegion": "Punjab",
    "postalCode": "54600",
    "addressCountry": "PK"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 31.5204,
    "longitude": 74.3587
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday",
      "Saturday",
      "Sunday"
    ],
    "opens": "08:00",
    "closes": "23:00"
  },
  "sameAs": [
    "https://www.facebook.com/healthcarestorepk",
    "https://www.instagram.com/healthcarestorepk"
  ],
  "areaServed": [
    {
      "@type": "AdministrativeArea",
      "name": "Gulberg, Lahore"
    },
    {
      "@type": "AdministrativeArea",
      "name": "DHA, Lahore"
    },
    {
      "@type": "AdministrativeArea",
      "name": "Johar Town, Lahore"
    },
    {
      "@type": "AdministrativeArea",
      "name": "Model Town, Lahore"
    }
  ]
}
</script>

<main class="container" style="margin-top: 40px; margin-bottom: 60px;">
    <!-- Hero / Title Section -->
    <div style="background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: white; padding: 60px 40px; border-radius: var(--radius-lg); text-align: center; margin-bottom: 40px; box-shadow: var(--shadow-lg);">
        <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 16px;">Online Medicine Delivery in Lahore</h1>
        <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto; opacity: 0.9;">
            Get 100% genuine prescription (Rx) and over-the-counter (OTC) medicines delivered directly to your doorstep in Lahore.
        </p>
        <div style="margin-top: 30px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="shop.php" class="btn" style="background-color: var(--accent); border-color: var(--accent); color: white; font-weight: 600;">Shop Now <i class="fas fa-shopping-bag"></i></a>
            <?php
            $waUrl = getWhatsAppLink("Hello HealthCare Store! I am in Lahore and want to place a quick medicine order.");
            ?>
            <a href="<?php echo $waUrl; ?>" target="_blank" class="btn" style="background-color: #25D366; border-color: #25D366; color: white; font-weight: 600;">
                <i class="fab fa-whatsapp"></i> Order via WhatsApp
            </a>
        </div>
    </div>

    <!-- Local Coverage & Details Section -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;">
        <div>
            <h2 style="font-size: 1.6rem; font-weight: 700; color: var(--dark); margin-bottom: 16px;">Your Trusted Local Pharmacy Partner</h2>
            <p style="margin-bottom: 24px; color: var(--text); line-height: 1.7;">
                Welcome to HealthCare Store, Lahore's leading digital health provider. Our physical store is located centrally in <strong>Gulberg III, Lahore</strong>, and we operate an advanced online fulfillment network servicing major residential and commercial sectors across the city.
            </p>

            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--dark); margin-bottom: 12px;">Coverage Areas in Lahore</h3>
            <p style="margin-bottom: 16px; color: var(--text); line-height: 1.7;">
                We offer rapid home delivery within 2 hours in the following neighborhoods:
            </p>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 30px;">
                <div style="background: white; padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <span>Gulberg (I, II, III, IV, V)</span>
                </div>
                <div style="background: white; padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <span>DHA (Phases 1 to 8)</span>
                </div>
                <div style="background: white; padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <span>Johar Town & PIA Society</span>
                </div>
                <div style="background: white; padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <span>Model Town & Garden Town</span>
                </div>
                <div style="background: white; padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <span>Faisal Town & Township</span>
                </div>
                <div style="background: white; padding: 12px 18px; border-radius: var(--radius); border: 1px solid var(--border); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <span>Cavalry Ground & Cantt</span>
                </div>
            </div>

            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--dark); margin-bottom: 12px;">Click & Collect (Pickup)</h3>
            <p style="margin-bottom: 24px; color: var(--text); line-height: 1.7;">
                Prefer to skip the delivery wait? Choose <strong>Click & Collect (In-Store Pickup)</strong> during checkout. Your package will be audited, verified, and sealed by our certified pharmacist, ready for pickup at our Gulberg outlet in just <strong>30 minutes</strong>.
            </p>
        </div>

        <!-- Sidebar Store Info -->
        <div style="background: white; border-radius: var(--radius); padding: 24px; box-shadow: var(--shadow); border-top: 4px solid var(--accent);">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-store-alt" style="color: var(--accent);"></i> Store Location
            </h3>
            <div style="font-size: 0.9rem; line-height: 1.6; display: flex; flex-direction: column; gap: 12px;">
                <p>
                    <strong>Address:</strong><br>
                    123 Main Boulevard, Gulberg III,<br>
                    Lahore, Punjab, Pakistan
                </p>
                <p>
                    <strong>Operating Hours:</strong><br>
                    Monday – Sunday: 8:00 AM – 11:00 PM
                </p>
                <p>
                    <strong>Phone Support:</strong><br>
                    +92 300 1234567
                </p>
                <p>
                    <strong>Email:</strong><br>
                    lahore@healthcarestore.pk
                </p>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
