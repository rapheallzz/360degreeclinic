<?php
$company = read_json('data/company_info.json');
if (empty($company)) {
    $company = ['company_name' => 'Default Company', 'address' => ''];
}
?>
<footer class="footer">
    <p>© <span id="current-year"></span> <?php echo htmlspecialchars($company['company_name']); ?>. All rights reserved.</p>
    <?php if (!empty($company['address'])) { ?>
        <div class="footer-address">
            <p class="address-text">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo htmlspecialchars($company['address']); ?>
                <a href="https://maps.google.com/?q=<?php echo urlencode($company['address']); ?>" target="_blank" class="map-link">View on Map</a>
            </p>
            <p class="powered-by">Powered by <a href="https://gosortplux.com" target="_blank">Go Sortplux</a></p>
        </div>
    <?php } else { ?>
        <p class="powered-by">Powered by <a href="https://gosortplux.com" target="_blank">Go Sortplux</a></p>
    <?php } ?>
</footer>
<script>
    document.getElementById("current-year").textContent = new Date().getFullYear();
</script>