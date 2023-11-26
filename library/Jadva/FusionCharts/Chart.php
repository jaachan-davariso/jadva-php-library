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
 * @package    Jadva_FusionCharts
 * @subpackage Jadva_FusionCharts_Chart
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Chart.php 142 2009-04-10 11:46:11Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Class around a FusionCharts Chart
 *
 * @see        http://www.fusioncharts.com/
 *
 * @category   JAdVA
 * @package    Jadva_FusionCharts
 * @subpackage Jadva_FusionCharts_Chart
 * @copyright  Copyright (c) 2008 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 */
class Jadva_FusionCharts_Chart
{
	//------------------------------------------------
	const SERIES_TYPE_SINGLE_SERIES      = 1;
	const SERIES_TYPE_MULTI_SERIES       = 2;
	const SERIES_TYPE_SCATTER_AND_BUBBLE = 3;
	const SERIES_TYPE_MSSTACKED          = 4;
	const SERIES_TYPE_GANTT              = 5;
	//------------------------------------------------
	/**
	 * Returns the series type of this graph
	 *
	 * @return  integer  The series type of this graph
	 */
	public function getSeriesType()
	{
		return $this->_seriesType;
	}
	//------------------------------------------------
	/**
	 * Returns whether the number of data sets in this graph has been set yet
	 *
	 * @return  TRUE if the series type of this graph has been set yet, FALSE otherwise
	 */
	public function hasSeriesType()
	{
		return NULL !== $this->_seriesType;
	}
	//------------------------------------------------
	/**
	 * Sets the series type of this graph
	 *
	 * Possible values:
	 * <pre>
	 *    1 => Single Series
	 *    2 => Multi Series
	 *    3 => Scatter and Bubble
	 *    4 => MSStacked
	 *    5 => Gantt
	 * </pre>
	 *
	 * @param  integer  $in_seriesType  The series type of this graph
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setSeriesType($in_seriesType)
	{
		$seriesType = (int) $in_seriesType;

		if( ($seriesType <= 0) || (6 <= $seriesType) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Series type must be between 1 and 5');
		}

		if( $this->hasGraphType() ) {
			if( self::$_graphTypeToSeriesType[$this->getGraphType()] !== $seriesType ) {
				$this->_graphType = NULL;
			}
		} else {
			$this->_graphType = self::$_seriesTypeToDefaultGraphType[$seriesType];
		}

		$this->_seriesType = $seriesType;

		if( (self::SERIES_TYPE_SINGLE_SERIES === $seriesType) && !$this->hasDataSetCount() ) {
			$this->setDataSetCount(1);
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the number of data sets in this graph
	 *
	 * @return  integer  The number of data sets in this graph
	 */
	public function getDataSetCount()
	{
		return $this->_dataSetCount;
	}
	//------------------------------------------------
	/**
	 * Returns whether the number of data sets in this graph has been set yet
	 *
	 * @return  TRUE if the number of data sets in this graph has been set yet, FALSE otherwise
	 */
	public function hasDataSetCount()
	{
		return NULL !== $this->_dataSetCount;
	}
	//------------------------------------------------
	/**
	 * Sets the number of data sets in this graph
	 *
	 * @param  integer  $in_dataSetCount  The number of data sets in this graph
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setDataSetCount($in_dataSetCount)
	{
		$dataSetCount = (int) $in_dataSetCount;

		if( !$this->hasSeriesType() ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Set the series type before setting the data set count.');
		}

		if( (1 == $this->getSeriesType()) && (1 !== $dataSetCount) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Single Series can only have one data set');
		}

		$this->_dataSetCount = $dataSetCount;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns the type of the graph
	 *
	 * @return  string  The type of the graph
	 */
	public function getGraphType()
	{
		return $this->_graphType;
	}
	//------------------------------------------------
	/**
	 * Returns whether the type of the graph has been set yet
	 *
	 * @return  boolean  TRUE if the type of the graph has been set yet, FALSE otherwise
	 */
	public function hasGraphType()
	{
		return NULL !== $this->_graphType;
	}
	//------------------------------------------------
	/**
	 * Sets the type of the graph
	 *
	 * @param  string  $in_type  The type of the graph
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setGraphType($in_type)
	{
		$type = (string) $in_type;

		if( !array_key_exists($type, self::$_graphTypeToSeriesType) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('No such graph type: "' . $in_type .'"');
		}

		$graphTypeSeriesType = self::$_graphTypeToSeriesType[$type];
		if( $this->hasSeriesType() ) {
			if( $this->getSeriesType() !== $graphTypeSeriesType ) {
				/** @see Jadva_FusionCharts_Exception */
				require_once 'Jadva/FusionCharts/Exception.php';
				throw new Jadva_FusionCharts_Exception(
					'The series type for the new chart type ("' . $graphTypeSeriesType
					  . '") does not match the one already set for this graph ("' . $this->getSeriesType()
					  . '").'
				);
			}
		} else {
			$this->setSeriesType($graphTypeSeriesType);
		}

		$this->_graphType = $in_type;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds (and overrides) attributes for the graphs
	 *
	 * @param  array  $attribs  The graph attributes to set
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addGraphAttributes(array $attribs)
	{
		foreach($attribs as $key => $value) {
			if( in_array($key, self::$_allowdGraphAttributes) ) {
				$this->_graphAttributes[$key] = $value;
			}
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets attributes for the graphs
	 *
	 * @param  array  $attribs  The graph attributes to set
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setGraphAttributes(array $attribs)
	{
		$this->_graphAttributes = array();
		$this->addGraphAttributes($attribs);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds a value to a dataset
	 *
	 * @param  integer  $in_dataSetNumber  The number of the data set
	 * @param  float    $in_value          The value to add
	 * @param  array    $attribs           (OPTIONAL) Additonal attributes for the value
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addValue($in_dataSetNumber, $in_value, array $attribs = array())
	{
		$dataSetNumber = (int) $in_dataSetNumber;
		$value         = (float) $in_value;


		if( !$this->hasDataSetCount() ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Set the data count first');
		}
		if( ($dataSetNumber < 0) || ($this->getDataSetCount() <= $dataSetNumber) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Data set number must be between 0 and ' . $this->getDataSetCount() - 1);
		}

		$options = array(
			'value' => $value,
		);

		if( self::SERIES_TYPE_SINGLE_SERIES === $this->getSeriesType() ) {
			$allowedAttributes = &self::$_allowdSetAttributesSingleSeries;
		} else {
			$allowedAttributes = &self::$_allowdSetAttributesOther;
		}

		foreach($attribs as $key => $attribValue) {
			if( in_array($key, $allowedAttributes) ) {
				$options[$key] = $attribValue;
			}
		}

		$this->_values[$dataSetNumber][] = $options;

		if( NULL === $this->_maxValue ) {
			$this->_maxValue = $value;
		} else {
			$this->_maxValue = max($this->_maxValue, $value);
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Generates an XML document of this chart
	 *
	 * @return  string  The XML document of this chart
	 */
	public function toXml()
	{
		$document = new DOMDocument;

		$graph = $document->createElement('graph');

		foreach($this->_graphAttributes as $key => $value ) {
			$graph->setAttribute($key, $value);
		}

		if( !array_key_exists('yAxisMaxValue', $this->_graphAttributes) ) {
			$graph->setAttribute('yAxisMaxValue', $this->_maxValue * 1.1);
		}

		$dataSetCount = $this->getDataSetCount();
		for($dataSetNumber = 0; $dataSetNumber < $dataSetCount; $dataSetNumber++) {
			if( 1 < $dataSetCount ) {
				$dataSet = $document->createElement('dataset');
			} else {
				$dataSet = $graph;
			}

			foreach($this->_values[$dataSetNumber] as $valueInfo) {
				$set = $document->createElement('set');
				$set->setAttribute('value', $valueInfo['value']);

				if( 1 == $dataSetCount ) {
					$name = $valueInfo['name'];
					if( !empty($name) ) {
						$set->setAttribute('name', $name);
					}
				}

				$dataSet->appendChild($set);
			}

			if( 1 < $dataSetCount ) {
				$document->appendChild($dataSet);
			}
		}

		$document->appendChild($graph);

		return $document->saveXML();
	}
	//------------------------------------------------
	/**
	 * Maps the graph type onto the data set count
	 *
	 * @var  array
	 */
	protected static $_graphTypeToSeriesType = array(
		//Free Single Series
		'Area2D'     => 1, 'Bar2D'      => 1, 'Column2D'   => 1, 'Column3D'   => 1, 'Doughnut2D' => 1,
		'Line'       => 1, 'Pie2D'      => 1, 'Pie3D'      => 1, 'Funnel'     => 1,

		//Free Multi Series
		'MSArea2D'         => 2, 'MSBar2D'          => 2, 'MSColumn2D'       => 2, 'MSColumn2DLineDY' => 2,
		'MSColumn3D'       => 2, 'MSColumn3DLineDY' => 2, 'MSLine'           => 2, 'StackedArea2D'    => 2,
		'StackedBar2D'     => 2, 'StackedColumn2D'  => 2, 'StackedColumn3D'  => 2,
	);
	//------------------------------------------------
	/**
	 * Maps the data set count onto the graph used by default
	 *
	 * @var  array
	 */
	protected static $_seriesTypeToDefaultGraphType = array(
		1 => 'Line',
		2 => 'MSLine',
	);
	//------------------------------------------------
	/**
	 * Contains the attributes allowed to set for graphs
	 *
	 * @var  array
	 */
	protected static $_allowdGraphAttributes = array(
		//Background Properties
		'bgColor', 'bgAlpha', 'bgSWF',

		//Canvas Properties
		'canvasBgColor', 'canvasBgAlpha', 'canvasBorderColor', 'canvasBorderThickness',

		//Chart and Axis Titles
		'caption', 'subCaption', 'xAxisName', 'yAxisName',

		//Chart Numerical Limits
		'yAxisMinValue', 'yAxisMaxValue',

		//Generic Properties
		'shownames', 'showValues', 'showLimits', 'rotateNames', 'animation', 'showColumnShadow',

		//Font Properties
		'baseFont', 'baseFontSize', 'baseFontColor', 'outCnvBaseFont ', 'outCnvBaseFontSze', 'outCnvBaseFontColor',

		//Number Formatting Options
		'numberPrefix',     'numberSuffix',      'formatNumber',     'formatNumberScale',
		'decimalSeparator', 'thousandSeparator', 'decimalPrecision', 'divLineDecimalPrecision',
		'limitsDecimalPrecision',

		//Zero Plane
		'zeroPlaneThickness', 'zeroPlaneColor', 'zeroPlaneAlpha',

		//Divisional Lines (Horizontal)
		'numdivlines',      'divlinecolor',            'divLineThickness',    'divLineAlpha', 
		'showDivLineValue', 'showAlternateHGridColor', 'alternateHGridColor', 'alternateHGridAlpha',

		//Divisional Lines (Vertical)
		'numVDivLines',            'VDivlinecolor',       'VDivLineThickness',   'VDivLineAlpha',
		'showAlternateVGridColor', 'alternateVGridColor', 'alternateVGridAlpha',

		//Hover Caption Properties
		'showhovercap', 'hoverCapBgColor', 'hoverCapBorderColor', 'hoverCapSepChar',

		//Chart Margins
		'chartLeftMargin', 'chartRightMargin', 'chartTopMargin', 'chartBottomMargin',
	);
	//------------------------------------------------
	/**
	 * Contains the attributes allowed to set for set elements, for single series graphs
	 *
	 * @var  array
	 */
	protected static $_allowdSetAttributesSingleSeries = array(
		'name', 'color', 'hoverText', 'link', 'alpha', 'showName',
	);
	//------------------------------------------------
	/**
	 * Contains the attributes allowed to set for set elements, for graphs that aren't single series
	 *
	 * @var  array
	 */
	protected static $_allowdSetAttributesOther = array(
		'color', 'link', 'alpha',
	);
	//------------------------------------------------
	/**
	 * Contains the series type of this chart
	 *
	 * @var  integer|NULL
	 */
	protected $_seriesType = NULL;
	//------------------------------------------------
	/**
	 * Contains the number of data sets in this chart
	 *
	 * @var  integer|NULL
	 */
	protected $_dataSetCount = NULL;
	//------------------------------------------------
	/**
	 * Contains the SWF name to display the chart with
	 *
	 * @var  string|NULL
	 */
	protected $_graphType = NULL;

	protected $_maxValue = NULL;
	//------------------------------------------------
	protected $_graphAttributes = array();
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
