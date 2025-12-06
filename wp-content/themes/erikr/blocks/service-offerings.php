<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Service_Offerings extends Widget_Base {
    public function get_name() { return 'service_offerings'; }
    public function get_title() { return 'Service Offerings'; }
    public function get_icon() { return 'eicon-info-box'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('content', ['label' => 'Content']);

        // Master Title (optional)
        $this->add_control('master_title', [
            'label' => 'Master Title (Optional)',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
        ]);

        // Service Title
        $this->add_control('service_title', [
            'label' => 'Service Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'UX/UI Design',
        ]);

        // Service Subtitle
        $this->add_control('service_subtitle', [
            'label' => 'Service Subtitle',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Make it make sense. And make it feel good.',
        ]);

        // Service Description
        $this->add_control('service_description', [
            'label' => 'Service Description',
            'type' => Controls_Manager::WYSIWYG,
            'default' => 'I design interfaces that respect attention. Research when it\'s needed, momentum when it\'s not. I balance product goals with inclusive, evidence-based decisions.',
        ]);

        // Tags (comma separated)
        $this->add_control('tags', [
            'label' => 'Tags (comma separated)',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Discovery, Information architecture, Wireframes, Prototypes',
            'default' => 'Discovery, Information architecture, Wireframes, Prototypes, Visual design, Design systems, Accessibility reviews',
        ]);

        $this->add_control('tags_separator', [
            'type' => Controls_Manager::DIVIDER,
        ]);

        // Card 1 Title
        $this->add_control('card1_title', [
            'label' => 'First Card Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Outcomes',
        ]);

        // Card 1 Content
        $this->add_control('card1_content', [
            'label' => 'First Card Content',
            'type' => Controls_Manager::WYSIWYG,
            'default' => '<ul><li>Clear user flows & prototypes</li><li>Component library + tokens (light/dark, contrast)</li><li>Handoff kit (specs, annotations, behaviors)</li></ul>',
        ]);

        // Card 2 Title
        $this->add_control('card2_title', [
            'label' => 'Second Card Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'default' => 'Process',
        ]);

        // Card 2 Content
        $this->add_control('card2_content', [
            'label' => 'Second Card Content',
            'type' => Controls_Manager::WYSIWYG,
            'default' => '<ul><li>Listen & define — goals, users, constraints</li><li>Map & sketch — flows, wireframes, early tests</li><li>Design & refine — high-fidelity UI, states, edge cases</li><li>Handoff & support — specs, tokens, async review</li></ul>',
        ]);

        $this->add_control('cards_separator', [
            'type' => Controls_Manager::DIVIDER,
        ]);

        // FAQ Repeater
        $faq = new Repeater();
        $faq->add_control('faq_title', [
            'label' => 'Question',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
        ]);
        $faq->add_control('faq_content', [
            'label' => 'Answer',
            'type' => Controls_Manager::WYSIWYG,
        ]);

        $this->add_control('faqs', [
            'label' => 'Frequently Asked Questions',
            'type' => Controls_Manager::REPEATER,
            'fields' => $faq->get_controls(),
            'title_field' => '{{{ faq_title }}}',
            'default' => [
                [
                    'faq_title' => 'How long does it take?',
                    'faq_content' => '<p>Long answer of the faq. Long answer of the faq. Long answer of the faq.</p>',
                ],
                [
                    'faq_title' => 'Do you test with users?',
                    'faq_content' => '<p>Yes, when appropriate for the project scope and timeline.</p>',
                ],
                [
                    'faq_title' => 'What tools?',
                    'faq_content' => '<p>Figma for design, prototyping, and handoff. Sometimes pen and paper for early sketches.</p>',
                ],
            ],
        ]);

        $this->add_control('faq_separator', [
            'type' => Controls_Manager::DIVIDER,
        ]);

        // CTA Button
        $this->add_control('button_text', [
            'label' => 'Button Text',
            'type' => Controls_Manager::TEXT,
            'default' => 'Start a UX/UI scope',
        ]);

        $this->add_control('button_link', [
            'label' => 'Button Link',
            'type' => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'options' => ['url', 'is_external', 'nofollow'],
            'default' => [
                'url' => home_url('/contact'),
            ],
        ]);

        $this->add_control('button_style', [
            'label' => 'Button Style',
            'type' => Controls_Manager::SELECT,
            'default' => 'primary',
            'options' => [
                'primary' => 'Primary',
                'primary-outline' => 'Primary Outline',
                'outline' => 'Outline',
                'link' => 'Link',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $master_title = trim($s['master_title'] ?? '');
        $service_title = trim($s['service_title'] ?? '');
        $service_subtitle = trim($s['service_subtitle'] ?? '');
        $service_description = trim($s['service_description'] ?? '');
        $tags = trim($s['tags'] ?? '');
        
        $card1_title = trim($s['card1_title'] ?? '');
        $card1_content = trim($s['card1_content'] ?? '');
        $card2_title = trim($s['card2_title'] ?? '');
        $card2_content = trim($s['card2_content'] ?? '');
        
        $faqs = is_array($s['faqs'] ?? null) ? $s['faqs'] : [];
        
        $button_text = trim($s['button_text'] ?? '');
        $button_link = $s['button_link']['url'] ?? home_url('/contact');
        $button_style = $s['button_style'] ?? 'primary';

        $btnMap = [
            'primary' => 'btn--primary',
            'primary-outline' => 'btn--outline-primary',
            'outline' => 'btn--outline',
            'link' => 'btn--link',
        ];
        $btnClass = $btnMap[$button_style] ?? 'btn--primary';

        $is_ext = !empty($s['button_link']['is_external']);
        $nofollow = !empty($s['button_link']['nofollow']);
        $target = $is_ext ? ' target="_blank"' : '';
        $rel = trim(($is_ext ? 'noopener' : '') . ' ' . ($nofollow ? 'nofollow' : ''));
        $rel = $rel ? ' rel="' . esc_attr($rel) . '"' : '';

        // Parse tags
        $tag_array = array_filter(array_map('trim', explode(',', $tags)));

        // Generate section ID from master_title or service_title
        $title_for_id = $master_title ?: $service_title;
        $section_id = $title_for_id ? sanitize_title($title_for_id) : '';

        ?>
        <section class="service-offerings"<?= $section_id ? ' id="' . esc_attr($section_id) . '"' : '' ?>>
            <div class="service-offerings__inner">
                <?php if ($master_title): ?>
                    <h2 class="service-offerings__master-title"><?= esc_html($master_title) ?></h2>
                <?php endif; ?>

                <div class="service-offerings__header">
                    <?php if ($service_title): ?>
                        <h3 class="service-offerings__title"><?= esc_html($service_title) ?></h3>
                    <?php endif; ?>
                    <?php if ($service_subtitle): ?>
                        <p class="service-offerings__subtitle subtitle font-weight-700"><?= esc_html($service_subtitle) ?></p>
                    <?php endif; ?>
                    <?php if ($service_description): ?>
                        <div class="service-offerings__description paragraph"><?= wp_kses_post($service_description) ?></div>
                    <?php endif; ?>
                </div>

                <?php if ($tag_array): ?>
                    <div class="service-offerings__tags">
                        <?php foreach ($tag_array as $tag): ?>
                            <span class="service-offerings__tag"><?= esc_html($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($card1_title || $card1_content || $card2_title || $card2_content): ?>
                    <div class="service-offerings__cards">
                        <?php if ($card1_title || $card1_content): ?>
                            <div class="service-offerings__card">
                                <?php if ($card1_title): ?>
                                    <h4 class="service-offerings__card-title"><?= esc_html($card1_title) ?></h4>
                                <?php endif; ?>
                                <?php if ($card1_content): ?>
                                    <div class="service-offerings__card-content paragraph"><?= wp_kses_post($card1_content) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($card2_title || $card2_content): ?>
                            <div class="service-offerings__card">
                                <?php if ($card2_title): ?>
                                    <h4 class="service-offerings__card-title"><?= esc_html($card2_title) ?></h4>
                                <?php endif; ?>
                                <?php if ($card2_content): ?>
                                    <div class="service-offerings__card-content paragraph"><?= wp_kses_post($card2_content) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($faqs): ?>
                    <div class="service-offerings__faqs">
                        <h4 class="service-offerings__faq-title">Frequently asked questions</h4>
                        <div class="service-offerings__faq-list">
                            <?php foreach ($faqs as $index => $faq):
                                $faq_title = trim($faq['faq_title'] ?? '');
                                $faq_content = trim($faq['faq_content'] ?? '');
                                if (!$faq_title && !$faq_content) continue;
                                $faq_id = 'faq-' . $this->get_id() . '-' . $index;
                            ?>
                                <div class="service-offerings__faq-item">
                                    <button 
                                        class="service-offerings__faq-question" 
                                        aria-expanded="false"
                                        aria-controls="<?= esc_attr($faq_id) ?>"
                                        type="button"
                                    >
                                        <span><?= esc_html($faq_title) ?></span>
                                        <svg class="service-offerings__faq-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="11.5" stroke="currentColor"/>
                                            <path d="M12 7v10M7 12h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                    <div 
                                        class="service-offerings__faq-answer" 
                                        id="<?= esc_attr($faq_id) ?>"
                                        aria-hidden="true"
                                    >
                                        <div class="service-offerings__faq-content paragraph--small"><?= wp_kses_post($faq_content) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($button_text && $button_link): ?>
                    <div class="service-offerings__actions">
                        <a class="btn <?= esc_attr($btnClass) ?>" href="<?= esc_url($button_link) ?>"<?= $target . $rel ?>><?= esc_html($button_text) ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <?php
    }
}
