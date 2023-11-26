<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.JaAchan.com/software/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@jaachan.com so we can send you a copy immediately.
 *
 * If you have a modification you would like to see included, (in
 * order to distribute it), email the changes to
 * softwaremodifications@jaachan.com. Should the modifications be
 * accepted, they will be released as soon as possible, and,
 * should you want to, you will be noted as contributor.
 *
 * @category   JAdVA
 * @package    Jadva_View
 * @subpackage Jadva_View_Helper
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: RenderFusionChartsChart.php 280 2009-08-28 12:13:00Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/** @see Zend_View_Helper_Abstract */
require_once 'Zend/View/Helper/Abstract.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * View helper to render a FusionCharts Chart
 *
 * @category   JAdVA
 * @package    Jadva_View
 * @subpackage Jadva_View_Helper
 */
class Jadva_View_Helper_RenderFusionChartsChart extends Zend_View_Helper_Abstract
{
	//------------------------------------------------
	/**
	 * Returns the HTML code that displays the given chart
	 *
	 * @param  Jadva_FusionCharts_Chart|Jadva_FusionCharts_Chart_AggregateInterface  $in_chart
	 *         The chart to render
	 * @param  integer  $in_width   The width of displayed graph
	 * @param  integer  $in_height  The height of displayed graph
	 * @param  string   $in_windowMode  Which window mode to use (window, opaque or transparent)
	 *
	 * @return  string|Jadva_View_Helper_RenderFusionChartsChart
	 *          The HTML code that displays the given chart, or the instance when no arguments are passed
	 */
	public function renderFusionChartsChart($in_chart = NULL, $in_width = 300, $in_height = 200, $in_windowMode = 'window')
	{
		if( func_num_args() == 0 ) {
			return $this;
		}

		return $this->chart($in_chart, $in_width, $in_height, $in_windowMode);
	}
	//------------------------------------------------
	/**
	 * Sets the SWF path, i.e. the URL of the directory where the Flash files for Fusion Charts are stored
	 *
	 * @param  string  $in_swfPath  The URL of the directory
	 *
	 * @return  Jadva_View_Helper_RenderFusionChartsChart  Provides a fluent interface
	 */
	public function setSwfPath($in_swfPath)
	{
		$this->_swfPath = (string) $in_swfPath;


		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the SWF path
	 *
	 * Will try to guess by making use of a linkTo View Helper if no path is set
	 *
	 * @return  string  The SWF Path
	 */
	public function getSwfPath()
	{
		if( NULL === $this->_swfPath ) {
			$this->_swfPath = $this->view->linkTo('/res/FusionCharts/');
		}

		return $this->_swfPath;
	}
	//------------------------------------------------
	/**
	 * Returns the HTML code that displays the given chart
	 *
	 * Zend_Locale will be called upon to localise the chart, if available.
	 *
	 * @param  Jadva_FusionCharts_Chart|Jadva_FusionCharts_Chart_AggregateInterface  $in_chart
	 *         The chart to render
	 * @param  integer  $in_width   The width of displayed graph
	 * @param  integer  $in_height  The height of displayed graph
	 * @param  string   $in_windowMode  Which window mode to use (window, opaque or transparent)
	 *
	 * @return  string  The HTML code that displays the given chart
	 */
	public function chart($in_chart = NULL, $in_width = 300, $in_height = 200, $in_windowMode = 'window')
	{
		//Parse parameters
		if( $in_chart instanceof Jadva_FusionCharts_Chart ) {
			$chart = $in_chart;
		} elseif( $in_chart instanceof Jadva_FusionCharts_Chart_AggregateInterface ) {
			$chart = $in_chart->getFusionChartsChart();
		} else {
			trigger_error(
				'Parameter given to view helper must be instance of Jadva_FusionCharts_Chart or implement Jadva_FusionCharts_Chart_AggregateInterface.',
				E_USER_WARNING
			);

			return '';
		}

		if( !$chart->hasGraphType() ) {
			trigger_error(
				'The given chart has no graph type set yet.',
				E_USER_WARNING
			);

			return '';
		}

		if( !is_numeric($in_width) ) {
			$in_windowMode = $in_width;
			$in_width = 300;
			$in_height = 200;
		} elseif( !is_numeric($in_height) ) {
			$in_windowMode = $in_height;
			$in_height = 200;
		}

		$width  = (int) $in_width;
		$height = (int) $in_height;

		//Localise chart
		if( class_exists('Zend_Locale_Format') ) {
			$options = Zend_Locale_Format::setOptions();
			$symbols = Zend_Locale_Data::getList($options['locale'],'symbols');

			$chart->addGraphAttributes(array(
				'decimalSeparator'  => $symbols['decimal'],
				'thousandSeparator' => $symbols['group'],
			));
		}
		

		//Clean up XML
		$xml = $chart->toXml(TRUE);

		$swf = $this->getSwfPath() . 'FCF_' . $chart->getGraphType() . '.swf';

		//Create HTML code
		static $chartNumber = 0;
		$chartNumber++;

		$chartId = __CLASS__ . '_chart_'  .$chartNumber;
		$divId   = __CLASS__ . '_div_'  .$chartNumber;
		$varId   = __CLASS__ . '_var_'  .$chartNumber;

		$return  = '<!-- Start Fusion Charts Chart code -->';
		$return .= '<div id="'  . $divId . '">Chart.</div>';
		$return .= '<script type="text/javascript"><!--// <![CDATA[' . PHP_EOL;

		$return .= 'var ' . $varId . ' = new FusionCharts("' . $swf . '"';
		$return .= ', "' . $chartId . '", ' . $width . ', ' . $height . ');' . PHP_EOL;
		$return .= $varId . '.setDataXML("' . addcslashes($xml, '"\/') . '");' . PHP_EOL;

		if( 'opaque' == $in_windowMode ) {
			$return .= $varId . '.setTransparent();' . PHP_EOL;
		} elseif( 'transparent' == $in_windowMode ) {
			$return .= $varId . '.setTransparent(1);' . PHP_EOL;
		}
	
		$return .= $varId . '.render("' . $divId . '");' . PHP_EOL;

		$return .= '//]]>--></script>';
		$return .= '<!-- End Fusion Charts Chart code -->';

		return $return;
	}
	//------------------------------------------------
	/**
	 * Contains the path the the swf files, when set
	 *
	 * @var  string|NULL
	 */
	protected $_swfPath = NULL;
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
