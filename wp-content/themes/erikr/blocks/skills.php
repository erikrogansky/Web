<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Skills extends Widget_Base {
    public function get_name()        { return 'er_skills'; }
    public function get_title()       { return 'Skills'; }
    public function get_icon()        { return 'eicon-skill-bar'; }
    public function get_categories()  { return ['er-elements']; }

    protected function register_controls() {

        /* -----------------------------
         * Section: General
         * ----------------------------- */
        $this->start_controls_section('general_section', ['label' => 'General']);

        $this->add_control('block_title', [
            'label' => 'Block Title',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'My Skills',
            'default' => 'My Skills',
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Featured Categories
         * ----------------------------- */
        $this->start_controls_section('featured_section', ['label' => 'Featured Categories']);

        $this->add_control('show_featured', [
            'label' => 'Show Featured Category Cards',
            'type'  => Controls_Manager::SWITCHER,
            'label_on' => 'Yes',
            'label_off'=> 'No',
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('featured_categories', [
            'label' => 'Featured Categories (Pick 3)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., frontend, backend, design',
            'description' => 'Enter up to 3 category slugs separated by commas. These will show as cards above the filters.',
            'condition' => ['show_featured' => 'yes'],
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Categories
         * ----------------------------- */
        $this->start_controls_section('categories_section', ['label' => 'Categories']);

        $category = new Repeater();

        $category->add_control('category_short_name', [
            'label' => 'Short Name (for pills)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., Frontend, Backend, Tools...',
        ]);

        $category->add_control('category_long_name', [
            'label' => 'Long Name (for featured cards)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., UX/UI Design, Web Development...',
        ]);

        $category->add_control('category_slug', [
            'label' => 'Category Slug',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., frontend, backend, tools...',
            'description' => 'Used for filtering. Should be lowercase, no spaces.',
        ]);

        $category->add_control('category_description', [
            'label' => 'Description (for featured cards)',
            'type'  => Controls_Manager::TEXTAREA,
            'rows' => 3,
            'placeholder' => 'Brief description of this category...',
        ]);

        $category->add_control('category_link_text', [
            'label' => 'Link Text (for featured cards)',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., See examples, View projects...',
            'default' => 'See examples',
        ]);

        $this->add_control('categories', [
            'label' => 'Categories',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $category->get_controls(),
            'title_field' => '{{{ category_short_name || "(untitled category)" }}}',
            'default' => [],
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Skills
         * ----------------------------- */
        $this->start_controls_section('skills_section', ['label' => 'Skills']);

        $skill = new Repeater();

        $skill->add_control('skill_image', [
            'label' => 'Skill Image',
            'type'  => Controls_Manager::MEDIA,
            'default' => [
                'url' => '',
            ],
        ]);

        $skill->add_control('skill_name', [
            'label' => 'Skill Name',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., React, Node.js, Figma...',
        ]);

        $skill->add_control('skill_link', [
            'label' => 'Skill Link',
            'type'  => Controls_Manager::URL,
            'dynamic' => ['active' => true],
            'placeholder' => 'https://...',
            'description' => 'External link (displayed as icon next to title)',
        ]);

        $skill->add_control('skill_description', [
            'label' => 'Short Description',
            'type'  => Controls_Manager::TEXTAREA,
            'rows' => 3,
            'placeholder' => 'Brief description of your experience with this skill...',
        ]);

        $skill->add_control('skill_category', [
            'label' => 'Category',
            'type'  => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Category slug (e.g., frontend)',
            'description' => 'Must match a category slug from above. Leave empty for "Others".',
        ]);

        $this->add_control('skills', [
            'label' => 'Skills',
            'type'  => Controls_Manager::REPEATER,
            'fields'=> $skill->get_controls(),
            'title_field' => '{{{ skill_name || "(untitled skill)" }}}',
            'default' => [],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        $skills = is_array($s['skills'] ?? null) ? $s['skills'] : [];
        $categories = is_array($s['categories'] ?? null) ? $s['categories'] : [];
        $block_title = trim($s['block_title'] ?? '');
        $show_featured = ($s['show_featured'] ?? 'yes') === 'yes';
        $featured_slugs = array_map('trim', explode(',', trim($s['featured_categories'] ?? '')));
        $featured_slugs = array_filter($featured_slugs);

        // Build category map for quick lookup
        $category_map = [];
        foreach ($categories as $cat) {
            $slug = trim($cat['category_slug'] ?? '');
            if ($slug) {
                $category_map[$slug] = $cat;
            }
        }

        // Get featured categories (max 3)
        $featured_cats = [];
        if ($show_featured) {
            foreach (array_slice($featured_slugs, 0, 3) as $slug) {
                if (isset($category_map[$slug])) {
                    $featured_cats[] = $category_map[$slug];
                }
            }
        }

        // Generate unique ID for this block instance
        $block_id = 'skills-' . $this->get_id();
        
        // Generate section ID from block title
        $section_id = $block_title ? sanitize_title($block_title) : $block_id;
        
        ?>
        <section class="skills-block" id="<?= esc_attr($section_id) ?>" data-skills-block>
            <!-- Featured Category Cards -->
            <?php if (!empty($featured_cats)): ?>
                <div class="skills-block__featured">
                    <?php foreach ($featured_cats as $fcat):
                        $long_name = trim($fcat['category_long_name'] ?? '');
                        $description = trim($fcat['category_description'] ?? '');
                        $link_text = trim($fcat['category_link_text'] ?? 'See examples');
                        $slug = trim($fcat['category_slug'] ?? '');
                    ?>
                        <article class="skills-block__featured-card">
                            <h3 class="skills-block__featured-title"><?= esc_html($long_name) ?></h3>
                            <?php if ($description): ?>
                                <p class="skills-block__featured-description"><?= esc_html($description) ?></p>
                            <?php endif; ?>
                            <a 
                                href="#<?= esc_attr($block_id) ?>-filters" 
                                class="skills-block__featured-link"
                                data-category-trigger="<?= esc_attr($slug) ?>"
                            >
                                <?= esc_html($link_text) ?>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Block Title -->
            <?php if ($block_title): ?>
                <h2 class="skills-block__heading"><?= esc_html($block_title) ?></h2>
            <?php endif; ?>

            <!-- Filter Controls -->
            <div class="skills-block__controls" id="<?= esc_attr($block_id) ?>-filters" data-controls>
                <div class="skills-block__pills" data-pills-container>
                    <!-- "All" pill (always first) -->
                    <button class="skills-block__pill skills-block__pill--active" data-category="all">
                        All
                    </button>

                    <!-- Custom category pills -->
                    <?php foreach ($categories as $cat): 
                        $cat_short = trim($cat['category_short_name'] ?? '');
                        $cat_slug = trim($cat['category_slug'] ?? '');
                        if (!$cat_short || !$cat_slug) continue;
                    ?>
                        <button class="skills-block__pill" data-category="<?= esc_attr($cat_slug) ?>">
                            <?= esc_html($cat_short) ?>
                        </button>
                    <?php endforeach; ?>

                    <!-- "Others" pill (always last) -->
                    <button class="skills-block__pill" data-category="others">
                        Others
                    </button>
                </div>

                <div class="skills-block__search">
                    <input 
                        type="text" 
                        class="skills-block__search-input" 
                        placeholder="Search skills..." 
                        data-search-input
                        aria-label="Search skills"
                    />
                    <svg class="skills-block__search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 19L14.65 14.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

            <!-- Skills Grid -->
            <div class="skills-block__grid" data-skills-grid>
                <?php foreach ($skills as $i => $skill):
                    $image_url = $skill['skill_image']['url'] ?? '';
                    $name = trim($skill['skill_name'] ?? '');
                    $description = trim($skill['skill_description'] ?? '');
                    $link_url = $skill['skill_link']['url'] ?? '';
                    $is_external = !empty($skill['skill_link']['is_external']);
                    $nofollow = !empty($skill['skill_link']['nofollow']);
                    $category = strtolower(trim($skill['skill_category'] ?? ''));
                    
                    // Default to "others" if no category or invalid category
                    if (empty($category)) {
                        $category = 'others';
                    }

                    $target = $is_external ? ' target="_blank"' : '';
                    $rel_str = trim(($is_external ? 'noopener' : '') . ' ' . ($nofollow ? 'nofollow' : ''));
                    $rel = $rel_str ? ' rel="' . esc_attr($rel_str) . '"' : '';

                    $is_initially_visible = ($i < 6); // Show first 6 initially
                    $hidden_class = $is_initially_visible ? '' : ' skills-block__card--hidden';
                ?>
                    <article 
                        class="skills-block__card<?= $hidden_class ?>" 
                        data-skill-card 
                        data-category="<?= esc_attr($category) ?>"
                        data-name="<?= esc_attr(strtolower($name)) ?>"
                    >
                        <?php if ($image_url): ?>
                            <div class="skills-block__card-image">
                                <img src="<?= esc_url($image_url) ?>" alt="<?= esc_attr($name) ?>" loading="lazy" />
                            </div>
                        <?php endif; ?>

                        <div class="skills-block__card-content">
                            <div class="skills-block__card-header">
                                <?php if ($name): ?>
                                    <h3 class="skills-block__card-title"><?= esc_html($name) ?></h3>
                                <?php endif; ?>

                                <?php if ($link_url): ?>
                                    <a 
                                        href="<?= esc_url($link_url) ?>" 
                                        class="skills-block__card-link"
                                        aria-label="Visit <?= esc_attr($name) ?> website"
                                        <?= $target . $rel ?>
                                    >
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 8.66667V12.6667C12 13.0203 11.8595 13.3594 11.6095 13.6095C11.3594 13.8595 11.0203 14 10.6667 14H3.33333C2.97971 14 2.64057 13.8595 2.39052 13.6095C2.14048 13.3594 2 13.0203 2 12.6667V5.33333C2 4.97971 2.14048 4.64057 2.39052 4.39052C2.64057 4.14048 2.97971 4 3.33333 4H7.33333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 2H14V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M6.66667 9.33333L14 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($description): ?>
                                <p class="skills-block__card-description"><?= esc_html($description) ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- Show More Button -->
            <?php if (count($skills) > 6): ?>
                <div class="skills-block__footer">
                    <button class="skills-block__show-more" data-show-more>
                        <span data-show-more-text>Show more</span>
                        <svg class="skills-block__show-more-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>

            <!-- No Results Message -->
            <div class="skills-block__no-results" data-no-results style="display: none;">
                <p>No skills found matching your criteria.</p>
            </div>
        </section>
        <?php
    }
}
