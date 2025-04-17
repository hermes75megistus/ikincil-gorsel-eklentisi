<?php
/**
 * Plugin Name: İkincil Görsel Eklentisi
 * Plugin URI: https://ornekadresi.com/ikincil-gorsel
 * Description: BuddyBoss ve Elementor ile uyumlu şekilde post yazılarına ikincil görsel ekleyebilmenizi sağlar.
 * Version: 1.0.0
 * Author: Claude
 * Author URI: https://ornekadresi.com
 * Text Domain: ikincil-gorsel
 */

// Güvenlik kontrolü
if (!defined('ABSPATH')) {
    exit; // Doğrudan erişimi engelle
}

class Ikincil_Gorsel_Eklentisi {

    /**
     * Constructor
     */
    public function __construct() {
        // Eklentinin çalışması için gerekli aksiyonlar
        add_action('init', array($this, 'init'));
        add_action('add_meta_boxes', array($this, 'ikincil_gorsel_meta_kutusu_ekle'));
        add_action('save_post', array($this, 'ikincil_gorsel_kaydet'));
        
        // Elementor widget kaydetme
        add_action('elementor/widgets/widgets_registered', array($this, 'elementor_widgetleri_kaydet'));
        add_action('elementor/dynamic_tags/register_tags', array($this, 'elementor_dinamik_etiket_kaydet'));
        
        // Frontend için stil
        add_action('wp_enqueue_scripts', array($this, 'onyuz_stilleri_yukle'));
        
        // Ajax işlemleri
        add_action('wp_ajax_ikincil_gorsel_sec', array($this, 'ajax_ikincil_gorsel_sec'));
    }

    /**
     * Başlangıç fonksiyonu
     */
    public function init() {
        // Eklenti için dil desteği
        load_plugin_textdomain('ikincil-gorsel', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Elementor kontrolü
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'elementor_yuklu_degil_uyarisi'));
        }
    }

    /**
     * Elementor yüklü değilse uyarı göster
     */
    public function elementor_yuklu_degil_uyarisi() {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }

        $message = sprintf(
            esc_html__('İkincil Görsel Eklentisi, Elementor Page Builder eklentisini gerektirmektedir. %1$sElementor Page Builder%2$s eklentisini yükleyin ve etkinleştirin.', 'ikincil-gorsel'),
            '<a href="' . esc_url(admin_url('plugin-install.php?s=Elementor&tab=search&type=term')) . '">',
            '</a>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    /**
     * Yazı düzenleme ekranına meta kutusu ekle
     */
    public function ikincil_gorsel_meta_kutusu_ekle() {
        add_meta_box(
            'ikincil_gorsel_meta_kutusu',
            __('İkincil Görsel', 'ikincil-gorsel'),
            array($this, 'ikincil_gorsel_meta_kutusu_icerik'),
            'post',
            'side',
            'low'
        );
    }

    /**
     * Meta kutusu içeriği
     */
    public function ikincil_gorsel_meta_kutusu_icerik($post) {
        // Güvenlik alanı
        wp_nonce_field('ikincil_gorsel_kaydet', 'ikincil_gorsel_nonce');

        // Mevcut değeri al
        $gorsel_id = get_post_meta($post->ID, '_ikincil_gorsel_id', true);
        $gorsel_url = '';
        
        if ($gorsel_id) {
            $gorsel_url = wp_get_attachment_image_url($gorsel_id, 'thumbnail');
        }
        ?>
        <div class="ikincil-gorsel-alani">
            <div class="ikincil-gorsel-onizleme" style="text-align: center; margin-bottom: 10px;">
                <?php if ($gorsel_url) : ?>
                    <img src="<?php echo esc_url($gorsel_url); ?>" style="max-width: 100%; height: auto;" />
                <?php else : ?>
                    <p><?php _e('Görsel seçilmedi', 'ikincil-gorsel'); ?></p>
                <?php endif; ?>
            </div>
            
            <input type="hidden" name="ikincil_gorsel_id" id="ikincil_gorsel_id" value="<?php echo esc_attr($gorsel_id); ?>" />
            
            <div style="text-align: center;">
                <button type="button" class="button ikincil-gorsel-sec" id="ikincil_gorsel_sec_buton">
                    <?php _e('Görsel Seç', 'ikincil-gorsel'); ?>
                </button>
                
                <?php if ($gorsel_id) : ?>
                    <button type="button" class="button ikincil-gorsel-kaldir" id="ikincil_gorsel_kaldir_buton">
                        <?php _e('Görseli Kaldır', 'ikincil-gorsel'); ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                // Medya seçici
                var mediaUploader;
                
                $('#ikincil_gorsel_sec_buton').on('click', function(e) {
                    e.preventDefault();
                    
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }
                    
                    mediaUploader = wp.media({
                        title: '<?php _e("İkincil Görsel Seç", "ikincil-gorsel"); ?>',
                        button: {
                            text: '<?php _e("Bu görseli kullan", "ikincil-gorsel"); ?>'
                        },
                        multiple: false
                    });
                    
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#ikincil_gorsel_id').val(attachment.id);
                        $('.ikincil-gorsel-onizleme').html('<img src="' + attachment.sizes.thumbnail.url + '" style="max-width: 100%; height: auto;" />');
                        
                        // Kaldır butonu ekle
                        if ($('#ikincil_gorsel_kaldir_buton').length === 0) {
                            $('.ikincil-gorsel-alani div:last').append('<button type="button" class="button ikincil-gorsel-kaldir" id="ikincil_gorsel_kaldir_buton"><?php _e("Görseli Kaldır", "ikincil-gorsel"); ?></button>');
                        }
                    });
                    
                    mediaUploader.open();
                });
                
                // Görseli kaldır butonu
                $(document).on('click', '#ikincil_gorsel_kaldir_buton', function(e) {
                    e.preventDefault();
                    $('#ikincil_gorsel_id').val('');
                    $('.ikincil-gorsel-onizleme').html('<p><?php _e("Görsel seçilmedi", "ikincil-gorsel"); ?></p>');
                    $(this).remove();
                });
            });
        </script>
        <?php
    }

    /**
     * İkincil görseli kaydet
     */
    public function ikincil_gorsel_kaydet($post_id) {
        // Otomatik kaydetme durumunda işlem yapma
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Güvenlik kontrolü
        if (!isset($_POST['ikincil_gorsel_nonce']) || !wp_verify_nonce($_POST['ikincil_gorsel_nonce'], 'ikincil_gorsel_kaydet')) {
            return;
        }
        
        // Yetki kontrolü
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Verileri kaydet
        if (isset($_POST['ikincil_gorsel_id'])) {
            $gorsel_id = sanitize_text_field($_POST['ikincil_gorsel_id']);
            
            if (empty($gorsel_id)) {
                delete_post_meta($post_id, '_ikincil_gorsel_id');
            } else {
                update_post_meta($post_id, '_ikincil_gorsel_id', $gorsel_id);
            }
        }
    }

    /**
     * Elementor için widget kaydet
     */
    public function elementor_widgetleri_kaydet() {
        // Elementor yüklü mü kontrol et
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Widget dosyası ekle
        require_once(plugin_dir_path(__FILE__) . 'widgets/ikincil-gorsel-widget.php');
        
        // Widget kaydet
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Ikincil_Gorsel_Widget());
    }
    
    /**
     * Elementor Dinamik Etiket kaydı
     */
    public function elementor_dinamik_etiket_kaydet($dynamic_tags) {
        // Elementor yüklü mü kontrol et
        if (!did_action('elementor/loaded')) {
            return;
        }
        
        // Etiket dosyasını dahil et
        require_once(plugin_dir_path(__FILE__) . 'tags/ikincil-gorsel-tag.php');
        
        // Grubu kaydet
        $dynamic_tags->register_group(
            'ikincil-gorsel-etiketleri',
            [
                'title' => __('İkincil Görsel Etiketleri', 'ikincil-gorsel')
            ]
        );
        
        // Etiketi kaydet
        $dynamic_tags->register_tag('Ikincil_Gorsel_Tag');
    }
    
    /**
     * Ön yüz stil dosyaları
     */
    public function onyuz_stilleri_yukle() {
        wp_enqueue_style(
            'ikincil-gorsel-style',
            plugins_url('assets/css/ikincil-gorsel.css', __FILE__),
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Ajax ile ikincil görsel seçimi
     */
    public function ajax_ikincil_gorsel_sec() {
        // Güvenlik kontrolü
        check_ajax_referer('ikincil_gorsel_ajax_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Yetkiniz yok!', 'ikincil-gorsel'));
        }
        
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $gorsel_id = isset($_POST['gorsel_id']) ? intval($_POST['gorsel_id']) : 0;
        
        if ($post_id && $gorsel_id) {
            update_post_meta($post_id, '_ikincil_gorsel_id', $gorsel_id);
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Geçersiz görsel veya yazı ID\'si!', 'ikincil-gorsel'));
        }
    }
}

