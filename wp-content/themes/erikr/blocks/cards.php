<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Cards extends Widget_Base {
    public function get_name() { return 'cards'; }
    public function get_title() { return 'Cards'; }
    public function get_icon() { return 'eicon-gallery-grid'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('content', ['label' => 'Content']);

        $this->add_control('title', [
            'label' => 'Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
        ]);

        $this->add_control('card_max_width', [
            'label' => 'Max Width',
            'type'  => Controls_Manager::SLIDER,
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1200,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 300,
            ],
        ]);

        $this->add_control('card_justify', [
            'label' => 'Justify Content',
            'type'  => Controls_Manager::SELECT,
            'default' => 'center',
            'options' => [
                'center'     => 'Center',
                'space-between' => 'Space Between',
                'space-around'  => 'Space Around',
            ],
        ]);

        $this->add_control('card_gap', [
            'label' => 'Gap',
            'type'  => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 2,
                ],
            ],
            'default' => [
                'size' => 2,
            ],
            'condition' => ['card_justify' => 'center'],
        ]);

        $card = new Repeater();
        $card->add_control('card_icon', [
            'label' => 'Icon',
            'type'  => Controls_Manager::MEDIA,
        ]);
        $card->add_control('card_title', [
            'label' => 'Card Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
        ]);
        $card->add_control('card_desc', [
            'label' => 'Description',
            'type'  => Controls_Manager::WYSIWYG,
        ]);
        $card->add_control('card_bg', [
            'label' => 'Backgrounded',
            'type'  => Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off'=> 'No',
            'return_value' => 'yes',
            'default' => '',
        ]);

        $this->add_control('cards', [
            'label' => 'Cards',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $card->get_controls(),
            'title_field' => '{{{ card_title || "(untitled card)" }}}',
        ]);

        $this->add_control('show_buttons', [
            'label' => 'Show Buttons',
            'type'  => Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off'=> 'No',
            'return_value' => 'yes',
            'default' => 'yes',
            'separator' => 'before',
        ]);

        $btn = new Repeater();
        $btn->add_control('btn_text', [
            'label' => 'Text',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
        ]);
        $btn->add_control('btn_type', [
            'label' => 'Type',
            'type'  => Controls_Manager::SELECT,
            'default' => 'primary',
            'options' => [
                'primary' => 'Primary',
                'primary-outline' => 'Primary Outline',
                'outline' => 'Outline',
                'link' => 'Link',
            ],
        ]);
        $btn->add_control('btn_link', [
            'label' => 'Page / URL',
            'type'  => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'options' => ['url', 'is_external', 'nofollow'],
            'placeholder' => home_url('/'),
        ]);

        $this->add_control('buttons', [
            'label' => 'Buttons',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $btn->get_controls(),
            'title_field' => '{{{ btn_text }}}',
            'condition' => ['show_buttons' => 'yes'],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $title    = trim($s['title'] ?? '');
        $cards    = is_array($s['cards'] ?? null) ? $s['cards'] : [];
        $showBtns = !empty($s['show_buttons']) && $s['show_buttons'] === 'yes';
        $buttons  = $showBtns ? (is_array($s['buttons'] ?? null) ? $s['buttons'] : []) : [];

        $btnMap = [
            'primary'          => 'btn--primary',
            'primary-outline'  => 'btn--outline-primary',
            'outline'          => 'btn--outline',
            'link'             => 'btn--link',
        ];
        ?>
        <section class="cards">
            <div class="cards__inner">
                <?php if ($title): ?>
                    <h2 class="cards__title"><?= esc_html($title) ?></h2>
                <?php endif; ?>

                <?php if ($cards): ?>
                    <ul class="cards__list" style="justify-content: <?= esc_attr($s['card_justify'] ?? 'center') ?>; <?= isset($s['card_gap']['size']) ? 'gap: ' . esc_attr($s['card_gap']['size'] . $s['card_gap']['unit']) . ';' : '' ?>">
                        <?php foreach ($cards as $c):
                            $icon = $c['card_icon']['url'] ?? '';
                            $ct   = trim($c['card_title'] ?? '');
                            $cd   = trim($c['card_desc'] ?? '');
                            $bg   = !empty($c['card_bg']) && $c['card_bg'] === 'yes';
                            if (!$icon && !$ct && !$cd) continue;

                            $item_classes = 'cards__item' . ($bg ? ' cards__item--bg' : '');
                        ?>
                            <li class="<?= esc_attr($item_classes) ?>" style="<?= isset($s['card_max_width']['size']) ? 'max-width: ' . esc_attr($s['card_max_width']['size'] . $s['card_max_width']['unit']) . ';' : '' ?>">
                                <?php if ($icon): ?>
                                    <div class="cards__icon">
                                        <?= render_icon($icon, $ct ?: '') ?>
                                    </div>
                                <?php endif; ?>

                                <div class="cards__content">
                                    <?php if ($ct): ?>
                                        <h3 class="cards__item-title subtitle font-weight-700"><?= esc_html($ct) ?></h3>
                                    <?php endif; ?>
                                    <?php if ($cd): ?>
                                        <div class="cards__item-desc paragraph"><?= wp_kses_post($cd) ?></div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($showBtns && $buttons): ?>
                    <div class="cards__actions">
                        <?php foreach ($buttons as $b):
                            $text = esc_html($b['btn_text'] ?? '');
                            $typeKey = $b['btn_type'] ?? 'primary';
                            $type = $btnMap[$typeKey] ?? 'btn--primary';
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
        </section>
        <?php
    }
}
