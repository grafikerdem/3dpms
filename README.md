# 🧩 3D Print Studio Management System (3DPMS)

![License](https://img.shields.io/badge/lisans-MIT-blue.svg)
![PRs](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-blueviolet.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple.svg)

---

🇬🇧 A lightweight, modular web-based management system designed for professional 3D printing studios and small production labs.

🇹🇷 Profesyonel 3D baskı stüdyoları ve küçük üretim atölyeleri için tasarlanmış, hafif ve modüler bir web tabanlı yönetim sistemi.

🇬🇧 3DPMS combines fast quoting, material tracking, and production planning into one simple interface. Developed with a philosophy of minimal setup.

🇹🇷 3DPMS; hızlı tekliflendirme, malzeme takibi ve üretim planlamasını tek bir basit arayüzde birleştirir. Minimum kurulum gereksinimi felsefesiyle geliştirilmiştir.

---

## ✨ Main Features / Ana Özellikler

* 🇬🇧 **🧩 Modular Project Management:** Easily manage clients, projects, and project parts.
* 🇹🇷 **🧩 Modüler Proje Yönetimi:** Müşteri, proje ve proje parçalarını kolayca yönetin.

* 🇬🇧 **💰 Real-Time Cost Engine:** Instantly calculate material, machine depreciation, energy, and labor costs.
* 🇹🇷 **💰 Gerçek Zamanlı Maliyet Motoru:** Malzeme, makine amortismanı, enerji ve işçilik maliyetlerini anında hesaplayın.

* 🇬🇧 **🖨️ Printer Fleet Management:** Keep records of your printers' depreciation, energy costs, and maintenance.
* 🇹🇷 **🖨️ Yazıcı Filo Yönetimi:** Yazıcılarınızın amortisman, enerji maliyeti ve bakım kayıtlarını tutun.

* 🇬🇧 **📦 Stock & Material Tracking:** Manage your filament/resin inventory, with automatic stock deduction upon project completion.
* 🇹🇷 **📦 Stok ve Malzeme Takibi:** Filament/reçine envanterinizi yönetin, projeler tamamlandıkça stoktan otomatik düşün.

* 🇬🇧 **⚙️ Simple Production Planning (MES-Lite):** Put approved jobs in a queue and view them either as a list or as a calendar.
* 🇹🇷 **⚙️ Basit Üretim Planlaması (MES-Lite):** Onaylanan işleri bir sıraya alın, ister liste isterseniz de takvim olarak görüntüleyin.

* 🇬🇧 **📊 Reporting & Analytics:** Get simple reports on profitability, material usage, and machine performance.
* 🇹🇷 **📊 Raporlama ve Analitik:** Karlılık, malzeme kullanımı ve makine performansı üzerine basit raporlar alın.

* 🇬🇧 **🌙 Light/Dark Theme Support:** A fully responsive and modern interface with Bootstrap 5.
* 🇹🇷 **🌙 Açık/Koyu Tema Desteği:** Bootstrap 5 ile tam duyarlı ve modern bir arayüz.

* 🇬🇧 **🚀 Quick Install Wizard:** Get the system running in just a few steps.
* 🇹🇷 **🚀 Hızlı Kurulum Sihirbazı:** Birkaç adımda sistemi çalışır hale getirin.

## 📸 Screenshots / Ekran Görüntüleri
![3DPMS Dashboard Görüntüsü](https://github.com/user-attachments/assets/1fc9d76c-bf04-4bd1-8d13-bd79493a177b)

## 🛠️ Tech Stack / Teknoloji Yığını

* 🇬🇧 **Backend:** PHP 8.0+ (Object-Oriented - OOP)
* 🇹🇷 **Backend:** PHP 8.0+ (Nesne Yönelimli - OOP)

* 🇬🇧 **Frontend:** Bootstrap 5 (Light/Dark Theme)
* 🇹🇷 **Frontend:** Bootstrap 5 (Açık/Koyu Tema)

* 🇬🇧 **Database:** MySQL
* 🇹🇷 **Veritabanı:** MySQL

* 🇬🇧 **Libraries (Planned):** Chart.js (Reporting), dompdf/tcpdf (PDF Output)
* 🇹🇷 **Kütüphaneler (Planlanan):** Chart.js (Raporlama), dompdf/tcpdf (PDF Çıktısı)

## 🚀 Installation / Kurulum

🇬🇧 Installing this system on a local or shared server is very simple.

🇹🇷 Bu sistemi yerel veya paylaşımlı bir sunucuya kurmak çok basittir.

1.  🇬🇧 **Clone/Download the Repo:**
2.  🇹🇷 **Repo'yu Klonlayın/İndirin:**
    ```bash
    git clone [https://github.com/grafikerdem/3dpms.git](https://github.com/grafikerdem/3dpms.git)
    cd 3dpms
    ```

3.  🇬🇧 **Start the Install Wizard:**
4.  🇹🇷 **Kurulum Sihirbazını Başlatın:**

    🇬🇧 Go to `http://yoursite.com/install` in your browser.
    
    🇹🇷 Tarayıcınızda `http://siteniz.com/install` adresine gidin.

5.  🇬🇧 **Follow the Steps:**
6.  🇹🇷 **Adımları Takip Edin:**
    * 🇬🇧 **Step 1: Server Check:** Checks PHP version (≥ 8.0), MySQL extension, and writable folder permissions.
    * 🇹🇷 **Adım 1: Sunucu Kontrolü:** PHP sürümü (≥ 8.0), MySQL eklentisi ve yazılabilir klasör izinleri kontrol edilir.
    
    * 🇬🇧 **Step 2: Database Configuration:** Enter your host, username, password, and database name.
    * 🇹🇷 **Adım 2: Veritabanı Yapılandırması:** Host, kullanıcı adı, şifre ve veritabanı adı bilgilerinizi girin.
    
    * 🇬🇧 **Step 3: Table Creation:** All required tables and default settings (currency, labor rates, etc.) are automatically created.
    * 🇹🇷 **Adım 3: Tablo Oluşturma:** Gerekli tüm tablolar ve varsayılan ayarlar (para birimi, işçilik ücretleri vb.) otomatik olarak oluşturulur.
    
    * 🇬🇧 **Step 4: Admin Account:** Create an administrator account to log into the system.
    * 🇹🇷 **Adım 4: Admin Hesabı:** Sisteme giriş yapmak için bir yönetici hesabı oluşturun.
    
    * 🇬🇧 **Step 5: Ready!** The install directory is automatically locked, and you are redirected to `/login.php`.
    * 🇹🇷 **Adım 5: Hazır!** Kurulum dizini otomatik olarak kilitlenir ve sizi `/login.php` sayfasına yönlendirir.

## 🗺️ Project Roadmap / Proje Yol Haritası

🇬🇧 This project is currently in an MVP (Minimum Viable Product) stage. Some features planned for the future:

🇹🇷 Bu proje şu anda bir MVP (Minimum Uygulanabilir Ürün) aşamasındadır. Gelecekte eklenmesi planlanan bazı özellikler:

* [ ] 🇬🇧 **User Roles:** Different permissions for Admin and Operator.
* [ ] 🇹🇷 **Kullanıcı Rolleri:** Admin ve Operatör için farklı yetkilendirmeler.

* [ ] 🇬🇧 **OctoPrint API Integration:** Automatically sync machine usage data.
* [ ] 🇹🇷 **OctoPrint API Entegrasyonu:** Makine kullanım verilerini otomatik senkronize etme.

* [ ] 🇬🇧 **Advanced Maintenance:** Periodic maintenance alerts and email notifications.
* [ ] 🇹🇷 **Gelişmiş Bakım:** Periyodik bakım uyarıları ve e-posta bildirimleri.

* [ ] 🇬🇧 **REST API:** Infrastructure for mobile app integration.
* [ ] 🇹🇷 **REST API:** Mobil uygulama entegrasyonu için altyapı.

* [ ] 🇬🇧 **Language Support (i18n):** Multi-language support.
* [ ] 🇹🇷 **Dil Desteği (i18n):** Çoklu dil desteği.

---

🇬🇧 **Out of Scope (for MVP):** (Not planned for now)

🇹🇷 **MVP Kapsamı Dışında Kalanlar:** (Şimdilik planlanmıyor)

* 🇬🇧 Automatic slicer or G-code analysis.
* 🇹🇷 Otomatik slicer veya G-code analizi.

* 🇬🇧 3D model (.stl) viewer.
* 🇹🇷 3D model (.stl) görüntüleyici.

* 🇬🇧 Formal invoicing or tax management.
* 🇹🇷 Resmi faturalandırma veya vergi yönetimi.

## 🤝 Contributing / Katkıda Bulun
