<?php

if (is_plugin_active('real-estate')) {
    require_once __DIR__ . '/property-disclaimer.php';

    register_widget(PropertyDisclaimerWidget::class);
}
