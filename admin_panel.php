<?php
session_start();
// Auth Guard: Only logged-in admins can view this page
if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    header("Location: admin_login.php");
    exit;
}

include 'db.php';

// ============================================================
//  UNIFIED POST HANDLER — All actions in ONE block
//  (Multiple separate if-POST blocks is a PHP bug: only the
//   first block ever runs. All actions must be handled here.)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- MEMBER actions ---
    if ($action === 'edit_member' && isset($_POST['id'])) {
        $id     = (int)$_POST['id'];
        $name   = $conn->real_escape_string($_POST['name']);
        $email  = $conn->real_escape_string($_POST['email']);
        $phone  = $conn->real_escape_string($_POST['phone']);
        $age    = (int)$_POST['age'];
        $gender = $conn->real_escape_string($_POST['gender']);
        $status = $conn->real_escape_string($_POST['status']);
        $conn->query("UPDATE members SET name='$name', email='$email', phone='$phone', age=$age, gender='$gender', status='$status' WHERE id=$id");
        $_SESSION['activeSection'] = 'members';
        header("Location: admin_panel.php");
        exit;
    }
    if ($action === 'delete_member' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM members WHERE id=$id");
        $_SESSION['activeSection'] = 'members';
        header("Location: admin_panel.php");
        exit;
    }

    // --- BLOG actions ---
    if ($action === 'add_blog') {
        $category    = $conn->real_escape_string($_POST['category']);
        $title       = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $image = '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $ext      = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = 'blog_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], 'uploads/' . $filename)) {
                $image = 'uploads/' . $filename;
            }
        }
        if (empty($image) && isset($_POST['image']) && !empty($_POST['image'])) {
            $image = $conn->real_escape_string($_POST['image']);
        }
        if (empty($image)) { $image = 'images/ground.avif'; }
        $conn->query("INSERT INTO blog_posts (category, title, description, image) VALUES ('$category', '$title', '$description', '$image')");
        $_SESSION['activeSection'] = 'blog';
        header("Location: admin_panel.php");
        exit;
    }
    if ($action === 'edit_blog' && isset($_POST['id'])) {
        $id          = (int)$_POST['id'];
        $category    = $conn->real_escape_string($_POST['category']);
        $title       = $conn->real_escape_string($_POST['title']);
        $description = $conn->real_escape_string($_POST['description']);
        $imageUpdate = "";
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $ext      = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = 'blog_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], 'uploads/' . $filename)) {
                $img = 'uploads/' . $filename;
                $imageUpdate = ", image='$img'";
            }
        } elseif (isset($_POST['image']) && !empty($_POST['image'])) {
            $img         = $conn->real_escape_string($_POST['image']);
            $imageUpdate = ", image='$img'";
        }
        $conn->query("UPDATE blog_posts SET category='$category', title='$title', description='$description' $imageUpdate WHERE id=$id");
        $_SESSION['activeSection'] = 'blog';
        header("Location: admin_panel.php");
        exit;
    }
    if ($action === 'delete_blog' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM blog_posts WHERE id=$id");
        $_SESSION['activeSection'] = 'blog';
        header("Location: admin_panel.php");
        exit;
    }

    // --- SERVICE actions ---
    if ($action === 'add_service') {
        $icon        = $conn->real_escape_string($_POST['icon']);
        $sname       = $conn->real_escape_string($_POST['title']);
        $price       = $conn->real_escape_string($_POST['price']);
        $description = $conn->real_escape_string($_POST['description']);
        $conn->query("INSERT INTO services (icon, name, description, price) VALUES ('$icon', '$sname', '$description', '$price')");
        $_SESSION['activeSection'] = 'services';
        header("Location: admin_panel.php");
        exit;
    }
    if ($action === 'edit_service' && isset($_POST['id'])) {
        $id          = (int)$_POST['id'];
        $icon        = $conn->real_escape_string(trim($_POST['icon']));
        $sname       = $conn->real_escape_string(trim($_POST['title']));
        $price       = $conn->real_escape_string(trim($_POST['price']));
        $description = $conn->real_escape_string(trim($_POST['description']));
        
        if ($id > 0) {
            $result = $conn->query("UPDATE services SET icon='$icon', name='$sname', description='$description', price='$price' WHERE id=$id");
            if ($result) {
                $_SESSION['flashSuccess'] = "Service \"$sname\" updated successfully.";
            } else {
                $_SESSION['flashError'] = 'Update failed: ' . $conn->error;
            }
        } else {
            $_SESSION['flashError'] = 'Invalid service ID. Could not update.';
        }
        $_SESSION['activeSection'] = 'services';
        header("Location: admin_panel.php");
        exit;
    }
    if ($action === 'delete_service' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM services WHERE id=$id");
        $_SESSION['flashSuccess'] = 'Service deleted successfully.';
        $_SESSION['activeSection'] = 'services';
        header("Location: admin_panel.php");
        exit;
    }
}

