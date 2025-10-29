# 🧩 3D Print Studio Management System (3DPMS)

![Lisans](https://img.shields.io/badge/lisans-MIT-blue.svg)
![PRs](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-blueviolet.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple.svg)

Profesyonel 3D baskı stüdyoları ve küçük üretim atölyeleri için tasarlanmış, hafif ve modüler bir web tabanlı yönetim sistemi.

3DPMS; hızlı tekliflendirme, malzeme takibi ve üretim planlamasını tek bir basit arayüzde birleştirir. Minimum kurulum gereksinimi ve sıfır bağımlılık (zero-dependency) felsefesiyle geliştirilmiştir.

---

## ✨ Ana Özellikler

* **🧩 Modüler Proje Yönetimi:** Müşteri, proje ve proje parçalarını kolayca yönetin.
* **💰 Gerçek Zamanlı Maliyet Motoru:** Malzeme, makine amortismanı, enerji ve işçilik maliyetlerini anında hesaplayın.
* **🖨️ Yazıcı Filo Yönetimi:** Yazıcılarınızın amortisman, enerji maliyeti ve bakım kayıtlarını tutun.
* **📦 Stok ve Malzeme Takibi:** Filament/reçine envanterinizi yönetin, projeler tamamlandıkça stoktan otomatik düşün.
* **⚙️ Basit Üretim Planlaması (MES-Lite):** Onaylanan işleri bir sıraya alın ve `FullCalendar.js` ile görsel olarak planlayın.
* **📊 Raporlama ve Analitik:** Karlılık, malzeme kullanımı ve makine performansı üzerine basit raporlar alın.
* **🌙 Açık/Koyu Tema Desteği:** Bootstrap 5 ile tam duyarlı ve modern bir arayüz.
* **🚀 Hızlı Kurulum Sihirbazı:** Birkaç adımda sistemi çalışır hale getirin.

## 📸 Ekran Görüntüleri (Screenshots)

*(Buraya uygulamanızın dashboard, proje sayfası veya ayarlar gibi güzel görünen ekran görüntülerini ekleyin. Örn: `![Dashboard](link/to/dashboard.png)`)*

![3DPMS Dashboard Görüntüsü](link/to/screenshot.png)

## 🛠️ Teknoloji Yığını

* **Backend:** PHP 8.0+ (Nesne Yönelimli - OOP)
* **Frontend:** Bootstrap 5 (Açık/Koyu Tema)
* **Veritabanı:** MySQL
* **Kütüphaneler (Planlanan):** Chart.js (Raporlama), dompdf/tcpdf (PDF Çıktısı)

## 🚀 Kurulum (Installation)

Bu sistemi yerel veya paylaşımlı bir sunucuya kurmak çok basittir.

1.  **Repo'yu Klonlayın/İndirin:**
    ```bash
    git clone [https://github.com/kullanici-adiniz/3dpms.git](https://github.com/kullanici-adiniz/3dpms.git)
    cd 3dpms
    ```

2.  **Kurulum Sihirbazını Başlatın:**
    Tarayıcınızda `http://siteniz.com/install` adresine gidin.

3.  **Adımları Takip Edin:**
    * **Adım 1: Sunucu Kontrolü:** PHP sürümü (≥ 8.0), MySQL eklentisi ve yazılabilir klasör izinleri kontrol edilir.
    * **Adım 2: Veritabanı Yapılandırması:** Host, kullanıcı adı, şifre ve veritabanı adı bilgilerinizi girin.
    * **Adım 3: Tablo Oluşturma:** Gerekli tüm tablolar ve varsayılan ayarlar (para birimi, işçilik ücretleri vb.) otomatik olarak oluşturulur.
    * **Adım 4: Admin Hesabı:** Sisteme giriş yapmak için bir yönetici hesabı oluşturun.
    * **Adım 5: Hazır!** Kurulum dizini otomatik olarak kilitlenir ve sizi `/login.php` sayfasına yönlendirir.

## 🗺️ Proje Yol Haritası (Roadmap)

Bu proje şu anda bir MVP (Minimum Uygulanabilir Ürün) aşamasındadır. Gelecekte eklenmesi planlanan bazı özellikler:

* [ ] **Kullanıcı Rolleri:** Admin ve Operatör için farklı yetkilendirmeler.
* [ ] **OctoPrint API Entegrasyonu:** Makine kullanım verilerini otomatik senkronize etme.
* [ ] **Gelişmiş Bakım:** Periyodik bakım uyarıları ve e-posta bildirimleri.
* [ ] **REST API:** Mobil uygulama entegrasyonu için altyapı.
* [ ] **Dil Desteği (i18n):** Çoklu dil desteği.

**MVP Kapsamı Dışında Kalanlar:** (Şimdilik planlanmıyor)
* Otomatik slicer veya G-code analizi.
* 3D model (.stl) görüntüleyici.
* Resmi faturalandırma veya vergi yönetimi.

## 🤝 Katkıda Bulunma (Contributing)

Bu projeyi daha iyi hale getirmek için yapacağınız katkılara açığız!

1.  Proje repo'sunu **fork** edin.
2.  Yeni bir **feature branch** oluşturun (`git checkout -b feature/yeni-ozellik`).
3.  Değişikliklerinizi **commit** edin (`git commit -m 'Yeni bir özellik eklendi'`).
4.  Branch'inizi **push** edin (`git push origin feature/yeni-ozellik`).
5.  Bir **Pull Request (PR)** açın.

Lütfen PR açmadan önce olası değişiklikleri "Issues" (Sorunlar) bölümünde tartışmaktan çekinmeyin.

## 📄 Lisans (License)

Bu proje [MIT Lisansı](LICENSE.md) altında lisanslanmıştır.
