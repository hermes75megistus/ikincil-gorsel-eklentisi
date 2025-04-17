<?php
// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    exit; // Doğrudan erişimi engelle
}

/**
 * İkincil Görsel Dinamik Etiketi
 */
class Ikincil_Gorsel_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

    /**
     * Etiket kategorisi
     */
    public function get_categories() {
        return ['media', 'ikincil-gorsel-etiketleri'];
    }

    /**
     * Etiket adı
     */
    public function get_name() {
        return 'ikincil-gorsel';
    }

    /**
     * Etiket başlığı
     */
    public function get_title() {
        return __('İkincil Görsel', 'ikincil-gorsel');
    }

    /**
     * Etiket grubu
     */
    public function get_group() {
        return 'ikincil-gorsel-etiketleri';
    }

    /**
     * Veri tipi
     */
    public function get_panel_template_setting_key() {
        return 'gorsel_boyutu';
    }

    /**
     * Kontroller
     */
    protected function _register_controls() {
        $this->add_control(
            'gorsel_boyutu',
            [
                'label' => __('Görsel Boyutu', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'large',
                'options' => [
                    'thumbnail' => __('Küçük Boy', 'ikincil-gorsel'),
                    'medium' => __('Orta Boy', 'ikincil-gorsel'),
                    'large' => __('Büyük Boy', 'ikincil-gorsel'),
                    'full' => __('Tam Boy', 'ikincil-gorsel'),
                ],
            ]
        );
        
        $this->add_control(
            'post_url_ekle',
            [
                'label' => __('Yazı Bağlantısı Ekle', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Evet', 'ikincil-gorsel'),
                'label_off' => __('Hayır', 'ikincil-gorsel'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );
    }

    /**
     * Değer al
     */
    public function get_value(array $options = []) {
        $settings = $this->get_settings();
        $post_id = get_the_ID();
        
        if (!$post_id) {
            return [];
        }
        
        $gorsel_id = get_post_meta($post_id, '_ikincil_gorsel_id', true);
        
        if (!$gorsel_id) {
            return [];
        }
        
        $boyut = $settings['gorsel_boyutu'] ?: 'large';
        
        $image_data = [
            'id' => $gorsel_id,
            'url' => wp_get_attachment_image_src($gorsel_id, $boyut)[0],
        ];
        
        // Yazı bağlantısı eklenmesi istenmişse
        if (isset($settings['post_url_ekle']) && 'yes' === $settings['post_url_ekle']) {
            $image_data['link_to_post'] = get_permalink($post_id);
        }
        
        return $image_data;
    }
}
