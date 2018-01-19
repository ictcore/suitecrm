<?php

global $sugar_version, $sugar_flavor;

$manifest = array(
    'acceptable_sugar_flavors' => array('CE','PRO'),
    'acceptable_sugar_versions' => array(
        'regex_matches' => array(
            0 => "6.5.*",
        ),
    ),
    'readme' => '',
    'key' => 'bc',
    'author' => 'adeel',
    'description' => 'Ictbroadcast campaign',
    'icon' => '',
    'is_uninstallable' => true,
    'name' => 'SuiteCRM Ictbroadcast',
    'published_date' => '2017-12-20 00:00:00',
    'type' => 'module',
    'version' => '1.0.0',
    'remove_tables' => 'prompt',
);

$installdefs = array(
    'id' => 'SuiteCRM_ictbroadcast',
    'beans' =>
    array(
        0 =>
        array(
            'module' => 'CE_custom_ictbroadcast',
            'class' => 'CE_custom_ictbroadcast',
            'path' => 'modules/CE_custom_ictbroadcast/CE_custom_ictbroadcast.php',
            'tab' => false,
        ),
    ),
    'image_dir' => '<basepath>/icons',
    'pre_execute' => array(
        0 => '<basepath>/scripts/pre_execute.php',
    ),
    'post_uninstall' => array(
        0 => '<basepath>/scripts/post_uninstall.php',
    ),
    'copy' =>
    array(
        0 =>
        array(
            'from' => '<basepath>/SugarModules/custom/Extension/modules/Administration/Ext/Administration/ce_config_tab.php',
            'to' => 'custom/Extension/modules/Administration/Ext/Administration/ce_config_tab.php',
        ),
        1 =>
        array(
            'from' => '<basepath>/SugarModules/custom/Extension/modules/Administration/Ext/Language/en_us.ce_config_tab.php',
            'to' => 'custom/Extension/modules/Administration/Ext/Language/en_us.ce_config_tab.php',
        ),
        2 =>
        array(
            'from' => '<basepath>/SugarModules/custom/Extension/application/Ext/EntryPointRegistry/customIctbroadcast_EntryPoint.php',
            'to' => 'custom/Extension/application/Ext/EntryPointRegistry/customIctbroadcast_EntryPoint.php',
        ),
        3 =>
        array(
            'from' => '<basepath>/SugarModules/custom/include/generic/SugarWidgets/SugarWidgetSubPanelTopIctbroadcastBtn.php',
            'to' => 'custom/include/generic/SugarWidgets/SugarWidgetSubPanelTopIctbroadcastBtn.php',
        ),
       
        4 =>
        array(
            'from' => '<basepath>/SugarModules/modules/CE_custom_ictbroadcast',
            'to' => 'modules/CE_custom_ictbroadcast',
        ),
        5 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/main_viewList/view.list.php',
            'to' => 'include/MVC/View/views/view.list.php',
        ),
        // copy custom view.list for Apply custom Ictbroadcast code.
        6 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Accounts/views/view.list.php',
            'to' => 'custom/modules/Accounts/views/view.list.php',
        ),
        7 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Cases/views/view.list.php',
            'to' => 'custom/modules/Cases/views/view.list.php',
        ),
        8 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Contacts/views/view.list.php',
            'to' => 'custom/modules/Contacts/views/view.list.php',
        ),
        9 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Leads/views/view.list.php',
            'to' => 'custom/modules/Leads/views/view.list.php',
        ),
        10 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Meetings/views/view.list.php',
            'to' => 'custom/modules/Meetings/views/view.list.php',
        ),
        11 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Opportunities/views/view.list.php',
            'to' => 'custom/modules/Opportunities/views/view.list.php',
        ),
        12 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Project/views/view.list.php',
            'to' => 'custom/modules/Project/views/view.list.php',
        ),
        13 =>
        array(
            'from' => '<basepath>/SugarModules/Diff_ViewFile/SuiteCRMListSmarty/custom/modules/Prospects/views/view.list.php',
            'to' => 'custom/modules/Prospects/views/view.list.php',
        ),
       
    ),
    'language' =>
    array(
        0 =>
        array(
            'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
            'to_module' => 'application',
            'language' => 'en_us',
        ),
    ),
);
