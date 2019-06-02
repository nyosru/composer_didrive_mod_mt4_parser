<?php

$vv['mt4parse'] = \Nyos\Mod\Mt4::getInfo($db,$vv['folder']);

$vv['tpl_body'] = \f\like_tpl('body', dir_mods_mod_vers_tpl , dir_site_module_nowlev_tpl , DR);