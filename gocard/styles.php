<?php
header("Content-type: text/css");
require 'config.php';
$colors = read_json('data/colors.json');
$css = '';
foreach ($colors as $color) {
    switch ($color['element']) {
        case 'body-bg-dark':
            $css .= "body.dark-mode { background: {$color['color_value']}; }";
            break;
        case 'body-bg-light':
            $css .= "body.light-mode { background: {$color['color_value']}; }";
            break;
        case 'accent-color':
            $css .= "
                .menu-trigger, .nav-menu li a, .job-title, h2, h3, .back-link, .map-link, .social-links a, .contact-links a {
                    color: {$color['color_value']};
                }
                .nav-menu li a::after, .view-profile-btn, .card-form button, .profile-form button, .tab-btn, .tab-btn.active, .accordion-toggle {
                    background: {$color['color_value']};
                }
                .profile-img, .staff-photo { border-color: {$color['color_value']}; }
                .tab-btn.active, .tab-btn:hover { border-bottom-color: {$color['color_value']}; }
            ";
            break;
        case 'text-color-dark':
            $css .= "body.dark-mode { color: {$color['color_value']}; }";
            break;
        case 'text-color-light':
            $css .= "body.light-mode { color: {$color['color_value']}; }";
            break;
    }
}
echo $css;
?>