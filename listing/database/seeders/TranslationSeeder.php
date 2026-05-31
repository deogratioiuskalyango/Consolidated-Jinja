<?php

namespace Database\Seeders;

use Botble\LanguageAdvanced\Database\Seeders\BaseTranslationSeeder;
use Botble\LanguageAdvanced\Database\Seeders\Traits\HasLanguageSeeder;
use Botble\LanguageAdvanced\Database\Seeders\Traits\HasMenuTranslationSeeder;
use Botble\LanguageAdvanced\Database\Seeders\Traits\HasPageTranslation;
use Botble\LanguageAdvanced\Database\Seeders\Traits\HasThemeOptionSeeder;
use Botble\LanguageAdvanced\Database\Seeders\Traits\HasWidgetSeeder;
use Botble\Menu\Database\Traits\HasMenuSeeder;
use Botble\Page\Models\Page;

class TranslationSeeder extends BaseTranslationSeeder
{
    use HasLanguageSeeder;
    use HasMenuSeeder;
    use HasMenuTranslationSeeder;
    use HasPageTranslation;
    use HasThemeOptionSeeder;
    use HasWidgetSeeder;

    public function run(): void
    {
        if (! is_plugin_active('language')) {
            return;
        }

        $this->createLanguages();

        $locales = $this->locales();

        $this->seedThemeOptions($locales);
        $this->seedMenus($locales);
        $this->seedWidgets($locales);

        if (is_plugin_active('language-advanced')) {
            // Auto-seed all translatable models from JSON files
            $this->seedAllTranslatableModelsFromJson($locales);

            // Complex page translations (HTML content with shortcodes)
            $this->seedPageTranslations($locales);
        }
    }

    protected function locales(): array
    {
        return ['ar', 'vi', 'fr', 'id', 'tr'];
    }

    protected function pageTranslations(): array
    {
        return [
            'ar' => [
                'Homepage 1' => [
                    'name' => 'الصفحة الرئيسية 1',
                    'content' => $this->getArabicHomepage1Content(),
                ],
                'Homepage 2' => [
                    'name' => 'الصفحة الرئيسية 2',
                    'content' => $this->getArabicHomepage2Content(),
                ],
                'Homepage 3' => [
                    'name' => 'الصفحة الرئيسية 3',
                    'content' => $this->getArabicHomepage3Content(),
                ],
                'Homepage 4' => [
                    'name' => 'الصفحة الرئيسية 4',
                    'content' => $this->getArabicHomepage4Content(),
                ],
                'Homepage 5' => [
                    'name' => 'الصفحة الرئيسية 5',
                    'content' => $this->getArabicHomepage5Content(),
                ],
                'Blog' => ['name' => 'المدونة'],
                'Contact Us' => ['name' => 'اتصل بنا'],
                'Our Services' => ['name' => 'خدماتنا'],
                'FAQs' => ['name' => 'الأسئلة الشائعة'],
                'About Us' => ['name' => 'معلومات عنا'],
                'Pricing Plans' => ['name' => 'خطط الأسعار'],
                'Privacy Policy' => ['name' => 'سياسة الخصوصية'],
                'Coming Soon' => ['name' => 'قريبًا'],
                'Properties' => ['name' => 'العقارات'],
                'Projects' => ['name' => 'المشاريع'],
                'Cookie Policy' => ['name' => 'سياسة ملفات تعريف الارتباط'],
            ],
            'vi' => [
                'Homepage 1' => [
                    'name' => 'Trang chủ 1',
                    'content' => $this->getVietnameseHomepage1Content(),
                ],
                'Homepage 2' => [
                    'name' => 'Trang chủ 2',
                    'content' => $this->getVietnameseHomepage2Content(),
                ],
                'Homepage 3' => [
                    'name' => 'Trang chủ 3',
                    'content' => $this->getVietnameseHomepage3Content(),
                ],
                'Homepage 4' => [
                    'name' => 'Trang chủ 4',
                    'content' => $this->getVietnameseHomepage4Content(),
                ],
                'Homepage 5' => [
                    'name' => 'Trang chủ 5',
                    'content' => $this->getVietnameseHomepage5Content(),
                ],
                'Blog' => ['name' => 'Blog'],
                'Contact Us' => ['name' => 'Liên hệ'],
                'Our Services' => ['name' => 'Dịch vụ của chúng tôi'],
                'FAQs' => ['name' => 'Câu hỏi thường gặp'],
                'About Us' => ['name' => 'Về chúng tôi'],
                'Pricing Plans' => ['name' => 'Bảng giá'],
                'Privacy Policy' => ['name' => 'Chính sách bảo mật'],
                'Coming Soon' => ['name' => 'Sắp ra mắt'],
                'Properties' => ['name' => 'Bất động sản'],
                'Projects' => ['name' => 'Dự án'],
                'Cookie Policy' => ['name' => 'Chính sách cookie'],
            ],
            'fr' => [
                'Homepage 1' => [
                    'name' => 'Accueil 1',
                    'content' => $this->getFrenchHomepage1Content(),
                ],
                'Homepage 2' => [
                    'name' => 'Accueil 2',
                    'content' => $this->getFrenchHomepage2Content(),
                ],
                'Homepage 3' => [
                    'name' => 'Accueil 3',
                    'content' => $this->getFrenchHomepage3Content(),
                ],
                'Homepage 4' => [
                    'name' => 'Accueil 4',
                    'content' => $this->getFrenchHomepage4Content(),
                ],
                'Homepage 5' => [
                    'name' => 'Accueil 5',
                    'content' => $this->getFrenchHomepage5Content(),
                ],
                'Blog' => ['name' => 'Blog'],
                'Contact Us' => ['name' => 'Contact'],
                'Our Services' => ['name' => 'Nos services'],
                'FAQs' => ['name' => 'FAQ'],
                'About Us' => ['name' => 'À propos'],
                'Pricing Plans' => ['name' => 'Tarifs'],
                'Privacy Policy' => ['name' => 'Politique de confidentialité'],
                'Coming Soon' => ['name' => 'Bientôt disponible'],
                'Properties' => ['name' => 'Biens'],
                'Projects' => ['name' => 'Projets'],
                'Cookie Policy' => ['name' => 'Politique de cookies'],
            ],
            'id' => [
                'Homepage 1' => [
                    'name' => 'Beranda 1',
                    'content' => $this->getIndonesianHomepage1Content(),
                ],
                'Homepage 2' => [
                    'name' => 'Beranda 2',
                    'content' => $this->getIndonesianHomepage2Content(),
                ],
                'Homepage 3' => [
                    'name' => 'Beranda 3',
                    'content' => $this->getIndonesianHomepage3Content(),
                ],
                'Homepage 4' => [
                    'name' => 'Beranda 4',
                    'content' => $this->getIndonesianHomepage4Content(),
                ],
                'Homepage 5' => [
                    'name' => 'Beranda 5',
                    'content' => $this->getIndonesianHomepage5Content(),
                ],
                'Blog' => ['name' => 'Blog'],
                'Contact Us' => ['name' => 'Kontak'],
                'Our Services' => ['name' => 'Layanan kami'],
                'FAQs' => ['name' => 'FAQ'],
                'About Us' => ['name' => 'Tentang Kami'],
                'Pricing Plans' => ['name' => 'Paket harga'],
                'Privacy Policy' => ['name' => 'Kebijakan Privasi'],
                'Coming Soon' => ['name' => 'Segera hadir'],
                'Properties' => ['name' => 'Properti'],
                'Projects' => ['name' => 'Proyek'],
                'Cookie Policy' => ['name' => 'Kebijakan Cookie'],
            ],
            'tr' => [
                'Homepage 1' => ['name' => 'Ana Sayfa 1'],
                'Homepage 2' => ['name' => 'Ana Sayfa 2'],
                'Homepage 3' => ['name' => 'Ana Sayfa 3'],
                'Homepage 4' => ['name' => 'Ana Sayfa 4'],
                'Homepage 5' => ['name' => 'Ana Sayfa 5'],
                'Blog' => ['name' => 'Blog'],
                'Contact Us' => ['name' => 'İletişim'],
                'Our Services' => ['name' => 'Hizmetlerimiz'],
                'FAQs' => ['name' => 'SSS'],
                'About Us' => ['name' => 'Hakkımızda'],
                'Pricing Plans' => ['name' => 'Fiyat Planları'],
                'Privacy Policy' => ['name' => 'Gizlilik Politikası'],
                'Coming Soon' => ['name' => 'Yakında'],
                'Properties' => ['name' => 'Emlak'],
                'Projects' => ['name' => 'Projeler'],
                'Cookie Policy' => ['name' => 'Çerez Politikası'],
            ],
        ];
    }

    protected function getVietnameseHomepage1Content(): string
    {
        // Translation dictionary
        $t = function ($text) {
            $translations = [
                'Find Your' => 'Tìm ngôi nhà',
                'Dream Home,Perfect Home,Real Estate' => 'Mơ ước,Hoàn hảo,Bất động sản',
                "We are a real estate agency that will help you find the best residence you dream of, let's discuss for your dream house?" => 'Chúng tôi là công ty bất động sản giúp bạn tìm ngôi nhà tốt nhất mà bạn mơ ước, hãy cùng thảo luận về ngôi nhà mơ ước của bạn!',
                'Recommended For You' => 'Đề xuất cho bạn',
                'Features Properties' => 'Bất động sản nổi bật',
                'View All Properties' => 'Xem tất cả bất động sản',
                'Our Location For You' => 'Vị trí của chúng tôi',
                'Explore Cities' => 'Khám phá thành phố',
                'What We Do?' => 'Chúng tôi làm gì?',
                'Our Services' => 'Dịch vụ của chúng tôi',
                'Buy A New Home' => 'Mua nhà mới',
                'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Khám phá ngôi nhà mơ ước một cách dễ dàng. Khám phá các bất động sản đa dạng và hướng dẫn chuyên môn cho trải nghiệm mua nhà liền mạch.',
                'Learn More' => 'Tìm hiểu thêm',
                'Rent A Home' => 'Thuê nhà',
                'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Khám phá nơi thuê hoàn hảo một cách dễ dàng. Khám phá các danh sách đa dạng được thiết kế chính xác phù hợp với nhu cầu lối sống độc đáo của bạn.',
                'Sell A Home' => 'Bán nhà',
                "Sell confidently with expert guidance and effective strategies, showcasing your property's best features for a successful sale." => 'Bán một cách tự tin với hướng dẫn chuyên môn và chiến lược hiệu quả, giới thiệu các tính năng tốt nhất của bất động sản để bán thành công.',
                'SATISFIED CLIENTS' => 'KHÁCH HÀNG HÀI LÒNG',
                'AWARDS RECEIVED' => 'GIẢI THƯỞNG NHẬN ĐƯỢC',
                'SUCCESSFUL TRANSACTIONS' => 'GIAO DỊCH THÀNH CÔNG',
                'MONTHLY TRAFFIC' => 'LƯỢT TRUY CẬP HÀNG THÁNG',
                'View All Services' => 'Xem tất cả dịch vụ',
                'Why Choose Homzen' => 'Tại sao chọn Homzen',
                'Our Benefit' => 'Lợi ích của chúng tôi',
                'Proven Expertise' => 'Chuyên môn đã được chứng minh',
                'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Đội ngũ giàu kinh nghiệm của chúng tôi xuất sắc trong lĩnh vực bất động sản với nhiều năm điều hướng thị trường thành công, mang đến quyết định sáng suốt và kết quả tối ưu.',
                'Customized Solutions' => 'Giải pháp tùy chỉnh',
                'We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.' => 'Chúng tôi tự hào tạo ra các chiến lược cá nhân hóa phù hợp với mục tiêu độc đáo của bạn, đảm bảo hành trình bất động sản liền mạch.',
                'Transparent Partnerships' => 'Đối tác minh bạch',
                'Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.' => 'Minh bạch là chìa khóa trong mối quan hệ khách hàng của chúng tôi. Chúng tôi ưu tiên giao tiếp rõ ràng và thực hành đạo đức, nuôi dưỡng niềm tin và độ tin cậy.',
                'Best Property Value' => 'Giá trị bất động sản tốt nhất',
                'Top Properties' => 'Bất động sản hàng đầu',
                'View All' => 'Xem tất cả',
                "What's People Say's" => 'Mọi người nói gì',
                'Meet Our Agents' => 'Gặp môi giới của chúng tôi',
                'Our Teams' => 'Đội ngũ của chúng tôi',
                'Mortgage Calculator' => 'Công cụ tính khoản vay mua nhà',
                'Calculate your monthly mortgage payments' => 'Tính số tiền trả góp hàng tháng',
                'Helpful Homzen Guides' => 'Hướng dẫn hữu ích từ Homzen',
                'Latest News' => 'Tin tức mới nhất',
                'Discover the latest insights, trends, and expert analysis in this comprehensive article that covers key aspects of the topic.' => 'Khám phá những hiểu biết, xu hướng và phân tích chuyên sâu mới nhất trong bài viết toàn diện này bao gồm các khía cạnh chính của chủ đề.',
            ];

            return $translations[$text] ?? $text;
        };

        // Get original page to copy non-translatable attributes (like image paths)
        $page = Page::query()->where('name', 'Homepage 1')->first();
        if (! $page) {
            return '';
        }

        // Parse existing shortcodes and translate them
        $content = $page->content;

        // Simple string replacement for shortcode attributes
        $content = str_replace([
            'title="Find Your"',
            'animation_text="Dream Home,Perfect Home,Real Estate"',
            'description="We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?"',
            'title="Recommended For You"',
            'subtitle="Features Properties"',
            'button_label="View All Properties"',
            'title="Our Location For You"',
            'subtitle="Explore Cities"',
            'title="What We Do?"',
            'subtitle="Our Services"',
            'services_title_1="Buy A New Home"',
            'services_description_1="Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience."',
            'services_button_label_1="Learn More"',
            'services_title_2="Rent A Home"',
            'services_description_2="Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs."',
            'services_button_label_2="Learn More"',
            'services_title_3="Sell A Home"',
            "services_description_3=\"Sell confidently with expert guidance and effective strategies, showcasing your property's best features for a successful sale.\"",
            'services_button_label_3="Learn More"',
            'counters_label_1="SATISFIED CLIENTS"',
            'counters_label_2="AWARDS RECEIVED"',
            'counters_label_3="SUCCESSFUL TRANSACTIONS"',
            'counters_label_4="MONTHLY TRAFFIC"',
            'button_label="View All Services"',
            'title="Why Choose Homzen"',
            'subtitle="Our Benefit"',
            'services_title_1="Proven Expertise"',
            'services_description_1="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'services_title_2="Customized Solutions"',
            'services_description_2="We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey."',
            'services_title_3="Transparent Partnerships"',
            'services_description_3="Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout."',
            'title="Best Property Value"',
            'subtitle="Top Properties"',
            'button_label="View All"',
            'title="What’s People Say’s"',
            'description="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'title="Meet Our Agents"',
            'subtitle="Our Teams"',
            'form_title="Mortgage Calculator"',
            'form_description="Calculate your monthly mortgage payments"',
            'title="Helpful Homzen Guides"',
            'subtitle="Latest News"',
        ], [
            'title="' . $t('Find Your') . '"',
            'animation_text="' . $t('Dream Home,Perfect Home,Real Estate') . '"',
            'description="' . $t("We are a real estate agency that will help you find the best residence you dream of, let's discuss for your dream house?") . '"',
            'title="' . $t('Recommended For You') . '"',
            'subtitle="' . $t('Features Properties') . '"',
            'button_label="' . $t('View All Properties') . '"',
            'title="' . $t('Our Location For You') . '"',
            'subtitle="' . $t('Explore Cities') . '"',
            'title="' . $t('What We Do?') . '"',
            'subtitle="' . $t('Our Services') . '"',
            'services_title_1="' . $t('Buy A New Home') . '"',
            'services_description_1="' . $t('Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.') . '"',
            'services_button_label_1="' . $t('Learn More') . '"',
            'services_title_2="' . $t('Rent A Home') . '"',
            'services_description_2="' . $t('Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.') . '"',
            'services_button_label_2="' . $t('Learn More') . '"',
            'services_title_3="' . $t('Sell A Home') . '"',
            'services_description_3="' . $t("Sell confidently with expert guidance and effective strategies, showcasing your property's best features for a successful sale.") . '"',
            'services_button_label_3="' . $t('Learn More') . '"',
            'counters_label_1="' . $t('SATISFIED CLIENTS') . '"',
            'counters_label_2="' . $t('AWARDS RECEIVED') . '"',
            'counters_label_3="' . $t('SUCCESSFUL TRANSACTIONS') . '"',
            'counters_label_4="' . $t('MONTHLY TRAFFIC') . '"',
            'button_label="' . $t('View All Services') . '"',
            'title="' . $t('Why Choose Homzen') . '"',
            'subtitle="' . $t('Our Benefit') . '"',
            'services_title_1="' . $t('Proven Expertise') . '"',
            'services_description_1="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'services_title_2="' . $t('Customized Solutions') . '"',
            'services_description_2="' . $t('We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.') . '"',
            'services_title_3="' . $t('Transparent Partnerships') . '"',
            'services_description_3="' . $t('Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.') . '"',
            'title="' . $t('Best Property Value') . '"',
            'subtitle="' . $t('Top Properties') . '"',
            'button_label="' . $t('View All') . '"',
            'title="' . $t("What's People Say's") . '"',
            'description="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'title="' . $t('Meet Our Agents') . '"',
            'subtitle="' . $t('Our Teams') . '"',
            'form_title="' . $t('Mortgage Calculator') . '"',
            'form_description="' . $t('Calculate your monthly mortgage payments') . '"',
            'title="' . $t('Helpful Homzen Guides') . '"',
            'subtitle="' . $t('Latest News') . '"',
        ], $content);

        return $content;
    }

