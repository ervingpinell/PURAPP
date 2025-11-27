<?php

return [
    'title' => 'Tax Management',
    'create' => 'Create Tax',
    'edit' => 'Edit Tax',
    'included' => 'Included',
    'not_included' => 'Not Included',
    'mixed' => 'Mixed',
    'fields' => [
        'name' => 'Name',
        'code' => 'Code',
        'rate' => 'Rate/Amount',
        'type' => 'Type',
        'apply_to' => 'Apply To',
        'is_inclusive' => 'Inclusive',
        'is_active' => 'Active',
        'sort_order' => 'Order',
    ],
    'types' => [
        'percentage' => 'Percentage (%)',
        'fixed' => 'Fixed Amount ($)',
    ],
    'apply_to_options' => [
        'subtotal' => 'Subtotal',
        'total' => 'Total (Cascading)',
        'per_person' => 'Per Person',
    ],
    'messages' => [
        'created' => 'Tax created successfully.',
        'updated' => 'Tax updated successfully.',
        'deleted' => 'Tax deleted successfully.',
        'toggled' => 'Tax status updated.',
        'select_taxes' => 'Select the taxes that apply to this tour.',
    ],
    'breakdown' => [
        'title' => 'Price Breakdown',
        'subtotal' => 'Subtotal',
        'tax' => 'Tax',
        'total' => 'Total',
    ],
];