// Read and clear the active section flash (for tab restore after redirect)
$activeSection = $_SESSION['activeSection'] ?? 'dashboard';
unset($_SESSION['activeSection']);

// --- Fetch Services ---
$servicesList = [];
$res = $conn->query("SELECT * FROM services ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        // DB column is `name`, we normalize to key `title` for templates
        $servicesList[] = [
            'id'          => $row['id']          ?? 0,
            'icon'        => $row['icon']         ?? '🏋️',
            'title'       => $row['name']         ?? '(Untitled)',
            'description' => $row['description']  ?? '',
            'price'       => $row['price']        ?? '',
            'created_at'  => $row['created_at']   ?? '',
        ];
    }
}

// --- Fetch Members ---
$membersList = [];
$res = $conn->query("SELECT * FROM members ORDER BY joined DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $membersList[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'age' => $row['age'],
            'gender' => $row['gender'],
            'joined' => date('d M Y', strtotime($row['joined'])),
            'status' => $row['status'],
            'color' => '#' . substr(md5($row['name']), 0, 6)
        ];
    }
}

// --- Fetch Messages ---
$messagesList = [];
$unreadCount = 0;
$res = $conn->query("SELECT * FROM messages ORDER BY date DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $previewText = $row['message'];
        if (strlen($previewText) > 40) {
            $previewText = substr($previewText, 0, 40) . '...';
        }
        $messagesList[] = [
            'id' => $row['id'],
            'sender' => $row['sender'],
            'email' => $row['email'],
            'subject' => $row['subject'],
            'preview' => $previewText,
            'full_message' => $row['message'],
            'date' => date('d M Y', strtotime($row['date'])),
            'status' => $row['status']
        ];
        if ($row['status'] === 'unread') {
            $unreadCount++;
        }
    }
}

// --- Fetch Blog Posts ---
$blogList = [];
$res = $conn->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $blogList[] = [
            'id'          => $row['id']          ?? 0,
            'category'    => $row['category']    ?? '',
            'title'       => $row['title']       ?? '(Untitled)',
            'description' => $row['description'] ?? '',
            'image'       => $row['image']       ?? 'images/ground.avif',
            'created_at'  => $row['created_at']  ?? '',
        ];
    }
}

