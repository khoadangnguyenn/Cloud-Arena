<?php require APPROOT . '/views/layouts/admin/header.php'; ?>

<?php
$packages = $data['packages'] ?? [];
$pagination = $data['pagination'] ?? ['page' => 1, 'last_page' => 1, 'total' => 0, 'per_page' => 6];
$start = ((int) $pagination['page'] - 1) * (int) $pagination['per_page'] + 1;
$end = min((int) $pagination['total'], ((int) $pagination['page']) * (int) $pagination['per_page']);
?>

<div class="row mt-5">
    <div class="col-12">
        <div class="panel-header mb-3">
            <div>
                <h2 class="panel-title">Service Management</h2>
                <p class="panel-muted">Manage hosting plans and pricing</p>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="ti-plus me-1"></i> Add New Package
            </button>
        </div>

        <?php if (!empty($data['flash'])): ?>
            <div class="alert alert-<?php echo $data['flash']['type'] === 'success' ? 'success' : 'danger'; ?>">
                <?php echo htmlspecialchars($data['flash']['message']); ?>
            </div>
        <?php endif; ?>

        <section class="card panel-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>RAM</th>
                                <th>CPU</th>
                                <th>Storage</th>
                                <th>Price</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($packages)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No packages found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($packages as $package): ?>
                                    <tr>
                                        <td>#<?php echo (int) $package->id; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($package->name); ?></strong>
                                            <div class="small text-muted"><?php echo htmlspecialchars($package->status ?? 'active'); ?></div>
                                        </td>
                                        <td><?php echo (int) $package->ram_mb; ?> MB</td>
                                        <td><?php echo (int) $package->cpu_cores; ?> Cores</td>
                                        <td><?php echo (int) $package->disk_gb; ?> GB</td>
                                        <td>$<?php echo number_format((float) $package->price, 2); ?>/mo</td>
                                        <td class="text-end">
                                            <form action="<?php echo URLROOT; ?>/adminproducts/delete/<?php echo (int) $package->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this package?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($data['csrf_admin'] ?? ''); ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ((int) $pagination['last_page'] > 1): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <p class="mb-0 text-muted small">
                            Showing <?php echo $start; ?> to <?php echo $end; ?> of <?php echo (int) $pagination['total']; ?> packages
                        </p>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <?php for ($p = 1; $p <= (int) $pagination['last_page']; $p++): ?>
                                    <li class="page-item <?php echo (int) $pagination['page'] === $p ? 'active' : ''; ?>">
                                        <a class="page-link bg-transparent border-secondary text-light" href="<?php echo URLROOT; ?>/adminproducts?page=<?php echo $p; ?>"><?php echo $p; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?php echo URLROOT; ?>/adminproducts/add" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($data['csrf_admin'] ?? ''); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Package Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price (USD/month)</label>
                            <input type="number" min="0" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ram_gb" class="form-label">RAM (GB)</label>
                            <input type="number" min="0" class="form-control" id="ram_gb" name="ram_gb" value="2">
                        </div>
                        <div class="col-md-4">
                            <label for="cpu_cores" class="form-label">CPU Cores</label>
                            <input type="number" min="0" class="form-control" id="cpu_cores" name="cpu_cores" value="2">
                        </div>
                        <div class="col-md-4">
                            <label for="disk_gb" class="form-label">Storage (GB)</label>
                            <input type="number" min="0" class="form-control" id="disk_gb" name="disk_gb" value="20">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="image_url" class="form-label">Image URL (optional)</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://example.com/service.jpg">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/layouts/admin/footer.php'; ?>
