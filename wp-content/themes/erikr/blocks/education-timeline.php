<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Education_Timeline extends Widget_Base {
    public function get_name()        { return 'er_education_timeline'; }
    public function get_title()       { return 'Education Timeline'; }
    public function get_icon()        { return 'eicon-time-line'; }
    public function get_categories()  { return ['er-elements']; }

    protected function register_controls() {

        /* -----------------------------
         * Section: Block (General)
         * ----------------------------- */
        $this->start_controls_section('block_section', ['label' => 'Block']);

        $this->add_control('block_title', [
            'label' => 'Block Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Education & Milestones',
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Cards (Repeater)
         * ----------------------------- */
        $this->start_controls_section('cards_section', ['label' => 'Cards']);

        $card = new Repeater();

        // Timeline meta
        $card->add_control('timeline_label', [
            'label' => 'Label Above Card (e.g., “Finished journey at high school”)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
        ]);
        $card->add_control('timeline_date_badge', [
            'label' => 'Time Chip (e.g., “June 2022”)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
        ]);

        // Core card content
        $card->add_control('start', [
            'label' => 'Start',
            'type'  => Controls_Manager::TEXT,
            'placeholder' => 'Sept 2022',
        ]);
        $card->add_control('end', [
            'label' => 'End',
            'type'  => Controls_Manager::TEXT,
            'placeholder' => 'June 2025',
        ]);
        $card->add_control('title', [
            'label' => 'Title (e.g., school / role)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Northeastern University',
        ]);
        $card->add_control('subtitle', [
            'label' => 'Subtitle (e.g., address or program)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => '360 Huntington Ave, Boston, MA',
        ]);

        // Single chip (badge)
        $card->add_control('chip_text', [
            'label' => 'Chip',
            'type'  => Controls_Manager::TEXT,
            'placeholder' => 'MS in Information Systems',
        ]);

        // Body blocks
        $card->add_control('why', [
            'label' => 'Why I chose this',
            'type'  => Controls_Manager::WYSIWYG,
            'placeholder' => '💡 Why I chose this…',
        ]);
        $card->add_control('learned', [
            'label' => 'What I’ve learned',
            'type'  => Controls_Manager::WYSIWYG,
            'placeholder' => '✨ What I’ve learned…',
        ]);

        // Optional link
        $card->add_control('link_text', [
            'label' => 'Link Text',
            'type'  => Controls_Manager::TEXT,
            'placeholder' => "See school's website",
        ]);
        $card->add_control('link_url', [
            'label' => 'Link URL',
            'type'  => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'placeholder' => 'https://…',
        ]);

        // Side override (default alternates)
        $card->add_control('side', [
            'label' => 'Side',
            'type'  => Controls_Manager::SELECT,
            'default' => 'auto',
            'options' => [
                'auto'  => 'Auto (alternate)',
                'left'  => 'Left',
                'right' => 'Right',
            ],
        ]);

        $this->add_control('cards', [
            'label' => 'Timeline Cards',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $card->get_controls(),
            'title_field' => '{{{ title || (start + " – " + end) }}}',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $cards = is_array($s['cards'] ?? null) ? $s['cards'] : [];
        $block_title = trim($s['block_title'] ?? '');

        ?>
        <section class="education-timeline education-timeline--grid">
            <?php if ($block_title): ?>
                <h2 class="education-timeline__heading text-center"><?= esc_html($block_title) ?></h2>
            <?php endif; ?>

            <!-- Central vertical track -->
            <div class="education-timeline__track" aria-hidden="true"></div>

            <?php if ($cards): ?>
                <div class="education-timeline__list">
                    <?php foreach ($cards as $i => $c):
                        $alt_side = ($i % 2 === 0) ? 'left' : 'right';
                        $side = ($c['side'] ?? 'auto') === 'auto' ? $alt_side : $c['side'];

                        $label   = trim($c['timeline_label'] ?? '');
                        $badge   = trim($c['timeline_date_badge'] ?? '');
                        $start   = trim($c['start'] ?? '');
                        $end     = trim($c['end'] ?? '');
                        $title   = trim($c['title'] ?? '');
                        $subtitle= trim($c['subtitle'] ?? '');
                        $chip    = trim($c['chip_text'] ?? '');
                        $why     = $c['why'] ?? '';
                        $learned = $c['learned'] ?? '';

                        $link_t  = trim($c['link_text'] ?? '');
                        $link_u  = $c['link_url']['url'] ?? '';
                        $is_ext  = !empty($c['link_url']['is_external']);
                        $nofollow= !empty($c['link_url']['nofollow']);
                        $target  = $is_ext ? ' target="_blank"' : '';
                        $rel_str = trim(($is_ext ? 'noopener' : '') . ' ' . ($nofollow ? 'nofollow' : ''));
                        $rel     = $rel_str ? ' rel="' . esc_attr($rel_str) . '"' : '';

                        $left_has_card  = ($side === 'left');
                        $right_has_card = ($side === 'right');
                    ?>
                    <div class="education-timeline__row">
                        <!-- Left cell -->
                        <div class="education-timeline__cell education-timeline__cell--left">
                            <?php if ($left_has_card): ?>
                                <!-- Chip/badge for mobile/tablet -->
                                <?php if ($badge): ?>
                                    <div class="education-timeline__time education-timeline__time--on-card-side">
                                        <span class="education-timeline__date-badge paragraph paragraph--small font-weight-800"><?= esc_html($badge) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($label): ?>
                                    <div class="education-timeline__label subtitle font-weight-900"><?= esc_html($label) ?></div>
                                <?php endif; ?>

                                <article class="education-timeline-card">
                                    <header class="education-timeline-card__header">
                                        <div class="education-timeline-card__dates paragraph paragraph--small font-weight-700">
                                            <?php if ($start): ?><span class="education-timeline-card__date"><?= esc_html($start) ?></span><?php endif; ?>
                                            <?php if ($start || $end): ?><span class="education-timeline-card__dash"> – </span><?php endif; ?>
                                            <?php if ($end): ?><span class="education-timeline-card__date"><?= esc_html($end) ?></span><?php endif; ?>
                                        </div>

                                        <?php if ($link_t && $link_u): ?>
                                            <a class="education-timeline-card__header-link paragraph paragraph--small"
                                               href="<?= esc_url($link_u) ?>"<?= $target . $rel ?>><?= esc_html($link_t) ?></a>
                                        <?php endif; ?>

                                        <?php if ($title): ?>
                                            <h3 class="education-timeline-card__title subtitle font-weight-700"><?= esc_html($title) ?></h3>
                                        <?php endif; ?>
                                        <?php if ($subtitle): ?>
                                            <div class="education-timeline-card__subtitle paragraph paragraph--small"><?= esc_html($subtitle) ?></div>
                                        <?php endif; ?>

                                        <?php if ($chip): ?>
                                            <div class="education-timeline-card__chips">
                                                <span class="chip paragraph paragraph--micro font-weight-900"><?= esc_html($chip) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </header>

                                    <?php if (!empty($why)): ?>
                                        <div class="education-timeline-card__block">
                                            <div class="education-timeline-card__content paragraph"><?= wp_kses_post($why) ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($learned)): ?>
                                        <div class="education-timeline-card__block">
                                            <div class="education-timeline-card__content paragraph"><?= wp_kses_post($learned) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            <?php else: ?>
                                <!-- Opposite-side time chip when card is on RIGHT -->
                                <?php if ($badge): ?>
                                    <div class="education-timeline__time education-timeline__time--left-side">
                                        <span class="education-timeline__date-badge paragraph paragraph--small font-weight-800"><?= esc_html($badge) ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Center cell (dot only) -->
                        <div class="education-timeline__cell education-timeline__cell--center" aria-hidden="true">
                            <span class="education-timeline__dot"></span>
                        </div>

                        <!-- Right cell -->
                        <div class="education-timeline__cell education-timeline__cell--right">
                            <?php if ($right_has_card): ?>
                                <!-- Chip/badge for mobile/tablet -->
                                <?php if ($badge): ?>
                                    <div class="education-timeline__time education-timeline__time--on-card-side">
                                        <span class="education-timeline__date-badge paragraph paragraph--small font-weight-800"><?= esc_html($badge) ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($label): ?>
                                    <div class="education-timeline__label subtitle font-weight-900"><?= esc_html($label) ?></div>
                                <?php endif; ?>

                                <article class="education-timeline-card">
                                    <header class="education-timeline-card__header">
                                        <div class="education-timeline-card__dates paragraph paragraph--small font-weight-700">
                                            <?php if ($start): ?><span class="education-timeline-card__date"><?= esc_html($start) ?></span><?php endif; ?>
                                            <?php if ($start || $end): ?><span class="education-timeline-card__dash"> – </span><?php endif; ?>
                                            <?php if ($end): ?><span class="education-timeline-card__date"><?= esc_html($end) ?></span><?php endif; ?>
                                        </div>

                                        <?php if ($link_t && $link_u): ?>
                                            <a class="education-timeline-card__header-link paragraph paragraph--small"
                                               href="<?= esc_url($link_u) ?>"<?= $target . $rel ?>><?= esc_html($link_t) ?></a>
                                        <?php endif; ?>

                                        <?php if ($title): ?>
                                            <h3 class="education-timeline-card__title subtitle font-weight-700"><?= esc_html($title) ?></h3>
                                        <?php endif; ?>
                                        <?php if ($subtitle): ?>
                                            <div class="education-timeline-card__subtitle paragraph paragraph--small"><?= esc_html($subtitle) ?></div>
                                        <?php endif; ?>

                                        <?php if ($chip): ?>
                                            <div class="education-timeline-card__chips">
                                                <span class="chip paragraph paragraph--micro font-weight-900"><?= esc_html($chip) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </header>

                                    <?php if (!empty($why)): ?>
                                        <div class="education-timeline-card__block">
                                            <div class="education-timeline-card__content paragraph"><?= wp_kses_post($why) ?></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($learned)): ?>
                                        <div class="education-timeline-card__block">
                                            <div class="education-timeline-card__content paragraph"><?= wp_kses_post($learned) ?></div>
                                        </div>
                                    <?php endif; ?>
                                </article>
                            <?php else: ?>
                                <!-- Opposite-side time chip when card is on LEFT -->
                                <?php if ($badge): ?>
                                    <div class="education-timeline__time education-timeline__time--right-side">
                                        <span class="education-timeline__date-badge paragraph paragraph--small font-weight-800"><?= esc_html($badge) ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}
