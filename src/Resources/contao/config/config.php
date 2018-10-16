<?php

/**
 * WBGym
 *
 * Copyright (C) 2008-2013 Webteam Weinberg-Gymnasium Kleinmachnow
 *
 * @package 	WGBym
 * @author 		Marvin Ritter <marvin.ritter@gmail.com>
 * @license 	http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/*
 * Back end modules
 */

$GLOBALS['BE_MOD']['wbgym']['christmasmarket'] = array('tables'	=> array('tl_christmasmarket'));

/*
 * Front end modules
 */
$GLOBALS['FE_MOD']['wbgym']['wb_christmasmarket']	= 'WBGym\ModuleChristmasmarket';

?>