    /**
     * Get Arabic content for Homepage 1
     * Translations are copied from /scripts/dictionaries/ar.json
     */
    protected function getArabicHomepage1Content(): string
    {
        // Translation dictionary
        $t = function ($text) {
            $translations = [
                'Find Your' => 'ابحث عن',
                'Dream Home,Perfect Home,Real Estate' => 'منزل الأحلام,منزل مثالي,عقارات',
                'We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?' => 'نحن وكالة عقارية ستساعدك في العثور على أفضل مسكن تحلم به، دعنا نناقش منزل أحلامك!',
                'Recommended For You' => 'موصى به لك',
                'Features Properties' => 'عقارات مميزة',
                'View All Properties' => 'عرض جميع العقارات',
                'Our Location For You' => 'موقعنا من أجلك',
                'Explore Cities' => 'استكشف المدن',
                'What We Do?' => 'ماذا نفعل؟',
                'Our Services' => 'خدماتنا',
                'Buy A New Home' => 'شراء منزل جديد',
                'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'اكتشف منزل أحلامك بسهولة. استكشف العقارات المتنوعة والإرشادات المهنية لتجربة شراء سلسة.',
                'Learn More' => 'اعرف المزيد',
                'Rent A Home' => 'استئجار منزل',
                'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'اكتشف الإيجار المثالي بسهولة. استكشف مجموعة متنوعة من القوائم المصممة خصيصًا لتناسب احتياجات نمط حياتك الفريد.',
                'Sell A Home' => 'بيع منزل',
                'Sell confidently with expert guidance and effective strategies, showcasing your property’s best features for a successful sale.' => 'بع بثقة مع الإرشاد المهني والاستراتيجيات الفعالة، عرض أفضل ميزات عقارك لبيع ناجح.',
                'SATISFIED CLIENTS' => 'العملاء الراضون',
                'AWARDS RECEIVED' => 'الجوائز المستلمة',
                'SUCCESSFUL TRANSACTIONS' => 'المعاملات الناجحة',
                'MONTHLY TRAFFIC' => 'الزيارات الشهرية',
                'View All Services' => 'عرض جميع الخدمات',
                'Why Choose Homzen' => 'لماذا تختار Homzen',
                'Our Benefit' => 'فوائدنا',
                'Proven Expertise' => 'خبرة مثبتة',
                'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'يتفوق فريقنا ذو الخبرة في العقارات مع سنوات من التنقل الناجح في السوق، ويقدم قرارات مستنيرة ونتائج مثالية.',
                'Customized Solutions' => 'حلول مخصصة',
                'We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.' => 'نفخر بصياغة استراتيجيات شخصية لتتناسب مع أهدافك الفريدة، مما يضمن رحلة عقارية سلسة.',
                'Transparent Partnerships' => 'شراكات شفافة',
                'Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.' => 'الشفافية هي المفتاح في علاقاتنا مع العملاء. نعطي الأولوية للتواصل الواضح والممارسات الأخلاقية، مما يعزز الثقة والموثوقية.',
                'Best Property Value' => 'أفضل قيمة عقارية',
                'Top Properties' => 'أفضل العقارات',
                'View All' => 'عرض الكل',
                'What’s People Say’s' => 'ماذا يقول الناس',
                'Meet Our Agents' => 'تعرف على وكلائنا',
                'Our Teams' => 'فرقنا',
                'Mortgage Calculator' => 'حاسبة الرهن العقاري',
                'Calculate your monthly mortgage payments' => 'احسب مدفوعات الرهن العقاري الشهرية',
                'Helpful Homzen Guides' => 'أدلة Homzen المفيدة',
                'Latest News' => 'آخر الأخبار',
            ];

            return $translations[$text] ?? $text;
        };

        // Get original page to copy non-translatable attributes (like image paths)
        $page = Page::query()->where('name', 'Homepage 1')->first();
        if (! $page) {
            return '';
        }

        // Parse existing shortcodes and translate them
        $content = $page->content;

        // Simple string replacement for shortcode attributes
        $content = str_replace([
            'title="Find Your"',
            'animation_text="Dream Home,Perfect Home,Real Estate"',
            'description="We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?"',
            'title="Recommended For You"',
            'subtitle="Features Properties"',
            'button_label="View All Properties"',
            'title="Our Location For You"',
            'subtitle="Explore Cities"',
            'title="What We Do?"',
            'subtitle="Our Services"',
            'services_title_1="Buy A New Home"',
            'services_description_1="Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience."',
            'services_button_label_1="Learn More"',
            'services_title_2="Rent A Home"',
            'services_description_2="Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs."',
            'services_button_label_2="Learn More"',
            'services_title_3="Sell A Home"',
            'services_description_3="Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale."',
            'services_button_label_3="Learn More"',
            'counters_label_1="SATISFIED CLIENTS"',
            'counters_label_2="AWARDS RECEIVED"',
            'counters_label_3="SUCCESSFUL TRANSACTIONS"',
            'counters_label_4="MONTHLY TRAFFIC"',
            'button_label="View All Services"',
            'title="Why Choose Homzen"',
            'subtitle="Our Benefit"',
            'services_title_1="Proven Expertise"',
            'services_description_1="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'services_title_2="Customized Solutions"',
            'services_description_2="We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey."',
            'services_title_3="Transparent Partnerships"',
            'services_description_3="Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout."',
            'title="Best Property Value"',
            'subtitle="Top Properties"',
            'button_label="View All"',
            'title="What’s People Say’s"',
            'description="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'title="Meet Our Agents"',
            'subtitle="Our Teams"',
            'form_title="Mortgage Calculator"',
            'form_description="Calculate your monthly mortgage payments"',
            'title="Helpful Homzen Guides"',
            'subtitle="Latest News"',
        ], [
            'title="' . $t('Find Your') . '"',
            'animation_text="' . $t('Dream Home,Perfect Home,Real Estate') . '"',
            'description="' . $t('We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?') . '"',
            'title="' . $t('Recommended For You') . '"',
            'subtitle="' . $t('Features Properties') . '"',
            'button_label="' . $t('View All Properties') . '"',
            'title="' . $t('Our Location For You') . '"',
            'subtitle="' . $t('Explore Cities') . '"',
            'title="' . $t('What We Do?') . '"',
            'subtitle="' . $t('Our Services') . '"',
            'services_title_1="' . $t('Buy A New Home') . '"',
            'services_description_1="' . $t('Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.') . '"',
            'services_button_label_1="' . $t('Learn More') . '"',
            'services_title_2="' . $t('Rent A Home') . '"',
            'services_description_2="' . $t('Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.') . '"',
            'services_button_label_2="' . $t('Learn More') . '"',
            'services_title_3="' . $t('Sell A Home') . '"',
            'services_description_3="' . $t('Sell confidently with expert guidance and effective strategies, showcasing your property’s best features for a successful sale.') . '"',
            'services_button_label_3="' . $t('Learn More') . '"',
            'counters_label_1="' . $t('SATISFIED CLIENTS') . '"',
            'counters_label_2="' . $t('AWARDS RECEIVED') . '"',
            'counters_label_3="' . $t('SUCCESSFUL TRANSACTIONS') . '"',
            'counters_label_4="' . $t('MONTHLY TRAFFIC') . '"',
            'button_label="' . $t('View All Services') . '"',
            'title="' . $t('Why Choose Homzen') . '"',
            'subtitle="' . $t('Our Benefit') . '"',
            'services_title_1="' . $t('Proven Expertise') . '"',
            'services_description_1="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'services_title_2="' . $t('Customized Solutions') . '"',
            'services_description_2="' . $t('We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.') . '"',
            'services_title_3="' . $t('Transparent Partnerships') . '"',
            'services_description_3="' . $t('Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.') . '"',
            'title="' . $t('Best Property Value') . '"',
            'subtitle="' . $t('Top Properties') . '"',
            'button_label="' . $t('View All') . '"',
            'title="' . $t('What’s People Say’s') . '"',
            'description="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'title="' . $t('Meet Our Agents') . '"',
            'subtitle="' . $t('Our Teams') . '"',
            'form_title="' . $t('Mortgage Calculator') . '"',
            'form_description="' . $t('Calculate your monthly mortgage payments') . '"',
            'title="' . $t('Helpful Homzen Guides') . '"',
            'subtitle="' . $t('Latest News') . '"',
        ], $content);

        return $content;
    }

