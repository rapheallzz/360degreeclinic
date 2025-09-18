<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$users = read_json('data/users.json');
$user = array_filter($users, fn($u) => $u['id'] == $user_id)[array_key_first(array_filter($users, fn($u) => $u['id'] == $user_id))];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['current_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
        if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
            $error = "Please fill in all password fields to change your password.";
        } else {
            if (!password_verify($_POST['current_password'], $user['password'])) {
                $error = "Current password is incorrect.";
            } else {
                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    $error = "New password and confirmation do not match.";
                } else {
                    if (strlen($_POST['new_password']) < 8) {
                        $error = "New password must be at least 8 characters long.";
                    } else {
                        $user['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    }
                }
            }
        }
    }

    $user['full_name'] = $_POST['full_name'];
    $user['job_title'] = $_POST['job_title'];
    $user['about'] = $_POST['about'];

    $email = trim($_POST['email']);
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $user['email'] = $email;
    }

    $phone_number = trim($_POST['phone_number']);
    if (!empty($phone_number) && !preg_match('/^\+[1-9][0-9]{7,14}$/', $phone_number)) {
        $error = "Phone number must be in international format (e.g., +1234567890).";
    } else {
        $user['phone_number'] = $phone_number;
    }

    $whatsapp_number = trim($_POST['whatsapp_number']);
    if (!empty($whatsapp_number) && !preg_match('/^\+[1-9][0-9]{7,14}$/', $whatsapp_number)) {
        $error = "WhatsApp number must be in international format (e.g., +1234567890).";
    } else {
        $user['whatsapp_number'] = $whatsapp_number;
    }

    $user['social_links'] = [
        'twitter' => $_POST['twitter'],
        'linkedin' => $_POST['linkedin'],
        'github' => $_POST['github'],
        'facebook' => $_POST['facebook'],
        'instagram' => $_POST['instagram']
    ];
    $user['qualifications'] = array_filter(explode("\n", trim($_POST['qualifications'])));
    $user['experience'] = array_filter(explode("\n", trim($_POST['experience'])));
    $user['certifications'] = array_filter(explode("\n", trim($_POST['certifications'])));
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
            $photo = $_FILES['profile_photo'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($photo['type'], $allowed_types)) {
                $error = "Only JPEG, PNG, and GIF files are allowed.";
            } else {
                $file_name = uniqid() . "_" . basename($photo['name']);
                $photo_path = "Uploads/" . $file_name;
                if (!file_exists('Uploads/')) {
                    mkdir('Uploads/', 0777, true);
                }
                if (move_uploaded_file($photo['tmp_name'], $photo_path)) {
                    $user['profile_photo'] = $file_name;
                } else {
                    $error = "Failed to upload profile photo.";
                }
            }
        } else {
            $error = "File upload error: " . $_FILES['profile_photo']['error'];
        }
    }

    if (!isset($error)) {
        foreach ($users as &$u) {
            if ($u['id'] == $user_id) {
                $u = $user;
                break;
            }
        }
        write_json('data/users.json', $users);
        header("Location: view_profile.php?id=$user_id");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
            <img src="Uploads/<?php echo htmlspecialchars(read_json('data/company_info.json')['logo_path'] ?: 'placeholder.jpg'); ?>" alt="Company Logo" class="logo-img">
        </div>
        <ul class="nav-menu">
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#experience">Experience</a></li>
            <li><a href="#works">Works</a></li>
            <li><a href="#blog">Blog</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="profile.php">Edit Profile</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') { ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php } ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section class="profile-edit-section">
            <h1>Edit Profile</h1>
            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="form-column">
                    <h3>Personal Information</h3>
                    <?php if (!empty($user['profile_photo'])) { ?>
                        <div class="profile-photo-preview">
                            <label>Current Profile Photo:</label>
                            <img src="Uploads/<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Current Profile Photo">
                        </div>
                    <?php } ?>
                    <label>Profile Photo:
                        <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/gif">
                    </label>
                    <label>Full Name:
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </label>
                    <label>Job Title:
                        <input type="text" name="job_title" value="<?php echo htmlspecialchars($user['job_title']); ?>" required>
                    </label>
                    <label>About:
                        <textarea name="about"><?php echo htmlspecialchars($user['about']); ?></textarea>
                    </label>
                </div>
                <div class="form-column">
                    <h3>Contact Information</h3>
                    <label>Email:
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" placeholder="example@domain.com">
                    </label>
                    <label>Phone Number:
                        <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" placeholder="+1234567890">
                    </label>
                    <label>WhatsApp Number:
                        <input type="tel" name="whatsapp_number" value="<?php echo htmlspecialchars($user['whatsapp_number'] ?? ''); ?>" placeholder="+1234567890">
                    </label>
                </div>
                <div class="form-column">
                    <h3>Social Media Links</h3>
                    <label>Twitter:
                        <input type="text" name="twitter" value="<?php echo htmlspecialchars($user['social_links']['twitter']); ?>">
                    </label>
                    <label>LinkedIn:
                        <input type="text" name="linkedin" value="<?php echo htmlspecialchars($user['social_links']['linkedin']); ?>">
                    </label>
                    <label>GitHub:
                        <input type="text" name="github" value="<?php echo htmlspecialchars($user['social_links']['github']); ?>">
                    </label>
                    <label>Facebook:
                        <input type="text" name="facebook" value="<?php echo htmlspecialchars($user['social_links']['facebook']); ?>">
                    </label>
                    <label>Instagram:
                        <input type="text" name="instagram" value="<?php echo htmlspecialchars($user['social_links']['instagram']); ?>">
                    </label>
                </div>
                <div class="form-column">
                    <h3>Professional Details</h3>
                    <label>Qualifications (one per line):
                        <textarea name="qualifications"><?php echo htmlspecialchars(implode("\n", $user['qualifications'])); ?></textarea>
                    </label>
                    <label>Experience (one per line):
                        <textarea name="experience"><?php echo htmlspecialchars(implode("\n", $user['experience'])); ?></textarea>
                    </label>
                    <label>Certifications (one per line):
                        <textarea name="certifications"><?php echo htmlspecialchars(implode("\n", $user['certifications'])); ?></textarea>
                    </label>
                </div>
                <div class="form-column">
                    <h3>Change Password (Optional)</h3>
                    <label>Current Password:
                        <input type="password" name="current_password">
                    </label>
                    <label>New Password:
                        <input type="password" name="new_password">
                    </label>
                    <label>Confirm New Password:
                        <input type="password" name="confirm_password">
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit">Save Profile</button>
                    <a href="view_profile.php?id=<?php echo $user_id; ?>" class="back-link">Back</a>
                </div>
            </form>
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

        gsap.from(".form-column", { opacity: 0, x: 50, stagger: 0.2, duration: 0.8, ease: "power3.out" });
    </script>
</body>
</html>