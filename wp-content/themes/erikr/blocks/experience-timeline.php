<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Experience_Timeline extends Widget_Base {
    public function get_name() { return 'er_experience_timeline'; }
    public function get_title() { return 'Experience Timeline'; }
    public function get_icon() { return 'eicon-time-line'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {

        /* -----------------------------
         * Section: Block (General)
         * ----------------------------- */
        $this->start_controls_section('block_section', ['label' => 'Block']);

        $this->add_control('block_title', [
            'label' => 'Block Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => '',
            'placeholder' => 'Experience',
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Cards (Repeater)
         * ----------------------------- */
        $this->start_controls_section('cards_section', ['label' => 'Cards']);

        $chip_rep = new Repeater();
        $chip_rep->add_control('chip_text', [
            'label' => 'Chip Text',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => '',
        ]);

        $tool_rep = new Repeater();
        $tool_rep->add_control('tool_text', [
            'label' => 'Toolkit Item',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => '',
        ]);

        $card = new Repeater();

        $card->add_control('start', [
            'label' => 'Start',
            'type'  => Controls_Manager::TEXT,
            'default' => '',
            'placeholder' => 'Aug 2023',
        ]);
        $card->add_control('end', [
            'label' => 'End',
            'type'  => Controls_Manager::TEXT,
            'default' => 'present',
            'placeholder' => 'present',
        ]);
        $card->add_control('position', [
            'label' => 'Position / Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Translator (English to Slovak)',
        ]);
        $card->add_control('chips', [
            'label' => 'Top Chips',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $chip_rep->get_controls(),
            'title_field' => '{{{ chip_text || "(chip)" }}}',
        ]);
        $card->add_control('how_started', [
            'label' => 'How it started',
            'type'  => Controls_Manager::WYSIWYG,
            'default' => '',
            'placeholder' => 'How it started…',
        ]);
        $card->add_control('what_i_do', [
            'label' => 'What I do',
            'type'  => Controls_Manager::WYSIWYG,
            'default' => '',
            'placeholder' => 'What I do…',
        ]);
        $card->add_control('toolkit', [
            'label' => 'Toolkit (chips)',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $tool_rep->get_controls(),
            'title_field' => '{{{ tool_text || "(tool)" }}}',
        ]);
        $card->add_control('what_i_learned', [
            'label' => 'What I’ve learned',
            'type'  => Controls_Manager::WYSIWYG,
            'default' => '',
            'placeholder' => 'What I’ve learned…',
        ]);

        $this->add_control('cards', [
            'label' => 'Timeline Cards',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $card->get_controls(),
            'title_field' => '{{{ position || (start + " – " + end) }}}',
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Final CTA Card
         * ----------------------------- */
        $this->start_controls_section('cta_section', ['label' => 'Final Card (CTA)']);

        $this->add_control('cta_title', [
            'label' => 'Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Your Project Could Be Next',
        ]);
        $this->add_control('cta_description', [
            'label' => 'Description',
            'type'  => Controls_Manager::TEXTAREA,
            'label_block' => true,
            'default' => "I've worked on UX, dev, and translations…\nnow I’d love to add your story here",
        ]);
        $this->add_control('cta_btn_text', [
            'label' => 'Button Text',
            'type'  => Controls_Manager::TEXT,
            'default' => 'Add your story',
        ]);
        $this->add_control('cta_btn_link', [
            'label' => 'Button Link',
            'type'  => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'placeholder' => home_url('/contact'),
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $cards = is_array($s['cards'] ?? null) ? $s['cards'] : [];

        // Block title
        $block_title = trim($s['block_title'] ?? '');

        // CTA
        $cta_title = trim($s['cta_title'] ?? '');
        $cta_desc  = trim($s['cta_description'] ?? '');
        $cta_btn_text = trim($s['cta_btn_text'] ?? '');
        $cta_url  = $s['cta_btn_link']['url'] ?? '';
        $cta_is_ext = !empty($s['cta_btn_link']['is_external']);
        $cta_nof   = !empty($s['cta_btn_link']['nofollow']);
        $cta_target = $cta_is_ext ? ' target="_blank"' : '';
        $cta_rel = trim(($cta_is_ext ? 'noopener' : '') . ' ' . ($cta_nof ? 'nofollow' : ''));
        $cta_rel = $cta_rel ? ' rel="' . esc_attr($cta_rel) . '"' : '';

        // Generate section ID from block title
        $section_id = $block_title ? sanitize_title($block_title) : '';

        ?>
        <section class="timeline"<?= $section_id ? ' id="' . esc_attr($section_id) . '"' : '' ?>>
            <?php if ($block_title): ?>
                <h2 class="timeline__heading text-center"><?= esc_html($block_title) ?></h2>
            <?php endif; ?>

            <div class="timeline__track" aria-hidden="true"></div>

            <?php if ($cards): ?>
                <div class="timeline__list">
                    <?php foreach ($cards as $i => $c):
                        $side = ($i % 2 === 0) ? 'left' : 'right';
                        $start = trim($c['start'] ?? '');
                        $end   = trim($c['end'] ?? '');
                        $pos   = trim($c['position'] ?? '');

                        $chips = is_array($c['chips'] ?? null) ? $c['chips'] : [];
                        $tools = is_array($c['toolkit'] ?? null) ? $c['toolkit'] : [];

                        $how    = isset($c['how_started']) ? $c['how_started'] : '';
                        $what   = isset($c['what_i_do']) ? $c['what_i_do'] : '';
                        $learn  = isset($c['what_i_learned']) ? $c['what_i_learned'] : '';
                    ?>
                    <article class="timeline__item timeline__item--<?= esc_attr($side) ?>">
                        <div class="timeline-card">
                            <header class="timeline-card__header">
                                <div class="timeline-card__dates">
                                    <?php if ($start || $end): ?>
                                        <span class="timeline-card__date paragraph paragraph--small font-weight-700"><?= esc_html($start ?: '') ?></span>
                                        <span class="timeline-card__dash paragraph paragraph--small font-weight-700">–</span>
                                        <span class="timeline-card__date paragraph paragraph--small font-weight-700"><?= esc_html($end ?: '') ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($pos): ?>
                                    <h3 class="timeline-card__title subtitle font-weight-700"><?= esc_html($pos) ?></h3>
                                <?php endif; ?>
                                <?php if ($chips): ?>
                                    <div class="timeline-card__chips">
                                        <?php foreach ($chips as $chip):
                                            $t = trim($chip['chip_text'] ?? '');
                                            if (!$t) continue; ?>
                                            <span class="chip paragraph paragraph--micro font-weight-900"><?= esc_html($t) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </header>

                            <?php if (!empty($how)): ?>
                                <div class="timeline-card__block">
                                    <!-- <div class="timeline-card__label paragraph">💡 How it started: </div> -->
                                    <div class="timeline-card__content paragraph"><?= wp_kses_post($how) ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($what)): ?>
                                <div class="timeline-card__block">
                                    <!-- <div class="timeline-card__label paragraph">📌 What I do: </div> -->
                                    <div class="timeline-card__content paragraph"><?= wp_kses_post($what) ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($tools): ?>
                                <div class="timeline-card__block">
                                    <div class="timeline-card__label paragraph font-weight-700">Toolkit: </div>
                                    <div class="timeline-card__chips">
                                        <?php foreach ($tools as $tool):
                                            $tx = trim($tool['tool_text'] ?? '');
                                            if (!$tx) continue; ?>
                                            <span class="chip chip--tool paragraph paragraph--micro font-weight-900"><?= esc_html($tx) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($learn)): ?>
                                <div class="timeline-card__block">
                                    <!-- <div class="timeline-card__label paragraph">✨ What I’ve learned: </div> -->
                                    <div class="timeline-card__content paragraph"><?= wp_kses_post($learn) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="timeline__cta">
                <div class="timeline-card">
                    <?php if ($cta_title): ?>
                        <h3 class="timeline-card__title h5 text-center"><?= esc_html($cta_title) ?></h3>
                    <?php endif; ?>
                    <?php if ($cta_desc): ?>
                        <p class="timeline-card__desc paragraph text-center"><?= nl2br(esc_html($cta_desc)) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($cta_url) && $cta_btn_text): ?>
                        <a class="btn btn--primary btn--small timeline-card__btn"
                           href="<?= esc_url($cta_url) ?>"<?= $cta_target . $cta_rel ?>><?= esc_html($cta_btn_text) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
