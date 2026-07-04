<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Testimonials extends Widget_Base {
    public function get_name() { return 'er_testimonials'; }
    public function get_title() { return 'Testimonials'; }
    public function get_icon() { return 'eicon-testimonial'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('general_section', ['label' => 'General']);

        $this->add_control('title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'What People Say',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('testimonials_section', ['label' => 'Testimonials']);

        $testimonial = new Repeater();

        $testimonial->add_control('quote', [
            'label' => 'Quote',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 6,
            'placeholder' => 'A testimonial or quote about your skills and work ethic.',
        ]);

        $testimonial->add_control('person_name', [
            'label' => 'Name',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Jane Doe',
        ]);

        $testimonial->add_control('person_role', [
            'label' => 'Role / Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Product Manager',
        ]);

        $testimonial->add_control('person_org', [
            'label' => 'Organization / Context',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Company, university, or project context',
        ]);

        $testimonial->add_control('avatar', [
            'label' => 'Avatar',
            'type' => Controls_Manager::MEDIA,
        ]);

        $testimonial->add_control('profile_link', [
            'label' => 'Profile / Context Link',
            'type' => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'options' => ['url', 'nofollow'],
            'placeholder' => 'https://...',
        ]);

        $this->add_control('testimonials', [
            'label' => 'Testimonials',
            'type' => Controls_Manager::REPEATER,
            'fields' => $testimonial->get_controls(),
            'title_field' => '{{{ person_name || "(testimonial)" }}}',
            'default' => [],
        ]);

        $this->end_controls_section();
    }

    private function has_testimonial_content(array $testimonial): bool {
        return (bool) (
            trim($testimonial['quote'] ?? '') ||
            trim($testimonial['person_name'] ?? '') ||
            trim($testimonial['person_role'] ?? '') ||
            trim($testimonial['person_org'] ?? '') ||
            !empty($testimonial['avatar']['url'] ?? '')
        );
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $title = trim($s['title'] ?? '');
        $testimonials = [];
        $raw_testimonials = is_array($s['testimonials'] ?? null) ? $s['testimonials'] : [];

        foreach ($raw_testimonials as $testimonial) {
            if ($this->has_testimonial_content($testimonial)) {
                $testimonials[] = $testimonial;
            }
        }

        if (!$title && empty($testimonials)) return;

        $is_multiple = count($testimonials) > 1;
        $section_id = $title ? sanitize_title($title) : '';
        $block_classes = 'testimonials-block' . ($is_multiple ? ' testimonials-block--multiple' : ' testimonials-block--single');
        ?>
        <section class="<?= esc_attr($block_classes) ?>"<?= $section_id ? ' id="' . esc_attr($section_id) . '"' : '' ?> data-testimonials-block>
            <?php if ($title): ?>
                <div class="testimonials-block__header">
                    <h2 class="testimonials-block__title"><?= esc_html($title) ?></h2>
                </div>
            <?php endif; ?>

            <?php if ($testimonials): ?>
                <div class="testimonials-block__viewport">
                    <div class="testimonials-block__rail" data-testimonials-rail aria-label="Testimonials">
                        <?php foreach ($testimonials as $testimonial):
                            $quote = trim($testimonial['quote'] ?? '');
                            $name = trim($testimonial['person_name'] ?? '');
                            $role = trim($testimonial['person_role'] ?? '');
                            $org = trim($testimonial['person_org'] ?? '');
                            $avatar_url = $testimonial['avatar']['url'] ?? '';
                            $profile_url = $testimonial['profile_link']['url'] ?? '';
                            $nofollow = !empty($testimonial['profile_link']['nofollow']);
                            $rel = trim('noopener ' . ($nofollow ? 'nofollow' : ''));
                            $rel = $rel ? ' rel="' . esc_attr($rel) . '"' : '';
                        ?>
                            <figure class="testimonials-block__item" data-testimonial-item>
                                <?php if ($quote): ?>
                                    <blockquote class="testimonials-block__quote">
                                        <p><?= nl2br(esc_html($quote)) ?></p>
                                    </blockquote>
                                <?php endif; ?>

                                <?php if ($name || $role || $org || $avatar_url): ?>
                                    <figcaption class="testimonials-block__author">
                                        <?php if ($avatar_url): ?>
                                            <img class="testimonials-block__avatar" src="<?= esc_url($avatar_url) ?>" alt="<?= esc_attr($name ?: 'Testimonial author') ?>" loading="lazy">
                                        <?php endif; ?>

                                        <span class="testimonials-block__author-text">
                                            <?php if ($name): ?>
                                                <?php if ($profile_url): ?>
                                                    <a class="testimonials-block__name" href="<?= esc_url($profile_url) ?>" target="_blank"<?= $rel ?>><?= esc_html($name) ?></a>
                                                <?php else: ?>
                                                    <span class="testimonials-block__name"><?= esc_html($name) ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if ($role || $org): ?>
                                                <span class="testimonials-block__meta">
                                                    <?= esc_html(trim($role . ($role && $org ? ' · ' : '') . $org)) ?>
                                                </span>
                                            <?php endif; ?>
                                        </span>
                                    </figcaption>
                                <?php endif; ?>
                            </figure>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($is_multiple): ?>
                    <div class="testimonials-block__controls" aria-label="Testimonial controls">
                        <button class="testimonials-block__control" type="button" aria-label="Previous testimonial" data-testimonials-prev>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.25 13.5L6.75 9L11.25 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <button class="testimonials-block__control" type="button" aria-label="Next testimonial" data-testimonials-next>
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
        <?php
    }
}
