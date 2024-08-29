<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.1.1' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);

			/*
			 * Editor Style.
			 */
			add_editor_style( 'classic-editor.css' );

			/*
			 * Gutenberg wide images.
			 */
			add_theme_support( 'align-wide' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Admin notice
if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

function custom_select_dropdown() {
    return '
    <select id="list_city">
        <option value="">Tất cả</option>
        <option value="29">29-Hà Nội</option>
        <option value="30">30-Hà Nội</option>
        <option value="51">51-TP.Hồ Chí Minh</option>
        <option value="11">11-Cao Bằng</option>
        <option value="12">12-Lạng Sơn</option>
        <option value="14">14-Quảng Ninh</option>
        <option value="15">15-Hải Phòng</option>
        <option value="17">17-Thái Bình</option>
        <option value="18">18-Nam Định</option>
        <option value="19">19-Phú Thọ</option>
        <option value="20">20-Thái Nguyên</option>
        <option value="21">21-Yên Bái</option>
        <option value="22">22-Tuyên Quang</option>
        <option value="23">23-Hà Giang</option>
        <option value="24">24-Lào Cai</option>
        <option value="25">25-Lai Châu</option>
        <option value="26">26-Sơn La</option>
        <option value="27">27-Điện Biên</option>
        <option value="28">28-Hòa Bình</option>
        <option value="34">34-Hải Dương</option>
        <option value="35">35-Ninh Bình</option>
        <option value="36">36-Thanh Hóa</option>
        <option value="37">37-Nghệ An</option>
        <option value="38">38-Hà Tĩnh</option>
        <option value="43">43-Đà Nẵng</option>
        <option value="47">47-ĐắK Lắk</option>
        <option value="48">48-Đắk Nông</option>
        <option value="49">49-Lâm Đồng</option>
        <option value="60">60-Đồng Nai</option>
        <option value="61">61-Bình Dương</option>
        <option value="62">62-Long An</option>
        <option value="63">63-Tiền Giang</option>
        <option value="64">64-Vĩnh Long</option>
        <option value="65">65-Cần Thơ</option>
        <option value="66">66-Đồng Tháp</option>
        <option value="67">67-An Giang</option>
        <option value="68">68-Kiên Giang</option>
        <option value="69">69-Cà Mau</option>
        <option value="70">70-Tây Ninh</option>
        <option value="71">71-Tây Ninh</option>
        <option value="72">72-Bà Rịa Vũng Tàu</option>
        <option value="73">73-Quảng Bình</option>
        <option value="74">74-Quảng Trị</option>
        <option value="75">75-Thừa Thiên Huế</option>
        <option value="77">77-Bình Định</option>
        <option value="78">78-Phú Yên</option>
        <option value="79">79-Khánh Hòa</option>
        <option value="81">81-Gia Lai</option>
        <option value="82">82-Kon Tum</option>
        <option value="83">83-Sóc Trăng</option>
        <option value="84">84-Trà Vinh</option>
        <option value="85">85-Ninh Thuận</option>
        <option value="86">86-Bình Thuận</option>
        <option value="88">88-Vĩnh Phúc</option>
        <option value="89">89-Hưng Yên</option>
        <option value="90">90-Hà Nam</option>
        <option value="92">92-Quảng Nam</option>
        <option value="93">93-Bình Phước</option>
        <option value="94">94-Bạc Liêu</option>
        <option value="95">95-Hậu Giang</option>
        <option value="97">97-Bắc Kạn</option>
        <option value="98">98-Bắc Giang</option>
        <option value="99">99-Bắc Ninh</option>
    </select>';
}
add_shortcode('custom_dropdown', 'custom_select_dropdown');

// file tuỳ biến js

function custom_enqueue_scripts() {
	wp_enqueue_script('custom-js', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), null, true);
 }
 add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

 function custom_hide_product_images_except_single() {
    if (!is_product()) {
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    }
}
add_action('wp', 'custom_hide_product_images_except_single');

function custom_remove_product_thumbnails() {
    if (!is_product()) {
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    }
}
add_action('wp', 'custom_remove_product_thumbnails');

function custom_remove_related_product_elements() {
    if (is_product()) {
        // Ẩn ảnh sản phẩm trong mục "Sản phẩm tương tự"
        remove_action('woocommerce_before_related_products', 'woocommerce_show_product_images', 20);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_thumbnails', 20);

        // Ẩn nút "Thêm vào giỏ hàng" trong mục "Sản phẩm tương tự"
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
    }
}
add_action('wp', 'custom_remove_related_product_elements');