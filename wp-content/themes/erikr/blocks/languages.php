<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Languages extends Widget_Base {
    public function get_name() { return 'er_languages'; }
    public function get_title() { return 'Languages'; }
    public function get_icon() { return 'eicon-globe'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('general_section', ['label' => 'General']);

        $this->add_control('title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Languages I Work In',
        ]);

        $this->add_control('intro', [
            'label' => 'Intro',
            'type' => Controls_Manager::WYSIWYG,
        ]);

        $this->end_controls_section();

        $this->start_controls_section('known_languages_section', ['label' => 'Known Languages']);

        $language = new Repeater();

        $language->add_control('language_name', [
            'label' => 'Language Name',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'English',
        ]);

        $language->add_control('language_level', [
            'label' => 'CEFR Level',
            'type' => Controls_Manager::SELECT,
            'default' => 'c1',
            'options' => [
                'native' => 'Native',
                'c2' => 'C2',
                'c1' => 'C1',
                'b2' => 'B2',
                'b1' => 'B1',
                'a2' => 'A2',
                'a1' => 'A1',
            ],
        ]);

        $language->add_control('language_level_label', [
            'label' => 'Human Level Label',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Professional working fluency',
        ]);

        $language->add_control('language_level_percent', [
            'label' => 'Level Bar',
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => '%',
                'size' => 80,
            ],
        ]);

        $language->add_control('language_description', [
            'label' => 'Short Description',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 3,
        ]);

        $language->add_control('credential_label', [
            'label' => 'Credential Label',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'National State Exam',
        ]);

        $language->add_control('credential_meta', [
            'label' => 'Credential Meta',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Year, institution, or details',
        ]);

        $language->add_control('active_learning', [
            'label' => 'Currently Learning / Improving',
            'type' => Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off' => 'No',
            'return_value' => 'yes',
            'default' => '',
        ]);

        $language->add_control('active_goal', [
            'label' => 'Active Goal',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Improving spoken confidence',
            'condition' => ['active_learning' => 'yes'],
        ]);

        $this->add_control('known_languages', [
            'label' => 'Known Languages',
            'type' => Controls_Manager::REPEATER,
            'fields' => $language->get_controls(),
            'title_field' => '{{{ language_name || "(language)" }}}',
            'default' => [],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('growth_roadmap_section', ['label' => 'Growth Roadmap']);

        $roadmap = new Repeater();

        $roadmap->add_control('roadmap_language_name', [
            'label' => 'Language Name',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'French',
        ]);

        $roadmap->add_control('roadmap_intent', [
            'label' => 'Intent',
            'type' => Controls_Manager::SELECT,
            'default' => 'learning',
            'options' => [
                'learning' => 'Learning next',
                'improving' => 'Improving',
                'maintaining' => 'Maintaining',
            ],
        ]);

        $roadmap->add_control('roadmap_reason', [
            'label' => 'Reason',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 2,
        ]);

        $this->add_control('growth_roadmap', [
            'label' => 'Growth Roadmap',
            'type' => Controls_Manager::REPEATER,
            'fields' => $roadmap->get_controls(),
            'title_field' => '{{{ roadmap_language_name || "(roadmap item)" }}}',
            'default' => [],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $title = trim($s['title'] ?? '');
        $intro = trim($s['intro'] ?? '');
        $known_languages = is_array($s['known_languages'] ?? null) ? $s['known_languages'] : [];
        $growth_roadmap = is_array($s['growth_roadmap'] ?? null) ? $s['growth_roadmap'] : [];

        $level_map = [
            'native' => 'Native',
            'c2' => 'C2',
            'c1' => 'C1',
            'b2' => 'B2',
            'b1' => 'B1',
            'a2' => 'A2',
            'a1' => 'A1',
        ];

        $intent_map = [
            'learning' => 'Learning next',
            'improving' => 'Improving',
            'maintaining' => 'Maintaining',
        ];

        $section_id = $title ? sanitize_title($title) : '';
        ?>
        <section class="languages-block"<?= $section_id ? ' id="' . esc_attr($section_id) . '"' : '' ?>>
            <?php if ($title || $intro): ?>
                <div class="languages-block__header">
                    <?php if ($title): ?>
                        <h2 class="languages-block__title"><?= esc_html($title) ?></h2>
                    <?php endif; ?>

                    <?php if ($intro): ?>
                        <div class="languages-block__intro paragraph"><?= wp_kses_post($intro) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php
            $has_known = false;
            foreach ($known_languages as $language) {
                if (trim($language['language_name'] ?? '')) { $has_known = true; break; }
            }
            ?>
            <?php if ($has_known): ?>
                <div class="languages-block__grid">
                    <?php foreach ($known_languages as $language):
                        $name = trim($language['language_name'] ?? '');
                        if (!$name) continue;

                        $level_key = $language['language_level'] ?? '';
                        $level = $level_map[$level_key] ?? '';
                        $level_label = trim($language['language_level_label'] ?? '');
                        $level_percent = isset($language['language_level_percent']['size']) ? (int) $language['language_level_percent']['size'] : 0;
                        $level_percent = max(0, min(100, $level_percent));
                        $description = trim($language['language_description'] ?? '');
                        $credential_label = trim($language['credential_label'] ?? '');
                        $credential_meta = trim($language['credential_meta'] ?? '');
                        $is_active = ($language['active_learning'] ?? '') === 'yes';
                        $active_goal = trim($language['active_goal'] ?? '');
                    ?>
                        <article class="languages-block__card">
                            <div class="languages-block__card-header">
                                <h3 class="languages-block__language"><?= esc_html($name) ?></h3>
                                <?php if ($level): ?>
                                    <span class="languages-block__level-badge"><?= esc_html($level) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($level_label): ?>
                                <p class="languages-block__level-label"><?= esc_html($level_label) ?></p>
                            <?php endif; ?>

                            <div class="languages-block__level-bar" aria-label="<?= esc_attr($name . ' level: ' . $level_percent . '%') ?>">
                                <span style="width: <?= esc_attr($level_percent) ?>%;"></span>
                            </div>

                            <?php if ($description): ?>
                                <p class="languages-block__description"><?= esc_html($description) ?></p>
                            <?php endif; ?>

                            <?php if ($credential_label || $is_active): ?>
                                <div class="languages-block__badges">
                                    <?php if ($credential_label): ?>
                                        <div class="languages-block__credential">
                                            <span class="languages-block__credential-label"><?= esc_html($credential_label) ?></span>
                                            <?php if ($credential_meta): ?>
                                                <span class="languages-block__credential-meta"><?= esc_html($credential_meta) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($is_active): ?>
                                        <div class="languages-block__active-badge">
                                            <span class="languages-block__active-dot" aria-hidden="true"></span>
                                            <span>Active learning</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($is_active && $active_goal): ?>
                                <p class="languages-block__active-goal"><?= esc_html($active_goal) ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php
            $has_roadmap = false;
            foreach ($growth_roadmap as $item) {
                if (trim($item['roadmap_language_name'] ?? '')) { $has_roadmap = true; break; }
            }
            ?>
            <?php if ($has_roadmap): ?>
                <div class="languages-block__roadmap">
                    <h3 class="languages-block__roadmap-title">Growth Roadmap</h3>
                    <div class="languages-block__roadmap-list">
                        <?php foreach ($growth_roadmap as $item):
                            $name = trim($item['roadmap_language_name'] ?? '');
                            if (!$name) continue;

                            $intent_key = $item['roadmap_intent'] ?? 'learning';
                            $intent = $intent_map[$intent_key] ?? $intent_map['learning'];
                            $reason = trim($item['roadmap_reason'] ?? '');
                            $intent_class = sanitize_html_class($intent_key);
                        ?>
                            <article class="languages-block__roadmap-item languages-block__roadmap-item--<?= esc_attr($intent_class) ?>">
                                <div class="languages-block__roadmap-heading">
                                    <h4 class="languages-block__roadmap-language"><?= esc_html($name) ?></h4>
                                    <span class="languages-block__roadmap-intent"><?= esc_html($intent) ?></span>
                                </div>

                                <?php if ($reason): ?>
                                    <p class="languages-block__roadmap-reason"><?= esc_html($reason) ?></p>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}
