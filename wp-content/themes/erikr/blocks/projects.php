<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Projects extends Widget_Base {
    public function get_name() { return 'er_projects'; }
    public function get_title() { return 'Projects'; }
    public function get_icon() { return 'eicon-folder-o'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        $this->start_controls_section('general_section', ['label' => 'General']);

        $this->add_control('title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Projects That Connect the Toolkit',
        ]);

        $this->add_control('intro', [
            'label' => 'Intro',
            'type' => Controls_Manager::WYSIWYG,
        ]);

        $this->end_controls_section();

        $this->start_controls_section('projects_section', ['label' => 'Projects']);

        $project = new Repeater();

        $project->add_control('project_title', [
            'label' => 'Project Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Project name',
        ]);

        $project->add_control('project_comment', [
            'label' => 'Short Story Comment',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 4,
            'placeholder' => 'A concise note about what this project is and why it belongs here.',
        ]);

        $project->add_control('project_story_note', [
            'label' => 'Story Note',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 2,
            'placeholder' => 'What it taught me, proved, or connected in my story.',
        ]);

        $project->add_control('project_image', [
            'label' => 'Image / Preview',
            'type' => Controls_Manager::MEDIA,
        ]);

        $project->add_control('project_context', [
            'label' => 'Context Badge',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'University, Personal, Client, Research...',
        ]);

        $project->add_control('project_year', [
            'label' => 'Year',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => '2026',
        ]);

        $project->add_control('project_status', [
            'label' => 'Status',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'In progress, Shipped, Prototype...',
        ]);

        $project->add_control('project_chips', [
            'label' => 'Mixed Chips',
            'type' => Controls_Manager::TEXTAREA,
            'rows' => 3,
            'placeholder' => "UX Design\nVue.js\nUniversity",
            'description' => 'Enter chips separated by commas or new lines.',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $project->add_control("link_{$i}_text", [
                'label' => "Link {$i} Text",
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => $i === 1 ? 'Live project' : 'GitHub / Case study / Paper',
                'separator' => $i === 1 ? 'before' : 'none',
            ]);

            $project->add_control("link_{$i}_url", [
                'label' => "Link {$i} URL",
                'type' => Controls_Manager::URL,
                'dynamic' => ['active' => true],
                'options' => ['url', 'nofollow'],
                'placeholder' => home_url('/'),
            ]);
        }

        $this->add_control('projects', [
            'label' => 'Projects',
            'type' => Controls_Manager::REPEATER,
            'fields' => $project->get_controls(),
            'title_field' => '{{{ project_title || "(project)" }}}',
            'default' => [],
        ]);

        $this->end_controls_section();
    }

    private function get_chips(string $raw): array {
        $chips = preg_split('/[\r\n,]+/', $raw);
        $chips = array_map('trim', $chips ?: []);
        $chips = array_filter($chips);

        return array_values(array_unique($chips));
    }

    private function get_links(array $project): array {
        $links = [];

        for ($i = 1; $i <= 3; $i++) {
            $text = trim($project["link_{$i}_text"] ?? '');
            $url = $project["link_{$i}_url"]['url'] ?? '';

            if (!$url) continue;

            $links[] = [
                'text' => $text ?: 'Open project',
                'url' => $url,
                'nofollow' => !empty($project["link_{$i}_url"]['nofollow']),
            ];
        }

        return $links;
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        $title = trim($s['title'] ?? '');
        $intro = trim($s['intro'] ?? '');
        $projects = is_array($s['projects'] ?? null) ? $s['projects'] : [];

        $has_projects = false;
        foreach ($projects as $project) {
            if (trim($project['project_title'] ?? '')) { $has_projects = true; break; }
        }

        if (!$title && !$intro && !$has_projects) return;

        $section_id = $title ? sanitize_title($title) : '';
        ?>
        <section class="projects-block"<?= $section_id ? ' id="' . esc_attr($section_id) . '"' : '' ?>>
            <?php if ($title || $intro): ?>
                <div class="projects-block__header">
                    <?php if ($title): ?>
                        <h2 class="projects-block__title"><?= esc_html($title) ?></h2>
                    <?php endif; ?>

                    <?php if ($intro): ?>
                        <div class="projects-block__intro paragraph"><?= wp_kses_post($intro) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($has_projects): ?>
                <div class="projects-block__list">
                    <?php foreach ($projects as $index => $project):
                        $project_title = trim($project['project_title'] ?? '');
                        if (!$project_title) continue;

                        $comment = trim($project['project_comment'] ?? '');
                        $story_note = trim($project['project_story_note'] ?? '');
                        $image = $project['project_image'] ?? [];
                        $image_url = $image['url'] ?? '';
                        $context = trim($project['project_context'] ?? '');
                        $year = trim($project['project_year'] ?? '');
                        $status = trim($project['project_status'] ?? '');
                        $chips = $this->get_chips($project['project_chips'] ?? '');
                        $links = $this->get_links($project);
                        $row_classes = 'projects-block__item';
                        if (!$image_url) {
                            $row_classes .= ' projects-block__item--text-only';
                        }
                        if ($index % 2 === 1) {
                            $row_classes .= ' projects-block__item--alternate';
                        }
                    ?>
                        <article class="<?= esc_attr($row_classes) ?>">
                            <div class="projects-block__content">
                                <div class="projects-block__meta">
                                    <?php if ($context): ?>
                                        <span class="projects-block__context"><?= esc_html($context) ?></span>
                                    <?php endif; ?>
                                    <?php if ($year): ?>
                                        <span><?= esc_html($year) ?></span>
                                    <?php endif; ?>
                                    <?php if ($status): ?>
                                        <span><?= esc_html($status) ?></span>
                                    <?php endif; ?>
                                </div>

                                <h3 class="projects-block__project-title"><?= esc_html($project_title) ?></h3>

                                <?php if ($comment): ?>
                                    <p class="projects-block__comment"><?= nl2br(esc_html($comment)) ?></p>
                                <?php endif; ?>

                                <?php if ($story_note): ?>
                                    <p class="projects-block__story-note"><?= nl2br(esc_html($story_note)) ?></p>
                                <?php endif; ?>

                                <?php if ($chips): ?>
                                    <ul class="projects-block__chips" aria-label="<?= esc_attr($project_title . ' tags') ?>">
                                        <?php foreach ($chips as $chip): ?>
                                            <li><?= esc_html($chip) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if ($links): ?>
                                    <div class="projects-block__links">
                                        <?php foreach ($links as $link_index => $link):
                                            $target = ' target="_blank"';
                                            $rel = trim('noopener ' . ($link['nofollow'] ? 'nofollow' : ''));
                                            $rel = $rel ? ' rel="' . esc_attr($rel) . '"' : '';
                                            $btn_class = $link_index === 0 ? 'btn--primary' : 'btn--outline-primary';
                                        ?>
                                            <a class="btn btn--small <?= esc_attr($btn_class) ?>" href="<?= esc_url($link['url']) ?>"<?= $target . $rel ?>>
                                                <?= esc_html($link['text']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($image_url): ?>
                                <div class="projects-block__media">
                                    <img src="<?= esc_url($image_url) ?>" alt="<?= esc_attr($project_title) ?>" loading="lazy">
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}
