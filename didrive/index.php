<?php


/**
 * добавление записи
 */
if (isset($_POST['addnew']{1})) {

    try {

        Nyos\mod\items::addNew( $db, $vv['folder'], $vv['now_level'], $_POST, $_FILES );
        $vv['warn'] .= ( isset($vv['warn']{3}) ? '<br/>' : '' ) . 'Запись добавлена';

    } catch (Exception $e) {

        $vv['warn'] .= ( isset($vv['warn']{3}) ? '<br/>' : '' ) . 'Произошла неописуемая ситуация #' . $e->getCode() . '.' . $e->getLine() . ' (ошибка: ' . $e->getMessage() . ' )';

    }
    
}

$vv['krohi'] = [];
$vv['krohi'][1] = array(
    'text' => $vv['now_level']['name'],
    'uri' => $vv['now_level']['cfg.level']
);


$vv['tpl_body'] = \f\like_tpl('body', dir_mods_mod_vers_didrive_tpl,  dir_site_module_nowlev_tpldidr, DR );
