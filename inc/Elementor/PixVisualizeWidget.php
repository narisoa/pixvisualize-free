<?php

/**
 * @package  PixVisualize
 */
namespace Inc\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
/**
 * Elementor widget that renders the PixVisualize block markup.
 *
 * The widget reuses the exact server-side markup produced by the Gutenberg
 * block (render.php) so the React front-end script (view.js) can hydrate it
 * the same way it does for native blocks.
 *
 * The content panels mirror the Gutenberg block inspector panels
 * ("Images & Content", "Caption", "Layout & Orientation", "Interactions").
 * Visual controls live in the STYLE tab ("Handle & Separator", "Labels",
 * "Caption", "Container", "Wrapper") — the Elementor convention — with the
 * same control names and order as the Gutenberg panels.
 */
class PixVisualizeWidget extends Widget_Base {
    public function get_name() {
        return 'pixvisualize';
    }

    public function get_title() {
        return __( 'PixVisualize', 'pixvisualize' );
    }

    public function get_icon() {
        return 'pixvisualize-elementor-icon';
    }

    public function get_categories() {
        return array('general');
    }

    public function get_keywords() {
        return array(
            'pixvisualize',
            'compare',
            'comparison',
            'before',
            'after',
            'image',
            'slider'
        );
    }

    public function get_script_depends() {
        return array('create-block-pixvisualize-view');
    }

    public function get_style_depends() {
        return array('create-block-pixvisualize-style');
    }

    /**
     * Stack override: drop Elementor's native Advanced > Border section.
     *
     * The native border targets the outer widget container while Style >
     * Container styles the inner slider card — keeping both would stack two
     * nested borders. Stripping it from the merged stack also stops
     * previously saved native border values from rendering; the editor panel
     * itself is filtered via JS (elements/widget/controls/common/default).
     *
     * @param bool $with_common_controls Whether to include the common controls.
     * @return array Widget stack of controls.
     */
    public function get_stack( $with_common_controls = true ) {
        $stack = parent::get_stack( $with_common_controls );
        if ( isset( $stack['controls']['_section_border'] ) ) {
            foreach ( $stack['controls'] as $control_id => $control ) {
                if ( '_section_border' === $control_id ) {
                    unset($stack['controls'][$control_id]);
                    continue;
                }
                if ( isset( $control['section'] ) && '_section_border' === $control['section'] ) {
                    unset($stack['controls'][$control_id]);
                }
            }
        }
        return $stack;
    }

