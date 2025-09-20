    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Summernote JS (if needed) -->
    <?php if (isset($include_summernote) && $include_summernote): ?>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <?php endif; ?>

    <!-- Custom scripts -->
    <?php if (isset($custom_scripts)): ?>
    <?php echo $custom_scripts; ?>
    <?php endif; ?>
</body>
</html>
