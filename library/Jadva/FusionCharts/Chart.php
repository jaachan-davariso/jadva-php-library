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
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: Chart.php 292 2009-09-02 16:25:54Z jaachan $
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
 */
class Jadva_FusionCharts_Chart
{
	//------------------------------------------------
	/** Charts with only one series of data */
	const SERIES_TYPE_SINGLE_SERIES      = 1;
	/** Charts with multiple series of data */
	const SERIES_TYPE_MULTI_SERIES       = 2;
	const SERIES_TYPE_SCATTER_AND_BUBBLE = 3;
	const SERIES_TYPE_MSSTACKED          = 4;
	const SERIES_TYPE_GANTT              = 5;

	/** Custom colour scheme given by the user */
	const COLOUR_SCHEME_CUSTOM        = 'colourSchemeCustom';
	/** Standard colour scheme that comes with Fusion Charts Free */
	const COLOUR_SCHEME_FUSION_CHARTS = 'colourSchemeFusionCharts';
	/** Red, with increasing and decreasing levels of green and blue */
	const COLOUR_SCHEME_RED   = 'colourSchemeRed';
	/** Green, with increasing and decreasing levels of red and blue */
	const COLOUR_SCHEME_GREEN   = 'colourSchemeGreen';
	/** Blue, with increasing and decreasing levels of red and green */
	const COLOUR_SCHEME_BLUE   = 'colourSchemeBlue';
	//------------------------------------------------
	/**
	 * Default constructor
	 *
	 * @param  array  $options  Options to set
	 */
	public function __construct(array $options = array())
	{
		$this->setOptions($options);

		$this->init();
	}
	//------------------------------------------------
	/**
	 * Initialising
	 *
	 * You can use this function to initialise a specific chart decendent class. It's called after the contructor
	 * is done.
	 */
	public function init()
	{
	}
	//------------------------------------------------
	/**
	 * Sets the options
	 *
	 * @param  array  $options  Options to set
	 *
	 * @return  Jadva_File_Filter_Abstract  Provides a fluent interface
	 */
	public function setOptions(array $options)
	{
		unset($options['options']);
		foreach($options as $optionName => $optionValue) {
			$methodName = 'set' . ucfirst($optionName);
			if( method_exists($this, $methodName) ) {
				$this->$methodName($optionValue);
			}
		}

		return $this;
	}
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
	 * Returns whether automatic colouring is on
	 *
	 * @return  TRUE if automatic colouring is on, FALSE otherwise
	 */
	public function isAutoColour()
	{
		return FALSE !== $this->_colourSchemeName;
	}
	//------------------------------------------------
	/**
	 * Turns automatic colouring on or off
	 *
	 * Will reset the counter
	 *
	 * @param  array|string|boolean  The name of the scheme, the custom scheme or FALSE to turn it off
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setAutoColour($scheme = self::COLOUR_SCHEME_FUSION_CHARTS)
	{
		if( TRUE === $scheme ) {
			$scheme = self::COLOUR_SCHEME_FUSION_CHARTS;
		}

		if( FALSE === $scheme ) {
			$this->_colourSchemeName = FALSE;
		} elseif( is_array($scheme) ) {
			if( empty($scheme) ) {
				/** @see Jadva_FusionCharts_Exception */
				require_once 'Jadva/FusionCharts/Exception.php';
				throw new Jadva_FusionCharts_Exception('Colour schemes must contain at least one colour.');
			}

			$this->_colourSchemeName = self::COLOUR_SCHEME_CUSTOM;
			$this->_customColourScheme = $scheme;
		} elseif( self::COLOUR_SCHEME_CUSTOM === $scheme ) {
			$this->_colourSchemeName = self::COLOUR_SCHEME_CUSTOM;
		} elseif( array_key_exists($scheme, self::$_colourSchemes) ) {
			$this->_colourSchemeName = $scheme;
		} else {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Could not understand colour scheme setting');
		}

		$this->_colourSchemeNameCounter = 0;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Skips a colour in the colour scheme
	 *
	 * For instance, when you want to match up two graphs, when one of them misses a bar
	 *
	 * @pre  $this->isAutoColour();
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function skipColour()
	{
		if( !$this->isAutoColour() ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Auto colouring is off, cannot skip colour');
		}

		$this->_getColour();

		return $this;
	}
	//------------------------------------------------
	/**
	 * Returns whether to apply our y-axis min and max fix
	 *
	 * @return  boolean  TRUE if we're to apply our y-axis min and max fix, FALSE otherwise
	 */
	public function isYaxisMinMaxFix()
	{
		return $this->_yaxisMinMaxFix;
	}
	//------------------------------------------------
	/**
	 * Sets whether to apply our y-axis min and max fix
	 *
	 * FusionCharts doesn't take trend lines in account, and doesn't account for graphs with only 0 values. We try
	 * to fix that:
	 * <ul>
	 *   <li>If a value contains only 0 values, we set the max y-axis value to 5, ensuring that you don't get weird
	 *       NaN's</li>
	 *   <li>We recalculate the max and min, and take into account the trend lines
	 * </ul>
	 *
	 * It's off by default. It doesn't work for stacked charts (yet).
	 *
	 * @param  boolean  $in_yaxisMinMaxFix  TRUE to apply our y-axis min and max fix, FALSE not to apply it.
	 *
	 * @todo  Fix it so this works for stacked charts as well.
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setYaxisMinMaxFix($in_yaxisMinMaxFix)
	{
		$this->_yaxisMinMaxFix = (boolean) $in_yaxisMinMaxFix;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds a data set
	 *
	 * @param  array  $attribs  The attributes for the data set
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addDataSet(array $attribs = array())
	{
		if( $this->hasDataSetCount() ) {
			$this->setDataSetCount($this->getDataSetCount() + 1);
		} else {
			$this->setDataSetCount(1);
		}

		if(
			$this->_colourSchemeName
		&&
			(self::SERIES_TYPE_MULTI_SERIES === $this->getSeriesType())
		&&
			!array_key_exists('color', $attribs)
		) {
			$attribs['color'] = $this->_getColour();
		}

		$this->setDataSetAttributes($this->getDataSetCount() - 1, $attribs);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds multiple data sets
	 *
	 * @param  array  $dataSets  The data sets
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addDataSets(array $dataSets)
	{
		foreach($dataSets as $dataSet) {
			$this->addDataSet($dataSet);
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

		if( (self::SERIES_TYPE_SINGLE_SERIES == $this->getSeriesType()) && (1 !== $dataSetCount) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Single Series can only have one data set');
		}

		if( $this->hasDataSetCount() ) {
			$oldCount = $this->getDataSetCount();
		} else {
			$oldCount = 0;
		}

		$this->_dataSetCount = $dataSetCount;

		for($it = $oldCount; $it < $dataSetCount; $it++) {
			$this->_values[$it] = array();
			$this->_datasetAttributeList[$it] = array();
		}

		for($it = $dataSetCount; $it < $oldCount; $it++) {
			unset($this->_values[$it]);
			unset($this->_datasetAttributeList[$it]);
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Checks whether the given data set attribute is valid for this graph
	 *
	 * @param  string  $name  The name of the data set attribute
	 *
	 * @return  boolean  TRUE if the attribute is valid, FALSE otherwise
	 */
	public function isValidDataSetAttribute($name)
	{
		if( in_array($name, self::$_allowedDataSetAttributes) ) {
			return TRUE;
		}

		if( !array_key_exists($this->_graphType, self::$_additionalAllowedDataSetAttributes) ) {
			return FALSE;
		}

		return in_array($name, self::$_additionalAllowedDataSetAttributes[$this->_graphType]);
	}
	//------------------------------------------------
	/**
	 * Sets the attributes for the given data set
	 *
	 * @param  integer  $in_dataSetNumber  The number of the data set
	 * @param  array    $attribs           The attributes to set
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setDataSetAttributes($in_dataSetNumber, array $attribs)
	{
		$dataSetNumber = (int) $in_dataSetNumber;

		if( !$this->hasDataSetCount() ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('No data set has been added yet. Add one before adding values.');
		}

		if( ($dataSetNumber < 0) || ($this->getDataSetCount() <= $dataSetNumber) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Data set number must be between 0 and ' . ($this->getDataSetCount() - 1));
		}

		$attributeList = array();
		foreach($attribs as $key => $value) {
			$key = strtolower($key);

			if( !$this->isValidDataSetAttribute($key) ) {
				continue;
			}

			$attributeList[$key] = $value;
		}

		$this->_datasetAttributeList[$dataSetNumber] = $attributeList;

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
	 * Checks whether the given graph attribute is valid for this graph
	 *
	 * @param  string  $name  The name of the graph attribute
	 *
	 * @return  boolean  TRUE if the attribute is valid, FALSE otherwise
	 */
	public function isValidGraphAttribute($name)
	{
		if( in_array($name, self::$_allowedGraphAttributes) ) {
			return TRUE;
		}

		if( !array_key_exists($this->_graphType, self::$_additionalAllowedGraphAttributes) ) {
			return FALSE;
		}

		return in_array($name, self::$_additionalAllowedGraphAttributes[$this->_graphType]);
	}
	//------------------------------------------------
	/**
	 * Adds (and overrides) a single graph attribute
	 * 
	 * @param  string  $name   The graph attribute to set
	 * @param  string  $value  The value to set it to
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addGraphAttribute($name, $value)
	{
		$name = strtolower($name);

		if( $this->isValidGraphAttribute($name) ) {
			$this->_graphAttributeList[$name] = $value;
		}

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
			$this->addGraphAttribute($key, $value);
		}

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets attributes for the <graph> element
	 *
	 * @param  array  $attribs  The graph attributes to set
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setGraphAttributes(array $attribs)
	{
		$this->_graphAttributeList = array();
		$this->addGraphAttributes($attribs);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Sets attributes for the <categories> element
	 *
	 * @param  array  $attribs  The categories attributes to set
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function setCategoriesAttributes(array $attribs)
	{
		$attributeList = array();
		foreach($attribs as $key => $value) {
			$key = strtolower($key);

			if( !in_array($key, self::$_allowedCategoriesAttributes) ) {
				continue;
			}

			$attributeList[$key] = $value;
		}

		$this->_categoriesAttributeList = $attributeList;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds a category with the given attributes
	 *
	 * @param  array  $attribs  The attributes for the category
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addCategory(array $attribs = array())
	{
		if( self::SERIES_TYPE_SINGLE_SERIES == $this->getSeriesType() ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Categories are only for Multi Series');
		}

		$category = array();
		foreach($attribs as $key => $value) {
			$key = strtolower($key);

			if( !in_array($key, self::$_allowedCategoryAttributes) ) {
				continue;
			}

			$category[$key] = $value;
		}

		$this->_categoryList[] = $category;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Adds a list of categories with the given attrobutes
	 *
	 * @param  array  $categories  The attributes for each category
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addCategories(array $categories)
	{
		foreach($categories as $category) {
			$this->addCategory($category);
		}

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
		if( NULL === $in_value ) {
			$value = NULL;
		} else {
			$value = (float) $in_value;
		}


		if( !$this->hasDataSetCount() ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Set the data count first');
		}
		if( ($dataSetNumber < 0) || ($this->getDataSetCount() <= $dataSetNumber) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception('Data set number must be between 0 and ' . ($this->getDataSetCount() - 1));
		}

		$set = array(
			'value' => $value,
		);

		foreach($attribs as $key => $attribValue) {
			$key = strtolower($key);

			if( !$this->isValidSetAttribute($key) ) { 
				continue;
			}

			$set[$key] = $attribValue;
		}

		if(
			$this->_colourSchemeName
		&&
			(self::SERIES_TYPE_SINGLE_SERIES === $this->getSeriesType())
		&&
			!array_key_exists('color', $set)
		) {
			$set['color'] = $this->_getColour();
		}

		$this->_values[$dataSetNumber][] = $set;

		$this->_addedValue($value);

		return $this;
	}
	//------------------------------------------------
	/**
	 * Checks whether the given set attribute is valid for this graph
	 *
	 * @param  string  $name  The name of the set attribute
	 *
	 * @return  boolean  TRUE if the attribute is valid, FALSE otherwise
	 */
	public function isValidSetAttribute($name)
	{
		if( self::SERIES_TYPE_SINGLE_SERIES === $this->getSeriesType() ) {
			if( in_array($name, self::$_allowedSetAttributesSingleSeries) ) {
				return TRUE;
			}
		} else {
			if( in_array($name, self::$_allowedSetAttributesOther) ) {
				return TRUE;
			}
		}

		if( !array_key_exists($this->_graphType, self::$_additionalAllowedSetAttributes) ) {
			return FALSE;
		}

		return in_array($name, self::$_additionalAllowedSetAttributes[$this->_graphType]);
	}
	//------------------------------------------------
	/**
	 * Adds a trendline with the given attributes
	 *
	 * @param  array  $attribs  The attributes for the given trend line
	 *
	 * @return  Jadva_FusionCharts_Chart  Provides a fluent interface
	 */
	public function addTrendLine($attribs)
	{
		if( is_numeric($attribs) ) {
			$attribs = array('startvalue' => $attribs);
		} elseif( !is_array($attribs) ) {
			/** @see Jadva_FusionCharts_Exception */
			require_once 'Jadva/FusionCharts/Exception.php';
			throw new Jadva_FusionCharts_Exception(sprintf(
				'Argument 1 passed to %1$s must be numeric or array',
				__METHOD__
			));
		}

		$trendLine = array();
		foreach($attribs as $key => $value) {
			$key = strtolower($key);

			if( !in_array($key, self::$_allowedTrendLineAttributes) ) {
				continue;
			}

			$trendLine[$key] = $value;
		}

		if( array_key_exists('startvalue', $trendLine) ) {
			$this->_addedValue($trendLine['startvalue']);
		}

		if( array_key_exists('endvalue', $trendLine) ) {
			$this->_addedValue($trendLine['endvalue']);
		}

		$this->_trendLineList[] = $trendLine;

		return $this;
	}
	//------------------------------------------------
	/**
	 * Generates an XML document of this chart
	 *
	 * @param  boolean  $removeXmlHeader  (OPTIONAL) Whether to remove the <?xml header.
	 *
	 * @return  string  The XML document of this chart
	 */
	public function toXml($removeXmlHeader = FALSE)
	{
		$document = new DOMDocument;

		$graph = $document->createElement('graph');

		$graphAttributeList = $this->_graphAttributeList;

		if( $this->isYaxisMinMaxFix() ) {
			$this->_fixYaxisMinMaxValues($graphAttributeList);
		}

		$this->_storeAttributesToDomElement($graphAttributeList, $graph);

		if( self::SERIES_TYPE_MULTI_SERIES == $this->getSeriesType() ) {
			$categories = $document->createElement('categories');
			foreach($this->_categoriesAttributeList as $key => $value) {
				$categories->setAttribute($key, $value);
			}

			foreach($this->_categoryList as $categoryData) {
				$category = $document->createElement('category');
				$this->_storeAttributesToDomElement($categoryData, $category);
				$categories->appendChild($category);
			}

			$graph->appendChild($categories);
		}

		$dataSetCount = $this->getDataSetCount();
		for($dataSetNumber = 0; $dataSetNumber < $dataSetCount; $dataSetNumber++) {
			if( self::SERIES_TYPE_MULTI_SERIES == $this->getSeriesType() ) {
				$dataSet = $document->createElement('dataset');
				$this->_storeAttributesToDomElement($this->_datasetAttributeList[$dataSetNumber], $dataSet);
			} else {
				$dataSet = $graph;
			}

			foreach($this->_values[$dataSetNumber] as $valueInfo) {
				$set = $document->createElement('set');
				$this->_storeAttributesToDomElement($valueInfo, $set);
				$dataSet->appendChild($set);
			}

			if( self::SERIES_TYPE_MULTI_SERIES == $this->getSeriesType() ) {
				$graph->appendChild($dataSet);
			}
		}

		if( 0 < count($this->_trendLineList) ) {
			$trendLineListNode = $document->createElement('trendlines');

			foreach($this->_trendLineList as $trendLineData) {
				$trendLineNode = $document->createElement('line');
				$this->_storeAttributesToDomElement($trendLineData, $trendLineNode);
				$trendLineListNode->appendChild($trendLineNode);
			}

			$graph->appendChild($trendLineListNode);
		}

		$document->appendChild($graph);

		$xml = $document->saveXML();

		if( $removeXmlHeader ) {
			if( '<?xml' == substr($xml, 0, 5) ) {
				$end = strpos($xml, '?>');
				$xml = trim(substr($xml, $end + 2));
			}
		}

		return $xml;
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
	protected static $_allowedGraphAttributes = array(
		//Background Properties
		'bgcolor', 'bgalpha', 'bgswf',

		//Canvas Properties
		'canvasbgcolor', 'canvasbgalpha', 'canvasbordercolor', 'canvasborderthickness',

		//Chart and Axis Titles
		'caption', 'subcaption', 'xaxisname', 'yaxisname',

		//Chart Numerical Limits
		'yaxisminvalue', 'yaxismaxvalue',

		//Generic Properties
		'shownames', 'showvalues', 'showlimits', 'rotatenames', 'animation', 'showcolumnshadow',

		//Font Properties
		'basefont', 'basefontsize', 'basefontcolor', 'outcnvbasefont ', 'outcnvbasefontsze', 'outcnvbasefontcolor',

		//Number Formatting Options
		'numberprefix',     'numbersuffix',      'formatnumber',     'formatnumberscale',
		'decimalseparator', 'thousandseparator', 'decimalprecision', 'divlinedecimalprecision',
		'limitsdecimalprecision',

		//Zero Plane
		'zeroplanethickness', 'zeroplanecolor', 'zeroplanealpha',

		//Divisional Lines (Horizontal)
		'numdivlines',      'divlinecolor',            'divlinethickness',    'divlinealpha', 
		'showdivlinevalue', 'showalternatehgridcolor', 'alternatehgridcolor', 'alternatehgridalpha',

		//Divisional Lines (Vertical)
		'numvdivlines',            'vdivlinecolor',       'vdivlinethickness',   'vdivlinealpha',
		'showalternatevgridcolor', 'alternatevgridcolor', 'alternatevgridalpha',

		//Hover Caption Properties
		'showhovercap', 'hovercapbgcolor', 'hovercapbordercolor', 'hovercapsepchar',

		//Chart Margins
		'chartleftmargin', 'chartrightmargin', 'charttopmargin', 'chartbottommargin',
	);
	//------------------------------------------------
	/**
	 * Aadditional attributes for <graph> elements, per graph type
	 *
	 * @var  array
	 */
	protected static $_additionalAllowedGraphAttributes = array(
		'MSArea2D' => array('showareaborder', 'areaborderthickness', 'areabordercolor', 'areaalpha'),
		'StackedArea2D' => array('showareaborder', 'areaborderthickness', 'areabordercolor', 'areaalpha'),
		'MSColumn2DLineDY' => array('pyaxisname', 'syaxisname',
		                  'showanchors', 'anchorsides', 'anchorradius', 'anchorbordercolor', 
		                  'anchorborderthickness', 'anchorbgcolor', 'anchorbgalpha', 'anchoralpha'),
		'MSColumn3DLineDY' => array('pyaxisname', 'syaxisname',
		                  'showanchors', 'anchorsides', 'anchorradius', 'anchorbordercolor', 
		                  'anchorborderthickness', 'anchorbgcolor', 'anchorbgalpha', 'anchoralpha'),
		'Funnel' => array('issliced', 'slicingdistance'),
	);
	//------------------------------------------------
	/**
	 * Contains the attributes allowed to set for set elements, for single series graphs
	 *
	 * @var  array
	 */
	protected static $_allowedSetAttributesSingleSeries = array(
		'name', 'color', 'hovertext', 'link', 'alpha', 'showname',
	);
	//------------------------------------------------
	/**
	 * Attributes allowed to set for set elements, for graphs that aren't single series
	 *
	 * @var  array
	 */
	protected static $_allowedSetAttributesOther = array(
		'color', 'link', 'alpha',
	);
	//------------------------------------------------
	/**
	 * Additional attributes for <set> elements, per graph type
	 *
	 * @var  array
	 */
	protected static $_additionalAllowedSetAttributes = array(
		'Pie3D'    => array('issliced'),
		'Pie2D'    => array('issliced'),
	);
	//------------------------------------------------
	/**
	 * Attributes allowed in <categories> elements
	 *
	 * @var  array
	 */
	protected static $_allowedCategoriesAttributes = array(
		'font', 'fontsize', 'fontcolor',
	);
	//------------------------------------------------
	/**
	 * Attributes allowed in <category> elements
	 *
	 * @var  array
	 */
	protected static $_allowedCategoryAttributes = array(
		'name', 'hovertext', 'showname',
	);
	//------------------------------------------------
	/**
	 * Attributes allowed in <dataset> elements
	 *
	 * @var  array
	 */
	protected static $_allowedDataSetAttributes = array(
		'seriesname', 'color', 'showvalues', 'alpha',
	);
	//------------------------------------------------
	/**
	 * Additional attributes allowed in <dataset> elements, per graph type
	 *
	 * @var  array
	 */
	protected static $_additionalAllowedDataSetAttributes = array(
		'MSArea2D' => array('showareaborder', 'areaborderthickness', 'areabordercolor', 'areaalpha'),

		'MSLine' => array('showanchors', 'anchorsides', 'anchorradius', 'anchorbordercolor', 
		                  'anchorborderthickness', 'anchorbgcolor', 'anchorbgalpha', 'anchoralpha',
		                  'linethickness'),

		'StackedArea2D' => array('showareaborder', 'areaborderthickness', 'areabordercolor', 'areaalpha'),

		'MSColumn2DLineDY' => array('parentyaxis'),
		'MSColumn3DLineDY' => array('parentyaxis'),
	);
	//------------------------------------------------
	/**
	 * Attributes allowed in <line> elements
	 *
	 * @var  array
	 */
	protected static $_allowedTrendLineAttributes = array(
		'startvalue', 'endvalue', 'color', 'displayvalue', 'thickness', 'istrendzone', 'showontop', 'alpha'
	);
	//------------------------------------------------
	/**
	 * Colour schemes for automatic assignment of colours
	 *
	 * @var  array
	 */
	protected static $_colourSchemes = array(
		self::COLOUR_SCHEME_FUSION_CHARTS => array(
			'AFD8F8', 'F6BD0F', '8BBA00', 'A66EDD', 'F984A1', 'CCCC00', '999999',
			'0099CC', 'FF0000', '006F00', '0099FF', 'FF66CC', '669966', '7C7CB4',
			'FF9933', '9900FF', '99FFCC', 'CCCCFF', '669900', '1941A5',
		),
		self::COLOUR_SCHEME_GREEN => array(
			'00FF00', '33FF00', '66FF00', '99FF00', 'CCFF00', 'CCFF33', 'CCFF66',
			'CCFF99', 'CCFFCC', '99FFCC', '66FFCC', '33FFCC', '00FFCC', '00FF99',
			'00FF66', '00FF33',
		),
		self::COLOUR_SCHEME_BLUE => array(
			'0000FF', '0033FF', '0066FF', '0099FF', '00CCFF', '33CCFF', '66CCFF',
			'99CCFF', 'CCCCFF', 'CC99FF', 'CC66FF', 'CC33FF', 'CC00FF', '9900FF',
			'6600FF', '3300FF',
		),
		self::COLOUR_SCHEME_RED => array(
			'FF0000', 'FF3300', 'FF6600', 'FF9900', 'FFCC00', 'FFCC33', 'FFCC66',
			'FFCC99', 'FFCCCC', 'FF99CC', 'FF66CC', 'FF33CC', 'FF00CC', 'FF0099',
			'FF0066', 'FF0033',
		),
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
	 * Contains name of the colour scheme, if any
	 *
	 * @var  string|boolean
	 */
	protected $_colourSchemeName = FALSE;
	//------------------------------------------------
	/**
	 * Whether to apply our y-axis min and max fix
	 *
	 * @var  boolean
	 */
	protected $_yaxisMinMaxFix = FALSE;
	//------------------------------------------------
	/**
	 * Contains the counter of the colour scheme
	 *
	 * @var  integer
	 */
	protected $_colourSchemeNameCounter = 0;
	//------------------------------------------------
	/**
	 * Contains the custom colour scheme, if any
	 *
	 * @var  array
	 */
	protected $_customColourScheme = NULL;
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
	//------------------------------------------------
	/**
	 * Contains the maximum value added to this graph
	 *
	 * @var  float|NULL
	 */
	protected $_maxValue = NULL;
	//------------------------------------------------
	/**
	 * Contains the minimum value added to this graph
	 *
	 * @var  float|NULL
	 */
	protected $_minValue = NULL;
	//------------------------------------------------
	/**
	 * The list of categories
	 *
	 * @var  array
	 */
	protected $_categoryList = array();
	//------------------------------------------------
	/**
	 * The <dataset> attributes
	 *
	 * @var  array
	 */
	protected $_datasetAttributeList = array();
	//------------------------------------------------
	/**
	 * The <categories> attributes
	 *
	 * @var  array
	 */
	protected $_categoriesAttributeList = array();
	//------------------------------------------------
	/**
	 * The trend lines
	 *
	 * @var  array
	 */
	protected $_trendLineList = array();
	//------------------------------------------------
	/**
	 * Contains the attributes for this graph
	 *
	 * @var  array
	 */
	protected $_graphAttributeList = array();
	//------------------------------------------------
	/**
	 * Stores options as attributes in a DOM node
	 *
	 * @param  array       $attribs  The attributes, mapping name to value
	 * @param  DOMElement  $element  The element
	 *
	 * @return  void
	 */
	protected function _storeAttributesToDomElement(array $attribs, DOMElement $element)
	{
		foreach($attribs as $key => $value) {
			if( NULL === $value ) {
				continue;
			}

			$element->setAttribute($key, $value);
		}
	}
	//------------------------------------------------
	/**
	 * Returns the next colour, and ups the counter
	 *
	 * @pre  A colour scheme must have been set
	 * @return  string  The colour
	 */
	protected function _getColour()
	{
		if( self::COLOUR_SCHEME_CUSTOM == $this->_colourSchemeName ) {
			$count = count($this->_customColourScheme);
		} else {
			$count = count(self::$_colourSchemes[$this->_colourSchemeName]);
		}

		$index = $this->_colourSchemeNameCounter % $count;

		if( self::COLOUR_SCHEME_CUSTOM == $this->_colourSchemeName ) {
			$colour = $this->_customColourScheme[$index];
		} else {
			$colour = self::$_colourSchemes[$this->_colourSchemeName][$index];
		}

		$this->_colourSchemeNameCounter++;

		return $colour;
	}
	//------------------------------------------------
	/**
	 * Processes an added value
	 *
	 * @param  float  $value  The added value
	 *
	 * @return  void
	 */
	protected function _addedValue($value)
	{
		if( NULL === $this->_maxValue ) {
			$this->_maxValue = $value;
		} else {
			$this->_maxValue = max($this->_maxValue, $value);
		}

		if( NULL === $this->_minValue ) {
			$this->_minValue = $value;
		} else {
			$this->_minValue = min($this->_minValue, $value);
		}
	}
	//------------------------------------------------
	/**
	 * Calculates the max and min like FusionCharts does. Except that it takes into account 0 values and trend lines
	 *
	 * @param  array  &$attribs  The graph attributes to fix
	 *
	 * @return  void
	 */
	protected function _fixYaxisMinMaxValues(array &$attribs)
	{
		$maxValue = $this->_maxValue;
		$minValue = $this->_minValue;

		if( (0 == $maxValue) && (0 == $minValue) ) {
			$attribs['yaxismaxvalue'] = 5;
			return;
		}

		//Find the powers of then between which the values lie
		$maxPowerOfTen = floor(log(abs($maxValue), 10));
		$minPowerOfTen = floor(log(abs($minValue), 10));

		//Find the order of magnitude of the y axis
		$powerOfTen = max($minPowerOfTen, $maxPowerOfTen);
		$y_interval = pow(10, $powerOfTen);

		//Find out if we're just above the interval. If so, take it down a notch
		// Otherwise, adding a 101 to a graph would leave the graph only half filled
		if( (abs($maxValue) / $y_interval < 3) && (abs($minValue) / $y_interval < 3) ) {
			--$powerOfTen;
			$y_interval = pow(10, $powerOfTen);
		}

		//Calculate the lower bound
		if( $minValue < 0 ) {
			//Automatically negative since $minValue is negative
			$y_lowerBound = (floor($minValue / $y_interval) - 1) * $y_interval;
		} else {
			$y_lowerBound = 0;
		}

		//Calculate the upper bound
		if( $maxValue <= 0 ) {
			$y_topBound = 0;
		} else {
			$y_topBound = (floor($maxValue / $y_interval) + 1) * $y_interval;
		}

		//Store them
		if( !array_key_exists('yaxismaxvalue', $attribs) || (NULL === $attribs['yaxismaxvalue']) ) {
			$attribs['yaxismaxvalue'] = $y_topBound;
		}

		if( !array_key_exists('yaxisminvalue', $attribs) || (NULL === $attribs['yaxisminvalue']) ) {
			$attribs['yaxisminvalue'] = $y_lowerBound;
		}
	}
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
