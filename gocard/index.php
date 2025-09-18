<?php
require 'config.php';

// Redirect authenticated non-admin users to their profile
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] !== 'admin') {
    header("Location: view_profile.php?id=" . $_SESSION['user_id']);
    exit;
}

$company = read_json('data/company_info.json');
if (empty($company)) {
    $company = ['company_name' => 'Default Company', 'logo_path' => 'placeholder.jpg', 'address' => ''];
}

$users = read_json('data/users.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | <?php echo htmlspecialchars($company['company_name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="<?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'light-mode' : 'dark-mode'; ?>">
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
            <?php if (isset($_SESSION['user_id'])) { ?>
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
        <section class="dashboard-section">
            <h1>Team Dashboard</h1>
            <div class="masonry-grid">
                <?php if (empty($users)) { ?>
                    <p>No team members found.</p>
                <?php } else { ?>
                    <?php foreach ($users as $user) { ?>
                        <div class="masonry-item">
                            <div class="staff-card">
                                <img src="Uploads/<?php echo htmlspecialchars($user['profile_photo'] ?: 'placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($user['full_name']); ?>" class="staff-photo">
                                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                                <p class="job-title"><?php echo htmlspecialchars($user['job_title']); ?></p>
                                <a href="view_profile.php?id=<?php echo $user['id']; ?>" class="view-profile-btn">View Profile</a>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
    <script>
        const themeSwitch = document.getElementById("theme-switch");
        themeSwitch.addEventListener("change", () => {
            document.body.classList.toggle("dark-mode");
            document.body.classList.toggle("light-mode");
        });

        gsap.from(".masonry-item", {
            opacity: 0,
            y: 50,
            stagger: 0.2,
            duration: 1,
            ease: "power3.out",
            delay: 0.5
        });
    </script>
</body>
</html>