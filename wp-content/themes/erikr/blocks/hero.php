<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Header_Hero extends Widget_Base {
    public function get_name() { return 'header_hero'; }
    public function get_title() { return 'Header Hero'; }
    public function get_icon() { return 'eicon-call-to-action'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('content', ['label' => 'Content']);

        $this->add_control('title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 2,
            'placeholder' => "Line 1\nLine 2",
        ]);

        $this->add_control('subtitle', [
            'label' => 'Subtitle',
            'type' => Controls_Manager::WYSIWYG,
        ]);

        $rep = new Repeater();
        $rep->add_control('btn_text', [
            'label' => 'Text',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
        ]);
        $rep->add_control('btn_type', [
            'label' => 'Type',
            'type' => Controls_Manager::SELECT,
            'default' => 'primary',
            'options' => [
                'primary' => 'Primary',
                'primary-outline' => 'Primary Outline',
                'outline' => 'Outline',
                'link' => 'Link',
            ],
        ]);
        $rep->add_control('btn_link', [
            'label' => 'Page / URL',
            'type' => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'options' => ['url', 'is_external', 'nofollow'],
            'placeholder' => home_url('/'),
        ]);

        $this->add_control('buttons', [
            'label' => 'Buttons',
            'type' => Controls_Manager::REPEATER,
            'fields' => $rep->get_controls(),
            'title_field' => '{{{ btn_text }}}',
        ]);

        $this->add_control('image', [
            'label' => 'Right Image',
            'type' => Controls_Manager::MEDIA,
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $title = isset($s['title']) ? nl2br(esc_html($s['title'])) : '';
        $subtitle = isset($s['subtitle']) ? wp_kses_post($s['subtitle']) : '';
        $buttons = is_array($s['buttons'] ?? null) ? $s['buttons'] : [];
        $img = $s['image']['url'] ?? '';

        $map = [
            'primary' => 'btn--primary',
            'primary-outline' => 'btn--outline-primary',
            'outline' => 'btn--outline',
            'link' => 'btn--link',
        ];
        ?>
        <section class="header-hero">
            <div class="header-hero__inner">
                <div class="header-hero__left">
                    <?php if ($title): ?>
                        <h1 class="header-hero__title"><?= $title ?></h1>
                    <?php endif; ?>
                    <?php if ($subtitle): ?>
                        <div class="header-hero__subtitle subtitle"><?= $subtitle ?></div>
                    <?php endif; ?>
                    <?php if ($buttons): ?>
                        <div class="header-hero__actions">
                            <?php foreach ($buttons as $b):
                                $text = esc_html($b['btn_text'] ?? '');
                                $typeKey = $b['btn_type'] ?? 'primary';
                                $type = $map[$typeKey] ?? 'btn--primary';
                                $url = $b['btn_link']['url'] ?? '';
                                if (!$text || !$url) continue;
                                $is_ext = !empty($b['btn_link']['is_external']);
                                $nofollow = !empty($b['btn_link']['nofollow']);
                                $target = $is_ext ? ' target="_blank"' : '';
                                $rel = trim(($is_ext ? 'noopener' : '') . ' ' . ($nofollow ? 'nofollow' : ''));
                                $rel = $rel ? ' rel="' . esc_attr($rel) . '"' : '';
                            ?>
                                <a class="btn <?= esc_attr($type) ?>" href="<?= esc_url($url) ?>"<?= $target . $rel ?>><?= $text ?></a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="header-hero__right">
                    <?php if ($img): ?>
                        <img class="header-hero__image" src="<?= esc_url($img) ?>" alt="">
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
