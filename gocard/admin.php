<?php
require 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$users = read_json('data/users.json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['colors'])) {
        $colors = read_json('data/colors.json');
        foreach ($_POST['colors'] as $element => $color) {
            foreach ($colors as &$c) {
                if ($c['element'] == $element) {
                    $c['color_value'] = $color;
                    break;
                }
            }
        }
        write_json('data/colors.json', $colors);
        $success = "Colors updated successfully.";
    }
    if (isset($_POST['company_name'])) {
        $company = read_json('data/company_info.json');
        $company['company_name'] = $_POST['company_name'];
        $company['address'] = $_POST['address'] ?? '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logo = $_FILES['logo'];
            $file_name = uniqid() . "_" . basename($logo['name']);
            $logo_path = "Uploads/" . $file_name;
            if (!file_exists('Uploads/')) {
                mkdir('Uploads/', 0777, true);
            }
            if (move_uploaded_file($logo['tmp_name'], $logo_path)) {
                $company['logo_path'] = $file_name;
            } else {
                $error = "Failed to upload logo.";
            }
        }
        write_json('data/company_info.json', $company);
        $success = "Company info updated successfully.";
    }
    if (isset($_POST['new_user'])) {
        $new_user = [
            'id' => count($users) + 1,
            'username' => $_POST['username'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role' => $_POST['role'],
            'full_name' => $_POST['full_name'],
            'job_title' => $_POST['job_title'],
            'about' => $_POST['about'],
            'profile_photo' => 'default-photo.jpg',
            'social_links' => [
                'twitter' => '',
                'linkedin' => '',
                'github' => '',
                'facebook' => '',
                'instagram' => ''
            ],
            'email' => '',
            'phone_number' => '',
            'whatsapp_number' => '',
            'qualifications' => [],
            'experience' => [],
            'certifications' => []
        ];
        $users[] = $new_user;
        write_json('data/users.json', $users);
        $success = "User created successfully.";
        header("Location: admin.php");
        exit;
    }
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        if ($user_id == $_SESSION['user_id']) {
            $error = "You cannot delete your own account.";
        } else {
            $users = array_filter($users, fn($u) => $u['id'] != $user_id);
            $users = array_values($users);
            write_json('data/users.json', $users);
            $success = "User deleted successfully.";
        }
    }
    if (isset($_POST['reset_password'])) {
        $user_id = (int)$_POST['user_id'];
        $new_password = $_POST['new_password'];
        $user_found = false;
        foreach ($users as &$u) {
            if ($u['id'] == $user_id) {
                $u['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $user_found = true;
                break;
            }
        }
        if ($user_found) {
            write_json('data/users.json', $users);
            $success = "Password reset successfully.";
        } else {
            $error = "User not found.";
        }
    }
}

$colors = read_json('data/colors.json');
$company = read_json('data/company_info.json');
$users = read_json('data/users.json');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | <?php echo htmlspecialchars($company['company_name']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles.php">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="light-mode">
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
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="admin.php">Admin Panel</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <section class="admin-section">
            <h1>Admin Dashboard</h1>
            <?php if (isset($success)) echo "<p class='success-message'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>

            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-toggle">Adjust Colors</button>
                    <div class="accordion-content">
                        <form method="POST" class="card-form">
                            <?php foreach ($colors as $color) { ?>
                                <label>
                                    <?php echo ucwords(str_replace('-', ' ', $color['element'])); ?>:
                                    <input type="color" name="colors[<?php echo $color['element']; ?>]" value="<?php echo $color['color_value']; ?>">
                                </label>
                            <?php } ?>
                            <button type="submit">Save Colors</button>
                        </form>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-toggle">Company Info</button>
                    <div class="accordion-content">
                        <form method="POST" enctype="multipart/form-data" class="card-form">
                            <label>Company Name:
                                <input type="text" name="company_name" value="<?php echo htmlspecialchars($company['company_name']); ?>" required>
                            </label>
                            <label>Address:
                                <textarea name="address"><?php echo htmlspecialchars($company['address'] ?? ''); ?></textarea>
                            </label>
                            <label>Logo:
                                <input type="file" name="logo">
                            </label>
                            <button type="submit">Save Company Info</button>
                        </form>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-toggle">Create New User</button>
                    <div class="accordion-content">
                        <form method="POST" class="card-form">
                            <label>Username:
                                <input type="text" name="username" required>
                            </label>
                            <label>Password:
                                <input type="password" name="password" required>
                            </label>
                            <label>Role:
                                <select name="role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </label>
                            <label>Full Name:
                                <input type="text" name="full_name" required>
                            </label>
                            <label>Job Title:
                                <input type="text" name="job_title" required>
                            </label>
                            <label>About:
                                <textarea name="about"></textarea>
                            </label>
                            <input type="hidden" name="new_user" value="1">
                            <button type="submit">Create User</button>
                        </form>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-toggle">Manage Users</button>
                    <div class="accordion-content">
                        <div class="card-table">
                            <table>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                                <?php foreach ($users as $user) { ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                                        <td>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="delete_user" value="1">
                                                <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="password" name="new_password" placeholder="New Password" required>
                                                <input type="hidden" name="reset_password" value="1">
                                                <button type="submit" class="reset-btn">Reset Password</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <a href="index.php" class="back-link">Back to Home</a>
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

        document.querySelectorAll(".accordion-toggle").forEach(button => {
            button.addEventListener("click", () => {
                const content = button.nextElementSibling;
                const isOpen = content.style.display === "block";
                document.querySelectorAll(".accordion-content").forEach(c => c.style.display = "none");
                if (!isOpen) {
                    content.style.display = "block";
                    gsap.from(content, { opacity: 0, height: 0, duration: 0.5, ease: "power2.out" });
                }
            });
        });

        gsap.from(".accordion-item", { opacity: 0, y: 30, stagger: 0.2, duration: 0.8, ease: "power3.out" });
    </script>
</body>
</html>