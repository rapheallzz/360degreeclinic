<?php
require 'config.php';

$company = read_json('data/company_info.json');
if (empty($company)) {
    $company = ['company_name' => 'Default Company', 'logo_path' => 'placeholder.jpg', 'address' => ''];
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$users = read_json('data/users.json');
$user = array_filter($users, fn($u) => $u['id'] == $user_id);
if (empty($user)) {
    die("User not found.");
}
$user = $user[array_key_first($user)];

$whatsapp_number = $user['whatsapp_number'] ?? $user['whatsapp'] ?? '';
$whatsapp_link = '';
if (!empty($whatsapp_number)) {
    $whatsapp_number = preg_replace('/[^0-9+]/', '', $whatsapp_number);
    $whatsapp_link = "https://wa.me/" . $whatsapp_number;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['full_name']); ?> | <?php echo htmlspecialchars($company['company_name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo isset($_SESSION['role']) ? 'light-mode' : 'dark-mode'; ?>">
    <div class="theme-toggle">
        <input type="checkbox" id="theme-switch" class="theme-switch">
        <label for="theme-switch" class="theme-icon">
            <i class="fas fa-moon"></i>
            <i class="fas fa-sun"></i>
        </label>
    </div>

    <input type="checkbox" id="menu-toggle" class="menu-toggle">
    <label for="menu-toggle" class="menu-trigger">
        <i class="fas fa-bars"></i>
    </label>

    <nav class="sidebar">
        <div class="logo">
            <img src="Uploads/<?php echo htmlspecialchars($company['logo_path'] ?: 'placeholder.jpg'); ?>" alt="Company Logo" class="logo-img">
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#works">Works</a></li>
            <li><a href="#blog">Blog</a></li>
            <li><a href="#contact">Contact</a></li>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']) { ?>
                <li><a href="profile.php">Edit Profile</a></li>
            <?php } ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php } ?>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <li><a href="logout.php">Logout</a></li>
            <?php } else { ?>
                <li><a href="login.php">Login</a></li>
            <?php } ?>
        </ul>
    </nav>

    <main class="main-content">
        <section class="profile-section">
            <div class="profile-header">
                <div class="profile-photo">
                    <img src="Uploads/<?php echo htmlspecialchars($user['profile_photo'] ?: 'placeholder.jpg'); ?>" alt="Staff Photo" class="profile-img">
                    <div class="photo-overlay"></div>
                </div>
                <div class="profile-intro">
                    <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <p class="job-title"><?php echo htmlspecialchars($user['job_title']); ?></p>
                    <p class="company-name"><?php echo htmlspecialchars($company['company_name']); ?></p>
                    <div class="social-links">
                        <a href="<?php echo htmlspecialchars($user['social_links']['twitter']); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo htmlspecialchars($user['social_links']['linkedin']); ?>" target="_blank"><i class="fab fa-linkedin"></i></a>
                        <a href="<?php echo htmlspecialchars($user['social_links']['github']); ?>" target="_blank"><i class="fab fa-github"></i></a>
                        <a href="<?php echo htmlspecialchars($user['social_links']['facebook']); ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                        <a href="<?php echo htmlspecialchars($user['social_links']['instagram']); ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div class="contact-links">
                        <?php if (!empty($whatsapp_link)) { ?>
                            <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <?php } else { ?>
                            <a href="#" class="disabled"><i class="fab fa-whatsapp"></i></a>
                        <?php } ?>
                        <a href="mailto:<?php echo htmlspecialchars($user['email'] ?? ''); ?>"><i class="fas fa-envelope"></i></a>
                        <a href="tel:<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>"><i class="fas fa-phone"></i></a>
                        <button class="save-contact-btn" onclick="saveContact()">Save Contact</button>
                    </div>
                </div>
            </div>
            <div class="tabbed-content">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="about">About</button>
                    <button class="tab-btn" data-tab="qualifications">Qualifications</button>
                    <button class="tab-btn" data-tab="experience">Experience</button>
                    <button class="tab-btn" data-tab="certifications">Certifications</button>
                </div>
                <div class="tab-content active" id="about">
                    <p><?php echo htmlspecialchars($user['about']); ?></p>
                </div>
                <div class="tab-content" id="qualifications">
                    <ul class="content-list">
                        <?php foreach ($user['qualifications'] as $qualification) { ?>
                            <li><?php echo htmlspecialchars($qualification); ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="tab-content" id="experience">
                    <ul class="content-list">
                        <?php foreach ($user['experience'] as $exp) { ?>
                            <li><?php echo htmlspecialchars($exp); ?></li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="tab-content" id="certifications">
                    <ul class="content-list">
                        <?php foreach ($user['certifications'] as $cert) { ?>
                            <li><?php echo htmlspecialchars($cert); ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <?php if (!empty($whatsapp_link)) { ?>
        <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" class="whatsapp-float">
            <i class="fab fa-whatsapp"></i>
        </a>
    <?php } ?>

    <?php include 'footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script>
        const themeSwitch = document.getElementById("theme-switch");
        themeSwitch.addEventListener("change", () => {
            document.body.classList.toggle("dark-mode");
            document.body.classList.toggle("light-mode");
        });

        const tabs = document.querySelectorAll(".tab-btn");
        const contents = document.querySelectorAll(".tab-content");

        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                tabs.forEach(t => t.classList.remove("active"));
                contents.forEach(c => c.classList.remove("active"));
                tab.classList.add("active");
                document.getElementById(tab.dataset.tab).classList.add("active");
                gsap.from(`#${tab.dataset.tab}`, { opacity: 0, x: 20, duration: 0.5, ease: "power2.out" });
            });
        });

        gsap.from(".profile-header, .tabbed-content", {
            opacity: 0,
            y: 50,
            stagger: 0.2,
            duration: 1,
            ease: "power3.out",
            delay: 0.5
        });

        function saveContact() {
            const vCardData = [
                "BEGIN:VCARD",
                "VERSION:3.0",
                "FN:<?php echo htmlspecialchars($user['full_name']); ?>",
                "TITLE:<?php echo htmlspecialchars($user['job_title']); ?>",
                "ORG:<?php echo htmlspecialchars($company['company_name']); ?>",
                "TEL;TYPE=CELL:<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>",
                "TEL;TYPE=CELL:<?php echo htmlspecialchars($user['whatsapp_number'] ?? $user['whatsapp'] ?? ''); ?>",
                "EMAIL:<?php echo htmlspecialchars($user['email'] ?? ''); ?>",
                "URL:<?php echo htmlspecialchars($user['social_links']['linkedin']); ?>",
                "END:VCARD"
            ].join("\n");

            const blob = new Blob([vCardData], { type: "text/vcard" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "<?php echo htmlspecialchars($user['full_name']); ?>.vcf";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>