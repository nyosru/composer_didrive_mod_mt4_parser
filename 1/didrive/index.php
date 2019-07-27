<?php

/**
 * добавление дампа данных из мт4
 */
if (isset($_POST['save']) && isset($_FILES['file_d']) && isset($_FILES['file_d']['tmp_name']) && isset($_FILES['file_d']['error']) && $_FILES['file_d']['error'] == 0 && isset($_FILES['file_d']['size']) && $_FILES['file_d']['size'] > 50
) {

    //\f\pa($_FILES['file_d']);

    if (!is_dir(DR . dir_site_sd . 'datain/'))
        mkdir(DR . dir_site_sd . 'datain/', 0755);

    $new_file = date('Y-m-d_H_i_s', $_SERVER['REQUEST_TIME']) . '.dump.mt4.htm';
    move_uploaded_file( $_FILES['file_d']['tmp_name'] , DR . dir_site_sd . 'datain/' . $new_file );
    $vv['warn'] .= ( isset($vv['warn']{3}) ? '<br/>' : '' ) . ' Файл загрузили ';
    
    $e = \Nyos\Mod\Mt4::parse_mt4_statement( DR . dir_site_sd . 'datain/' . $new_file );
    // \f\pa($e);
    
    \Nyos\Mod\Mt4::addNewData( $db, $vv['folder'], $e );
    $vv['warn'] .= ( isset($vv['warn']{3}) ? '<br/>' : '' ) . ' Сделки записаны: '.sizeof($e).' шт' ;

    $e = \Nyos\Mod\Mt4::getInfo($db, $vv['folder']);
    //\f\pa($e);
    
}

$vv['krohi'] = [];
$vv['krohi'][1] = array(
    'text' => $vv['now_level']['name'],
    'uri' => $vv['now_level']['cfg.level']
);

//echo '$vv[\'tpl_body\'] = \f\like_tpl(\'body\', '. dir_mods_mod_vers_didrive_tpl .' , '. dir_site_module_nowlev_tpldidr .' , DR); ';
//echo '<br/>';
$vv['tpl_body'] = \f\like_tpl('body.htm', dir_mods_mod_vers_didrive_tpl, dir_site_module_nowlev_tpldidr, DR);
//$vv['tpl_body'] = dir_mods_mod_vers_didrive_tpl.'body.htm';
