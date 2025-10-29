<?php
$page_title = 'Raporlar';
require_once 'includes/header.php';

// En kârlı projeleri hesapla
// Bu örnekte, basitçe proje parçalarının toplam maliyetine göre sıralama yapıyoruz.
// Gerçek bir senaryoda, bu daha karmaşık olabilir (örn: gerçekleşen maliyet vs. teklif)
$stmt = $pdo->query(
    "SELECT p.project_number, p.customer_name, SUM(pp.total_cost) as total_revenue
     FROM projects p
     JOIN project_parts pp ON p.id = pp.project_id
     WHERE p.status = 'Tamamlandı'
     GROUP BY p.id
     ORDER BY total_revenue DESC
     LIMIT 10"
);
$most_profitable_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currency = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'currency'")->fetchColumn() ?? '₺';

?>

<h1 class="mb-4">Raporlar</h1>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header">En Kârlı 10 Proje</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Proje Numarası</th>
                            <th>Müşteri</th>
                            <th class="text-end">Toplam Gelir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($most_profitable_projects)): ?>
                            <tr><td colspan="4" class="text-center p-4">Henüz tamamlanmış proje bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach ($most_profitable_projects as $index => $project): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($project['project_number']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($project['customer_name']); ?></td>
                                    <td class="text-end"><?php echo number_format($project['total_revenue'], 2) . ' ' . $currency; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>