// İkincil görsel fonksiyonları
function ikincil_gorsel_id($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    return get_post_meta($post_id, '_ikincil_gorsel_id', true);
}

function ikincil_gorsel_url($size = 'thumbnail', $post_id = null) {
    $gorsel_id = ikincil_gorsel_id($post_id);
    
    if ($gorsel_id) {
        return wp_get_attachment_image_url($gorsel_id, $size);
    }
    
    return '';
}

function ikincil_gorsel($size = 'thumbnail', $attr = [], $post_id = null) {
    $gorsel_id = ikincil_gorsel_id($post_id);
    
    if ($gorsel_id) {
        return wp_get_attachment_image($gorsel_id, $size, false, $attr);
    }
    
    return '';
}

/**
 * İkincil görseli içeren HTML çıktısını link ile birlikte döndürür
 * Bu fonksiyon özellikle loop içinde kullanım için uygundur
 * 
 * @param string $size Görsel boyutu
 * @param array $attr Görsel özellikleri
 * @param int $post_id Yazı ID'si
 * @param bool $link_to_post Yazı bağlantısı eklenecek mi?
 * @return string HTML çıktısı
 */
function ikincil_gorsel_html($size = 'thumbnail', $attr = [], $post_id = null, $link_to_post = true) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $gorsel_id = ikincil_gorsel_id($post_id);
    
    if (!$gorsel_id) {
        return '';
    }
    
    $gorsel_html = wp_get_attachment_image($gorsel_id, $size, false, $attr);
    
    if ($link_to_post) {
        $post_url = get_permalink($post_id);
        return '<a href="' . esc_url($post_url) . '">' . $gorsel_html . '</a>';
    }
    
    return $gorsel_html;
}

// Eklenti başlat
$ikincil_gorsel_eklentisi = new Ikincil_Gorsel_Eklentisi();

// Elementor widget dosyası
require_once(plugin_dir_path(__FILE__) . 'include/elementor-register.php');
