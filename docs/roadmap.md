# 3D Print Studio Management System (3DPMS) - Geliştirme Yol Haritası

Bu belge, projenin geliştirme aşamalarını ve her aşamada tamamlanacak hedefleri özetlemektedir.

## Faz 1: Temel Altyapı ve Kurulum Sihirbazı

**Hedef:** Projenin temel dosya yapısını oluşturmak ve veritabanı kurulumunu otomatikleştiren bir kurulum sihirbazı geliştirmek.

- **Adım 1.1:** `context.md`'de belirtilen klasör yapısının oluşturulması (`/assets`, `/includes`, `/modules`, `/install`).
- **Adım 1.2:** Temel PHP dosyalarının oluşturulması (`index.php`, `login.php`, `settings.php`).
- **Adım 1.3:** Veritabanı bağlantısı için `includes/database.php` ve genel fonksiyonlar için `includes/functions.php` dosyalarının hazırlanması.
- **Adım 1.4:** Kurulum Sihirbazı Arayüzü (`install/index.php`):
    - Sunucu gereksinimlerini kontrol etme (PHP sürümü, MySQL eklentisi).
    - Veritabanı bilgilerini kullanıcıdan alma.
- **Adım 1.5:** Veritabanı Tablolarının Oluşturulması (`install/create_tables.php`):
    - `context.md`'de tanımlanan tüm tabloları (`projects`, `printers`, `materials` vb.) oluşturan SQL script'ini hazırlama.
    - Varsayılan ayarları (`settings` tablosu) ekleme.
- **Adım 1.6:** Yönetici Hesabı oluşturma ve kurulumu tamamlama.
- **Adım 1.7:** Tüm işlemleri `docs/changelog_faz1.md` dosyasına kaydetme.

## Faz 2: Çekirdek Modüller - Yazıcı ve Malzeme Yönetimi

**Hedef:** Sistemin maliyet hesaplama motoru için temel verileri yönetecek modülleri oluşturmak.

- **Adım 2.1:** Yazıcı Yönetim Modülü (`printers.php`):
    - Yazıcı ekleme, düzenleme, silme arayüzü.
    - Amortisman ve saatlik makine maliyetini otomatik hesaplama ve gösterme.
- **Adım 2.2:** Malzeme (Filament/Reçine) Yönetim Modülü (`materials.php`):
    - Malzeme ekleme, düzenleme, silme arayüzü.
    - Stok takibi ve düşük stok uyarıları için altyapı.
- **Adım 2.3:** Sarf Malzemesi Yönetimi (`consumables` tablosu ve ilgili arayüz).
- **Adım 2.4:** Tüm işlemleri `docs/changelog_faz2.md` dosyasına kaydetme.

## Faz 3: Proje Yönetimi ve Teklif Motoru

**Hedef:** Projeleri ve parçalarını yönetmek, dinamik maliyet hesaplaması yapmak ve PDF teklif çıktısı oluşturmak.

- **Adım 3.1:** Proje Yönetim Arayüzü (`projects.php`):
    - Yeni proje oluşturma (otomatik proje numarası ile).
    - Proje durumlarını (`Teklif`, `Onaylandı` vb.) yönetme.
- **Adım 3.2:** Projeye Parça Ekleme Arayüzü:
    - Yazıcı, malzeme, baskı süresi gibi verilerin girildiği form.
    - Girilen verilere göre maliyetin anlık olarak hesaplandığı dinamik arayüz (JavaScript/AJAX).
- **Adım 3.3:** Maliyet Hesaplama Motoru (`includes/functions.php`):
    - `context.md`'deki formüle göre toplam maliyeti hesaplayan fonksiyonların yazılması.
- **Adım 3.4:** PDF Teklif Üretimi:
    - `dompdf/tcpdf` kütüphanesi entegrasyonu.
    - Proje bilgilerini ve maliyet dökümünü içeren PDF şablonunun oluşturulması.
- **Adım 3.5:** Tüm işlemleri `docs/changelog_faz3.md` dosyasına kaydetme.

## Faz 4: Üretim Planlama ve Raporlama

**Hedef:** Onaylanan işlerin takibini sağlamak ve sistem verilerinden anlamlı raporlar üretmek.

- **Adım 4.1:** Üretim Planlama Modülü (`production.php`):
    - Onaylanan projelerin "Bekleyen İşler" listesinde gösterilmesi.
    - İşlerin yazıcılara atanması (sürükle-bırak veya dropdown ile).
    - `FullCalendar.js` entegrasyonu ile görsel takvim oluşturma.
- **Adım 4.2:** Dashboard Modülü (`dashboard.php`):
    - Aktif işler, düşük stok ve bakım uyarılarının gösterilmesi.
    - `Chart.js` kullanarak aylık kâr, malzeme kullanımı gibi grafiklerin eklenmesi.
- **Adım 4.3:** Raporlama Modülü (`reports.php`):
    - Proje kârlılığı (tahmini vs. gerçek maliyet).
    - Makine performans raporları (kullanım oranı, hata oranı).
- **Adım 4.4:** Tüm işlemleri `docs/changelog_faz4.md` dosyasına kaydetme.

## Faz 5: Arayüz İyileştirmeleri ve Son Dokunuşlar

**Hedef:** Kullanıcı deneyimini iyileştirmek ve sistemi production'a hazır hale getirmek.

- **Adım 5.1:** Light/Dark Mode geçişinin tüm sayfalara uygulanması.
- **Adım 5.2:** Mobil ve tablet uyumluluğunun (Responsive Design) test edilmesi ve iyileştirilmesi.
- **Adım 5.3:** Veri yedekleme ve dışa aktarma (JSON/CSV) özelliklerinin eklenmesi.
- **Adım 5.4:** Güvenlik kontrolleri (SQL Injection, XSS vb.).
- **Adım 5.5:** Genel kod temizliği ve dokümantasyonun gözden geçirilmesi.
- **Adım 5.6:** Tüm işlemleri `docs/changelog_faz5.md` dosyasına kaydetme.
