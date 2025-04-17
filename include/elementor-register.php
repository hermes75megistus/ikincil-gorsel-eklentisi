<?php
// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    exit; // Doğrudan erişimi engelle
}

/**
 * Elementor entegrasyonu için gerekli sınıflar
 */
class Ikincil_Gorsel_Elementor {

    /**
     * Constructor
     */
    public function __construct() {
        // Elementor widget'larını kaydet
        add_action('elementor/widgets/widgets_registered', [$this, 'widgets_kaydet']);
        
        // Elementor dinamik etiketleri kaydet
        add_action('elementor/dynamic_tags/register_tags', [$this, 'dinamik_etiketleri_kaydet']);
        
        // Elementor kategori ekle
        add_action('elementor/elements/categories_registered', [$this, 'widget_kategorileri_ekle']);
    }

    /**
     * Widget'ları kaydet
     */
    public function widgets_kaydet() {
        // Widget dosyalarını dahil et
        require_once(plugin_dir_path(__FILE__) . '../widgets/ikincil-gorsel-widget.php');
        
        // Widget'ı kaydet
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Ikincil_Gorsel_Widget());
    }

    /**
     * Dinamik etiketleri kaydet
     */
    public function dinamik_etiketleri_kaydet($dynamic_tags) {
        // Etiket dosyasını dahil et
        require_once(plugin_dir_path(__FILE__) . '../tags/ikincil-gorsel-tag.php');
        
        // Grup kaydet
        $dynamic_tags->register_group(
            'ikincil-gorsel-etiketleri',
            [
                'title' => __('İkincil Görsel Etiketleri', 'ikincil-gorsel')
            ]
        );
        
        // Etiket kaydet
        $dynamic_tags->register_tag('Ikincil_Gorsel_Tag');
    }

    /**
     * Widget kategorisi ekle
     */
    public function widget_kategorileri_ekle($elements_manager) {
        $elements_manager->add_category(
            'ikincil-gorsel-kategori',
            [
                'title' => __('İkincil Görsel', 'ikincil-gorsel'),
                'icon' => 'fa fa-image',
            ]
        );
    }
}

// Elementor entegrasyonunu başlat
new Ikincil_Gorsel_Elementor();