    protected function register_controls() {
        $is_pro = function_exists( 'pixvisualize_can_use_pro' ) && pixvisualize_can_use_pro();
        /* ------------------------------------------------------------------ *
         * CONTENT TAB — "Images & Content" (mirror of Gutenberg panel)       *
         * ------------------------------------------------------------------ */
        $this->start_controls_section( 'pixvisualize_content_section', array(
            'label' => __( 'Images & Content', 'pixvisualize' ),
        ) );
        $this->add_control( 'before_image', array(
            'label'       => __( 'Before Image', 'pixvisualize' ),
            'type'        => Controls_Manager::MEDIA,
            'media_types' => array('image'),
        ) );
        $this->add_control( 'after_image', array(
            'label'       => __( 'After Image', 'pixvisualize' ),
            'type'        => Controls_Manager::MEDIA,
            'media_types' => array('image'),
        ) );
        $this->add_control( 'before_label', array(
            'label'     => __( 'Before Label', 'pixvisualize' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'Before', 'pixvisualize' ),
            'condition' => array(
                'show_labels' => 'yes',
            ),
        ) );
        $this->add_control( 'after_label', array(
            'label'     => __( 'After Label', 'pixvisualize' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => __( 'After', 'pixvisualize' ),
            'condition' => array(
                'show_labels' => 'yes',
            ),
        ) );
        $this->add_control( 'show_labels', array(
            'label'   => __( 'Show Labels', 'pixvisualize' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ) );
        $this->end_controls_section();
        /* ------------------------------------------------------------------ *
         * CONTENT TAB — "Caption" (title + text below the slider)            *
         * ------------------------------------------------------------------ */
        $this->start_controls_section( 'pixvisualize_caption_section', array(
            'label' => __( 'Caption', 'pixvisualize' ),
        ) );
        $this->add_control( 'show_caption', array(
            'label'   => __( 'Show Caption', 'pixvisualize' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ) );
        $this->add_control( 'caption_title', array(
            'label'     => __( 'Caption Title', 'pixvisualize' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => '',
            'condition' => array(
                'show_caption' => 'yes',
            ),
        ) );
        $this->add_control( 'caption_text', array(
            'label'     => __( 'Caption Text', 'pixvisualize' ),
            'type'      => Controls_Manager::TEXT,
            'default'   => '',
            'condition' => array(
                'show_caption' => 'yes',
            ),
        ) );
        $this->end_controls_section();
        /* ------------------------------------------------------------------ *
         * CONTENT TAB — "Layout & Orientation"                               *
         * ------------------------------------------------------------------ */
        $this->start_controls_section( 'pixvisualize_layout_section', array(
            'label' => __( 'Layout & Orientation', 'pixvisualize' ),
        ) );
        $this->add_control( 'orientation', array(
            'label'   => __( 'Orientation', 'pixvisualize' ),
            'type'    => Controls_Manager::SELECT,
            'options' => $this->orientation_options(),
            'default' => 'horizontal',
        ) );
        $this->add_control( 'aspect_ratio', array(
            'label'       => __( 'Aspect Ratio', 'pixvisualize' ),
            'description' => __( 'Width / height proportion, always respected (contain): 21/9 fills the width, 9/21 fills the height.', 'pixvisualize' ),
            'type'        => Controls_Manager::SELECT,
            'options'     => array(
                '21/9' => '21 / 9',
                '2/1'  => '2 / 1',
                '16/9' => '16 / 9',
                '3/2'  => '3 / 2',
                '4/3'  => '4 / 3',
                '1/1'  => '1 / 1',
                '3/4'  => '3 / 4',
                '2/3'  => '2 / 3',
                '9/16' => '9 / 16',
                '1/2'  => '1 / 2',
                '9/21' => '9 / 21',
            ),
            'default'     => '16/9',
        ) );
        $this->add_control( 'height', array(
            'label'       => __( 'Height', 'pixvisualize' ),
            'description' => __( 'Desired zone height, capped by a fixed parent height (contain).', 'pixvisualize' ),
            'type'        => Controls_Manager::SLIDER,
            'size_units'  => array('px'),
            'range'       => array(
                'px' => array(
                    'min' => 100,
                    'max' => 2000,
                ),
            ),
            'default'     => array(
                'unit' => 'px',
                'size' => 450,
            ),
        ) );
        $this->add_control( 'full_width', array(
            'label'       => __( 'Full Width', 'pixvisualize' ),
            'description' => __( 'Spans the content width (ratio kept, centered). Useless with a fixed parent height — contain already caps it.', 'pixvisualize' ),
            'type'        => Controls_Manager::SWITCHER,
            'default'     => 'no',
        ) );
        $this->add_control( 'initial_position', array(
            'label'      => __( 'Initial Position', 'pixvisualize' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => array('%'),
            'range'      => array(
                '%' => array(
                    'min' => 0,
                    'max' => 100,
                ),
            ),
            'default'    => array(
                'unit' => '%',
                'size' => 50,
            ),
        ) );
        $this->end_controls_section();
        $this->start_controls_section( 'pixvisualize_container_style_section', array(
            'label' => __( 'Container', 'pixvisualize' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );
        $this->add_control( 'pixvisualize_container_shadow_heading', array(
            'type'  => Controls_Manager::HEADING,
            'label' => __( 'Container Shadow', 'pixvisualize' ),
        ) );
        $this->add_control( 'container_shadow', array(
            'label'   => __( 'Container Shadow', 'pixvisualize' ),
            'type'    => Controls_Manager::SWITCHER,
            'default' => 'no',
        ) );
        $this->add_control( 'pixvisualize_container_border_heading', array(
            'type'  => Controls_Manager::HEADING,
            'label' => __( 'Container Border', 'pixvisualize' ),
        ) );
        $this->add_control( 'show_border', array(
            'label'       => __( 'Show Border', 'pixvisualize' ),
            'description' => ( $is_pro ? __( 'Show the default 2px solid gray border (a custom width below overrides it).', 'pixvisualize' ) : __( 'FREE: show a default 2px solid gray border.', 'pixvisualize' ) ),
            'type'        => Controls_Manager::SWITCHER,
            'default'     => 'no',
        ) );
        $this->add_control( 'rounded_corners', array(
            'label'       => __( 'Rounded Corners', 'pixvisualize' ),
            'description' => ( $is_pro ? __( 'Round all corners with the default 16px radius (custom radii below override it).', 'pixvisualize' ) : __( 'FREE: round all corners with the default 16px radius.', 'pixvisualize' ) ),
            'type'        => Controls_Manager::SWITCHER,
            'default'     => 'no',
        ) );
        $this->end_controls_section();
        $this->end_controls_section();
        // Upsell in-context (pattern officiel Freemius : visible en free,
        // absent en premium — runtime, PAS de marker premium-only ici).
        if ( function_exists( 'pixvisualize_fs' ) && pixvisualize_fs()->is_not_paying() ) {
            $this->start_controls_section( 'pixvisualize_upsell_section', array(
                'label' => __( 'PixVisualize Pro', 'pixvisualize' ),
            ) );
            $this->add_control( 'pixvisualize_upsell_text', array(
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => $this->renderUpsellHtml(),
                'content_classes' => 'pixvisualize-upsell',
            ) );
            $this->end_controls_section();
        }
    }

    /**
     * Pro upsell card (Free only, upgrade URL via Freemius).
     */
    private function renderUpsellHtml() {
        $url = ( function_exists( 'pixvisualize_fs' ) ? esc_url( pixvisualize_fs()->get_upgrade_url() ) : '#' );
        $demo_url = ( function_exists( 'pixvisualize_get_demo_url' ) ? esc_url( pixvisualize_get_demo_url() ) : 'https://i-naridigit.com/demo/pixvisualize/' );
        $features = array(
            __( 'Advanced Elementor widget with full styling & live preview', 'pixvisualize' ),
            __( 'Advanced Gutenberg controls', 'pixvisualize' ),
            __( 'WooCommerce product gallery integration', 'pixvisualize' ),
            __( 'Generate, save, edit & reuse shortcodes', 'pixvisualize' ),
            __( 'Hover, Auto-Scan & Magnifier interactions', 'pixvisualize' ),
            __( 'Vertical orientation', 'pixvisualize' ),
            __( 'Responsive controls for every breakpoint', 'pixvisualize' ),
            __( 'Custom handles & SVG icons', 'pixvisualize' ),
            __( 'Advanced separator & handle styling', 'pixvisualize' ),
            __( 'Fully customizable Before/After labels', 'pixvisualize' ),
            __( 'Custom captions with advanced styling', 'pixvisualize' ),
            __( 'Advanced borders, shadows & corner radius controls', 'pixvisualize' )
        );
        $list = '';
        foreach ( $features as $feature ) {
            $list .= '<li><span class="pixvisualize-upsell-panel__dot">•</span>' . $feature . '</li>';
        }
        return '<div class="pixvisualize-upsell-panel">' . '<p class="pixvisualize-upsell-panel__title">' . esc_html__( 'Unlock advanced features to take your comparisons further:', 'pixvisualize' ) . '</p>' . '<ul class="pixvisualize-upsell-panel__list">' . $list . '</ul>' . '<div class="pixvisualize-upsell-panel__actions">' . '<a class="pixvisualize-upsell-panel__cta" href="' . $url . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Go Pro', 'pixvisualize' ) . '</a>' . '<a class="pixvisualize-upsell-panel__cta pixvisualize-upsell-panel__cta--secondary" href="' . $demo_url . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'View Pro demo', 'pixvisualize' ) . '</a>' . '</div>' . '</div>';
    }

    /**
     * Shared "Aspect Ratio" options used by the responsive ratio selects.
     * The first (empty) option means "inherit the desktop ratio".
     */
    protected function responsive_ratio_options() {
        return array(
            ''     => __( 'Inherit (desktop)', 'pixvisualize' ),
            '21/9' => '21 / 9',
            '2/1'  => '2 / 1',
            '16/9' => '16 / 9',
            '3/2'  => '3 / 2',
            '4/3'  => '4 / 3',
            '1/1'  => '1 / 1',
            '3/4'  => '3 / 4',
            '2/3'  => '2 / 3',
            '9/16' => '9 / 16',
            '1/2'  => '1 / 2',
            '9/21' => '9 / 21',
        );
    }

    /**
     * Orientation options. Vertical is Pro : stripped from the free build
     * (marker) and hidden at runtime without a licence.
     */
    protected function orientation_options() {
        $options = array(
            'horizontal' => __( 'Horizontal', 'pixvisualize' ),
        );
        return $options;
    }

    /**
     * Shared label position options.
     */
    protected function label_position_options() {
        return array(
            'top-left'     => __( 'Top Left', 'pixvisualize' ),
            'top-right'    => __( 'Top Right', 'pixvisualize' ),
            'bottom-left'  => __( 'Bottom Left', 'pixvisualize' ),
            'bottom-right' => __( 'Bottom Right', 'pixvisualize' ),
        );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $is_pro = function_exists( 'pixvisualize_can_use_pro' ) && pixvisualize_can_use_pro();
        /* Content */
        $beforeImage = ( isset( $settings['before_image']['url'] ) ? $settings['before_image']['url'] : '' );
        $afterImage = ( isset( $settings['after_image']['url'] ) ? $settings['after_image']['url'] : '' );
        $beforeLabel = ( !empty( $settings['before_label'] ) ? $settings['before_label'] : __( 'Before', 'pixvisualize' ) );
        $afterLabel = ( !empty( $settings['after_label'] ) ? $settings['after_label'] : __( 'After', 'pixvisualize' ) );
        $showLabels = 'yes' === ($settings['show_labels'] ?? 'yes');
        $orientation = 'horizontal';
        $aspectRatio = ( !empty( $settings['aspect_ratio'] ) ? $settings['aspect_ratio'] : '16/9' );
        $height = ( isset( $settings['height']['size'] ) ? (int) $settings['height']['size'] : 450 );
        $fullWidth = 'yes' === ($settings['full_width'] ?? 'no');
        $initialPosition = ( isset( $settings['initial_position']['size'] ) ? (int) $settings['initial_position']['size'] : 50 );
        $handleStyle = ( !empty( $settings['handle_style'] ) ? $settings['handle_style'] : 'circle' );
        $handleIconSvg = ( !empty( $settings['handle_icon_svg'] ) ? $settings['handle_icon_svg'] : '' );
        $handleSize = ( isset( $settings['handle_size']['size'] ) ? (int) $settings['handle_size']['size'] : 24 );
        $handleSize = max( 16, min( 64, $handleSize ) );
        // Legacy handle_background kept for backward compatibility (old
        // content): new content leaves it empty so the disc follows the
        // single synchronized separator_color control.
        $handleBackground = ( !empty( $settings['handle_background'] ) ? $settings['handle_background'] : '' );
        $handleArrowColor = ( !empty( $settings['handle_arrow_color'] ) ? $settings['handle_arrow_color'] : '#666666' );
        $handlePadding = ( isset( $settings['handle_padding']['size'] ) ? (int) $settings['handle_padding']['size'] : 0 );
        $handlePadding = max( 0, min( 20, $handlePadding ) );
        $labelPositionBefore = ( !empty( $settings['label_position_before'] ) ? $settings['label_position_before'] : 'top-left' );
        $labelPositionAfter = ( !empty( $settings['label_position_after'] ) ? $settings['label_position_after'] : 'top-right' );
        $labelBackground = ( !empty( $settings['label_background'] ) ? $settings['label_background'] : '#00000080' );
        $labelColor = ( !empty( $settings['label_color'] ) ? $settings['label_color'] : '#ffffff' );
        $labelFontSize = ( isset( $settings['label_font_size']['size'] ) ? (int) $settings['label_font_size']['size'] : 12 );
        $labelFontWeight = ( isset( $settings['label_font_weight']['size'] ) ? (int) $settings['label_font_weight']['size'] : 600 );
        $labelPaddingX = ( isset( $settings['label_padding_x']['size'] ) ? (int) $settings['label_padding_x']['size'] : 10 );
        $labelPaddingY = ( isset( $settings['label_padding_y']['size'] ) ? (int) $settings['label_padding_y']['size'] : 5 );
        $labelBorderRadius = ( isset( $settings['label_border_radius']['size'] ) ? (int) $settings['label_border_radius']['size'] : 4 );
        $showCaption = 'yes' === ($settings['show_caption'] ?? 'yes');
        $captionTitle = ( !empty( $settings['caption_title'] ) ? $settings['caption_title'] : '' );
        $captionText = ( !empty( $settings['caption_text'] ) ? $settings['caption_text'] : '' );
        $captionBg = ( !empty( $settings['caption_bg'] ) ? $settings['caption_bg'] : '#ffffff' );
        $captionTitleColor = ( !empty( $settings['caption_title_color'] ) ? $settings['caption_title_color'] : '#1e293b' );
        $captionTextColor = ( !empty( $settings['caption_text_color'] ) ? $settings['caption_text_color'] : '#64748b' );
        $captionAlignment = ( !empty( $settings['caption_alignment'] ) ? $settings['caption_alignment'] : 'center' );
        if ( !in_array( $captionAlignment, array('left', 'center', 'right'), true ) ) {
            $captionAlignment = 'center';
        }
        $captionTitleSize = ( isset( $settings['caption_title_size']['size'] ) ? (int) $settings['caption_title_size']['size'] : 22 );
        $captionTextSize = ( isset( $settings['caption_text_size']['size'] ) ? (int) $settings['caption_text_size']['size'] : 15 );
        // Legacy uniforme (widgets enregistrés avant la séparation X/Y).
        $legacyCaptionPadding = ( isset( $settings['caption_padding']['size'] ) && '' !== $settings['caption_padding']['size'] ? (int) $settings['caption_padding']['size'] : 16 );
        $captionPaddingX = ( isset( $settings['caption_padding_x']['size'] ) && '' !== $settings['caption_padding_x']['size'] ? (int) $settings['caption_padding_x']['size'] : $legacyCaptionPadding );
        $captionPaddingY = ( isset( $settings['caption_padding_y']['size'] ) && '' !== $settings['caption_padding_y']['size'] ? (int) $settings['caption_padding_y']['size'] : $legacyCaptionPadding );
        $separatorThickness = ( isset( $settings['separator_thickness']['size'] ) ? (int) $settings['separator_thickness']['size'] : 2 );
        $separatorColor = ( !empty( $settings['separator_color'] ) ? $settings['separator_color'] : '#ffffff' );
        $separatorShadow = 'yes' === ($settings['separator_shadow'] ?? 'yes');
        $containerShadow = 'yes' === ($settings['container_shadow'] ?? 'no');
        $containerShadowColor = ( !empty( $settings['container_shadow_color'] ) ? $settings['container_shadow_color'] : '#000000' );
        $containerShadowHorz = ( isset( $settings['container_shadow_horizontal']['size'] ) ? (int) $settings['container_shadow_horizontal']['size'] : 0 );
        $containerShadowVertical = ( isset( $settings['container_shadow_vertical']['size'] ) ? (int) $settings['container_shadow_vertical']['size'] : 8 );
        $containerShadowBlur = ( isset( $settings['container_shadow_blur']['size'] ) ? (int) $settings['container_shadow_blur']['size'] : 20 );
        $containerShadowSpread = ( isset( $settings['container_shadow_spread']['size'] ) ? (int) $settings['container_shadow_spread']['size'] : 0 );
        $containerShadowOpacity = ( isset( $settings['container_shadow_opacity']['size'] ) ? (int) $settings['container_shadow_opacity']['size'] : 35 );
        $hoverMode = 'yes' === ($settings['hover_mode'] ?? 'no');
        $autoScan = 'yes' === ($settings['auto_scan'] ?? 'no');
        $autoScanDuration = ( isset( $settings['auto_scan_duration']['size'] ) ? (int) $settings['auto_scan_duration']['size'] : 2500 );
        $magnifier = 'yes' === ($settings['magnifier'] ?? 'no');
        $magnifierZoom = ( isset( $settings['magnifier_zoom']['size'] ) ? (float) $settings['magnifier_zoom']['size'] : 2 );
        $tabletPosition = ( isset( $settings['tablet_position']['size'] ) ? (int) $settings['tablet_position']['size'] : 50 );
        $mobilePosition = ( isset( $settings['mobile_position']['size'] ) ? (int) $settings['mobile_position']['size'] : 50 );
        $tabletRatio = ( !empty( $settings['tablet_ratio'] ) ? $settings['tablet_ratio'] : '' );
        $mobileRatio = ( !empty( $settings['mobile_ratio'] ) ? $settings['mobile_ratio'] : '' );
        /* Style */
        $alignment = ( !empty( $settings['align_horizontal'] ) ? $settings['align_horizontal'] : 'center' );
        $wrapperStyle = '';
        $wrapperMargin = ( $is_pro && isset( $settings['wrapper_margin'] ) ? $settings['wrapper_margin'] : array() );
        if ( !empty( $wrapperMargin ) && isset( $wrapperMargin['top'] ) ) {
            $unit = ( !empty( $wrapperMargin['unit'] ) ? $wrapperMargin['unit'] : 'px' );
            $top = ( '' !== $wrapperMargin['top'] ? $wrapperMargin['top'] . $unit : '0' );
            $right = ( '' !== $wrapperMargin['right'] ? $wrapperMargin['right'] . $unit : '0' );
            $bottom = ( '' !== $wrapperMargin['bottom'] ? $wrapperMargin['bottom'] . $unit : '0' );
            $left = ( '' !== $wrapperMargin['left'] ? $wrapperMargin['left'] . $unit : '0' );
            $wrapperStyle .= "margin: {$top} {$right} {$bottom} {$left};";
        } elseif ( !$fullWidth ) {
            $alignMargin = ( 'left' === $alignment ? '0 auto 0 0' : (( 'right' === $alignment ? '0 0 0 auto' : '0 auto' )) );
            $wrapperStyle .= "margin: {$alignMargin};";
        }
        $borderWidth = ( isset( $settings['border_width']['size'] ) ? (int) $settings['border_width']['size'] : 0 );
        // FREE toggles (checkboxes): default 2px solid gray border, default 16px radius.
        $showBorder = 'yes' === ($settings['show_border'] ?? 'no');
        $roundedCorners = 'yes' === ($settings['rounded_corners'] ?? 'no');
        $borderColor = ( !empty( $settings['border_color'] ) ? $settings['border_color'] : '#000000' );
        $borderStyle = ( !empty( $settings['border_style'] ) ? $settings['border_style'] : 'solid' );
        $borderRadiusTL = ( isset( $settings['border_radius_tl']['size'] ) ? (int) $settings['border_radius_tl']['size'] : 0 );
        $borderRadiusTR = ( isset( $settings['border_radius_tr']['size'] ) ? (int) $settings['border_radius_tr']['size'] : 0 );
        $borderRadiusBR = ( isset( $settings['border_radius_br']['size'] ) ? (int) $settings['border_radius_br']['size'] : 0 );
        $borderRadiusBL = ( isset( $settings['border_radius_bl']['size'] ) ? (int) $settings['border_radius_bl']['size'] : 0 );
        $this->enqueue_block_assets();
        $blockId = 'pixvisualize-block-' . uniqid();
        ?>
		<div
			class="pixvisualize-block-wrapper"
			<?php 
        echo ( $wrapperStyle ? 'style="' . esc_attr( $wrapperStyle ) . '"' : '' );
        ?>
			data-before-image="<?php 
        echo esc_attr( $beforeImage );
        ?>"
			data-after-image="<?php 
        echo esc_attr( $afterImage );
        ?>"
			data-initial-position="<?php 
        echo esc_attr( $initialPosition );
        ?>"
			data-before-label="<?php 
        echo esc_attr( $beforeLabel );
        ?>"
			data-after-label="<?php 
        echo esc_attr( $afterLabel );
        ?>"
			data-show-labels="<?php 
        echo ( $showLabels ? '1' : '0' );
        ?>"
			data-aspect-ratio="<?php 
        echo esc_attr( $aspectRatio );
        ?>"
			data-height="<?php 
        echo esc_attr( $height );
        ?>"
			data-full-width="<?php 
        echo ( $fullWidth ? '1' : '0' );
        ?>"
			data-orientation="<?php 
        echo esc_attr( $orientation );
        ?>"
			<?php 
        ?>
			data-container-shadow="<?php 
        echo ( $containerShadow ? '1' : '0' );
        ?>"
			<?php 
        ?>
			data-container-border="<?php 
        echo ( $showBorder ? '1' : '0' );
        ?>"
			<?php 
        ?>
			data-container-rounded="<?php 
        echo ( $roundedCorners ? '1' : '0' );
        ?>"
			<?php 
        ?>
			data-alignment="<?php 
        echo esc_attr( $alignment );
        ?>"
			data-caption-title="<?php 
        echo esc_attr( $captionTitle );
        ?>"
			data-caption-text="<?php 
        echo esc_attr( $captionText );
        ?>"
			data-show-caption="<?php 
        echo ( $showCaption ? '1' : '0' );
        ?>"
			<?php 
        ?>
			id="<?php 
        echo esc_attr( $blockId );
        ?>"
		>
			<div class="pixvisualize-react-root"></div>
		</div>
		<?php 
    }

    protected function content_template() {
        ?>
		<#
		var beforeImage = ( settings.before_image && settings.before_image.url ) ? settings.before_image.url : '';
		var afterImage = ( settings.after_image && settings.after_image.url ) ? settings.after_image.url : '';
		var initialPosition = ( settings.initial_position && settings.initial_position.size ) ? settings.initial_position.size : 50;
		var height = ( settings.height && settings.height.size ) ? settings.height.size : 450;
		var beforeLabel = settings.before_label || 'Before';
		var afterLabel = settings.after_label || 'After';
		var showLabels = 'yes' === settings.show_labels;
		var aspectRatio = settings.aspect_ratio || '16/9';
		var fullWidth = 'yes' === settings.full_width;
		<?php 
        ?>
		var orientation = 'horizontal';
		var handleStyle = 'circle';
		var handleIconSvg = '';
		var handleSize = 24;
		var labelPositionBefore = 'top-left';
		var labelPositionAfter = 'top-right';
		var labelBackground = '#00000080';
		var labelColor = '#ffffff';
		var labelFontSize = 12;
		var labelFontWeight = 600;
		var labelPaddingX = 10;
		var labelPaddingY = 5;
		var labelBorderRadius = 4;
		var separatorThickness = 2;
		var separatorColor = '#ffffff';
		var separatorShadow = true;

		var containerShadow = 'yes' === settings.container_shadow;
		var containerShadowColor = '#000000';
		var containerShadowHorizontal = 0;
		var containerShadowVertical = 8;
		var containerShadowBlur = 20;
		var containerShadowSpread = 0;
		var containerShadowOpacity = 35;
		<?php 
        ?>

		var shadowAlpha = Math.max( 0, Math.min( 1, containerShadowOpacity / 100 ) );
		var shadowHex = containerShadowColor.trim().match( /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/ );
		var shadowRgb = 'rgba(0, 0, 0, ' + shadowAlpha + ')';
		if ( shadowHex ) {
			var hex = shadowHex[ 1 ];
			if ( 3 === hex.length ) {
				hex = hex.split( '' ).map( function ( ch ) { return ch + ch; } ).join( '' );
			}
			var hr = parseInt( hex.substr( 0, 2 ), 16 );
			var hg = parseInt( hex.substr( 2, 2 ), 16 );
			var hb = parseInt( hex.substr( 4, 2 ), 16 );
			shadowRgb = 'rgba(' + hr + ', ' + hg + ', ' + hb + ', ' + shadowAlpha + ')';
		}
		var containerShadowStyle = containerShadow ? ('box-shadow: ' + containerShadowHorizontal + 'px ' + containerShadowVertical + 'px ' + containerShadowBlur + 'px ' + containerShadowSpread + 'px ' + shadowRgb + ';') : '';

		<?php 
        ?>
		var borderWidth = 0;
		var borderColor = '#000000';
		var borderStyle = 'solid';
		var borderRadiusTL = 0;
		var borderRadiusTR = 0;
		var borderRadiusBR = 0;
		var borderRadiusBL = 0;
		<?php 
        ?>
		// FREE toggles (switchers): default 2px solid gray border, default 16px radius.
		var showBorder = 'yes' === settings.show_border;
		var roundedCorners = 'yes' === settings.rounded_corners;
		var effBorderWidth = borderWidth > 0 ? borderWidth : ( showBorder ? 2 : 0 );
		var effBorderColor = borderWidth > 0 ? borderColor : '#E5E7EB';
		var effBorderStyle = borderWidth > 0 ? borderStyle : 'solid';
		var effRadiusTL = borderRadiusTL > 0 ? borderRadiusTL : ( roundedCorners ? 16 : 0 );
		var effRadiusTR = borderRadiusTR > 0 ? borderRadiusTR : ( roundedCorners ? 16 : 0 );
		var effRadiusBR = borderRadiusBR > 0 ? borderRadiusBR : ( roundedCorners ? 16 : 0 );
		var effRadiusBL = borderRadiusBL > 0 ? borderRadiusBL : ( roundedCorners ? 16 : 0 );
		var borderStyleStr = effBorderWidth > 0 ? ('border: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + ';') : '';
		var borderRadiusStyle = 'border-radius: ' + effRadiusTL + 'px ' + effRadiusTR + 'px ' + effRadiusBR + 'px ' + effRadiusBL + 'px;';

		var isTransparentHandle = 'transparent' === handleStyle || 'arrows' === handleStyle;
		// Single synchronized control: bar + disc share separatorColor.
		// Legacy handle_background still wins when set (old content).
		<?php 
        ?>
		var handleBgSetting = '';
		<?php 
        ?>
		var discBg = handleBgSetting || ( isTransparentHandle ? 'transparent' : separatorColor );
		<?php 
        ?>
		var handlePaddingRaw = 0;
		<?php 
        ?>
		var handlePadding = Math.min( 20, Math.max( 0, handlePaddingRaw ) );
		var iconBg = discBg;
		<?php 
        ?>
		var iconColor = '#666666';
		<?php 
        ?>
		var groupedFilter = separatorShadow ? 'filter: drop-shadow(0 0 6px rgba(0, 0, 0, 0.6));' : '';
		// Inner spacing: on the icon box for custom SVG (glued to the
		// edge), on the glyph box otherwise. Zero keeps history.
		var isCustomIcon = 'custom' === handleStyle && handleIconSvg;
		var iconPadding = isCustomIcon ? handlePadding : 0;
		var glyphPadding = isCustomIcon ? 0 : handlePadding;

		var isVertical = 'vertical' === orientation;
		var ratioParts = aspectRatio.split('/');
		var ratioW = parseFloat( ratioParts[0] ) || 16;
		var ratioH = parseFloat( ratioParts[1] ) || 9;
		var clampedHeight = Math.min( 2000, Math.max( 100, height ) );
		var boxWidth = Math.round( clampedHeight * ratioW / ratioH );
		// Contain layout: fit inside the parent (width AND fixed height)
		// with the ratio respected — 21/9 fills the width, 9/21 fills
		// the height. Full width is useless with a fixed parent.
		// NOTE: no fixed height in the style below on purpose. Setting
		// width + height + aspect-ratio together over-constrains CSS
		// (aspect-ratio is then ignored) and wide ratios break in narrow
		// columns. Width + aspect-ratio lets the browser derive the
		// contained height; the preview script caps it when the parent
		// has a fixed height (same rule as the front view.js).
		var containerWidth = 'min(100%, ' + boxWidth + 'px)';
		var containerMargin = '0 auto';
		var hasImage = beforeImage || afterImage;

		var handleClass = isVertical
			? 'pixvisualize-elementor-preview__handle pixvisualize-elementor-preview__handle--vertical'
			: 'pixvisualize-elementor-preview__handle pixvisualize-elementor-preview__handle--horizontal';

		<?php 
        ?>

		// Caption + card look (mirrors the front-end CompareSlider rule:
		// with a caption the container border/shadow wrap the whole card,
		// without it they stay on the slider alone).
		var showCaption = 'yes' === settings.show_caption;
		var captionTitle = settings.caption_title || '';
		var captionText = settings.caption_text || '';
		<?php 
        ?>
		var captionBg = '#ffffff';
		var captionTitleColor = '#1e293b';
		var captionTextColor = '#64748b';
		var captionAlignment = 'center';
		var captionTitleSize = 22;
		var captionTextSize = 15;
		var legacyCaptionPadding = 16;
		var captionPaddingX = 16;
		var captionPaddingY = 16;
		<?php 
        ?>
		var captionVisible = showCaption && ( captionTitle || captionText );
		var cardShadow = captionVisible && containerShadow;
		// Card look: with a caption the container bottom corners move to
		// the caption (the slider bottom stays square).
		var captionBottomBR = effRadiusBR;
		var captionBottomBL = effRadiusBL;
		var boxWidthAttr = captionVisible ? 'width: 100%; margin: 0;' : ( 'width: ' + containerWidth + '; margin: ' + containerMargin + ';' );
		var boxShadowAttr = cardShadow ? '' : containerShadowStyle;
		var boxBorderAttr = borderStyleStr;
		var boxRadiusAttr = borderRadiusStyle;
		if ( captionVisible ) {
			boxBorderAttr = effBorderWidth > 0
				? ( 'border-top: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + '; border-left: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + '; border-right: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + ';' )
				: '';
			boxRadiusAttr = 'border-radius: ' + effRadiusTL + 'px ' + effRadiusTR + 'px 0 0;';
		}
		var boxHeightAttr = hasImage ? ( 'height: ' + clampedHeight + 'px;' ) : '';
		// boxHeightAttr is kept for reference only: it is NOT printed in
		// the preview style (see contain NOTE above). The effective height
		// is exposed via data-height for the preview script.
		var cardAttr = captionVisible
			? ( 'width: ' + containerWidth + '; margin: ' + containerMargin + '; max-width: 100%; box-sizing: border-box;' + ( cardShadow ? ( ' ' + containerShadowStyle + ' border-radius: ' + effRadiusTL + 'px ' + effRadiusTR + 'px ' + captionBottomBR + 'px ' + captionBottomBL + 'px;' ) : '' ) )
			: '';
		var captionBoxAttr = 'width: 100%; max-width: 100%; margin: 0; box-sizing: border-box; background-color: ' + captionBg + '; text-align: ' + captionAlignment + '; padding: ' + captionPaddingY + 'px ' + captionPaddingX + 'px; border-radius: 0 0 ' + captionBottomBR + 'px ' + captionBottomBL + 'px;' + ( effBorderWidth > 0
			? ( ' border-left: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + '; border-right: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + '; border-bottom: ' + effBorderWidth + 'px ' + effBorderStyle + ' ' + effBorderColor + ';' )
			: ' border-top: 1px solid #e5e7eb;' );
		var captionTitleAttr = 'color: ' + captionTitleColor + '; font-size: ' + captionTitleSize + 'px; font-weight: 700; line-height: 1.25; margin: ' + ( captionText ? '0 0 4px' : '0' ) + ';';
		var captionTextAttr = 'color: ' + captionTextColor + '; font-size: ' + captionTextSize + 'px; line-height: 1.5; margin: 0;';

		// Handle icon (same geometry as the front-end Handle component:
		// 24px circle, 20px chevrons, 34px double arrows).
		var isArrowsHandle = 'arrows' === handleStyle;
		var arrowInner = Math.max( 12, Math.round( handleSize * 0.65 ) );
		var arrowSize = isArrowsHandle ? handleSize + 10 : arrowInner;
		var arrowPaths = '';
		if ( isArrowsHandle ) {
			arrowPaths = isVertical
				? '<path d="M7 9l5-5 5 5"/><path d="M7 15l5 5 5-5"/><path d="M12 6v12"/>'
				: '<path d="M9 7l-5 5 5 5"/><path d="M15 7l5 5-5 5"/><path d="M6 12h12"/>';
		} else {
			arrowPaths = isVertical
				? '<path d="M6 9 L12 4 L18 9"/><path d="M6 15 L12 20 L18 15"/>'
				: '<path d="M9 6l-5 6 5 6"/><path d="M15 6l5 6-5 6"/>';
		}

		var lineStyle = isVertical
			? 'height: ' + separatorThickness + 'px; width: 100%;'
			: 'width: ' + separatorThickness + 'px; height: 100%;';
		lineStyle += ' background-color: ' + separatorColor + '; box-shadow: none;';

		var beforePosStyle = 'top: 12px; left: 12px;';
		if ( 'top-right' === labelPositionBefore ) { beforePosStyle = 'top: 12px; right: 12px;'; }
		if ( 'bottom-left' === labelPositionBefore ) { beforePosStyle = 'bottom: 12px; left: 12px;'; }
		if ( 'bottom-right' === labelPositionBefore ) { beforePosStyle = 'bottom: 12px; right: 12px;'; }

		var afterPosStyle = 'top: 12px; right: 12px;';
		if ( 'top-left' === labelPositionAfter ) { afterPosStyle = 'top: 12px; left: 12px;'; }
		if ( 'bottom-left' === labelPositionAfter ) { afterPosStyle = 'bottom: 12px; left: 12px;'; }
		if ( 'bottom-right' === labelPositionAfter ) { afterPosStyle = 'bottom: 12px; right: 12px;'; }

		var labelStyle = 'background-color: ' + labelBackground + '; color: ' + labelColor + '; font-size: ' + labelFontSize + 'px; font-weight: ' + labelFontWeight + '; padding: ' + labelPaddingY + 'px ' + labelPaddingX + 'px; border-radius: ' + labelBorderRadius + 'px;';
		#>
		<# if ( captionVisible ) { #><div class="pixvisualize-generator-card" style="{{ cardAttr }}"><# } #>
		<div
			class="pixvisualize-elementor-preview{{ hasImage ? '' : ' pixvisualize-elementor-preview--empty' }}"
			data-orientation="{{ orientation }}"
			data-aspect-ratio="{{ aspectRatio }}"
			data-height="{{ clampedHeight }}"
			<?php 
        ?>
			style="{{ boxWidthAttr }} aspect-ratio: {{ aspectRatio }}; --pc-pos: {{ initialPosition }}%; {{ boxShadowAttr }} {{ boxBorderAttr }} {{ boxRadiusAttr }}"
		>
			<# if ( afterImage ) { #>
				<img class="pixvisualize-elementor-preview__image pixvisualize-elementor-preview__after" src="{{ afterImage }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
			<# } #>
			<# if ( beforeImage ) { #>
				<img class="pixvisualize-elementor-preview__image pixvisualize-elementor-preview__before" src="{{ beforeImage }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
			<# } #>
			<# if ( hasImage ) { #>
				<div class="{{ handleClass }}" style="{{ groupedFilter }}">
					<div class="pixvisualize-elementor-preview__handle-line" style="{{ lineStyle }}"></div>
					<# if ( 'line' !== handleStyle ) { #>
						<div class="pixvisualize-elementor-preview__handle-icon" style="width: {{ handleSize }}px; height: {{ handleSize }}px; background-color: {{ iconBg }}; color: {{ iconColor }}; --svg-fill: {{ iconColor }}; border-radius: 50%; overflow: hidden; box-sizing: border-box; padding: {{ iconPadding }}px; box-shadow: none;">
							<# if ( 'custom' === handleStyle && handleIconSvg ) { #>
								{{{ handleIconSvg }}}
							<# } else { #>
								<span class="pixvisualize-elementor-preview__arrows" style="width: {{ arrowSize }}px; height: {{ arrowSize }}px; padding: {{ glyphPadding }}px; box-sizing: border-box;"><svg viewBox="0 0 24 24" aria-hidden="true">{{{ arrowPaths }}}</svg></span>
							<# } #>
						</div>
					<# } #>
				</div>
			<# } #>
			<# if ( showLabels ) { #>
				<# if ( beforeImage ) { #>
					<span class="pixvisualize-elementor-preview__label pixvisualize-elementor-preview__label-before" style="{{ labelStyle }}; {{ beforePosStyle }}">{{ beforeLabel }}</span>
				<# } #>
				<# if ( afterImage ) { #>
					<span class="pixvisualize-elementor-preview__label pixvisualize-elementor-preview__label-after" style="{{ labelStyle }}; {{ afterPosStyle }}">{{ afterLabel }}</span>
				<# } #>
			<# } #>
			<# if ( ! hasImage ) { #>
				<?php 
        esc_html_e( 'Select your Before and After images to display the comparison slider.', 'pixvisualize' );
        ?>
			<# } #>
		</div>
		<# if ( captionVisible ) { #>
			<div class="pixvisualize-generator-caption" style="{{ captionBoxAttr }}">
				<# if ( captionTitle ) { #><div class="pixvisualize-generator-caption-title" style="{{ captionTitleAttr }}">{{ captionTitle }}</div><# } #>
				<# if ( captionText ) { #><div class="pixvisualize-generator-caption-text" style="{{ captionTextAttr }}">{{ captionText }}</div><# } #>
			</div>
		</div>
		<# } #>
		<?php 
    }

    /**
     * Enqueue the block's front-end script and style so the React slider
     * can hydrate the widget markup on the front-end.
     */
    protected function enqueue_block_assets() {
        if ( wp_script_is( 'create-block-pixvisualize-view', 'registered' ) ) {
            wp_enqueue_script( 'create-block-pixvisualize-view' );
        }
        if ( wp_style_is( 'create-block-pixvisualize-style', 'registered' ) ) {
            wp_enqueue_style( 'create-block-pixvisualize-style' );
        }
    }

}