// Recent Members (Top 5)
$top5Members = array_slice($membersList, 0, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — FITNESS CLUB</title>
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<div class="admin-wrapper">

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h2>FITNESS CLUB</h2>
            <p>Admin Panel</p>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>
            <div class="nav-item active" onclick="showSection('dashboard', this)">
                <span class="nav-icon">📊</span> Dashboard
            </div>
            <div class="nav-item" onclick="showSection('members', this)">
                <span class="nav-icon">👥</span> Members
                <span class="badge"><?= count($membersList) ?></span>
            </div>

            <div class="nav-label">Manage</div>
            <div class="nav-item" onclick="showSection('services', this)">
                <span class="nav-icon">⚡</span> Services
            </div>
            <div class="nav-item" onclick="showSection('blog', this)">
                <span class="nav-icon">📝</span> Blog Posts
            </div>
            <div class="nav-item" onclick="showSection('messages', this)">
                <span class="nav-icon">💬</span> Messages
                <span class="badge"><?= count($messagesList) ?></span>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-user">
                <div class="admin-avatar">A</div>
                <div class="admin-user-info">
                    <div class="name">Administrator</div>
                    <div class="role">Super Admin</div>
                </div>
            </div>
            <button class="btn-logout" onclick="doLogout()">🚪 Logout</button>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-content">

        <!-- Top Bar -->
        <div class="topbar">
            <div class="topbar-left">
                <h2 id="pageTitle">Dashboard</h2>
                <div class="breadcrumb">Admin Panel &rsaquo; <span id="breadcrumbSub">Overview</span></div>
            </div>
            <div class="topbar-right">
                <div class="search-bar">
                    <span>🔍</span>
                    <input type="text" placeholder="Search…" id="globalSearch">
                </div>
                <button class="topbar-btn notification-dot" title="Notifications">🔔</button>
                <button class="topbar-btn" title="Toggle Sidebar" onclick="toggleSidebar()">☰</button>
            </div>
        </div>

        <!-- ==================== PAGE CONTENT ==================== -->
        <div class="page-content">

        <?php
        // Flash error message display
        $flashError = $_SESSION['flashError'] ?? '';
        unset($_SESSION['flashError']);
        $flashSuccess = $_SESSION['flashSuccess'] ?? '';
        unset($_SESSION['flashSuccess']);
        ?>
        <?php if ($flashError): ?>
        <div style="background:rgba(239,68,68,.12); border:1px solid rgba(239,68,68,.35); color:#ef4444; padding:12px 20px; border-radius:8px; margin-bottom:18px; font-size:.9rem;">
            ⚠️ <?= htmlspecialchars($flashError) ?>
        </div>
        <?php endif; ?>
        <?php if ($flashSuccess): ?>
        <div style="background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.35); color:#10b981; padding:12px 20px; border-radius:8px; margin-bottom:18px; font-size:.9rem;">
            ✅ <?= htmlspecialchars($flashSuccess) ?>
        </div>
        <?php endif; ?>

            <!-- ===== DASHBOARD SECTION ===== -->
            <div class="section active" id="section-dashboard">

                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div>
                        <h1>Welcome back, Admin! 💪</h1>
                        <p>Here's what's happening with your gym today — Monday, March 2026.</p>
                    </div>
                    <div class="banner-emoji">🏆</div>
                </div>

                <!-- Stat Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon red">👥</div>
                        <div class="stat-info">
                            <div class="value" id="counter-members">0</div>
                            <div class="label">Total Members</div>
                            <div class="change up">↑ 12 new this month</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon blue">⚡</div>
                        <div class="stat-info">
                            <div class="value" id="counter-services">0</div>
                            <div class="label">Active Services</div>
                            <div class="change up">↑ All running</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">📝</div>
                        <div class="stat-info">
                            <div class="value" id="counter-blog">0</div>
                            <div class="label">Blog Posts</div>
                            <div class="change up">↑ All running</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon yellow">💬</div>
                        <div class="stat-info">
                            <div class="value" id="counter-messages">0</div>
                            <div class="label">Messages</div>
                            <div class="change down">● <?= $unreadCount ?> unread</div>
                        </div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">

                    <!-- Recent Members -->
                    <div class="panel">
                        <div class="panel-header">
                            <div>
                                <h3>Recent Members</h3>
                                <div class="sub">Latest 5 registrations</div>
                            </div>
                            <button class="btn btn-outline btn-sm" onclick="showSection('members', document.querySelectorAll('.nav-item')[1])">View All</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($top5Members as $m): ?>
                                <tr>
                                    <td><div class="member-cell"><div class="member-avatar" style="background:<?= $m['color'] ?>"><?= strtoupper(substr($m['name'], 0, 1)) ?></div><div><div class="member-name"><?= htmlspecialchars($m['name']) ?></div><div class="member-email"><?= htmlspecialchars($m['email']) ?></div></div></div></td>
                                    <td><?= htmlspecialchars($m['gender']) ?></td>
                                    <td><span class="status-badge <?= $m['status'] ?>"><span class="status-dot"></span><?= ucfirst($m['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Recent Activity -->
                    <div class="panel">
                        <div class="panel-header">
                            <div>
                                <h3>Recent Activity</h3>
                                <div class="sub">Latest events</div>
                            </div>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(16,185,129,.12); color:#10b981">✅</div>
                                <div class="activity-text">
                                    <div class="title">New member registered — Rahul Sharma</div>
                                    <div class="time">2 minutes ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(59,130,246,.12); color:#3b82f6">💬</div>
                                <div class="activity-text">
                                    <div class="title">New contact message received</div>
                                    <div class="time">14 minutes ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(230,57,70,.12); color:#e63946">📝</div>
                                <div class="activity-text">
                                    <div class="title">Blog post "UFC Training" updated</div>
                                    <div class="time">1 hour ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(245,158,11,.12); color:#f59e0b">⚡</div>
                                <div class="activity-text">
                                    <div class="title">Swimming Pool price updated</div>
                                    <div class="time">3 hours ago</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot" style="background:rgba(139,92,246,.12); color:#8b5cf6">👤</div>
                                <div class="activity-text">
                                    <div class="title">Admin logged in from new device</div>
                                    <div class="time">Today, 6:00 AM</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ===== MEMBERS SECTION ===== -->
            <div class="section" id="section-members">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>All Members</h3>
                            <div class="sub"><?= count($membersList) ?> total registered members</div>
                        </div>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <div class="table-search">
                                <span>🔍</span>
                                <input type="text" placeholder="Search members…" id="memberSearch" oninput="filterMembers()">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden Member Form -->
                    <div id="memberFormContainer" class="form-container">
                        <h4 id="memberFormTitle">Edit Member</h4>
                        <form method="POST" action="admin_panel.php" class="admin-form">
                            <input type="hidden" name="action" id="memberAction" value="edit_member">
                            <input type="hidden" name="id" id="memberId" value="">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" id="memberName" class="input" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="memberEmail" class="input" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" id="memberPhone" class="input" required>
                                </div>
                                <div class="form-group">
                                    <label>Age</label>
                                    <input type="number" name="age" id="memberAge" class="input" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" id="memberGender" class="input" style="appearance: none;">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" id="memberStatus" class="input" style="appearance: none;">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                                <button type="button" class="btn btn-outline" onclick="document.getElementById('memberFormContainer').style.display='none'">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="data-table" id="membersTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Member</th>
                                    <th>Phone</th>
                                    <th>Age</th>
                                    <th>Gender</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="membersBody">
                                <!-- Rows injected by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ===== SERVICES SECTION ===== -->
            <div class="section" id="section-services">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>Gym Services</h3>
                            <div class="sub"><?= count($servicesList) ?> active services</div>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="showAddServiceForm()">➕ Add Service</button>
                    </div>

                    <!-- Hidden Service Form -->
                    <div id="serviceFormContainer" class="form-container">
                        <h4 id="serviceFormTitle">Add New Service</h4>
                        <form method="POST" action="admin_panel.php" class="admin-form">
                            <input type="hidden" name="action" id="serviceAction" value="add_service">
                            <input type="hidden" name="id" id="serviceId" value="">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Icon / Emoji</label>
                                    <input type="text" name="icon" id="serviceIcon" class="input" placeholder="e.g. 🧘" required>
                                </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="text" name="price" id="servicePrice" class="input" placeholder="e.g. $15 / class" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" id="serviceTitle" class="input" placeholder="Enter service title" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="serviceDescription" rows="3" class="input" placeholder="Enter service description..." required></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">💾 Save Service</button>
                                <button type="button" class="btn btn-outline" onclick="document.getElementById('serviceFormContainer').style.display='none'">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div class="services-grid">
                        <?php foreach($servicesList as $srv): ?>
                        <div class="service-card">
                            <div class="service-card-header">
                                <div class="service-icon"><?= htmlspecialchars($srv['icon'] ?? '🏋️') ?></div>
                            </div>
                            <h4><?= htmlspecialchars($srv['title'] ?? '(Untitled)') ?></h4>
                            <p><?= htmlspecialchars($srv['description'] ?? '') ?></p>
                            <div class="service-price"><?= htmlspecialchars($srv['price'] ?? '') ?></div>
                            <div class="service-actions">
                                <button class="btn btn-info btn-sm" onclick="editService(<?= (int)($srv['id'] ?? 0) ?>)">✏️ Edit</button>
                                <form method="POST" action="admin_panel.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                    <input type="hidden" name="action" value="delete_service">
                                    <input type="hidden" name="id" value="<?= (int)($srv['id'] ?? 0) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ===== BLOG SECTION ===== -->
            <div class="section" id="section-blog">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>Blog Posts</h3>
                            <div class="sub"><?= count($blogList) ?> published posts</div>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="showAddBlogForm()">➕ New Post</button>
                    </div>

                    <!-- Hidden Blog Form -->
                    <div id="blogFormContainer" class="form-container">
                        <h4 id="blogFormTitle">Add New Blog Post</h4>
                        <form method="POST" action="admin_panel.php" enctype="multipart/form-data" class="admin-form">
                            <input type="hidden" name="action" id="blogAction" value="add_blog">
                            <input type="hidden" name="id" id="blogId" value="">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Category</label>
                                    <input type="text" name="category" id="blogCategory" class="input" placeholder="e.g. Yoga, Nutrition" required>
                                </div>
                                <div class="form-group">
                                    <label>Upload Image</label>
                                    <input type="file" name="image_file" id="blogImageFile" class="input" accept="image/*">
                                    <small style="color:var(--text-muted); font-size: 0.75rem;">Or use Image URL below:</small>
                                    <input type="text" name="image" id="blogImage" class="input" placeholder="e.g. images/yoga.webp" style="margin-top: 5px;">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" id="blogTitle" class="input" placeholder="Enter post title" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="blogDescription" rows="4" class="input" placeholder="Enter post content..." required></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">💾 Save Post</button>
                                <button type="button" class="btn btn-outline" onclick="document.getElementById('blogFormContainer').style.display='none'">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div class="blog-grid">
                        <?php foreach($blogList as $blog): ?>
                        <div class="blog-card">
                            <img class="blog-card-img" src="<?= htmlspecialchars($blog['image'] ?? 'images/ground.avif') ?>" alt="<?= htmlspecialchars($blog['title'] ?? '') ?>" onerror="this.style.background='linear-gradient(135deg,#1a1a3e,#e63946)'; this.style.height='160px'">
                            <div class="blog-card-body">
                                <span class="blog-category"><?= htmlspecialchars($blog['category'] ?? '') ?></span>
                                <h4><?= htmlspecialchars($blog['title'] ?? '(Untitled)') ?></h4>
                                <p><?= htmlspecialchars($blog['description'] ?? '') ?></p>
                                <div class="blog-actions">
                                    <button class="btn btn-info btn-sm" onclick="editBlog(<?= (int)($blog['id'] ?? 0) ?>)">✏️ Edit</button>
                                    <form method="POST" action="admin_panel.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <input type="hidden" name="action" value="delete_blog">
                                        <input type="hidden" name="id" value="<?= (int)($blog['id'] ?? 0) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete</button>
                                    </form>
                                    <button class="btn btn-success btn-sm" onclick="window.open('Page2.php')">👁️ View</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ===== MESSAGES SECTION ===== -->
            <div class="section" id="section-messages">
                <div class="panel">
                    <div class="panel-header">
                        <div>
                            <h3>Contact Messages</h3>
                            <div class="sub"><?= count($messagesList) ?> total — <?= $unreadCount ?> unread</div>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button class="btn btn-outline btn-sm" onclick="markAllRead()">✅ Mark All Read</button>
                        </div>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sender</th>
                                    <th>Subject</th>
                                    <th>Preview</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="messagesBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div><!-- end page-content -->
    </div><!-- end main-content -->
</div><!-- end admin-wrapper -->

<script>
// ============================================================
//  DATA — declared first to avoid Temporal Dead Zone errors
//  (const/let are NOT hoisted; functions that use them must
//   only be CALLED after these declarations)
// ============================================================
const membersData  = <?= json_encode($membersList) ?>;
const messagesData = <?= json_encode($messagesList) ?>;
const blogData     = <?= json_encode($blogList) ?>;
const serviceData  = <?= json_encode($servicesList) ?>;

// ============================================================
//  SECTION NAVIGATION
// ============================================================
const sectionMeta = {
    dashboard: { title: 'Dashboard',   breadcrumb: 'Overview' },
    members:   { title: 'Members',     breadcrumb: 'All Members' },
    services:  { title: 'Services',    breadcrumb: 'Manage Services' },
    blog:      { title: 'Blog Posts',  breadcrumb: 'Manage Blog' },
    messages:  { title: 'Messages',    breadcrumb: 'Contact Messages' },
};

function showSection(name, navEl) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('section-' + name).classList.add('active');
    if (navEl) navEl.classList.add('active');
    const meta = sectionMeta[name];
    document.getElementById('pageTitle').textContent     = meta.title;
    document.getElementById('breadcrumbSub').textContent = meta.breadcrumb;
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

// ============================================================
//  COUNTER ANIMATION
// ============================================================
function animateCounter(id, target, duration) {
    const el = document.getElementById(id);
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start += step;
        if (start >= target) { start = target; clearInterval(timer); }
        el.textContent = Math.floor(start);
    }, 16);
}

// Run immediately — data consts are already declared above so no TDZ error
animateCounter('counter-members',  <?= count($membersList) ?>, 1200);
animateCounter('counter-services', <?= count($servicesList) ?>,  800);
animateCounter('counter-blog',     <?= count($blogList) ?>,  600);
animateCounter('counter-messages', <?= count($messagesList) ?>, 1000);
renderMembers();
renderMessages();

document.addEventListener('DOMContentLoaded', () => {
    // PHP session flash tells us which tab to restore after a form submission.
    // window.location.hash does NOT work because PHP header() strips the hash.
    const activeSection = '<?= htmlspecialchars($activeSection) ?>';
    const navMap = {
        'dashboard': document.querySelectorAll('.nav-item')[0],
        'members':   document.querySelectorAll('.nav-item')[1],
        'services':  document.querySelectorAll('.nav-item')[2],
        'blog':      document.querySelectorAll('.nav-item')[3],
        'messages':  document.querySelectorAll('.nav-item')[4]
    };
    if (activeSection && navMap[activeSection]) {
        showSection(activeSection, navMap[activeSection]);
    }
});

// ============================================================
//  MEMBERS FUNCTIONS
// ============================================================

function renderMembers(filter = '') {
    const tbody = document.getElementById('membersBody');
    const filtered = membersData.filter(m =>
        m.name.toLowerCase().includes(filter.toLowerCase()) ||
        m.email.toLowerCase().includes(filter.toLowerCase()) ||
        m.gender.toLowerCase().includes(filter.toLowerCase())
    );
    tbody.innerHTML = filtered.map((m, i) => `
        <tr>
            <td style="color:var(--text-muted);font-size:.82rem;">${i + 1}</td>
            <td>
                <div class="member-cell">
                    <div class="member-avatar" style="background:${m.color}">${m.name[0]}</div>
                    <div>
                        <div class="member-name">${m.name}</div>
                        <div class="member-email">${m.email}</div>
                    </div>
                </div>
            </td>
            <td>${m.phone}</td>
            <td>${m.age}</td>
            <td>${m.gender}</td>
            <td style="font-size:.83rem;color:var(--text-muted)">${m.joined}</td>
            <td><span class="status-badge ${m.status}"><span class="status-dot"></span>${capitalize(m.status)}</span></td>
            <td>
                <div style="display:flex;gap:6px;">
                    <button class="btn btn-info btn-sm" onclick="editMember(${m.id})">✏️ Edit</button>
                    <form method="POST" action="admin_panel.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete member ${m.name}?');">
                        <input type="hidden" name="action" value="delete_member">
                        <input type="hidden" name="id" value="${m.id}">
                        <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete</button>
                    </form>
                </div>
            </td>
        </tr>
    `).join('');
}

function filterMembers() {
    renderMembers(document.getElementById('memberSearch').value);
}

function editMember(id) {
    const m = membersData.find(x => x.id == id);
    if(m) {
        document.getElementById('memberFormContainer').style.display='block';
        document.getElementById('memberId').value = m.id;
        document.getElementById('memberName').value = m.name;
        document.getElementById('memberEmail').value = m.email;
        document.getElementById('memberPhone').value = m.phone;
        document.getElementById('memberAge').value = m.age;
        document.getElementById('memberGender').value = m.gender;
        document.getElementById('memberStatus').value = m.status;
        document.getElementById('memberFormContainer').scrollIntoView({behavior: 'smooth'});
    }
}

// ============================================================
//  MESSAGES FUNCTIONS
// ============================================================

function renderMessages() {
    const tbody = document.getElementById('messagesBody');
    tbody.innerHTML = messagesData.map((m, i) => `
        <tr style="${m.status === 'unread' ? 'font-weight:600;' : ''}">
            <td style="color:var(--text-muted);font-size:.82rem;">${i + 1}</td>
            <td>
                <div class="msg-meta">${m.sender}</div>
                <div style="font-size:.75rem;color:var(--text-muted)">${m.email}</div>
            </td>
            <td class="msg-subject">${m.subject}</td>
            <td class="msg-preview" style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.preview}</td>
            <td style="font-size:.8rem;color:var(--text-muted);white-space:nowrap">${m.date}</td>
            <td><span class="status-badge ${m.status}"><span class="status-dot"></span>${capitalize(m.status)}</span></td>
            <td>
                <div style="display:flex;gap:6px;">
                    <button class="btn btn-info btn-sm" onclick="viewMessage(${i})">👁️ View</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteMessage(${i})">🗑️</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function viewMessage(i) {
    const m = messagesData[i];
    const msgText = m.full_message ? m.full_message : m.preview;
    alert(`📧 Message from ${m.sender}\nEmail: ${m.email}\nSubject: ${m.subject}\n\n"${msgText}"\n\nDate: ${m.date}`);
    messagesData[i].status = 'read';
    renderMessages();
}

function deleteMessage(i) {
    if (confirm('Delete this message?')) {
        messagesData.splice(i, 1);
        renderMessages();
    }
}

function markAllRead() {
    messagesData.forEach(m => m.status = 'read');
    renderMessages();
    alert('All messages marked as read!');
}

// ============================================================
//  BLOG FUNCTIONS
// ============================================================
function showAddBlogForm() {
    document.getElementById('blogFormContainer').style.display='block';
    document.getElementById('blogAction').value='add_blog';
    document.getElementById('blogId').value='';
    document.getElementById('blogCategory').value='';
    document.getElementById('blogTitle').value='';
    document.getElementById('blogImage').value='';
    document.getElementById('blogImageFile').value='';
    document.getElementById('blogDescription').value='';
    document.getElementById('blogFormTitle').innerText='Add New Blog Post';
    document.getElementById('blogFormContainer').scrollIntoView({behavior: 'smooth'});
}

function editBlog(id) {
    try {
        const b = blogData.find(x => x.id == id);
        if(b) {
            document.getElementById('blogFormContainer').style.display='block';
            document.getElementById('blogAction').value='edit_blog';
            document.getElementById('blogId').value=b.id;
            document.getElementById('blogCategory').value=b.category;
            document.getElementById('blogTitle').value=b.title;
            document.getElementById('blogImage').value=b.image;
            try { document.getElementById('blogImageFile').value=''; } catch(e) { console.warn("Could not clear file input"); }
            document.getElementById('blogDescription').value=b.description;
            document.getElementById('blogFormTitle').innerText='Edit Blog Post';
            
            // Scroll to center of form for better visibility
            document.getElementById('blogFormContainer').scrollIntoView({behavior: 'smooth', block: 'center'});
        } else {
            alert('Error: Blog post data not found in cache. Please refresh the page.');
        }
    } catch (err) {
        console.error('Error in editBlog:', err);
        alert('An error occurred while opening the edit form. See console for details.');
    }
}

// ============================================================
//  SERVICE FUNCTIONS
// ============================================================
function showAddServiceForm() {
    document.getElementById('serviceFormContainer').style.display='block';
    document.getElementById('serviceAction').value='add_service';
    document.getElementById('serviceId').value='';
    document.getElementById('serviceIcon').value='';
    document.getElementById('serviceTitle').value='';
    document.getElementById('servicePrice').value='';
    document.getElementById('serviceDescription').value='';
    document.getElementById('serviceFormTitle').innerText='Add New Service';
    document.getElementById('serviceFormContainer').scrollIntoView({behavior: 'smooth'});
}

function editService(id) {
    const s = serviceData.find(x => x.id == id);
    if (s) {
        document.getElementById('serviceFormContainer').style.display = 'block';
        document.getElementById('serviceAction').value       = 'edit_service';
        document.getElementById('serviceId').value           = s.id;
        document.getElementById('serviceIcon').value         = s.icon;
        document.getElementById('serviceTitle').value        = s.title;
        document.getElementById('servicePrice').value        = s.price;
        document.getElementById('serviceDescription').value  = s.description;
        document.getElementById('serviceFormTitle').innerText = 'Edit Service: ' + s.title;
        document.getElementById('serviceFormContainer').scrollIntoView({behavior: 'smooth', block: 'center'});
    } else {
        console.error('editService: no service found with id =', id, ' in serviceData:', serviceData);
        alert('Could not load service data. Please hard-refresh the page (Ctrl+F5) and try again.');
    }
}

// ============================================================
//  HELPERS
// ============================================================
function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

function confirmDelete(name) {
    if (confirm(`Are you sure you want to delete "${name}"?`)) {
        alert(`"${name}" has been deleted.`);
    }
}

function doLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'admin_logout.php';
    }
}
</script>

</body>
</html>
