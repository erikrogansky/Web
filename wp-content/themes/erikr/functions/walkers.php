<?php
class Header_Menu_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $output .= '<a class="font-weight-700 link link--dark paragraph paragraph--small" href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
}

class Mobile_Menu_Walker extends Walker_Nav_Menu {
    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $output .= '<a class="font-weight-700 link link--dark paragraph" href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
}

class Social_Menu_Walker extends Walker_Nav_Menu {
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $icon = get_field('social_icon', $item);

        if ( $icon ) {
            $output .= '<a class="btn btn--icon social-icon" href="' . esc_url( $item->url ) . '" target="_blank" rel="noopener">';
            $output .= render_icon($icon, $item->title);
            $output .= '</a>';
        }
    }
}

class Footer_Menu_Walker extends Walker_Nav_Menu {
    private $first_done = false;

    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        if ($depth === 0 && $this->first_done) {
            $output .= '<span class="font-weight-700 paragraph paragraph--small not-selectable" aria-hidden="true">•</span>';
        }

        $output .= '<a class="font-weight-700 link link--dark paragraph paragraph--small" href="' . esc_url($item->url) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';

        if ($depth === 0 && !$this->first_done) {
            $this->first_done = true;
        }
    }
}

