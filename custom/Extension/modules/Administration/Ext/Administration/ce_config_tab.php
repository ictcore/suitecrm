<?php
$admin_option_defs = array();

$admin_option_defs['Administration']['CE_ListView_Configuration'] = array(
    '',
    'Ictbroadcast Configuration for list-views',
    'Enable custom ictbroadcast feature for module list-views.',
    'index.php?module=CE_custom_ictbroadcast&action=ce_config_lv'
);

$admin_option_defs['Administration']['CE_Subpanel_Configuration'] = array(
    '',
    'ictbroadcast Integration',
    'custom ictbroadcast Integration.',
    'index.php?module=CE_custom_ictbroadcast&action=ce_config_sp'
);

$admin_group_header[] = array(
    'ICTBroadcast Configuration',
    '',
    false,
    $admin_option_defs,
    'ICTBroadcast Configuration And Integration'
);