    /**
     * Get French content for Homepage 1
     * Translations are copied from /scripts/dictionaries/fr.json
     */
    protected function getFrenchHomepage1Content(): string
    {
        // Translation dictionary
        $t = function ($text) {
            $translations = [
                'Find Your' => 'Trouvez votre',
                'Dream Home,Perfect Home,Real Estate' => 'Maison de rêve,Maison parfaite,Immobilier',
                'We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?' => 'Nous sommes une agence immobilière qui vous aidera à trouver la meilleure résidence dont vous rêvez, discutons de votre maison de rêve!',
                'Recommended For You' => 'Recommandé pour vous',
                'Features Properties' => 'Propriétés en vedette',
                'View All Properties' => 'Voir toutes les propriétés',
                'Our Location For You' => 'Notre emplacement pour vous',
                'Explore Cities' => 'Explorez les villes',
                'What We Do?' => 'Que faisons-nous?',
                'Our Services' => 'Nos services',
                'Buy A New Home' => 'Acheter une nouvelle maison',
                'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Découvrez votre maison de rêve sans effort. Explorez des propriétés diverses et des conseils d\'experts pour une expérience d\'achat fluide.',
                'Learn More' => 'En savoir plus',
                'Rent A Home' => 'Louer une maison',
                'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Découvrez votre location parfaite sans effort. Explorez une grande variété de listes adaptées précisément à vos besoins de style de vie uniques.',
                'Sell A Home' => 'Vendre une maison',
                'Sell confidently with expert guidance and effective strategies, showcasing your property’s best features for a successful sale.' => 'Vendez en toute confiance avec des conseils d\'experts et des stratégies efficaces, mettant en valeur les meilleures caractéristiques de votre propriété pour une vente réussie.',
                'SATISFIED CLIENTS' => 'CLIENTS SATISFAITS',
                'AWARDS RECEIVED' => 'PRIX REÇUS',
                'SUCCESSFUL TRANSACTIONS' => 'TRANSACTIONS RÉUSSIES',
                'MONTHLY TRAFFIC' => 'TRAFIC MENSUEL',
                'View All Services' => 'Voir tous les services',
                'Why Choose Homzen' => 'Pourquoi choisir Homzen',
                'Our Benefit' => 'Nos avantages',
                'Proven Expertise' => 'Expertise éprouvée',
                'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Notre équipe expérimentée excelle dans l\'immobilier avec des années de navigation réussie sur le marché, offrant des décisions éclairées et des résultats optimaux.',
                'Customized Solutions' => 'Solutions personnalisées',
                'We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.' => 'Nous sommes fiers de créer des stratégies personnalisées pour correspondre à vos objectifs uniques, garantissant un parcours immobilier fluide.',
                'Transparent Partnerships' => 'Partenariats transparents',
                'Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.' => 'La transparence est essentielle dans nos relations avec les clients. Nous priorisons une communication claire et des pratiques éthiques, favorisant la confiance et la fiabilité.',
                'Best Property Value' => 'Meilleure valeur immobilière',
                'Top Properties' => 'Meilleures propriétés',
                'View All' => 'Voir tout',
                'What’s People Say’s' => 'Ce que disent les gens',
                'Meet Our Agents' => 'Rencontrez nos agents',
                'Our Teams' => 'Nos équipes',
                'Mortgage Calculator' => 'Calculateur hypothécaire',
                'Calculate your monthly mortgage payments' => 'Calculez vos paiements hypothécaires mensuels',
                'Helpful Homzen Guides' => 'Guides utiles Homzen',
                'Latest News' => 'Dernières nouvelles',
            ];

            return $translations[$text] ?? $text;
        };

        // Get original page to copy non-translatable attributes (like image paths)
        $page = Page::query()->where('name', 'Homepage 1')->first();
        if (! $page) {
            return '';
        }

        // Parse existing shortcodes and translate them
        $content = $page->content;

        // Simple string replacement for shortcode attributes
        $content = str_replace([
            'title="Find Your"',
            'animation_text="Dream Home,Perfect Home,Real Estate"',
            'description="We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?"',
            'title="Recommended For You"',
            'subtitle="Features Properties"',
            'button_label="View All Properties"',
            'title="Our Location For You"',
            'subtitle="Explore Cities"',
            'title="What We Do?"',
            'subtitle="Our Services"',
            'services_title_1="Buy A New Home"',
            'services_description_1="Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience."',
            'services_button_label_1="Learn More"',
            'services_title_2="Rent A Home"',
            'services_description_2="Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs."',
            'services_button_label_2="Learn More"',
            'services_title_3="Sell A Home"',
            'services_description_3="Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale."',
            'services_button_label_3="Learn More"',
            'counters_label_1="SATISFIED CLIENTS"',
            'counters_label_2="AWARDS RECEIVED"',
            'counters_label_3="SUCCESSFUL TRANSACTIONS"',
            'counters_label_4="MONTHLY TRAFFIC"',
            'button_label="View All Services"',
            'title="Why Choose Homzen"',
            'subtitle="Our Benefit"',
            'services_title_1="Proven Expertise"',
            'services_description_1="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'services_title_2="Customized Solutions"',
            'services_description_2="We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey."',
            'services_title_3="Transparent Partnerships"',
            'services_description_3="Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout."',
            'title="Best Property Value"',
            'subtitle="Top Properties"',
            'button_label="View All"',
            'title="What’s People Say’s"',
            'description="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'title="Meet Our Agents"',
            'subtitle="Our Teams"',
            'form_title="Mortgage Calculator"',
            'form_description="Calculate your monthly mortgage payments"',
            'title="Helpful Homzen Guides"',
            'subtitle="Latest News"',
        ], [
            'title="' . $t('Find Your') . '"',
            'animation_text="' . $t('Dream Home,Perfect Home,Real Estate') . '"',
            'description="' . $t('We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?') . '"',
            'title="' . $t('Recommended For You') . '"',
            'subtitle="' . $t('Features Properties') . '"',
            'button_label="' . $t('View All Properties') . '"',
            'title="' . $t('Our Location For You') . '"',
            'subtitle="' . $t('Explore Cities') . '"',
            'title="' . $t('What We Do?') . '"',
            'subtitle="' . $t('Our Services') . '"',
            'services_title_1="' . $t('Buy A New Home') . '"',
            'services_description_1="' . $t('Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.') . '"',
            'services_button_label_1="' . $t('Learn More') . '"',
            'services_title_2="' . $t('Rent A Home') . '"',
            'services_description_2="' . $t('Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.') . '"',
            'services_button_label_2="' . $t('Learn More') . '"',
            'services_title_3="' . $t('Sell A Home') . '"',
            'services_description_3="' . $t('Sell confidently with expert guidance and effective strategies, showcasing your property’s best features for a successful sale.') . '"',
            'services_button_label_3="' . $t('Learn More') . '"',
            'counters_label_1="' . $t('SATISFIED CLIENTS') . '"',
            'counters_label_2="' . $t('AWARDS RECEIVED') . '"',
            'counters_label_3="' . $t('SUCCESSFUL TRANSACTIONS') . '"',
            'counters_label_4="' . $t('MONTHLY TRAFFIC') . '"',
            'button_label="' . $t('View All Services') . '"',
            'title="' . $t('Why Choose Homzen') . '"',
            'subtitle="' . $t('Our Benefit') . '"',
            'services_title_1="' . $t('Proven Expertise') . '"',
            'services_description_1="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'services_title_2="' . $t('Customized Solutions') . '"',
            'services_description_2="' . $t('We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.') . '"',
            'services_title_3="' . $t('Transparent Partnerships') . '"',
            'services_description_3="' . $t('Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.') . '"',
            'title="' . $t('Best Property Value') . '"',
            'subtitle="' . $t('Top Properties') . '"',
            'button_label="' . $t('View All') . '"',
            'title="' . $t('What’s People Say’s') . '"',
            'description="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'title="' . $t('Meet Our Agents') . '"',
            'subtitle="' . $t('Our Teams') . '"',
            'form_title="' . $t('Mortgage Calculator') . '"',
            'form_description="' . $t('Calculate your monthly mortgage payments') . '"',
            'title="' . $t('Helpful Homzen Guides') . '"',
            'subtitle="' . $t('Latest News') . '"',
        ], $content);

        return $content;
    }

    /**
     * Get Indonesian content for Homepage 1
     * Translations are copied from /scripts/dictionaries/id.json
     */
    protected function getIndonesianHomepage1Content(): string
    {
        // Translation dictionary
        $t = function ($text) {
            $translations = [
                'Find Your' => 'Temukan',
                'Dream Home,Perfect Home,Real Estate' => 'Rumah Impian,Rumah Sempurna,Properti',
                'We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?' => 'Kami adalah agen properti yang akan membantu Anda menemukan tempat tinggal terbaik yang Anda impikan, mari diskusikan rumah impian Anda!',
                'Recommended For You' => 'Direkomendasikan untuk Anda',
                'Features Properties' => 'Properti Unggulan',
                'View All Properties' => 'Lihat Semua Properti',
                'Our Location For You' => 'Lokasi Kami untuk Anda',
                'Explore Cities' => 'Jelajahi Kota',
                'What We Do?' => 'Apa yang Kami Lakukan?',
                'Our Services' => 'Layanan Kami',
                'Buy A New Home' => 'Beli Rumah Baru',
                'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Temukan rumah impian Anda dengan mudah. Jelajahi properti beragam dan panduan ahli untuk pengalaman pembelian yang lancar.',
                'Learn More' => 'Pelajari Lebih Lanjut',
                'Rent A Home' => 'Sewa Rumah',
                'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Temukan sewa sempurna Anda dengan mudah. Jelajahi berbagai daftar yang disesuaikan dengan kebutuhan gaya hidup unik Anda.',
                'Sell A Home' => 'Jual Rumah',
                'Sell confidently with expert guidance and effective strategies, showcasing your property’s best features for a successful sale.' => 'Jual dengan percaya diri dengan panduan ahli dan strategi efektif, menampilkan fitur terbaik properti Anda untuk penjualan yang sukses.',
                'SATISFIED CLIENTS' => 'KLIEN PUAS',
                'AWARDS RECEIVED' => 'PENGHARGAAN DITERIMA',
                'SUCCESSFUL TRANSACTIONS' => 'TRANSAKSI SUKSES',
                'MONTHLY TRAFFIC' => 'LALU LINTAS BULANAN',
                'View All Services' => 'Lihat Semua Layanan',
                'Why Choose Homzen' => 'Mengapa Memilih Homzen',
                'Our Benefit' => 'Keuntungan Kami',
                'Proven Expertise' => 'Keahlian Terbukti',
                'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Tim berpengalaman kami unggul dalam properti dengan bertahun-tahun navigasi pasar yang sukses, menawarkan keputusan yang tepat dan hasil optimal.',
                'Customized Solutions' => 'Solusi yang Disesuaikan',
                'We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.' => 'Kami bangga menciptakan strategi yang dipersonalisasi untuk mencocokkan tujuan unik Anda, memastikan perjalanan properti yang lancar.',
                'Transparent Partnerships' => 'Kemitraan Transparan',
                'Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.' => 'Transparansi adalah kunci dalam hubungan klien kami. Kami memprioritaskan komunikasi yang jelas dan praktik etis, memupuk kepercayaan dan keandalan.',
                'Best Property Value' => 'Nilai Properti Terbaik',
                'Top Properties' => 'Properti Teratas',
                'View All' => 'Lihat Semua',
                'What’s People Say’s' => 'Apa Kata Orang',
                'Meet Our Agents' => 'Temui Agen Kami',
                'Our Teams' => 'Tim Kami',
                'Mortgage Calculator' => 'Kalkulator Hipotek',
                'Calculate your monthly mortgage payments' => 'Hitung pembayaran hipotek bulanan Anda',
                'Helpful Homzen Guides' => 'Panduan Homzen yang Berguna',
                'Latest News' => 'Berita Terbaru',
            ];

            return $translations[$text] ?? $text;
        };

        // Get original page to copy non-translatable attributes (like image paths)
        $page = Page::query()->where('name', 'Homepage 1')->first();
        if (! $page) {
            return '';
        }

        // Parse existing shortcodes and translate them
        $content = $page->content;

        // Simple string replacement for shortcode attributes
        $content = str_replace([
            'title="Find Your"',
            'animation_text="Dream Home,Perfect Home,Real Estate"',
            'description="We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?"',
            'title="Recommended For You"',
            'subtitle="Features Properties"',
            'button_label="View All Properties"',
            'title="Our Location For You"',
            'subtitle="Explore Cities"',
            'title="What We Do?"',
            'subtitle="Our Services"',
            'services_title_1="Buy A New Home"',
            'services_description_1="Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience."',
            'services_button_label_1="Learn More"',
            'services_title_2="Rent A Home"',
            'services_description_2="Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs."',
            'services_button_label_2="Learn More"',
            'services_title_3="Sell A Home"',
            'services_description_3="Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale."',
            'services_button_label_3="Learn More"',
            'counters_label_1="SATISFIED CLIENTS"',
            'counters_label_2="AWARDS RECEIVED"',
            'counters_label_3="SUCCESSFUL TRANSACTIONS"',
            'counters_label_4="MONTHLY TRAFFIC"',
            'button_label="View All Services"',
            'title="Why Choose Homzen"',
            'subtitle="Our Benefit"',
            'services_title_1="Proven Expertise"',
            'services_description_1="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'services_title_2="Customized Solutions"',
            'services_description_2="We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey."',
            'services_title_3="Transparent Partnerships"',
            'services_description_3="Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout."',
            'title="Best Property Value"',
            'subtitle="Top Properties"',
            'button_label="View All"',
            'title="What’s People Say’s"',
            'description="Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results."',
            'title="Meet Our Agents"',
            'subtitle="Our Teams"',
            'form_title="Mortgage Calculator"',
            'form_description="Calculate your monthly mortgage payments"',
            'title="Helpful Homzen Guides"',
            'subtitle="Latest News"',
        ], [
            'title="' . $t('Find Your') . '"',
            'animation_text="' . $t('Dream Home,Perfect Home,Real Estate') . '"',
            'description="' . $t('We are a real estate agency that will help you find the best residence you dream of, let’s discuss for your dream house?') . '"',
            'title="' . $t('Recommended For You') . '"',
            'subtitle="' . $t('Features Properties') . '"',
            'button_label="' . $t('View All Properties') . '"',
            'title="' . $t('Our Location For You') . '"',
            'subtitle="' . $t('Explore Cities') . '"',
            'title="' . $t('What We Do?') . '"',
            'subtitle="' . $t('Our Services') . '"',
            'services_title_1="' . $t('Buy A New Home') . '"',
            'services_description_1="' . $t('Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.') . '"',
            'services_button_label_1="' . $t('Learn More') . '"',
            'services_title_2="' . $t('Rent A Home') . '"',
            'services_description_2="' . $t('Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.') . '"',
            'services_button_label_2="' . $t('Learn More') . '"',
            'services_title_3="' . $t('Sell A Home') . '"',
            'services_description_3="' . $t('Sell confidently with expert guidance and effective strategies, showcasing your property’s best features for a successful sale.') . '"',
            'services_button_label_3="' . $t('Learn More') . '"',
            'counters_label_1="' . $t('SATISFIED CLIENTS') . '"',
            'counters_label_2="' . $t('AWARDS RECEIVED') . '"',
            'counters_label_3="' . $t('SUCCESSFUL TRANSACTIONS') . '"',
            'counters_label_4="' . $t('MONTHLY TRAFFIC') . '"',
            'button_label="' . $t('View All Services') . '"',
            'title="' . $t('Why Choose Homzen') . '"',
            'subtitle="' . $t('Our Benefit') . '"',
            'services_title_1="' . $t('Proven Expertise') . '"',
            'services_description_1="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'services_title_2="' . $t('Customized Solutions') . '"',
            'services_description_2="' . $t('We pride ourselves on crafting personalized strategies to match your unique goals, ensuring a seamless real estate journey.') . '"',
            'services_title_3="' . $t('Transparent Partnerships') . '"',
            'services_description_3="' . $t('Transparency is key in our client relationships. We prioritize clear communication and ethical practices, fostering trust and reliability throughout.') . '"',
            'title="' . $t('Best Property Value') . '"',
            'subtitle="' . $t('Top Properties') . '"',
            'button_label="' . $t('View All') . '"',
            'title="' . $t('What’s People Say’s') . '"',
            'description="' . $t('Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.') . '"',
            'title="' . $t('Meet Our Agents') . '"',
            'subtitle="' . $t('Our Teams') . '"',
            'form_title="' . $t('Mortgage Calculator') . '"',
            'form_description="' . $t('Calculate your monthly mortgage payments') . '"',
            'title="' . $t('Helpful Homzen Guides') . '"',
            'subtitle="' . $t('Latest News') . '"',
        ], $content);

        return $content;
    }

