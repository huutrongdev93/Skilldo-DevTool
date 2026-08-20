<?php

use TAXONOMY_NAMESPACE\TAXONOMY_CLASS_NAME;

add_action('init', [TAXONOMY_CLASS_NAME::class, 'register']);
