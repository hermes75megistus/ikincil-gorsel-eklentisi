<?php
// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    exit; // Doğrudan erişimi engelle
}

/**
 * İkincil Görsel Elementor Widget
 */
class Ikincil_Gorsel_Widget extends \Elementor\Widget_Base {

    /**
     * Widget adı
     */
    public function get_name() {
        return 'ikincil_gorsel';
    }

    /**
     * Widget başlığı
     */
    public function get_title() {
        return __('İkincil Görsel', 'ikincil-gorsel');
    }

    /**
     * Widget ikonu
     */
    public function get_icon() {
        return 'eicon-image';
    }

    /**
     * Widget kategorileri
     */
    public function get_categories() {
        return ['basic', 'theme-elements'];
    }

    /**
     * Widget anahtar kelimeleri
     */
    public function get_keywords() {
        return ['görsel', 'ikincil', 'resim', 'medya', 'buddyboss'];
    }

    /**
     * Widget kontrolleri
     */
    protected function _register_controls() {
        // İçerik Kontrolü
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('İçerik', 'ikincil-gorsel'),
            ]
        );

        $this->add_control(
            'kaynak_tipi',
            [
                'label' => __('Görsel Kaynağı', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'dinamik',
                'options' => [
                    'dinamik' => __('Dinamik (Mevcut Yazı)', 'ikincil-gorsel'),
                    'secim' => __('Özel Görsel Seç', 'ikincil-gorsel'),
                ],
            ]
        );

        $this->add_control(
            'gorsel',
            [
                'label' => __('Görsel Seç', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'kaynak_tipi' => 'secim',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Image_Size::get_type(),
            [
                'name' => 'gorsel',
                'default' => 'large',
                'separator' => 'none',
            ]
        );

        $this->add_responsive_control(
            'hizalama',
            [
                'label' => __('Hizalama', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Sol', 'ikincil-gorsel'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Orta', 'ikincil-gorsel'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Sağ', 'ikincil-gorsel'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ikincil-gorsel-alani' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'baglanti',
            [
                'label' => __('Görsel Bağlantısı', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::URL,
                'placeholder' => __('https://ornek-adresiniz.com', 'ikincil-gorsel'),
                'show_external' => true,
                'default' => [
                    'url' => '',
                    'is_external' => false,
                    'nofollow' => false,
                ],
            ]
        );

        $this->end_controls_section();

        // Stil Kontrolü
        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Stil', 'ikincil-gorsel'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'genislik',
            [
                'label' => __('Genişlik', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ikincil-gorsel-alani img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'cerceve_rengi',
            [
                'label' => __('Çerçeve Rengi', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ikincil-gorsel-alani img' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'gorsel_cerceve',
                'label' => __('Çerçeve', 'ikincil-gorsel'),
                'selector' => '{{WRAPPER}} .ikincil-gorsel-alani img',
            ]
        );

        $this->add_responsive_control(
            'cerceve_yaricap',
            [
                'label' => __('Çerçeve Yarıçapı', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ikincil-gorsel-alani img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'gorsel_golge',
                'label' => __('Gölge', 'ikincil-gorsel'),
                'selector' => '{{WRAPPER}} .ikincil-gorsel-alani img',
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => __('İç Boşluk', 'ikincil-gorsel'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ikincil-gorsel-alani' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Widget render
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        echo '<div class="ikincil-gorsel-alani">';
        
        if ('dinamik' === $settings['kaynak_tipi']) {
            $gorsel_id = ikincil_gorsel_id();
            if ($gorsel_id) {
                $gorsel_html = wp_get_attachment_image(
                    $gorsel_id,
                    $settings['gorsel_size'],
                    false,
                    [
                        'class' => 'elementor-ikincil-gorsel',
                    ]
                );
            } else {
                // Eğer ikincil görsel yoksa
                $gorsel_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html(
                    [
                        'id' => '',
                        'url' => \Elementor\Utils::get_placeholder_image_src(),
                    ],
                    'gorsel'
                );
            }
        } else {
            // Özel seçilen görsel
            $gorsel_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html($settings, 'gorsel');
        }
        
        if (!empty($settings['baglanti']['url'])) {
            $target = $settings['baglanti']['is_external'] ? ' target="_blank"' : '';
            $nofollow = $settings['baglanti']['nofollow'] ? ' rel="nofollow"' : '';
            
            echo '<a href="' . esc_url($settings['baglanti']['url']) . '"' . $target . $nofollow . '>';
            echo $gorsel_html;
            echo '</a>';
        } else {
            echo $gorsel_html;
        }
        
        echo '</div>';
    }
}
