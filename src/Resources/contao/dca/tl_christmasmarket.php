<?php

/**
 * WBGym
 * 
 * Copyright (C) 2015 Webteam Weinberg-Gymnasium Kleinmachnow
 * 
 * @package     WGBym
 * @author      Johannes Cram <j-cram@gmx.de>
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Table tl_christmasmarket
 */
$GLOBALS['TL_DCA']['tl_christmasmarket'] = array(
	// Config
	'config' => array(
		'dataContainer'		=> 'Table',
		'enableVersioning'	=> true,
		'sql' 				=> array(
			'keys' => array(
				'id' => 'primary',
				'course,approved' => 'index',
			)
		)
	),

	// List
	'list' => array(
		'sorting' => array(
			'mode'		=> 1,
			'flag'		=>1,
			'fields'	=> array('course','wishnum'),
			'panelLayout' => 'filter,sort;search,limit',
	),
	'label' => array(
		'fields'		=> array('name'),
		'showColumns'	=> false,
		'label_callback' 	=> array('tl_christmasmarket', 'generateListLabels')
	),
	'global_operations' => array(
			'all' => array(
				'label'		=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'		=> 'act=select',
				'class'		=> 'header_edit_all',
				'attributes'=> 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array(
			'edit' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['edit'],
				'href'		=> 'act=edit',
				'icon'		=> 'edit.gif'
			),
			'copy' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['copy'],
				'href'		=> 'act=copy',
				'icon'		=> 'copy.gif'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_christmasmarket']['approved'],
				'icon'                => 'invisible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_christmasmarket', 'toggleIcon')
			),
			'delete' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['delete'],
				'href'		=> 'act=delete',
				'icon'		=> 'delete.gif',
				'attributes'=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array(
				'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['show'],
				'href'		=> 'act=show',
				'icon'		=> 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array(
		'__selector__' => array(''),
		'default' => '{name_header},name,wishnum;{class_header},course,email;{description_header},description;{approved_header},approved,'
	),

	// Fields
	'fields' => array(
		'id' => array(
			'sql'		=> "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array(
			'sql'		=> "int(10) unsigned NOT NULL default '0'"
		),
		'name' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['name'],
			'exclude'	=> false,
			'inputType'	=> 'text',
			'search'	=> true,
			'sorting'	=> true,
			'eval'		=> array('mandatory' => true, 'tl_class' =>'w50'),
			'sql'		=> "varchar(64) NOT NULL default ''"
		),
		'course' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['course'],
			'exclude'	=> false,
			'inputType'	=> 'select',
			'foreignKey'=> 'tl_courses.title',
			'options_callback' => array('WBGym\WBGym', 'courseList'),
			'sorting'		=> true,
			'search'		=> true,
			'filter'		=> true,
			'eval'		=> array('mandatory' => true, 'chosen' => true,'tl_class' => 'w50'),
			'sql'		=> "int(11) NOT NULL default '0'"
		),
		'wishnum' => array(
			'label'				=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['wishnum'],
			'exclude'			=> false,
			'inputType'		=> 'radio',
			'filter'				=> true,
			'options'			=> array('1','2'),
			'eval'				=> array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'				=> "int(10) NOT NULL default '0'",
			'search'			=> true
		),
		'description' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['description'],
			'exclude'	=> false,
			'inputType'	=> 'textarea',
			'eval'		=> array('mandatory' => false, 'rte' => 'tinyMCE'),
			'sql'		=> "varchar(255) NOT NULL"
		),
		'email' => array(
			'label'		=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['email'],
			'exclude'	=> false,
			'inputType'	=> 'text',
			'eval'		=> array('mandatory' => false, 'tl_class' => 'w50', 'rgxp' => 'email'),
			'sql'		=> "varchar(64) NOT NULL"
		),
		'approved' => array(
			'label'				=> &$GLOBALS['TL_LANG']['tl_christmasmarket']['approved'],
			'exclude'			=> false,
			'filter'				=> true,
			'sorting'			=> true,
			'inputType'			=> 'checkbox',
			'eval'				=> array('tl_class' => 'w50'),
			'sql'				=> "char(1) NOT NULL default ''",
			'search'			=> true
		)
	)
);

class tl_christmasmarket extends Backend
{

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.$row['approved'];

		if ($row['approved'])
		{
			$icon = 'visible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}
	
		/**
	 * Disable/enable a user group
	 *
	 * @param integer       $intId
	 * @param boolean       $blnVisible
	 * @param DataContainer $dc
	 */
	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{


		$objVersions = new Versions('tl_christmasmarket', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_christmasmarket']['fields']['approved']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_christmasmarket']['fields']['approved']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}

		$time = time();

		// Update the database
		$this->Database->prepare("UPDATE tl_christmasmarket SET tstamp=$time, approved='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();
		$this->log('A new version of record "tl_christmasmarket.id='.$intId.'" has been created'.$this->getParentEntries('tl_christmasmarket', $intId), __METHOD__, TL_GENERAL);


	}
	
	public function generateListLabels($row, $label, DataContainer $dc, $args) {		
		$args[0] = '[' . $row['wishnum'] . '] ' . $row['name'];
		return $args;
	}
}


?>