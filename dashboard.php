<?php
$page_title = 'Dashboard';
$page_css = 'dashboard.css'; // Usaremos CSS específico para la rejilla
require_once 'includes/header.php';
?>

<div class="dashboard-grid">
    <div class="card highlight-card">
        <h3>Best Apartments</h3>
        <h2>Green Oasis Residence</h2>
        <p>124 Apartments</p>
        <p>539 Rooms</p>
    </div>

    <div class="card">
        <h4>Total Revenue</h4>
        <p class="data-value">$ 873,421.39</p>
    </div>

    <div class="card">
        <h4>Completed Deals</h4>
        <p class="data-value">1,269</p>
    </div>

    <div class="card full-width">
        <h4>Average Sale Value</h4>
        <p>Gráfico de ventas aquí.</p>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>