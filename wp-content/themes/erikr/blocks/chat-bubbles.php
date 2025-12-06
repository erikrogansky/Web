<?php
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

class Chat_Bubbles_Block extends Widget_Base {
    public function get_name() { return 'chat_bubbles_block'; }
    public function get_title() { return 'Chat Bubbles'; }
    public function get_icon() { return 'eicon-chat'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('section_content', ['label' => __('Content','theme')]);

        $this->add_control('title', [
            'label' => __('Title','theme'),
            'type'  => Controls_Manager::TEXT,
            'default' => __('Chat Bubbles','theme'),
        ]);

        $rep = new Repeater();

        $rep->add_control('item_text', [
            'label' => __('Text','theme'),
            'type'  => Controls_Manager::WYSIWYG,
            'default' => __('Your message goes here…','theme'),
        ]);

        $rep->add_control('item_icon', [
            'label' => __('Icon','theme'),
            'type'  => Controls_Manager::MEDIA,
            'default' => ['url' => ''],
        ]);

        $rep->add_control('item_side', [
            'label' => __('Side','theme'),
            'type'  => Controls_Manager::SELECT,
            'options' => [
                'left'  => __('Left (text left, icon right)','theme'),
                'right' => __('Right (icon left, text right)','theme'),
            ],
            'default' => 'left',
        ]);

        $rep->add_control('item_color', [
            'label' => __('Bubble Color','theme'),
            'type'  => Controls_Manager::SELECT,
            'options' => [
                'white'     => __('White','theme'),
                'blue'      => __('Blue','theme'),
                'dark-blue' => __('Dark Blue','theme'),
            ],
            'default' => 'white',
        ]);

        $this->add_control('items', [
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $rep->get_controls(),
            'title_field' => '{{{ item_text ? item_text.substring(0, 24) + (item_text.length > 24 ? "…" : "") : "Item" }}}',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $title = $s['title'] ?? '';
        $items = $s['items'] ?? [];
        ?>
        <section class="chat-bubbles">
            <?php if ($title): ?>
                <h2 class="chat-bubbles__title"><?= esc_html($title) ?></h2>
            <?php endif; ?>

            <div class="chat-bubbles__list">
                <?php foreach ($items as $it):
                    $text  = $it['item_text'] ?? '';
                    $side  = $it['item_side'] ?? 'left';
                    $color = $it['item_color'] ?? 'white';
                    $icon  = $it['item_icon']['url'] ?? '';

                    $classes = [
                        'chat-bubbles__item',
                        "chat-bubbles__item--$side",
                    ];

                    $bubble_classes = [
                        'chat-bubbles__bubble',
                        "chat-bubbles__bubble--$color",
                    ];

                    $bubble_container_classes = [
                        'chat-bubbles__container',
                        "chat-bubbles__container--$side",
                        "chat-bubbles__container--$color",
                    ];
                ?>
                    <div class="<?= esc_attr(implode(' ', $classes)) ?>">
                        <?php if ($side === 'right' && $icon): ?>
                            <div class="chat-bubbles__icon"><?php echo render_icon($icon); ?></div>
                        <?php endif; ?>

                        <div class="<?= esc_attr(implode(' ', $bubble_container_classes)) ?>">
                            <div class="<?= esc_attr(implode(' ', $bubble_classes)) ?>">
                                <div class="chat-bubbles__text paragraph"><?= wp_kses_post($text) ?></div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="39" viewBox="0 0 44 39">
                                <path d="M1.24739 32.8715C6.50425 26.5029 12.7572 15.3146 10 -0.00341797H44C38.6884 28.6794 16.3233 36.2962 3.724 38.2991C0.899606 38.7481 -0.573153 35.077 1.24739 32.8715Z"/>
                            </svg>
                        </div>


                        <?php if ($side === 'left' && $icon): ?>
                            <div class="chat-bubbles__icon"><?php echo render_icon($icon); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}
