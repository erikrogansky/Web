<?php
namespace ER_Elements\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if (!defined('ABSPATH')) exit;

class Contact_Form extends Widget_Base {
    public function get_name() { return 'er_contact_form'; }
    public function get_title() { return 'Contact Form'; }
    public function get_icon() { return 'eicon-form-horizontal'; }
    public function get_categories() { return ['er-elements']; }

    protected function register_controls() {
        
        /* -----------------------------
         * Section: Contact Info
         * ----------------------------- */
        $this->start_controls_section('contact_info_section', ['label' => 'Contact Information']);

        $this->add_control('info_title', [
            'label' => 'Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => "Let's Work Together",
            'default' => "Let's Work Together",
        ]);

        $this->add_control('info_subtitle', [
            'label' => 'Subtitle',
            'type' => Controls_Manager::TEXTAREA,
            'label_block' => true,
            'placeholder' => 'Have a project in mind? Drop me a message and let us create something amazing together.',
            'default' => 'Have a project in mind? Drop me a message and let us create something amazing together.',
        ]);

        $this->add_control('info_availability', [
            'label' => 'Availability Status',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Available for freelance work',
            'default' => 'Available for freelance work',
        ]);

        $this->add_control('response_time', [
            'label' => 'Response Time',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Usually responds within 24 hours',
            'default' => 'Usually responds within 24 hours',
        ]);

        $this->add_control('info_email', [
            'label' => 'Email',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'your@email.com',
        ]);

        $this->add_control('info_phone', [
            'label' => 'Phone',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => '+1 (555) 123-4567',
        ]);

        $this->add_control('info_location', [
            'label' => 'Location',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'City, Country',
        ]);

        $this->add_control('business_name', [
            'label' => 'Business/Company Name',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Your Name / Company Name',
        ]);

        $this->add_control('registration_number', [
            'label' => 'Registration Number',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., 12345678',
        ]);

        $this->add_control('vat_number', [
            'label' => 'VAT Number',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., SK1234567890',
        ]);

        $this->add_control('tax_number', [
            'label' => 'Tax Number',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., 1234567890',
        ]);

        $this->add_control('registered_at', [
            'label' => 'Registered At',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., Trade Register of XYZ',
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: Form Settings
         * ----------------------------- */
        $this->start_controls_section('form_settings_section', ['label' => 'Form Settings']);

        $this->add_control('form_title', [
            'label' => 'Form Title',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Send me a message',
            'default' => 'Send me a message',
        ]);

        $reasons = new Repeater();
        $reasons->add_control('reason_label', [
            'label' => 'Reason Label',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., Project Inquiry, Collaboration, Question...',
        ]);
        $reasons->add_control('reason_value', [
            'label' => 'Reason Value',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'e.g., project, collaboration, question...',
            'description' => 'Used in the email. Should be lowercase.',
        ]);

        $this->add_control('contact_reasons', [
            'label' => 'Contact Reasons',
            'type' => Controls_Manager::REPEATER,
            'fields' => $reasons->get_controls(),
            'title_field' => '{{{ reason_label }}}',
            'default' => [
                ['reason_label' => 'Project Inquiry', 'reason_value' => 'project'],
                ['reason_label' => 'Job Opportunity', 'reason_value' => 'job'],
                ['reason_label' => 'Collaboration', 'reason_value' => 'collaboration'],
                ['reason_label' => 'General Question', 'reason_value' => 'question'],
            ],
        ]);

        $this->add_control('recipient_email', [
            'label' => 'Recipient Email',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'your@email.com',
            'description' => 'Email address where form submissions will be sent.',
        ]);

        $this->end_controls_section();

        /* -----------------------------
         * Section: reCAPTCHA Settings
         * ----------------------------- */
        $this->start_controls_section('recaptcha_section', ['label' => 'reCAPTCHA Enterprise Settings']);

        $this->add_control('recaptcha_site_key', [
            'label' => 'reCAPTCHA Site Key',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => '6Ldr9SIsAAAAAPA8Ta7KJSoz2gbyPMAtICUqn81q',
            'description' => 'Your reCAPTCHA Enterprise site key',
        ]);

        $this->add_control('recaptcha_project_id', [
            'label' => 'Google Cloud Project ID',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'my-website-recap-1725601492278',
            'description' => 'Your Google Cloud project ID',
        ]);

        $this->add_control('recaptcha_api_key', [
            'label' => 'Google Cloud API Key',
            'type' => Controls_Manager::TEXT,
            'label_block' => true,
            'placeholder' => 'Your API key from Google Cloud Console',
            'description' => 'API key with reCAPTCHA Enterprise API enabled',
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();
        
        $info_title = esc_html($s['info_title'] ?? '');
        $info_subtitle = esc_html($s['info_subtitle'] ?? '');
        $info_availability = esc_html($s['info_availability'] ?? '');
        $response_time = esc_html($s['response_time'] ?? '');
        $info_email = esc_html($s['info_email'] ?? '');
        $info_phone = esc_html($s['info_phone'] ?? '');
        $info_location = esc_html($s['info_location'] ?? '');
        $business_name = esc_html($s['business_name'] ?? '');
        $registration_number = esc_html($s['registration_number'] ?? '');
        $vat_number = esc_html($s['vat_number'] ?? '');
        $tax_number = esc_html($s['tax_number'] ?? '');
        $registered_at = esc_html($s['registered_at'] ?? '');
        $form_title = esc_html($s['form_title'] ?? '');
        $reasons = is_array($s['contact_reasons'] ?? null) ? $s['contact_reasons'] : [];
        $site_key = esc_attr($s['recaptcha_site_key'] ?? '');
        
        $widget_id = $this->get_id();
        ?>
        <section class="contact-form-block" id="contact-form-<?= $widget_id ?>">
            <div class="contact-form-block__inner">
                
                <!-- Right Side: Contact Info -->
                <div class="contact-form-block__info">
                    <?php if ($info_title): ?>
                        <h2 class="contact-form-block__info-title"><?= $info_title ?></h2>
                    <?php endif; ?>
                    
                    <?php if ($info_subtitle): ?>
                        <p class="contact-form-block__info-subtitle"><?= $info_subtitle ?></p>
                    <?php endif; ?>
                    
                    <?php if ($info_availability): ?>
                        <div class="contact-form-block__availability">
                            <div class="availability-badge">
                                <svg class="availability-badge__dot" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="currentColor">
                                    <circle cx="6" cy="6" r="6"/>
                                </svg>
                                <span class="availability-badge__text"><?= $info_availability ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <h3 class="contact-form-block__section-title">Get in Touch</h3>
                    
                    <div class="contact-form-block__info-items">
                        <?php if ($info_email): ?>
                            <div class="contact-info-item">
                                <svg class="contact-info-item__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                                    <path d="m2 7 8.97 5.7a1.94 1.94 0 0 0 2.06 0L22 7"/>
                                </svg>
                                <a href="mailto:<?= $info_email ?>" class="contact-info-item__text">
                                    <?= $info_email ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($info_phone): ?>
                            <div class="contact-info-item">
                                <svg class="contact-info-item__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <a href="tel:<?= str_replace(' ', '', $info_phone) ?>" class="contact-info-item__text">
                                    <?= $info_phone ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($info_location): ?>
                            <div class="contact-info-item">
                                <svg class="contact-info-item__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span class="contact-info-item__text"><?= $info_location ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($business_name || $registration_number || $vat_number || $tax_number || $registered_at): ?>
                        <div class="business-info">
                            <h3 class="business-info__title">Legal Information</h3>
                            <div class="business-info__grid">
                                <?php if ($business_name): ?>
                                    <div class="business-info__item">
                                        <span class="business-info__label">Business Name</span>
                                        <span class="business-info__value"><?= $business_name ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($registration_number): ?>
                                    <div class="business-info__item">
                                        <span class="business-info__label">Reg. Number</span>
                                        <span class="business-info__value"><?= $registration_number ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($vat_number): ?>
                                    <div class="business-info__item">
                                        <span class="business-info__label">VAT Number</span>
                                        <span class="business-info__value"><?= $vat_number ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($tax_number): ?>
                                    <div class="business-info__item">
                                        <span class="business-info__label">Tax Number</span>
                                        <span class="business-info__value"><?= $tax_number ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($registered_at): ?>
                                    <div class="business-info__item business-info__item--full">
                                        <span class="business-info__label">Registered At</span>
                                        <span class="business-info__value"><?= $registered_at ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Left Side: Contact Form -->
                <div class="contact-form-block__form-wrapper">
                    <?php if ($form_title): ?>
                        <h3 class="contact-form-block__form-title"><?= $form_title ?></h3>
                    <?php endif; ?>
                    
                    <form class="contact-form" data-widget-id="<?= $widget_id ?>">
                        <div class="contact-form__messages"></div>
                        
                        <!-- Reason Selector -->
                        <div class="contact-form__field">
                            <label for="contact-reason-<?= $widget_id ?>" class="contact-form__label">
                                I'm contacting about
                            </label>
                            
                            <!-- Hidden Native Select -->
                            <select id="contact-reason-<?= $widget_id ?>" name="reason" class="contact-form__select-native" required style="display: none;">
                                <?php foreach ($reasons as $reason): ?>
                                    <option value="<?= esc_attr($reason['reason_value'] ?? '') ?>">
                                        <?= esc_html($reason['reason_label'] ?? '') ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="other">Other</option>
                            </select>
                            
                            <!-- Custom Dropdown -->
                            <div class="custom-select" data-select-id="contact-reason-<?= $widget_id ?>">
                                <div class="custom-select__trigger">
                                    <span class="custom-select__value"><?= esc_html($reasons[0]['reason_label'] ?? 'Other') ?></span>
                                    <svg class="custom-select__arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div class="custom-select__options">
                                    <?php foreach ($reasons as $index => $reason): ?>
                                        <div class="custom-select__option <?= $index === 0 ? 'is-selected' : '' ?>" data-value="<?= esc_attr($reason['reason_value'] ?? '') ?>">
                                            <?= esc_html($reason['reason_label'] ?? '') ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="custom-select__option" data-value="other">Other</div>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="contact-form__field">
                            <label for="contact-name-<?= $widget_id ?>" class="contact-form__label">
                                Name <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="contact-name-<?= $widget_id ?>" 
                                name="name" 
                                class="contact-form__input" 
                                required
                                autocomplete="name"
                            >
                        </div>

                        <!-- Email -->
                        <div class="contact-form__field">
                            <label for="contact-email-<?= $widget_id ?>" class="contact-form__label">
                                Email <span class="required">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="contact-email-<?= $widget_id ?>" 
                                name="email" 
                                class="contact-form__input" 
                                required
                                autocomplete="email"
                            >
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="contact-form__field">
                            <label for="contact-phone-<?= $widget_id ?>" class="contact-form__label">
                                Phone <span class="optional">(optional)</span>
                            </label>
                            <input 
                                type="tel" 
                                id="contact-phone-<?= $widget_id ?>" 
                                name="phone" 
                                class="contact-form__input"
                                autocomplete="tel"
                            >
                        </div>

                        <!-- Message -->
                        <div class="contact-form__field">
                            <label for="contact-message-<?= $widget_id ?>" class="contact-form__label">
                                Message <span class="required">*</span>
                            </label>
                            <textarea 
                                id="contact-message-<?= $widget_id ?>" 
                                name="message" 
                                class="contact-form__textarea" 
                                rows="5" 
                                required
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn--primary contact-form__submit">
                            <span class="contact-form__submit-text">Send Message</span>
                            <span class="contact-form__submit-loader"></span>
                        </button>

                        <!-- reCAPTCHA Badge Info -->
                        <?php if ($site_key): ?>
                            <div class="contact-form__recaptcha-notice">
                                This site is protected by reCAPTCHA and the Google
                                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy Policy</a> and
                                <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a> apply.
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

            </div>
        </section>

        <?php if ($site_key): ?>
            <script src="https://www.google.com/recaptcha/enterprise.js?render=<?= $site_key ?>"></script>
        <?php endif; ?>
        <?php
    }
}
