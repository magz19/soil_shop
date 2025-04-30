    </main>
    
    <!-- Footer -->
    <footer class="bg-light mt-5 py-4 border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3"><span class="text-warning">S-Oil</span> Products Store</h5>
                    <p class="text-muted">High-quality automotive oils and lubricants for your vehicle.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li><a href="index.php?page=products" class="text-decoration-none text-muted">Products</a></li>
                        <li><a href="index.php?page=orders" class="text-decoration-none text-muted">Track Orders</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Contact</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Mendiola St. Manila City</li>
                        <li><i class="fas fa-phone me-2"></i> 09454545</li>
                        <li><i class="fas fa-envelope me-2"></i> info@soil-products.com</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <p class="small text-muted mb-0">&copy; <?php echo date('Y'); ?> S-Oil Products Store. All rights reserved.</p>
                <div>
                    <a href="#" class="btn btn-outline-secondary btn-sm" id="back-to-top">
                        <i class="fas fa-arrow-up me-1"></i> Back to Top
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Back to top button
        document.addEventListener('DOMContentLoaded', function() {
            const backToTopBtn = document.getElementById('back-to-top');
            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>
</body>
</html>