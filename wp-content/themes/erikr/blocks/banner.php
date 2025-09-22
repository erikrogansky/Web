<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Banner extends Widget_Base {
    public function get_name() { return 'banner'; }
    public function get_title() { return 'Banner'; }
    public function get_icon() { return 'eicon-call-to-action'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('content', ['label' => 'Content']);

        $this->add_control('title', [
            'label' => 'Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => '',
            'placeholder' => 'Your banner headline',
        ]);

        $btn = new Repeater();
        $btn->add_control('btn_text', [
            'label' => 'Text',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Learn more',
        ]);
        $btn->add_control('btn_type', [
            'label' => 'Type',
            'type'  => Controls_Manager::SELECT,
            'default' => 'primary',
            'options' => [
                'primary'          => 'Primary',
                'primary-outline'  => 'Primary Outline',
                'outline'          => 'Outline',
                'link'             => 'Link',
            ],
        ]);
        $btn->add_control('btn_link', [
            'label' => 'Page / URL',
            'type'  => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'placeholder' => home_url('/'),
        ]);

        $this->add_control('buttons', [
            'label' => 'Buttons',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $btn->get_controls(),
            'title_field' => '{{{ btn_text || "(button)" }}}',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $title   = trim($s['title'] ?? '');
        $buttons = is_array($s['buttons'] ?? null) ? $s['buttons'] : [];

        $btnMap = [
            'primary'          => 'btn--primary',
            'primary-outline'  => 'btn--outline-primary',
            'outline'          => 'btn--outline',
            'link'             => 'btn--link',
        ];
        ?>
        <section class="banner">
            <div class="banner__inner">
                <?php if ($title): ?>
                    <h2 class="banner__title"><?= esc_html($title) ?></h2>
                <?php endif; ?>

                <?php if ($buttons): ?>
                    <div class="banner__actions">
                        <?php foreach ($buttons as $b):
                            $text = trim($b['btn_text'] ?? '');
                            $typeKey = $b['btn_type'] ?? 'primary';
                            $type = $btnMap[$typeKey] ?? 'btn--primary';
                            $url  = $b['btn_link']['url'] ?? '';
                            if (!$text || !$url) continue;
                            $is_ext = !empty($b['btn_link']['is_external']);
                            $nofollow = !empty($b['btn_link']['nofollow']);
                            $target = $is_ext ? ' target="_blank"' : '';
                            $rel = trim(($is_ext ? 'noopener' : '') . ' ' . ($nofollow ? 'nofollow' : ''));
                            $rel = $rel ? ' rel="' . esc_attr($rel) . '"' : '';
                        ?>
                            <a class="btn <?= esc_attr($type) ?>" href="<?= esc_url($url) ?>"<?= $target . $rel ?>><?= esc_html($text) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
