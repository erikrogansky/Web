<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit;

class Numbers extends Widget_Base {
    public function get_name() { return 'numbers'; }
    public function get_title() { return 'Numbers'; }
    public function get_icon() { return 'eicon-counter-circle'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        // Block title
        $this->start_controls_section('section_content', ['label' => 'Content']);
        $this->add_control('title', [
            'label' => 'Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
        ]);
        $this->end_controls_section();

        // Card 1
        $this->start_controls_section('card_1', ['label' => 'Card 1']);
        $this->add_control('c1_img', ['label' => 'Image/Icon', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('c1_number', ['label' => 'Number', 'type' => Controls_Manager::TEXT]);
        $this->add_control('c1_label', ['label' => 'Label', 'type' => Controls_Manager::TEXT]);
        $this->end_controls_section();

        // Card 2
        $this->start_controls_section('card_2', ['label' => 'Card 2']);
        $this->add_control('c2_img', ['label' => 'Image/Icon', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('c2_number', ['label' => 'Number', 'type' => Controls_Manager::TEXT]);
        $this->add_control('c2_label', ['label' => 'Label', 'type' => Controls_Manager::TEXT]);
        $this->end_controls_section();

        // Card 3
        $this->start_controls_section('card_3', ['label' => 'Card 3']);
        $this->add_control('c3_img', ['label' => 'Image/Icon', 'type' => Controls_Manager::MEDIA]);
        $this->add_control('c3_number', ['label' => 'Number', 'type' => Controls_Manager::TEXT]);
        $this->add_control('c3_label', ['label' => 'Label', 'type' => Controls_Manager::TEXT]);
        $this->end_controls_section();
    }

    protected function render_card($img, $number, $label) {
        $icon_url = $img['url'] ?? ''; // your render_icon() expects a URL like in your Cards widget
        ?>
        <li class="numbers__item">
            <div class="numbers__media">
                <?php if ($icon_url): ?>
                    <?= render_icon($icon_url, $label ?: $number ?: '') ?>
                <?php endif; ?>
            </div>
            <div class="numbers__content">
                <?php if (!empty($number)): ?>
                    <div class="numbers__number"><?= esc_html($number) ?></div>
                <?php endif; ?>
                <?php if (!empty($label)): ?>
                    <div class="numbers__label paragraph"><?= esc_html($label) ?></div>
                <?php endif; ?>
            </div>
        </li>
        <?php
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $title = trim($s['title'] ?? '');
        $cards = [
            ['img' => $s['c1_img'] ?? [], 'number' => $s['c1_number'] ?? '', 'label' => $s['c1_label'] ?? ''],
            ['img' => $s['c2_img'] ?? [], 'number' => $s['c2_number'] ?? '', 'label' => $s['c2_label'] ?? ''],
            ['img' => $s['c3_img'] ?? [], 'number' => $s['c3_number'] ?? '', 'label' => $s['c3_label'] ?? ''],
        ];

        // If truly nothing is set, skip render noise
        $has_any = false;
        foreach ($cards as $c) {
            if (($c['number'] ?? '') || ($c['label'] ?? '') || !empty(($c['img']['url'] ?? ''))) { $has_any = true; break; }
        }
        if (!$has_any && $title === '') return;
        ?>
        <section class="numbers">
            <?php if ($title): ?>
                <h2 class="numbers__title text-center"><?= esc_html($title) ?></h2>
            <?php endif; ?>
            <ul class="numbers__list">
                <?php foreach ($cards as $c) {
                    if (!$c['number'] && !$c['label'] && empty($c['img']['url'])) continue;
                    $this->render_card($c['img'], $c['number'], $c['label']);
                } ?>
            </ul>
        </section>
        <?php
    }
}