    /**
     * Get Vietnamese blog post content
     */
    protected function getVietnameseBlogPostContent(string $content): string
    {
        $translations = [
            'Understanding Housing Stocks' => 'Hiểu về cổ phiếu nhà ở',
            'Housing stocks encompass companies involved in various aspects of the real estate industry, including home builders, developers, and related service providers. Factors influencing these stocks range from interest rates and economic indicators to trends in home ownership rates.' => 'Cổ phiếu nhà ở bao gồm các công ty tham gia vào các khía cạnh khác nhau của ngành bất động sản, bao gồm các nhà xây dựng, nhà phát triển và các nhà cung cấp dịch vụ liên quan. Các yếu tố ảnh hưởng đến cổ phiếu này bao gồm lãi suất, các chỉ số kinh tế đến xu hướng tỷ lệ sở hữu nhà.',
            'Pay close attention to economic indicators such as employment rates, GDP growth, and consumer confidence. A strong economy often correlates with increased demand for housing, benefiting related stocks.' => 'Chú ý đến các chỉ số kinh tế như tỷ lệ việc làm, tăng trưởng GDP và niềm tin người tiêu dùng. Một nền kinh tế mạnh thường tương quan với nhu cầu nhà ở tăng, có lợi cho các cổ phiếu liên quan.',
            'Lower rates can boost home buying activity, benefiting housing stocks, while higher rates may have the opposite effect.' => 'Lãi suất thấp hơn có thể thúc đẩy hoạt động mua nhà, có lợi cho cổ phiếu nhà ở, trong khi lãi suất cao hơn có thể có tác dụng ngược lại.',
            'Identify Emerging Trends' => 'Xác định xu hướng mới nổi',
            'Stay informed about emerging trends in the housing market, such as the demand for sustainable homes, technological advancements, and demographic shifts. Companies aligning with these trends may present attractive investment opportunities.' => 'Luôn cập nhật về các xu hướng mới nổi trên thị trường nhà ở, chẳng hạn như nhu cầu về nhà bền vững, tiến bộ công nghệ và thay đổi nhân khẩu học. Các công ty phù hợp với các xu hướng này có thể mang lại cơ hội đầu tư hấp dẫn.',
            'Take a long-term investment approach if you believe in the stability and growth potential of the housing sector. Look for companies with solid fundamentals and a track record of success. For short-term traders, capitalize on market fluctuations driven by economic reports, interest rate changes, or industry-specific news. Keep a close eye on earnings reports and government housing data releases.' => 'Áp dụng cách tiếp cận đầu tư dài hạn nếu bạn tin vào sự ổn định và tiềm năng tăng trưởng của ngành nhà ở. Tìm kiếm các công ty có nền tảng vững chắc và thành tích thành công. Đối với nhà giao dịch ngắn hạn, tận dụng biến động thị trường do báo cáo kinh tế, thay đổi lãi suất hoặc tin tức cụ thể của ngành. Theo dõi chặt chẽ báo cáo thu nhập và dữ liệu nhà ở của chính phủ.',
            'Affordability:' => 'Khả năng chi trả:',
            'Compared to larger apartments, 1BHK units are more budget-friendly, making them ideal for individuals and young professionals.' => 'So với các căn hộ lớn hơn, các căn hộ 1 phòng ngủ thân thiện với ngân sách hơn, khiến chúng trở nên lý tưởng cho các cá nhân và chuyên gia trẻ.',
            'Convenience:' => 'Tiện lợi:',
            'These apartments are easier to maintain and are perfect for those who prefer a minimalist lifestyle.' => 'Những căn hộ này dễ bảo trì hơn và hoàn hảo cho những người thích lối sống tối giản.',
            'Modern Amenities:' => 'Tiện nghi hiện đại:',
            'Many 1BHK apartments in Dubai come with state-of-the-art facilities such as gyms, swimming pools, and 24/7 security.' => 'Nhiều căn hộ 1 phòng ngủ ở Dubai có các tiện nghi hiện đại như phòng tập thể dục, hồ bơi và an ninh 24/7.',
        ];

        // Normalize whitespace in content for better matching
        $normalizedContent = preg_replace('/\s+/', ' ', $content);

        foreach ($translations as $english => $vietnamese) {
            $normalizedContent = str_replace($english, $vietnamese, $normalizedContent);
        }

        // Restore original formatting by replacing in original content
        foreach ($translations as $english => $vietnamese) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $vietnamese, $content);
        }

        return $content;
    }

    /**
     * Get Arabic blog post content
     */
    protected function getArabicBlogPostContent(string $content): string
    {
        $translations = [
            'Understanding Housing Stocks' => 'فهم أسهم الإسكان',
            'Housing stocks encompass companies involved in various aspects of the real estate industry, including home builders, developers, and related service providers. Factors influencing these stocks range from interest rates and economic indicators to trends in home ownership rates.' => 'تشمل أسهم الإسكان الشركات المشاركة في جوانب مختلفة من صناعة العقارات، بما في ذلك بناة المنازل والمطورين ومقدمي الخدمات ذات الصلة. تتراوح العوامل المؤثرة على هذه الأسهم من أسعار الفائدة والمؤشرات الاقتصادية إلى الاتجاهات في معدلات ملكية المنازل.',
            'Pay close attention to economic indicators such as employment rates, GDP growth, and consumer confidence. A strong economy often correlates with increased demand for housing, benefiting related stocks.' => 'انتبه جيدًا للمؤشرات الاقتصادية مثل معدلات التوظيف ونمو الناتج المحلي الإجمالي وثقة المستهلك. غالبًا ما يرتبط الاقتصاد القوي بزيادة الطلب على الإسكان، مما يفيد الأسهم ذات الصلة.',
            'Lower rates can boost home buying activity, benefiting housing stocks, while higher rates may have the opposite effect.' => 'يمكن أن تؤدي المعدلات المنخفضة إلى تعزيز نشاط شراء المنازل، مما يفيد أسهم الإسكان، في حين أن المعدلات الأعلى قد يكون لها تأثير معاكس.',
            'Identify Emerging Trends' => 'تحديد الاتجاهات الناشئة',
            'Stay informed about emerging trends in the housing market, such as the demand for sustainable homes, technological advancements, and demographic shifts. Companies aligning with these trends may present attractive investment opportunities.' => 'ابق على اطلاع بالاتجاهات الناشئة في سوق الإسكان، مثل الطلب على المنازل المستدامة والتقدم التكنولوجي والتحولات الديموغرافية. قد تقدم الشركات التي تتماشى مع هذه الاتجاهات فرص استثمارية جذابة.',
            'Take a long-term investment approach if you believe in the stability and growth potential of the housing sector. Look for companies with solid fundamentals and a track record of success. For short-term traders, capitalize on market fluctuations driven by economic reports, interest rate changes, or industry-specific news. Keep a close eye on earnings reports and government housing data releases.' => 'اتبع نهج استثمار طويل الأجل إذا كنت تؤمن بالاستقرار وإمكانات النمو في قطاع الإسكان. ابحث عن شركات ذات أساسيات قوية وسجل حافل بالنجاح. بالنسبة للمتداولين على المدى القصير، استفد من تقلبات السوق الناتجة عن التقارير الاقتصادية أو تغييرات أسعار الفائدة أو الأخبار الخاصة بالصناعة. راقب عن كثب تقارير الأرباح وإصدارات بيانات الإسكان الحكومية.',
            'Affordability:' => 'القدرة على تحمل التكاليف:',
            'Compared to larger apartments, 1BHK units are more budget-friendly, making them ideal for individuals and young professionals.' => 'بالمقارنة مع الشقق الأكبر، فإن وحدات غرفة نوم واحدة أكثر ملاءمة للميزانية، مما يجعلها مثالية للأفراد والمهنيين الشباب.',
            'Convenience:' => 'الراحة:',
            'These apartments are easier to maintain and are perfect for those who prefer a minimalist lifestyle.' => 'هذه الشقق أسهل في الصيانة ومثالية لأولئك الذين يفضلون نمط حياة بسيط.',
            'Modern Amenities:' => 'وسائل الراحة الحديثة:',
            'Many 1BHK apartments in Dubai come with state-of-the-art facilities such as gyms, swimming pools, and 24/7 security.' => 'تأتي العديد من شقق غرفة نوم واحدة في دبي مع مرافق حديثة مثل الصالات الرياضية وحمامات السباحة والأمن على مدار الساعة.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $arabic) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $arabic, $content);
        }

        return $content;
    }

    /**
     * Get French blog post content
     */
    protected function getFrenchBlogPostContent(string $content): string
    {
        $translations = [
            'Understanding Housing Stocks' => 'Comprendre les actions immobilières',
            'Housing stocks encompass companies involved in various aspects of the real estate industry, including home builders, developers, and related service providers. Factors influencing these stocks range from interest rates and economic indicators to trends in home ownership rates.' => 'Les actions immobilières englobent les entreprises impliquées dans divers aspects de l\'industrie immobilière, y compris les constructeurs de maisons, les promoteurs et les fournisseurs de services connexes. Les facteurs influençant ces actions vont des taux d\'intérêt et des indicateurs économiques aux tendances des taux de propriété.',
            'Pay close attention to economic indicators such as employment rates, GDP growth, and consumer confidence. A strong economy often correlates with increased demand for housing, benefiting related stocks.' => 'Portez une attention particulière aux indicateurs économiques tels que les taux d\'emploi, la croissance du PIB et la confiance des consommateurs. Une économie forte est souvent corrélée à une demande accrue de logements, bénéficiant aux actions connexes.',
            'Lower rates can boost home buying activity, benefiting housing stocks, while higher rates may have the opposite effect.' => 'Des taux plus bas peuvent stimuler l\'activité d\'achat de maisons, bénéficiant aux actions immobilières, tandis que des taux plus élevés peuvent avoir l\'effet inverse.',
            'Identify Emerging Trends' => 'Identifier les tendances émergentes',
            'Stay informed about emerging trends in the housing market, such as the demand for sustainable homes, technological advancements, and demographic shifts. Companies aligning with these trends may present attractive investment opportunities.' => 'Restez informé des tendances émergentes sur le marché du logement, telles que la demande de maisons durables, les avancées technologiques et les changements démographiques. Les entreprises qui s\'alignent sur ces tendances peuvent présenter des opportunités d\'investissement attrayantes.',
            'Take a long-term investment approach if you believe in the stability and growth potential of the housing sector. Look for companies with solid fundamentals and a track record of success. For short-term traders, capitalize on market fluctuations driven by economic reports, interest rate changes, or industry-specific news. Keep a close eye on earnings reports and government housing data releases.' => 'Adoptez une approche d\'investissement à long terme si vous croyez en la stabilité et au potentiel de croissance du secteur du logement. Recherchez des entreprises avec des fondamentaux solides et un historique de succès. Pour les traders à court terme, capitalisez sur les fluctuations du marché motivées par les rapports économiques, les changements de taux d\'intérêt ou les nouvelles spécifiques à l\'industrie. Surveillez de près les rapports de bénéfices et les publications de données sur le logement du gouvernement.',
            'Affordability:' => 'Abordabilité:',
            'Compared to larger apartments, 1BHK units are more budget-friendly, making them ideal for individuals and young professionals.' => 'Comparées aux appartements plus grands, les unités 1 chambre sont plus abordables, ce qui les rend idéales pour les particuliers et les jeunes professionnels.',
            'Convenience:' => 'Commodité:',
            'These apartments are easier to maintain and are perfect for those who prefer a minimalist lifestyle.' => 'Ces appartements sont plus faciles à entretenir et sont parfaits pour ceux qui préfèrent un style de vie minimaliste.',
            'Modern Amenities:' => 'Équipements modernes:',
            'Many 1BHK apartments in Dubai come with state-of-the-art facilities such as gyms, swimming pools, and 24/7 security.' => 'De nombreux appartements 1 chambre à Dubaï sont équipés d\'installations de pointe telles que des gymnases, des piscines et une sécurité 24h/24 et 7j/7.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $french) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $french, $content);
        }

        return $content;
    }

    /**
     * Get Indonesian blog post content
     */
    protected function getIndonesianBlogPostContent(string $content): string
    {
        $translations = [
            'Understanding Housing Stocks' => 'Memahami Saham Perumahan',
            'Housing stocks encompass companies involved in various aspects of the real estate industry, including home builders, developers, and related service providers. Factors influencing these stocks range from interest rates and economic indicators to trends in home ownership rates.' => 'Saham perumahan mencakup perusahaan yang terlibat dalam berbagai aspek industri real estat, termasuk pembangun rumah, pengembang, dan penyedia layanan terkait. Faktor yang mempengaruhi saham ini berkisar dari suku bunga dan indikator ekonomi hingga tren tingkat kepemilikan rumah.',
            'Pay close attention to economic indicators such as employment rates, GDP growth, and consumer confidence. A strong economy often correlates with increased demand for housing, benefiting related stocks.' => 'Perhatikan indikator ekonomi seperti tingkat pekerjaan, pertumbuhan PDB, dan kepercayaan konsumen. Ekonomi yang kuat sering berkorelasi dengan peningkatan permintaan perumahan, menguntungkan saham terkait.',
            'Lower rates can boost home buying activity, benefiting housing stocks, while higher rates may have the opposite effect.' => 'Suku bunga yang lebih rendah dapat meningkatkan aktivitas pembelian rumah, menguntungkan saham perumahan, sementara suku bunga yang lebih tinggi mungkin memiliki efek sebaliknya.',
            'Identify Emerging Trends' => 'Identifikasi Tren yang Muncul',
            'Stay informed about emerging trends in the housing market, such as the demand for sustainable homes, technological advancements, and demographic shifts. Companies aligning with these trends may present attractive investment opportunities.' => 'Tetap terinformasi tentang tren yang muncul di pasar perumahan, seperti permintaan untuk rumah berkelanjutan, kemajuan teknologi, dan pergeseran demografis. Perusahaan yang sejalan dengan tren ini mungkin menawarkan peluang investasi yang menarik.',
            'Take a long-term investment approach if you believe in the stability and growth potential of the housing sector. Look for companies with solid fundamentals and a track record of success. For short-term traders, capitalize on market fluctuations driven by economic reports, interest rate changes, or industry-specific news. Keep a close eye on earnings reports and government housing data releases.' => 'Ambil pendekatan investasi jangka panjang jika Anda percaya pada stabilitas dan potensi pertumbuhan sektor perumahan. Cari perusahaan dengan fundamental yang solid dan catatan kesuksesan. Untuk pedagang jangka pendek, manfaatkan fluktuasi pasar yang didorong oleh laporan ekonomi, perubahan suku bunga, atau berita khusus industri. Pantau laporan pendapatan dan rilis data perumahan pemerintah dengan cermat.',
            'Affordability:' => 'Keterjangkauan:',
            'Compared to larger apartments, 1BHK units are more budget-friendly, making them ideal for individuals and young professionals.' => 'Dibandingkan dengan apartemen yang lebih besar, unit 1 kamar tidur lebih ramah anggaran, menjadikannya ideal untuk individu dan profesional muda.',
            'Convenience:' => 'Kenyamanan:',
            'These apartments are easier to maintain and are perfect for those who prefer a minimalist lifestyle.' => 'Apartemen ini lebih mudah dipelihara dan sempurna bagi mereka yang lebih suka gaya hidup minimalis.',
            'Modern Amenities:' => 'Fasilitas Modern:',
            'Many 1BHK apartments in Dubai come with state-of-the-art facilities such as gyms, swimming pools, and 24/7 security.' => 'Banyak apartemen 1 kamar tidur di Dubai dilengkapi dengan fasilitas canggih seperti gym, kolam renang, dan keamanan 24/7.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $indonesian) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $indonesian, $content);
        }

        return $content;
    }

    protected function getVietnameseCareerContent(string $content): string
    {
        $translations = [
            'Responsibilities' => 'Trách nhiệm',
            'Product knowledge: Deeply understand the technology and features of the product area to which you are assigned.' => 'Kiến thức sản phẩm: Hiểu sâu về công nghệ và tính năng của khu vực sản phẩm mà bạn được phân công.',
            'Research: Provide human and business impact and insights for products.' => 'Nghiên cứu: Cung cấp tác động con người và kinh doanh cũng như những hiểu biết cho sản phẩm.',
            'Deliverables: Create deliverables for your product area (for example competitive analyses, user flows, low fidelity wireframes, high fidelity mockups, prototypes, etc.) that solve real user problems through the user experience.' => 'Sản phẩm: Tạo các sản phẩm cho khu vực sản phẩm của bạn (ví dụ: phân tích cạnh tranh, luồng người dùng, wireframes độ trung thực thấp, mockups độ trung thực cao, nguyên mẫu, v.v.) giải quyết các vấn đề người dùng thực sự thông qua trải nghiệm người dùng.',
            'Communication: Communicate the results of UX activities within your product area to the design team department, cross-functional partners within your product area, and other interested Superformula team members using clear language that simplifies complexity.' => 'Giao tiếp: Truyền đạt kết quả của các hoạt động UX trong khu vực sản phẩm của bạn đến bộ phận đội thiết kế, các đối tác chức năng chéo trong khu vực sản phẩm của bạn và các thành viên nhóm Superformula quan tâm khác bằng ngôn ngữ rõ ràng giúp đơn giản hóa độ phức tạp.',
            'Requirements' => 'Yêu cầu',
            'A portfolio demonstrating well thought through and polished end to end customer journeys' => 'Một danh mục đầu tư chứng minh các hành trình khách hàng từ đầu đến cuối được suy nghĩ kỹ lưỡng và được hoàn thiện',
            '5+ years of industry experience in interactive design and / or visual design' => '5+ năm kinh nghiệm trong ngành về thiết kế tương tác và / hoặc thiết kế hình ảnh',
            'Excellent interpersonal skills' => 'Kỹ năng giao tiếp xuất sắc',
            'Aware of trends in mobile, communications, and collaboration' => 'Nhận thức về xu hướng di động, truyền thông và cộng tác',
            'Ability to create highly polished design prototypes, mockups, and other communication artifacts' => 'Khả năng tạo nguyên mẫu thiết kế được hoàn thiện cao, mockups và các hiện vật truyền thông khác',
            'The ability to scope and estimate efforts accurately and prioritize tasks and goals independently' => 'Khả năng xác định phạm vi và ước tính nỗ lực chính xác và ưu tiên các nhiệm vụ và mục tiêu một cách độc lập',
            'History of impacting shipping products with your work' => 'Lịch sử tác động đến sản phẩm vận chuyển với công việc của bạn',
            'A Bachelor\'s Degree in Design (or related field) or equivalent professional experience' => 'Bằng cử nhân Thiết kế (hoặc lĩnh vực liên quan) hoặc kinh nghiệm chuyên môn tương đương',
            'Proficiency in a variety of design tools such as Figma, Photoshop, Illustrator, and Sketch' => 'Thành thạo nhiều công cụ thiết kế như Figma, Photoshop, Illustrator và Sketch',
            'What\'s on Offer' => 'Đãi ngộ',
            'Annual bonus and holidays, social welfare, and health checks.' => 'Thưởng hàng năm và ngày lễ, phúc lợi xã hội và kiểm tra sức khỏe.',
            'Training and attachment in Taiwan and other Greater China branches.' => 'Đào tạo và gắn kết tại Đài Loan và các chi nhánh Đại Trung Hoa khác.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $vietnamese) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $vietnamese, $content);
        }

        return $content;
    }

    protected function getArabicCareerContent(string $content): string
    {
        $translations = [
            'Responsibilities' => 'المسؤوليات',
            'Product knowledge: Deeply understand the technology and features of the product area to which you are assigned.' => 'معرفة المنتج: فهم عميق للتكنولوجيا والميزات الخاصة بمنطقة المنتج المكلف بها.',
            'Research: Provide human and business impact and insights for products.' => 'البحث: توفير التأثير البشري والتجاري والرؤى للمنتجات.',
            'Deliverables: Create deliverables for your product area (for example competitive analyses, user flows, low fidelity wireframes, high fidelity mockups, prototypes, etc.) that solve real user problems through the user experience.' => 'المخرجات: إنشاء مخرجات لمنطقة المنتج الخاصة بك (على سبيل المثال التحليلات التنافسية، تدفقات المستخدم، الإطارات السلكية منخفضة الدقة، النماذج عالية الدقة، النماذج الأولية، إلخ) التي تحل مشاكل المستخدم الحقيقية من خلال تجربة المستخدم.',
            'Communication: Communicate the results of UX activities within your product area to the design team department, cross-functional partners within your product area, and other interested Superformula team members using clear language that simplifies complexity.' => 'التواصل: توصيل نتائج أنشطة تجربة المستخدم ضمن منطقة المنتج الخاصة بك إلى قسم فريق التصميم، والشركاء متعددي الوظائف ضمن منطقة المنتج الخاصة بك، وأعضاء فريق Superformula المهتمين الآخرين باستخدام لغة واضحة تبسط التعقيد.',
            'Requirements' => 'المتطلبات',
            'A portfolio demonstrating well thought through and polished end to end customer journeys' => 'محفظة توضح رحلات العملاء من البداية إلى النهاية المدروسة جيدًا والمصقولة',
            '5+ years of industry experience in interactive design and / or visual design' => '5+ سنوات من الخبرة في الصناعة في التصميم التفاعلي و / أو التصميم المرئي',
            'Excellent interpersonal skills' => 'مهارات تواصل ممتازة',
            'Aware of trends in mobile, communications, and collaboration' => 'على دراية بالاتجاهات في الهاتف المحمول والاتصالات والتعاون',
            'Ability to create highly polished design prototypes, mockups, and other communication artifacts' => 'القدرة على إنشاء نماذج تصميم مصقولة للغاية ونماذج بالحجم الطبيعي وأدوات اتصال أخرى',
            'The ability to scope and estimate efforts accurately and prioritize tasks and goals independently' => 'القدرة على تحديد نطاق الجهود وتقديرها بدقة وتحديد أولويات المهام والأهداف بشكل مستقل',
            'History of impacting shipping products with your work' => 'تاريخ من التأثير على منتجات الشحن من خلال عملك',
            'A Bachelor\'s Degree in Design (or related field) or equivalent professional experience' => 'درجة البكالوريوس في التصميم (أو مجال ذي صلة) أو خبرة مهنية معادلة',
            'Proficiency in a variety of design tools such as Figma, Photoshop, Illustrator, and Sketch' => 'الكفاءة في مجموعة متنوعة من أدوات التصميم مثل Figma و Photoshop و Illustrator و Sketch',
            'What\'s on Offer' => 'ما هو معروض',
            'Annual bonus and holidays, social welfare, and health checks.' => 'مكافأة سنوية وعطلات ورعاية اجتماعية وفحوصات صحية.',
            'Training and attachment in Taiwan and other Greater China branches.' => 'التدريب والإلحاق في تايوان وفروع الصين الكبرى الأخرى.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $arabic) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $arabic, $content);
        }

        return $content;
    }

    protected function getFrenchCareerContent(string $content): string
    {
        $translations = [
            'Responsibilities' => 'Responsabilités',
            'Product knowledge: Deeply understand the technology and features of the product area to which you are assigned.' => 'Connaissance du produit : Comprendre en profondeur la technologie et les fonctionnalités du domaine de produit auquel vous êtes affecté.',
            'Research: Provide human and business impact and insights for products.' => 'Recherche : Fournir l\'impact humain et commercial et des informations pour les produits.',
            'Deliverables: Create deliverables for your product area (for example competitive analyses, user flows, low fidelity wireframes, high fidelity mockups, prototypes, etc.) that solve real user problems through the user experience.' => 'Livrables : Créer des livrables pour votre domaine de produit (par exemple, analyses concurrentielles, flux utilisateurs, wireframes basse fidélité, maquettes haute fidélité, prototypes, etc.) qui résolvent de vrais problèmes utilisateurs grâce à l\'expérience utilisateur.',
            'Communication: Communicate the results of UX activities within your product area to the design team department, cross-functional partners within your product area, and other interested Superformula team members using clear language that simplifies complexity.' => 'Communication : Communiquer les résultats des activités UX au sein de votre domaine de produit au département de l\'équipe de conception, aux partenaires interfonctionnels au sein de votre domaine de produit et aux autres membres de l\'équipe Superformula intéressés en utilisant un langage clair qui simplifie la complexité.',
            'Requirements' => 'Exigences',
            'A portfolio demonstrating well thought through and polished end to end customer journeys' => 'Un portfolio démontrant des parcours clients de bout en bout bien pensés et soignés',
            '5+ years of industry experience in interactive design and / or visual design' => '5+ ans d\'expérience dans l\'industrie en conception interactive et / ou en conception visuelle',
            'Excellent interpersonal skills' => 'Excellentes compétences interpersonnelles',
            'Aware of trends in mobile, communications, and collaboration' => 'Conscient des tendances en matière de mobile, de communications et de collaboration',
            'Ability to create highly polished design prototypes, mockups, and other communication artifacts' => 'Capacité à créer des prototypes de conception très soignés, des maquettes et d\'autres artefacts de communication',
            'The ability to scope and estimate efforts accurately and prioritize tasks and goals independently' => 'La capacité de définir et d\'estimer les efforts avec précision et de prioriser les tâches et les objectifs de manière indépendante',
            'History of impacting shipping products with your work' => 'Historique d\'impact sur les produits expédiés avec votre travail',
            'A Bachelor\'s Degree in Design (or related field) or equivalent professional experience' => 'Un baccalauréat en design (ou domaine connexe) ou une expérience professionnelle équivalente',
            'Proficiency in a variety of design tools such as Figma, Photoshop, Illustrator, and Sketch' => 'Maîtrise d\'une variété d\'outils de conception tels que Figma, Photoshop, Illustrator et Sketch',
            'What\'s on Offer' => 'Ce qui est offert',
            'Annual bonus and holidays, social welfare, and health checks.' => 'Prime annuelle et congés, aide sociale et examens de santé.',
            'Training and attachment in Taiwan and other Greater China branches.' => 'Formation et attachement à Taiwan et dans d\'autres branches de la Grande Chine.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $french) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $french, $content);
        }

        return $content;
    }

    protected function getIndonesianCareerContent(string $content): string
    {
        $translations = [
            'Responsibilities' => 'Tanggung Jawab',
            'Product knowledge: Deeply understand the technology and features of the product area to which you are assigned.' => 'Pengetahuan produk: Memahami secara mendalam teknologi dan fitur area produk yang Anda tugaskan.',
            'Research: Provide human and business impact and insights for products.' => 'Riset: Memberikan dampak manusia dan bisnis serta wawasan untuk produk.',
            'Deliverables: Create deliverables for your product area (for example competitive analyses, user flows, low fidelity wireframes, high fidelity mockups, prototypes, etc.) that solve real user problems through the user experience.' => 'Hasil kerja: Membuat hasil kerja untuk area produk Anda (misalnya analisis kompetitif, alur pengguna, wireframe fidelitas rendah, mockup fidelitas tinggi, prototipe, dll.) yang memecahkan masalah pengguna nyata melalui pengalaman pengguna.',
            'Communication: Communicate the results of UX activities within your product area to the design team department, cross-functional partners within your product area, and other interested Superformula team members using clear language that simplifies complexity.' => 'Komunikasi: Komunikasikan hasil aktivitas UX dalam area produk Anda ke departemen tim desain, mitra lintas fungsi dalam area produk Anda, dan anggota tim Superformula lain yang tertarik menggunakan bahasa yang jelas yang menyederhanakan kompleksitas.',
            'Requirements' => 'Persyaratan',
            'A portfolio demonstrating well thought through and polished end to end customer journeys' => 'Portofolio yang menunjukkan perjalanan pelanggan dari ujung ke ujung yang dipikirkan dengan baik dan dipoles',
            '5+ years of industry experience in interactive design and / or visual design' => '5+ tahun pengalaman industri dalam desain interaktif dan / atau desain visual',
            'Excellent interpersonal skills' => 'Keterampilan interpersonal yang sangat baik',
            'Aware of trends in mobile, communications, and collaboration' => 'Menyadari tren dalam mobile, komunikasi, dan kolaborasi',
            'Ability to create highly polished design prototypes, mockups, and other communication artifacts' => 'Kemampuan untuk membuat prototipe desain yang sangat dipoles, mockup, dan artefak komunikasi lainnya',
            'The ability to scope and estimate efforts accurately and prioritize tasks and goals independently' => 'Kemampuan untuk menentukan ruang lingkup dan memperkirakan upaya secara akurat dan memprioritaskan tugas dan tujuan secara mandiri',
            'History of impacting shipping products with your work' => 'Riwayat berdampak pada produk pengiriman dengan pekerjaan Anda',
            'A Bachelor\'s Degree in Design (or related field) or equivalent professional experience' => 'Gelar Sarjana Desain (atau bidang terkait) atau pengalaman profesional yang setara',
            'Proficiency in a variety of design tools such as Figma, Photoshop, Illustrator, and Sketch' => 'Kemahiran dalam berbagai alat desain seperti Figma, Photoshop, Illustrator, dan Sketch',
            'What\'s on Offer' => 'Apa yang Ditawarkan',
            'Annual bonus and holidays, social welfare, and health checks.' => 'Bonus tahunan dan hari libur, kesejahteraan sosial, dan pemeriksaan kesehatan.',
            'Training and attachment in Taiwan and other Greater China branches.' => 'Pelatihan dan penempatan di Taiwan dan cabang Greater China lainnya.',
        ];

        // Replace with whitespace-insensitive regex
        foreach ($translations as $english => $indonesian) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $indonesian, $content);
        }

        return $content;
    }

    protected function getVietnameseHomepage2Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 2')->first();
        $content = $page->content;

        $translations = [
            'Find A Home That' => 'Tìm ngôi nhà',
            'We are a real estate agency that will help you find the best residence you dream of.' => 'Chúng tôi là công ty bất động sản giúp bạn tìm ngôi nhà tốt nhất mà bạn mơ ước.',
            'Try Searching For' => 'Thử tìm kiếm',
            'PROPERTY TYPE' => 'LOẠI BẤT ĐỘNG SẢN',
            'View All' => 'Xem tất cả',
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Khám phá các bất động sản tốt nhất của Homzen cho ngôi nhà mơ ước',
            'Featured Properties' => 'Bất động sản nổi bật',
            'View All Properties' => 'Xem tất cả bất động sản',
            'Discover What Sets Our Real Estate Expertise Apart' => 'Khám phá điều làm nên sự khác biệt của chuyên môn bất động sản',
            'Why Choose Us' => 'Tại sao chọn chúng tôi',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys. Our seasoned professionals, armed with extensive market knowledge, walk alongside you through every phase of your property endeavor. We prioritize understanding your unique aspirations, tailoring our expertise to match your vision.' => 'Tại Homzen, cam kết không lay chuyển của chúng tôi là tạo ra những hành trình bất động sản vô song. Các chuyên gia giàu kinh nghiệm, được trang bị kiến thức thị trường sâu rộng, đồng hành cùng bạn qua mọi giai đoạn của nỗ lực bất động sản. Chúng tôi ưu tiên hiểu rõ khát vọng độc đáo của bạn, điều chỉnh chuyên môn để phù hợp với tầm nhìn của bạn.',
            'Transparent Partnerships' => 'Quan hệ đối tác minh bạch',
            'Proven Expertise' => 'Chuyên môn đã được chứng minh',
            'Customized Solutions' => 'Giải pháp tùy chỉnh',
            'Local Area Knowledge' => 'Hiểu biết khu vực địa phương',
            'Buy A New Home' => 'Mua nhà mới',
            'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Khám phá ngôi nhà mơ ước một cách dễ dàng. Khám phá các bất động sản đa dạng và hướng dẫn chuyên gia cho trải nghiệm mua hàng liền mạch.',
            'Learn More' => 'Tìm hiểu thêm',
            'Rent A Home' => 'Thuê nhà',
            'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Khám phá nhà thuê hoàn hảo một cách dễ dàng. Khám phá nhiều danh sách được thiết kế chính xác cho nhu cầu lối sống độc đáo của bạn.',
            'Sell A Home' => 'Bán nhà',
            'Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale.' => 'Bán nhà một cách tự tin với hướng dẫn chuyên gia và chiến lược hiệu quả, trưng bày các tính năng tốt nhất của bất động sản của bạn để bán thành công.',
            'Contact Us' => 'Liên hệ',
            'Our Location For You' => 'Vị trí của chúng tôi dành cho bạn',
            'Explore States' => 'Khám phá các tiểu bang',
            'Best Property Value' => 'Giá trị bất động sản tốt nhất',
            'Top Properties' => 'Bất động sản hàng đầu',
            'Meet Our Agents' => 'Gặp gỡ nhân viên',
            'Our Teams' => 'Đội ngũ của chúng tôi',
            'Mortgage Calculator' => 'Máy tính thế chấp',
            'Calculate your monthly mortgage payments' => 'Tính toán khoản thanh toán thế chấp hàng tháng',
            'What\'s People Say\'s' => 'Mọi người nói gì',
            'Our Testimonials' => 'Đánh giá của khách hàng',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Đội ngũ giàu kinh nghiệm của chúng tôi xuất sắc trong lĩnh vực bất động sản với nhiều năm điều hướng thị trường thành công, cung cấp các quyết định sáng suốt và kết quả tối ưu.',
            'Helpful Homzen Guides' => 'Hướng dẫn hữu ích từ Homzen',
            'Latest News' => 'Tin tức mới nhất',
        ];

        foreach ($translations as $english => $vietnamese) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $vietnamese, $content);
        }

        return $content;
    }

    protected function getArabicHomepage2Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 2')->first();
        $content = $page->content;

        $translations = [
            'Find A Home That' => 'ابحث عن منزل',
            'We are a real estate agency that will help you find the best residence you dream of.' => 'نحن وكالة عقارية ستساعدك في العثور على أفضل مسكن تحلم به.',
            'Try Searching For' => 'جرب البحث عن',
            'PROPERTY TYPE' => 'نوع العقار',
            'View All' => 'عرض الكل',
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'اكتشف أفضل عقارات Homzen لمنزل أحلامك',
            'Featured Properties' => 'عقارات مميزة',
            'View All Properties' => 'عرض جميع العقارات',
            'Discover What Sets Our Real Estate Expertise Apart' => 'اكتشف ما يميز خبرتنا العقارية',
            'Why Choose Us' => 'لماذا تختارنا',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys. Our seasoned professionals, armed with extensive market knowledge, walk alongside you through every phase of your property endeavor. We prioritize understanding your unique aspirations, tailoring our expertise to match your vision.' => 'في Homzen، يكمن التزامنا الثابت في صياغة رحلات عقارية لا مثيل لها. يسير محترفونا ذوو الخبرة، المسلحون بمعرفة واسعة بالسوق، بجانبك خلال كل مرحلة من مساعيك العقارية. نحن نولي الأولوية لفهم تطلعاتك الفريدة، ونصمم خبرتنا لتتناسب مع رؤيتك.',
            'Transparent Partnerships' => 'شراكات شفافة',
            'Proven Expertise' => 'خبرة مثبتة',
            'Customized Solutions' => 'حلول مخصصة',
            'Local Area Knowledge' => 'معرفة المنطقة المحلية',
            'Buy A New Home' => 'شراء منزل جديد',
            'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'اكتشف منزل أحلامك دون عناء. استكشف عقارات متنوعة وإرشادات الخبراء لتجربة شراء سلسة.',
            'Learn More' => 'اعرف المزيد',
            'Rent A Home' => 'استئجار منزل',
            'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'اكتشف إيجارك المثالي دون عناء. استكشف مجموعة متنوعة من القوائم المصممة بدقة لتناسب احتياجات نمط حياتك الفريد.',
            'Sell A Home' => 'بيع منزل',
            'Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale.' => 'بع بثقة مع إرشادات الخبراء واستراتيجيات فعالة، عرض أفضل ميزات عقارك لبيع ناجح.',
            'Contact Us' => 'اتصل بنا',
            'Our Location For You' => 'موقعنا لك',
            'Explore States' => 'استكشف الولايات',
            'Best Property Value' => 'أفضل قيمة عقارية',
            'Top Properties' => 'أفضل العقارات',
            'Meet Our Agents' => 'تعرف على وكلائنا',
            'Our Teams' => 'فرقنا',
            'Mortgage Calculator' => 'حاسبة الرهن العقاري',
            'Calculate your monthly mortgage payments' => 'احسب أقساط الرهن العقاري الشهرية',
            'What\'s People Say\'s' => 'ماذا يقول الناس',
            'Our Testimonials' => 'شهاداتنا',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'يتفوق فريقنا ذو الخبرة في العقارات مع سنوات من التنقل الناجح في السوق، ويقدم قرارات مستنيرة ونتائج مثالية.',
            'Helpful Homzen Guides' => 'أدلة Homzen المفيدة',
            'Latest News' => 'آخر الأخبار',
        ];

        foreach ($translations as $english => $arabic) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $arabic, $content);
        }

        return $content;
    }

    protected function getFrenchHomepage2Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 2')->first();
        $content = $page->content;

        $translations = [
            'Find A Home That' => 'Trouvez une maison qui',
            'We are a real estate agency that will help you find the best residence you dream of.' => 'Nous sommes une agence immobilière qui vous aidera à trouver la meilleure résidence dont vous rêvez.',
            'Try Searching For' => 'Essayez de rechercher',
            'PROPERTY TYPE' => 'TYPE DE PROPRIÉTÉ',
            'View All' => 'Voir tout',
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Découvrez les meilleures propriétés de Homzen pour la maison de vos rêves',
            'Featured Properties' => 'Propriétés en vedette',
            'View All Properties' => 'Voir toutes les propriétés',
            'Discover What Sets Our Real Estate Expertise Apart' => 'Découvrez ce qui distingue notre expertise immobilière',
            'Why Choose Us' => 'Pourquoi nous choisir',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys. Our seasoned professionals, armed with extensive market knowledge, walk alongside you through every phase of your property endeavor. We prioritize understanding your unique aspirations, tailoring our expertise to match your vision.' => 'Chez Homzen, notre engagement inébranlable consiste à créer des parcours immobiliers inégalés. Nos professionnels chevronnés, armés de vastes connaissances du marché, vous accompagnent à chaque étape de votre projet immobilier. Nous privilégions la compréhension de vos aspirations uniques, en adaptant notre expertise à votre vision.',
            'Transparent Partnerships' => 'Partenariats transparents',
            'Proven Expertise' => 'Expertise éprouvée',
            'Customized Solutions' => 'Solutions personnalisées',
            'Local Area Knowledge' => 'Connaissance du secteur local',
            'Buy A New Home' => 'Acheter une nouvelle maison',
            'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Découvrez la maison de vos rêves sans effort. Explorez des propriétés diverses et bénéficiez de conseils d\'experts pour une expérience d\'achat fluide.',
            'Learn More' => 'En savoir plus',
            'Rent A Home' => 'Louer une maison',
            'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Découvrez votre location parfaite sans effort. Explorez une variété diversifiée d\'annonces adaptées précisément à vos besoins de style de vie uniques.',
            'Sell A Home' => 'Vendre une maison',
            'Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale.' => 'Vendez en toute confiance avec des conseils d\'experts et des stratégies efficaces, mettant en valeur les meilleures caractéristiques de votre propriété pour une vente réussie.',
            'Contact Us' => 'Contact',
            'Our Location For You' => 'Notre emplacement pour vous',
            'Explore States' => 'Explorer les états',
            'Best Property Value' => 'Meilleure valeur immobilière',
            'Top Properties' => 'Meilleures propriétés',
            'Meet Our Agents' => 'Rencontrez nos agents',
            'Our Teams' => 'Nos équipes',
            'Mortgage Calculator' => 'Calculateur hypothécaire',
            'Calculate your monthly mortgage payments' => 'Calculez vos mensualités hypothécaires',
            'What\'s People Say\'s' => 'Ce que disent les gens',
            'Our Testimonials' => 'Nos témoignages',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Notre équipe expérimentée excelle dans l\'immobilier avec des années de navigation réussie sur le marché, offrant des décisions éclairées et des résultats optimaux.',
            'Helpful Homzen Guides' => 'Guides utiles Homzen',
            'Latest News' => 'Dernières nouvelles',
        ];

        foreach ($translations as $english => $french) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $french, $content);
        }

        return $content;
    }

    protected function getIndonesianHomepage2Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 2')->first();
        $content = $page->content;

        $translations = [
            'Find A Home That' => 'Temukan rumah yang',
            'We are a real estate agency that will help you find the best residence you dream of.' => 'Kami adalah agen properti yang akan membantu Anda menemukan tempat tinggal terbaik yang Anda impikan.',
            'Try Searching For' => 'Coba cari',
            'PROPERTY TYPE' => 'JENIS PROPERTI',
            'View All' => 'Lihat semua',
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Temukan properti terbaik Homzen untuk rumah impian Anda',
            'Featured Properties' => 'Properti unggulan',
            'View All Properties' => 'Lihat semua properti',
            'Discover What Sets Our Real Estate Expertise Apart' => 'Temukan apa yang membedakan keahlian properti kami',
            'Why Choose Us' => 'Mengapa memilih kami',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys. Our seasoned professionals, armed with extensive market knowledge, walk alongside you through every phase of your property endeavor. We prioritize understanding your unique aspirations, tailoring our expertise to match your vision.' => 'Di Homzen, komitmen kami yang teguh terletak pada menciptakan perjalanan properti yang tak tertandingi. Para profesional berpengalaman kami, dipersenjatai dengan pengetahuan pasar yang luas, berjalan bersama Anda melalui setiap fase upaya properti Anda. Kami memprioritaskan pemahaman aspirasi unik Anda, menyesuaikan keahlian kami agar sesuai dengan visi Anda.',
            'Transparent Partnerships' => 'Kemitraan transparan',
            'Proven Expertise' => 'Keahlian terbukti',
            'Customized Solutions' => 'Solusi yang disesuaikan',
            'Local Area Knowledge' => 'Pengetahuan area lokal',
            'Buy A New Home' => 'Beli rumah baru',
            'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Temukan rumah impian Anda dengan mudah. Jelajahi properti yang beragam dan bimbingan ahli untuk pengalaman pembelian yang lancar.',
            'Learn More' => 'Pelajari lebih lanjut',
            'Rent A Home' => 'Sewa rumah',
            'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Temukan sewa sempurna Anda dengan mudah. Jelajahi berbagai daftar yang disesuaikan dengan tepat untuk memenuhi kebutuhan gaya hidup unik Anda.',
            'Sell A Home' => 'Jual rumah',
            'Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale.' => 'Jual dengan percaya diri dengan bimbingan ahli dan strategi efektif, menampilkan fitur terbaik properti Anda untuk penjualan yang sukses.',
            'Contact Us' => 'Kontak',
            'Our Location For You' => 'Lokasi kami untuk Anda',
            'Explore States' => 'Jelajahi negara bagian',
            'Best Property Value' => 'Nilai properti terbaik',
            'Top Properties' => 'Properti teratas',
            'Meet Our Agents' => 'Temui agen kami',
            'Our Teams' => 'Tim kami',
            'Mortgage Calculator' => 'Kalkulator hipotek',
            'Calculate your monthly mortgage payments' => 'Hitung pembayaran hipotek bulanan Anda',
            'What\'s People Say\'s' => 'Apa kata orang',
            'Our Testimonials' => 'Testimoni kami',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Tim berpengalaman kami unggul di bidang properti dengan pengalaman bertahun-tahun dalam navigasi pasar yang sukses, menawarkan keputusan yang tepat dan hasil yang optimal.',
            'Helpful Homzen Guides' => 'Panduan Homzen yang bermanfaat',
            'Latest News' => 'Berita terbaru',
        ];

        foreach ($translations as $english => $indonesian) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $indonesian, $content);
        }

        return $content;
    }

    // Homepage 3 Translations
    protected function getVietnameseHomepage3Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 3')->first();
        $content = $page->content;

        $translations = [
            'Indulge in Your' => 'Tận hưởng',
            'Sanctuary,Safe House' => 'Thánh địa,Ngôi nhà an toàn',
            'Discover your private oasis at Homzen, where every corner, from the spacious garden to the relaxing pool, is crafted for your comfort and enjoyment.' => 'Khám phá ốc đảo riêng tư của bạn tại Homzen, nơi mỗi góc, từ khu vườn rộng rãi đến hồ bơi thư giãn, được tạo ra cho sự thoải mái và thưởng thức của bạn.',
            'Properties By Cities' => 'Bất động sản theo thành phố',
            'EXPLORE CITIES' => 'KHÁM PHÁ THÀNH PHỐ',
            'Recommended For You' => 'Được đề xuất cho bạn',
            'Features Properties' => 'Bất động sản nổi bật',
        ];

        foreach ($translations as $english => $vietnamese) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $vietnamese, $content);
        }

        return $content;
    }

    protected function getArabicHomepage3Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 3')->first();
        $content = $page->content;

        $translations = [
            'Indulge in Your' => 'انغمس في',
            'Sanctuary,Safe House' => 'ملاذك،منزل آمن',
            'Discover your private oasis at Homzen, where every corner, from the spacious garden to the relaxing pool, is crafted for your comfort and enjoyment.' => 'اكتشف واحتك الخاصة في Homzen، حيث كل زاوية، من الحديقة الواسعة إلى حمام السباحة المريح، مصممة لراحتك واستمتاعك.',
            'Properties By Cities' => 'العقارات حسب المدينة',
            'EXPLORE CITIES' => 'استكشف المدن',
            'Recommended For You' => 'موصى به لك',
            'Features Properties' => 'عقارات مميزة',
        ];

        foreach ($translations as $english => $arabic) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $arabic, $content);
        }

        return $content;
    }

    protected function getFrenchHomepage3Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 3')->first();
        $content = $page->content;

        $translations = [
            'Indulge in Your' => 'Plongez dans votre',
            'Sanctuary,Safe House' => 'Sanctuaire,Maison sûre',
            'Discover your private oasis at Homzen, where every corner, from the spacious garden to the relaxing pool, is crafted for your comfort and enjoyment.' => 'Découvrez votre oasis privée chez Homzen, où chaque coin, du jardin spacieux à la piscine relaxante, est conçu pour votre confort et plaisir.',
            'Properties By Cities' => 'Propriétés par villes',
            'EXPLORE CITIES' => 'EXPLORER LES VILLES',
            'Recommended For You' => 'Recommandé pour vous',
            'Features Properties' => 'Propriétés en vedette',
        ];

        foreach ($translations as $english => $french) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $french, $content);
        }

        return $content;
    }

    protected function getIndonesianHomepage3Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 3')->first();
        $content = $page->content;

        $translations = [
            'Indulge in Your' => 'Nikmati',
            'Sanctuary,Safe House' => 'Tempat suci,Rumah aman',
            'Discover your private oasis at Homzen, where every corner, from the spacious garden to the relaxing pool, is crafted for your comfort and enjoyment.' => 'Temukan oasis pribadi Anda di Homzen, di mana setiap sudut, dari taman yang luas hingga kolam renang yang menenangkan, dirancang untuk kenyamanan dan kesenangan Anda.',
            'Properties By Cities' => 'Properti berdasarkan kota',
            'EXPLORE CITIES' => 'JELAJAHI KOTA',
            'Recommended For You' => 'Direkomendasikan untuk Anda',
            'Features Properties' => 'Properti unggulan',
        ];

        foreach ($translations as $english => $indonesian) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $indonesian, $content);
        }

        return $content;
    }

    // Homepage 4 Translations
    protected function getVietnameseHomepage4Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 4')->first();
        $content = $page->content;

        $translations = [
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Khám phá các bất động sản tốt nhất của Homzen cho ngôi nhà mơ ước',
            'FEATURED PROPERTIES' => 'BẤT ĐỘNG SẢN NỔI BẬT',
            'View All Properties' => 'Xem tất cả bất động sản',
            'Discover What Sets Our' => 'Khám phá điều làm nên',
            'WHAT WE DO' => 'CHÚNG TÔI LÀM GÌ',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys.' => 'Tại Homzen, cam kết không lay chuyển của chúng tôi là tạo ra những hành trình bất động sản vô song.',
            'Buy A New Home' => 'Mua nhà mới',
            'Explore diverse properties and expert guidance for a seamless buying experience.' => 'Khám phá các bất động sản đa dạng và hướng dẫn chuyên gia cho trải nghiệm mua hàng liền mạch.',
            'Learn More' => 'Tìm hiểu thêm',
            'Rent A Home' => 'Thuê nhà',
            'Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Khám phá nhiều danh sách được thiết kế chính xác cho nhu cầu lối sống độc đáo của bạn.',
            'Showcasing your property\'s best features for a successful sale.' => 'Trưng bày các tính năng tốt nhất của bất động sản của bạn để bán thành công.',
            'Our Location For You' => 'Vị trí của chúng tôi',
            'Explore Cities' => 'Khám phá thành phố',
            'Meet Our Agents' => 'Gặp môi giới của chúng tôi',
            'OUR TEAMS' => 'ĐỘI NGŨ CỦA CHÚNG TÔI',
            'Mortgage Calculator' => 'Máy tính thế chấp',
            'Calculate your monthly mortgage payments' => 'Tính toán khoản thanh toán thế chấp hàng tháng',
            'Recommended for you' => 'Được đề xuất cho bạn',
            'TOP PROPERTIES' => 'BẤT ĐỘNG SẢN HÀNG ĐẦU',
            'What\'s People Say\'s' => 'Mọi người nói gì',
            'OUR TESTIMONIALS' => 'ĐÁNH GIÁ CỦA KHÁCH HÀNG',
            'Why Choose Homzen' => 'Tại sao chọn Homzen',
            'OUR BENIFIT' => 'LỢI ÍCH CỦA CHÚNG TÔI',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Đội ngũ giàu kinh nghiệm của chúng tôi xuất sắc trong lĩnh vực bất động sản với nhiều năm điều hướng thị trường thành công, mang đến quyết định sáng suốt và kết quả tối ưu.',
            'Sell Your Property' => 'Bán bất động sản của bạn',
            'Get the best value with our professional selling strategies and market insights.' => 'Nhận giá trị tốt nhất với chiến lược bán hàng chuyên nghiệp và hiểu biết thị trường của chúng tôi.',
            'Rent A Property' => 'Thuê bất động sản',
            'Find the perfect rental property with our extensive listings and support.' => 'Tìm bất động sản cho thuê hoàn hảo với danh sách phong phú và hỗ trợ của chúng tôi.',
            'Property Management' => 'Quản lý bất động sản',
            'Professional management services to keep your property in top condition.' => 'Dịch vụ quản lý chuyên nghiệp để giữ bất động sản của bạn ở tình trạng tốt nhất.',
            'Real Estate Consulting' => 'Tư vấn bất động sản',
            'Expert advice and insights to help you make informed real estate decisions.' => 'Lời khuyên chuyên gia và hiểu biết để giúp bạn đưa ra quyết định bất động sản sáng suốt.',
            'Mortgage Services' => 'Dịch vụ thế chấp',
            'Find the best mortgage rates and options with our comprehensive services.' => 'Tìm lãi suất thế chấp và các tùy chọn tốt nhất với dịch vụ toàn diện của chúng tôi.',
            'Investment Properties' => 'Bất động sản đầu tư',
            'Discover lucrative investment opportunities and maximize your returns.' => 'Khám phá cơ hội đầu tư sinh lời và tối đa hóa lợi nhuận của bạn.',
            'Relocation Services' => 'Dịch vụ di dời',
            'Smooth and hassle-free relocation services to help you move with ease.' => 'Dịch vụ di dời thuận lợi và không rắc rối để giúp bạn di chuyển dễ dàng.',
            'Helpful Homzen Guides' => 'Hướng dẫn hữu ích từ Homzen',
            'LATEST NEWS' => 'TIN TỨC MỚI NHẤT',
            'List Your Properties On Homzen, Join Us Now!' => 'Đăng bất động sản của bạn trên Homzen, tham gia ngay!',
            'BECOME PARTNERS' => 'TRỞ THÀNH ĐỐI TÁC',
            'Become A Hosting' => 'Trở thành chủ nhà',
        ];

        foreach ($translations as $english => $vietnamese) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $vietnamese, $content);
        }

        return $content;
    }

    protected function getArabicHomepage4Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 4')->first();
        $content = $page->content;

        $translations = [
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'اكتشف أفضل عقارات Homzen لمنزل أحلامك',
            'FEATURED PROPERTIES' => 'العقارات المميزة',
            'View All Properties' => 'عرض جميع العقارات',
            'Discover What Sets Our' => 'اكتشف ما يميز',
            'WHAT WE DO' => 'ماذا نفعل',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys.' => 'في Homzen، يكمن التزامنا الثابت في صياغة رحلات عقارية لا مثيل لها.',
            'Buy A New Home' => 'شراء منزل جديد',
            'Explore diverse properties and expert guidance for a seamless buying experience.' => 'استكشف عقارات متنوعة وإرشادات الخبراء لتجربة شراء سلسة.',
            'Learn More' => 'معرفة المزيد',
            'Rent A Home' => 'استئجار منزل',
            'Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'استكشف مجموعة متنوعة من القوائم المصممة بدقة لتناسب احتياجات نمط حياتك الفريد.',
            'Showcasing your property\'s best features for a successful sale.' => 'عرض أفضل ميزات عقارك لبيع ناجح.',
            'Our Location For You' => 'موقعنا لك',
            'Explore Cities' => 'استكشف المدن',
            'Meet Our Agents' => 'تعرف على وكلائنا',
            'OUR TEAMS' => 'فرقنا',
            'Mortgage Calculator' => 'حاسبة الرهن العقاري',
            'Calculate your monthly mortgage payments' => 'احسب دفعات الرهن العقاري الشهرية',
            'Recommended for you' => 'موصى به لك',
            'TOP PROPERTIES' => 'أفضل العقارات',
            'What\'s People Say\'s' => 'ما يقوله الناس',
            'OUR TESTIMONIALS' => 'شهاداتنا',
            'Why Choose Homzen' => 'لماذا تختار Homzen',
            'OUR BENIFIT' => 'فوائدنا',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'يتفوق فريقنا المتمرس في العقارات بسنوات من التنقل الناجح في السوق، ويقدم قرارات مستنيرة ونتائج مثالية.',
            'Sell Your Property' => 'بيع عقارك',
            'Get the best value with our professional selling strategies and market insights.' => 'احصل على أفضل قيمة مع استراتيجيات البيع الاحترافية ورؤى السوق لدينا.',
            'Rent A Property' => 'استئجار عقار',
            'Find the perfect rental property with our extensive listings and support.' => 'اعثر على عقار الإيجار المثالي مع قوائمنا الواسعة والدعم.',
            'Property Management' => 'إدارة العقارات',
            'Professional management services to keep your property in top condition.' => 'خدمات إدارة احترافية للحفاظ على عقارك في أفضل حالة.',
            'Real Estate Consulting' => 'استشارات عقارية',
            'Expert advice and insights to help you make informed real estate decisions.' => 'نصائح الخبراء والرؤى لمساعدتك على اتخاذ قرارات عقارية مستنيرة.',
            'Mortgage Services' => 'خدمات الرهن العقاري',
            'Find the best mortgage rates and options with our comprehensive services.' => 'اعثر على أفضل أسعار الرهن العقاري والخيارات مع خدماتنا الشاملة.',
            'Investment Properties' => 'عقارات استثمارية',
            'Discover lucrative investment opportunities and maximize your returns.' => 'اكتشف فرص استثمارية مربحة وزد عوائدك إلى الحد الأقصى.',
            'Relocation Services' => 'خدمات النقل',
            'Smooth and hassle-free relocation services to help you move with ease.' => 'خدمات نقل سلسة وخالية من المتاعب لمساعدتك على الانتقال بسهولة.',
            'Helpful Homzen Guides' => 'أدلة Homzen المفيدة',
            'LATEST NEWS' => 'آخر الأخبار',
            'List Your Properties On Homzen, Join Us Now!' => 'أدرج عقاراتك على Homzen، انضم إلينا الآن!',
            'BECOME PARTNERS' => 'كن شريكًا',
            'Become A Hosting' => 'كن مضيفًا',
        ];

        foreach ($translations as $english => $arabic) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $arabic, $content);
        }

        return $content;
    }

    protected function getFrenchHomepage4Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 4')->first();
        $content = $page->content;

        $translations = [
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Découvrez les meilleures propriétés de Homzen pour la maison de vos rêves',
            'FEATURED PROPERTIES' => 'PROPRIÉTÉS EN VEDETTE',
            'View All Properties' => 'Voir toutes les propriétés',
            'Discover What Sets Our' => 'Découvrez ce qui distingue notre',
            'WHAT WE DO' => 'CE QUE NOUS FAISONS',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys.' => 'Chez Homzen, notre engagement inébranlable consiste à créer des parcours immobiliers inégalés.',
            'Buy A New Home' => 'Acheter une nouvelle maison',
            'Explore diverse properties and expert guidance for a seamless buying experience.' => 'Explorez des propriétés diverses et bénéficiez de conseils d\'experts pour une expérience d\'achat fluide.',
            'Learn More' => 'En savoir plus',
            'Rent A Home' => 'Louer une maison',
            'Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Explorez une variété diversifiée d\'annonces adaptées précisément à vos besoins de style de vie uniques.',
            'Showcasing your property\'s best features for a successful sale.' => 'Mettant en valeur les meilleures caractéristiques de votre propriété pour une vente réussie.',
            'Our Location For You' => 'Notre emplacement pour vous',
            'Explore Cities' => 'Explorer les villes',
            'Meet Our Agents' => 'Rencontrez nos agents',
            'OUR TEAMS' => 'NOS ÉQUIPES',
            'Mortgage Calculator' => 'Calculateur hypothécaire',
            'Calculate your monthly mortgage payments' => 'Calculez vos versements hypothécaires mensuels',
            'Recommended for you' => 'Recommandé pour vous',
            'TOP PROPERTIES' => 'MEILLEURES PROPRIÉTÉS',
            'What\'s People Say\'s' => 'Ce que disent les gens',
            'OUR TESTIMONIALS' => 'NOS TÉMOIGNAGES',
            'Why Choose Homzen' => 'Pourquoi choisir Homzen',
            'OUR BENIFIT' => 'NOS AVANTAGES',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Notre équipe expérimentée excelle dans l\'immobilier avec des années de navigation réussie sur le marché, offrant des décisions éclairées et des résultats optimaux.',
            'Sell Your Property' => 'Vendez votre propriété',
            'Get the best value with our professional selling strategies and market insights.' => 'Obtenez la meilleure valeur avec nos stratégies de vente professionnelles et nos connaissances du marché.',
            'Rent A Property' => 'Louer une propriété',
            'Find the perfect rental property with our extensive listings and support.' => 'Trouvez la propriété locative parfaite avec nos annonces étendues et notre soutien.',
            'Property Management' => 'Gestion immobilière',
            'Professional management services to keep your property in top condition.' => 'Services de gestion professionnels pour maintenir votre propriété en parfait état.',
            'Real Estate Consulting' => 'Conseil immobilier',
            'Expert advice and insights to help you make informed real estate decisions.' => 'Conseils d\'experts et informations pour vous aider à prendre des décisions immobilières éclairées.',
            'Mortgage Services' => 'Services hypothécaires',
            'Find the best mortgage rates and options with our comprehensive services.' => 'Trouvez les meilleurs taux hypothécaires et options avec nos services complets.',
            'Investment Properties' => 'Propriétés d\'investissement',
            'Discover lucrative investment opportunities and maximize your returns.' => 'Découvrez des opportunités d\'investissement lucratives et maximisez vos rendements.',
            'Relocation Services' => 'Services de relocation',
            'Smooth and hassle-free relocation services to help you move with ease.' => 'Services de déménagement fluides et sans tracas pour vous aider à déménager en toute simplicité.',
            'Helpful Homzen Guides' => 'Guides utiles Homzen',
            'LATEST NEWS' => 'DERNIÈRES NOUVELLES',
            'List Your Properties On Homzen, Join Us Now!' => 'Listez vos propriétés sur Homzen, rejoignez-nous maintenant!',
            'BECOME PARTNERS' => 'DEVENEZ PARTENAIRES',
            'Become A Hosting' => 'Devenez un hôte',
        ];

        foreach ($translations as $english => $french) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $french, $content);
        }

        return $content;
    }

    protected function getIndonesianHomepage4Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 4')->first();
        $content = $page->content;

        $translations = [
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Temukan properti terbaik Homzen untuk rumah impian Anda',
            'FEATURED PROPERTIES' => 'PROPERTI UNGGULAN',
            'View All Properties' => 'Lihat semua properti',
            'Discover What Sets Our' => 'Temukan apa yang membedakan',
            'WHAT WE DO' => 'APA YANG KAMI LAKUKAN',
            'At Homzen, our unwavering commitment lies in crafting unparalleled real estate journeys.' => 'Di Homzen, komitmen kami yang teguh terletak pada menciptakan perjalanan properti yang tak tertandingi.',
            'Buy A New Home' => 'Beli rumah baru',
            'Explore diverse properties and expert guidance for a seamless buying experience.' => 'Jelajahi properti yang beragam dan bimbingan ahli untuk pengalaman pembelian yang lancar.',
            'Learn More' => 'Pelajari lebih lanjut',
            'Rent A Home' => 'Sewa rumah',
            'Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Jelajahi berbagai daftar yang disesuaikan dengan tepat untuk memenuhi kebutuhan gaya hidup unik Anda.',
            'Showcasing your property\'s best features for a successful sale.' => 'Menampilkan fitur terbaik properti Anda untuk penjualan yang sukses.',
            'Our Location For You' => 'Lokasi kami untuk Anda',
            'Explore Cities' => 'Jelajahi kota',
            'Meet Our Agents' => 'Temui agen kami',
            'OUR TEAMS' => 'TIM KAMI',
            'Mortgage Calculator' => 'Kalkulator hipotek',
            'Calculate your monthly mortgage payments' => 'Hitung pembayaran hipotek bulanan Anda',
            'Recommended for you' => 'Direkomendasikan untuk Anda',
            'TOP PROPERTIES' => 'PROPERTI TERATAS',
            'What\'s People Say\'s' => 'Apa kata orang',
            'OUR TESTIMONIALS' => 'TESTIMONI KAMI',
            'Why Choose Homzen' => 'Mengapa memilih Homzen',
            'OUR BENIFIT' => 'MANFAAT KAMI',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Tim berpengalaman kami unggul di bidang properti dengan pengalaman bertahun-tahun dalam navigasi pasar yang sukses, menawarkan keputusan yang tepat dan hasil yang optimal.',
            'Sell Your Property' => 'Jual properti Anda',
            'Get the best value with our professional selling strategies and market insights.' => 'Dapatkan nilai terbaik dengan strategi penjualan profesional dan wawasan pasar kami.',
            'Rent A Property' => 'Sewa properti',
            'Find the perfect rental property with our extensive listings and support.' => 'Temukan properti sewa yang sempurna dengan daftar ekstensif dan dukungan kami.',
            'Property Management' => 'Manajemen properti',
            'Professional management services to keep your property in top condition.' => 'Layanan manajemen profesional untuk menjaga properti Anda dalam kondisi terbaik.',
            'Real Estate Consulting' => 'Konsultasi properti',
            'Expert advice and insights to help you make informed real estate decisions.' => 'Nasihat ahli dan wawasan untuk membantu Anda membuat keputusan properti yang tepat.',
            'Mortgage Services' => 'Layanan hipotek',
            'Find the best mortgage rates and options with our comprehensive services.' => 'Temukan tarif hipotek dan opsi terbaik dengan layanan komprehensif kami.',
            'Investment Properties' => 'Properti investasi',
            'Discover lucrative investment opportunities and maximize your returns.' => 'Temukan peluang investasi yang menguntungkan dan maksimalkan pengembalian Anda.',
            'Relocation Services' => 'Layanan relokasi',
            'Smooth and hassle-free relocation services to help you move with ease.' => 'Layanan relokasi yang lancar dan bebas repot untuk membantu Anda pindah dengan mudah.',
            'Helpful Homzen Guides' => 'Panduan Homzen yang bermanfaat',
            'LATEST NEWS' => 'BERITA TERBARU',
            'List Your Properties On Homzen, Join Us Now!' => 'Daftarkan properti Anda di Homzen, bergabunglah dengan kami sekarang!',
            'BECOME PARTNERS' => 'MENJADI MITRA',
            'Become A Hosting' => 'Menjadi tuan rumah',
        ];

        foreach ($translations as $english => $indonesian) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $indonesian, $content);
        }

        return $content;
    }

    // Homepage 5 Translations
    protected function getVietnameseHomepage5Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 5')->first();
        $content = $page->content;

        $translations = [
            'Why Choose Homzen' => 'Tại sao chọn Homzen',
            'Our Benefit' => 'Lợi ích của chúng tôi',
            'Buy A New Home' => 'Mua nhà mới',
            'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'Khám phá ngôi nhà mơ ước một cách dễ dàng. Khám phá các bất động sản đa dạng và hướng dẫn chuyên gia cho trải nghiệm mua hàng liền mạch.',
            'Learn More' => 'Tìm hiểu thêm',
            'Rent A Home' => 'Thuê nhà',
            'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'Khám phá nơi thuê hoàn hảo một cách dễ dàng. Khám phá các danh sách đa dạng được thiết kế chính xác phù hợp với nhu cầu lối sống độc đáo của bạn.',
            'Sell A Home' => 'Bán nhà',
            'Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale.' => 'Bán một cách tự tin với hướng dẫn chuyên môn và chiến lược hiệu quả, giới thiệu các tính năng tốt nhất của bất động sản để bán thành công.',
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'Khám phá các bất động sản tốt nhất của Homzen cho ngôi nhà mơ ước',
            'FEATURED PROPERTIES' => 'BẤT ĐỘNG SẢN NỔI BẬT',
            'View All Properties' => 'Xem tất cả bất động sản',
            'Our Location For You' => 'Vị trí của chúng tôi',
            'EXPLORE CITIES' => 'KHÁM PHÁ THÀNH PHỐ',
            'Recommended For You' => 'Được đề xuất cho bạn',
            'TOP PROPERTIES' => 'BẤT ĐỘNG SẢN HÀNG ĐẦU',
            'OUR BENIFIT' => 'LỢI ÍCH CỦA CHÚNG TÔI',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'Đội ngũ giàu kinh nghiệm của chúng tôi xuất sắc trong lĩnh vực bất động sản với nhiều năm điều hướng thị trường thành công, mang đến quyết định sáng suốt và kết quả tối ưu.',
            'Sell Your Property' => 'Bán bất động sản của bạn',
            'Get the best value with our professional selling strategies and market insights.' => 'Nhận giá trị tốt nhất với chiến lược bán hàng chuyên nghiệp và hiểu biết thị trường của chúng tôi.',
            'Rent A Property' => 'Thuê bất động sản',
            'Find the perfect rental property with our extensive listings and support.' => 'Tìm bất động sản cho thuê hoàn hảo với danh sách phong phú và hỗ trợ của chúng tôi.',
            'Property Management' => 'Quản lý bất động sản',
            'Professional management services to keep your property in top condition.' => 'Dịch vụ quản lý chuyên nghiệp để giữ bất động sản của bạn ở tình trạng tốt nhất.',
            'Real Estate Consulting' => 'Tư vấn bất động sản',
            'Expert advice and insights to help you make informed real estate decisions.' => 'Lời khuyên chuyên gia và hiểu biết để giúp bạn đưa ra quyết định bất động sản sáng suốt.',
            'Mortgage Services' => 'Dịch vụ thế chấp',
            'Find the best mortgage rates and options with our comprehensive services.' => 'Tìm lãi suất thế chấp và các tùy chọn tốt nhất với dịch vụ toàn diện của chúng tôi.',
            'Investment Properties' => 'Bất động sản đầu tư',
            'Discover lucrative investment opportunities and maximize your returns.' => 'Khám phá cơ hội đầu tư sinh lời và tối đa hóa lợi nhuận của bạn.',
            'Relocation Services' => 'Dịch vụ di dời',
            'Smooth and hassle-free relocation services to help you move with ease.' => 'Dịch vụ di dời thuận lợi và không rắc rối để giúp bạn di chuyển dễ dàng.',
            'Meet Our Agents' => 'Gặp môi giới của chúng tôi',
            'OUR TEAMS' => 'ĐỘI NGŨ CỦA CHÚNG TÔI',
            'Mortgage Calculator' => 'Máy tính thế chấp',
            'Calculate your monthly mortgage payments' => 'Tính toán khoản thanh toán thế chấp hàng tháng',
            'What\'s People Say\'s' => 'Mọi người nói gì',
            'The Most Recent Estate' => 'Bất động sản mới nhất',
            'LATEST NEWS' => 'TIN TỨC MỚI NHẤT',
        ];

        foreach ($translations as $english => $vietnamese) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $vietnamese, $content);
        }

        return $content;
    }

    protected function getArabicHomepage5Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 5')->first();
        $content = $page->content;

        $translations = [
            'Why Choose Homzen' => 'لماذا تختار Homzen',
            'Our Benefit' => 'فوائدنا',
            'Buy A New Home' => 'شراء منزل جديد',
            'Discover your dream home effortlessly. Explore diverse properties and expert guidance for a seamless buying experience.' => 'اكتشف منزل أحلامك بسهولة. استكشف عقارات متنوعة وإرشادات الخبراء لتجربة شراء سلسة.',
            'Learn More' => 'معرفة المزيد',
            'Rent A Home' => 'استئجار منزل',
            'Discover your perfect rental effortlessly. Explore a diverse variety of listings tailored precisely to suit your unique lifestyle needs.' => 'اكتشف إيجارك المثالي بسهولة. استكشف مجموعة متنوعة من القوائم المصممة بدقة لتناسب احتياجات نمط حياتك الفريد.',
            'Sell A Home' => 'بيع منزل',
            'Sell confidently with expert guidance and effective strategies, showcasing your property\'s best features for a successful sale.' => 'بع بثقة مع إرشادات الخبراء واستراتيجيات فعالة، وعرض أفضل ميزات عقارك لبيع ناجح.',
            'Discover Homzen\'s Finest Properties For Your Dream Home' => 'اكتشف أفضل عقارات Homzen لمنزل أحلامك',
            'FEATURED PROPERTIES' => 'العقارات المميزة',
            'View All Properties' => 'عرض جميع العقارات',
            'Our Location For You' => 'موقعنا لك',
            'EXPLORE CITIES' => 'استكشف المدن',
            'Recommended For You' => 'موصى به لك',
            'TOP PROPERTIES' => 'أفضل العقارات',
            'OUR BENIFIT' => 'فوائدنا',
            'Our seasoned team excels in real estate with years of successful market navigation, offering informed decisions and optimal results.' => 'يتفوق فريقنا المتمرس في العقارات بسنوات من التنقل الناجح في السوق، ويقدم قرارات مستنيرة ونتائج مثالية.',
            'Sell Your Property' => 'بيع عقارك',
            'Get the best value with our professional selling strategies and market insights.' => 'احصل على أفضل قيمة مع استراتيجيات البيع الاحترافية ورؤى السوق لدينا.',
            'Rent A Property' => 'استئجار عقار',
            'Find the perfect rental property with our extensive listings and support.' => 'اعثر على عقار الإيجار المثالي مع قوائمنا الواسعة والدعم.',
            'Property Management' => 'إدارة العقارات',
            'Professional management services to keep your property in top condition.' => 'خدمات إدارة احترافية للحفاظ على عقارك في أفضل حالة.',
            'Real Estate Consulting' => 'استشارات عقارية',
            'Expert advice and insights to help you make informed real estate decisions.' => 'نصائح الخبراء والرؤى لمساعدتك على اتخاذ قرارات عقارية مستنيرة.',
            'Mortgage Services' => 'خدمات الرهن العقاري',
            'Find the best mortgage rates and options with our comprehensive services.' => 'اعثر على أفضل أسعار الرهن العقاري والخيارات مع خدماتنا الشاملة.',
            'Investment Properties' => 'عقارات استثمارية',
            'Discover lucrative investment opportunities and maximize your returns.' => 'اكتشف فرص استثمارية مربحة وزد عوائدك إلى الحد الأقصى.',
            'Relocation Services' => 'خدمات النقل',
            'Smooth and hassle-free relocation services to help you move with ease.' => 'خدمات نقل سلسة وخالية من المتاعب لمساعدتك على الانتقال بسهولة.',
            'Meet Our Agents' => 'تعرف على وكلائنا',
            'OUR TEAMS' => 'فرقنا',
            'Mortgage Calculator' => 'حاسبة الرهن العقاري',
            'Calculate your monthly mortgage payments' => 'احسب دفعات الرهن العقاري الشهرية',
            'What\'s People Say\'s' => 'ما يقوله الناس',
            'The Most Recent Estate' => 'أحدث العقارات',
            'LATEST NEWS' => 'آخر الأخبار',
        ];

        foreach ($translations as $english => $arabic) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $arabic, $content);
        }

        return $content;
    }

    protected function getFrenchHomepage5Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 5')->first();
        $content = $page->content;

        $translations = [
            'Our Benefit' => 'Nos avantages',
            'EXPLORE CITIES' => 'EXPLORER LES VILLES',
            'The Most Recent Estate' => 'Les biens les plus récents',
        ];

        foreach ($translations as $english => $french) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $french, $content);
        }

        return $content;
    }

    protected function getIndonesianHomepage5Content(): string
    {
        $page = Page::query()->where('name', 'Homepage 5')->first();
        $content = $page->content;

        $translations = [
            'Our Benefit' => 'Manfaat kami',
            'EXPLORE CITIES' => 'JELAJAHI KOTA',
            'The Most Recent Estate' => 'Properti terbaru',
        ];

        foreach ($translations as $english => $indonesian) {
            $pattern = '/' . preg_quote($english, '/') . '/s';
            $pattern = str_replace(' ', '\s+', $pattern);
            $content = preg_replace($pattern, $indonesian, $content);
        }

        return $content;
    }

}
