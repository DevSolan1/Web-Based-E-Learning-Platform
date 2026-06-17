<?php
$pageTitle = 'Manage Categories - E-Learning Platform';
require_once '../includes/header.php';
require_once '../classes/Course.php';

if (!isLoggedIn() || getUserRole() !== 'admin') {
    redirect('../login.php');
}

$db = new Database();
$conn = $db->connect();

// Handle add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $description = sanitize($_POST['description']);
    
    $stmt = $conn->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $slug, $description);
    
    if ($stmt->execute()) {
        flash('success', 'Category added successfully');
    } else {
        flash('danger', 'Failed to add category');
    }
    redirect('categories.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    flash('success', 'Category deleted');
    redirect('categories.php');
}

$categories = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM courses WHERE category_id = c.id) as course_count FROM categories c ORDER BY name")->fetch_all(MYSQLI_ASSOC);
?>

<main class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-lg-9">
                <h2 class="mb-4">Manage Categories</h2>

                <!-- Add Category -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Add New Category</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="description" class="form-control" placeholder="Description (optional)">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="add_category" class="btn btn-primary w-100">Add Category</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Categories (<?= count($categories) ?>)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Description</th>
                                    <th>Courses</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                                    <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                                    <td><?= htmlspecialchars($cat['description'] ?? '-') ?></td>
                                    <td><?= $cat['course_count'] ?></td>
                                    <td>
                                        <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this category?">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>
