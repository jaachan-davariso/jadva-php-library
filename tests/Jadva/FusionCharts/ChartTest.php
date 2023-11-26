<?php
//----------------------------------------------------------------------------------------------------------------------
/**
 * JAdVA library tests
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
 * @package    Jadva
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2009 Ja`Achan da`Variso (http://www.JaAchan.com/)
 * @license    http://www.JaAchan.com/software/LICENSE.txt
 * @version    $Id: ChartTest.php 286 2009-08-28 13:34:36Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
if( !defined('TESTS_JADVA_BASE_DIR') ) {
	define('TESTS_JADVA_BASE_DIR', dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR);
}
//----------------------------------------------------------------------------------------------------------------------
require_once TESTS_JADVA_BASE_DIR . 'TestHelper.php';
//----------------------------------------------------------------------------------------------------------------------
require_once 'vfsStream/vfsStream.php';
//----------------------------------------------------------------------------------------------------------------------
/** @see Jadva_FusionCharts_Chart */
require_once 'Jadva/FusionCharts/Chart.php';
//----------------------------------------------------------------------------------------------------------------------
/**
 * This class tests the Jadva_FusionCharts_Abstract class.
 *
 * @category   JAdVA
 * @package    Jadva
 * @subpackage UnitTests
 */
class Jadva_FusionCharts_ChartTest extends PHPUnit_Framework_TestCase
{
	public function testEmptyChart()
	{
		$chart = new Jadva_FusionCharts_Chart;

		$xml = $this->_cleanUpChartXml($chart, FALSE);

		$this->assertEquals('<?xml version="1.0"?><graph/>', $xml);
	}

	public function testEmptyChartNoHeader()
	{
		$chart = new Jadva_FusionCharts_Chart;

		$xml = $this->_cleanUpChartXml($chart, TRUE);

		$this->assertEquals('<graph/>', $xml);
	}

	/**
	 * @expectedException  Jadva_FusionCharts_Exception
	 */
	public function testWrongSeriesType()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('MSColumn3D');
	}
	//--------
	//
	// SS
	//
	//--------

	public function dataProviderColumn2D3D()
	{
		return array(
			array('Column2D'),
			array('Column3D'),
		);
	}

	/**
	 * @dataProvider  dataProviderColumn2D3D
	 */
	public function testColumn2D3D($graphType)
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType($graphType)
		      ->addGraphAttribute('caption', 'Monthly Unit Sales')
		      ->addGraphAttribute('xAxisName', 'Month')
		      ->addGraphAttribute('yAxisName', 'Units')
		      ->addGraphAttribute('decimalPrecision', '0')
		      ->addGraphAttribute('formatNumberScale', '0');

		$chart->addValue(0, '462', array('name' => 'Jan', 'color' => 'AFD8F8'));
		$chart->addValue(0, '857', array('name' => 'Feb', 'color' => 'F6BD0F'));
		$chart->addValue(0, '671', array('name' => 'Mar', 'color' => '8BBA00'));
		$chart->addValue(0, '494', array('name' => 'Apr', 'color' => 'FF8E46'));
		$chart->addValue(0, '761', array('name' => 'May', 'color' => '008E8E'));
		$chart->addValue(0, '960', array('name' => 'Jun', 'color' => 'D64646'));
		$chart->addValue(0, '629', array('name' => 'Jul', 'color' => '8E468E'));
		$chart->addValue(0, '622', array('name' => 'Aug', 'color' => '588526'));
		$chart->addValue(0, '376', array('name' => 'Sep', 'color' => 'B3AA00'));
		$chart->addValue(0, '494', array('name' => 'Oct', 'color' => '008ED6'));
		$chart->addValue(0, '761', array('name' => 'Nov', 'color' => '9D080D'));
		$chart->addValue(0, '960', array('name' => 'Dec', 'color' => 'A186BE'));

		$this->assertEquals($this->_getXmlFromFile('testColumn2D3D'), $this->_cleanUpChartXml($chart));
	}


	public function testLine2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->addGraphAttribute('caption', 'Monthly Sales Summary')
		      ->addGraphAttribute('subCaption', 'For the year 2004')
		      ->addGraphAttribute('xAxisName', 'Month')
		      ->addGraphAttribute('yAxisMinValue', '15000')
		      ->addGraphAttribute('yAxisName', 'Sales')
		      ->addGraphAttribute('decimalPrecision', '0')
		      ->addGraphAttribute('formatNumberScale', '0')
		      ->addGraphAttribute('numberPrefix', '$')
		      ->addGraphAttribute('showNames', '1')
		      ->addGraphAttribute('showValues', '0')
		      ->addGraphAttribute('showAlternateHGridColor', '1')
		      ->addGraphAttribute('alternateHGridColor', 'ff5904')
		      ->addGraphAttribute('divLineColor', 'ff5904')
		      ->addGraphAttribute('divLineAlpha', '20')
		      ->addGraphAttribute('alternateHGridAlpha', '5');

		$chart->addValue(0, '17400', array('name' => 'Jan', 'hoverText' => 'January'));
		$chart->addValue(0, '19800', array('name' => 'Feb', 'hoverText' => 'February'));
		$chart->addValue(0, '21800', array('name' => 'Mar', 'hoverText' => 'March'));
		$chart->addValue(0, '23800', array('name' => 'Apr', 'hoverText' => 'April'));
		$chart->addValue(0, '29600', array('name' => 'May', 'hoverText' => 'May'));
		$chart->addValue(0, '27600', array('name' => 'Jun', 'hoverText' => 'June'));
		$chart->addValue(0, '31800', array('name' => 'Jul', 'hoverText' => 'July'));
		$chart->addValue(0, '39700', array('name' => 'Aug', 'hoverText' => 'August'));
		$chart->addValue(0, '37800', array('name' => 'Sep', 'hoverText' => 'September'));
		$chart->addValue(0, '21900', array('name' => 'Oct', 'hoverText' => 'October'));
		$chart->addValue(0, '32900', array('name' => 'Nov', 'hoverText' => 'November'));
		$chart->addValue(0, '39800', array('name' => 'Dec', 'hoverText' => 'December'));

		$this->assertEquals($this->_getXmlFromFile('testLine2D'), $this->_cleanUpChartXml($chart));
	}

	public function testPie3D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('Pie3D')
		      ->addGraphAttribute('showNames', '1')
		      ->addGraphAttribute('decimalPrecision', '0');

		$chart->addValue(0, '20', array('name' => 'USA'));
		$chart->addValue(0,  '7', array('name' => 'France'));
		$chart->addValue(0, '12', array('name' => 'India'));
		$chart->addValue(0, '11', array('name' => 'England'));
		$chart->addValue(0,  '8', array('name' => 'Italy'));
		$chart->addValue(0, '19', array('name' => 'Canada'));
		$chart->addValue(0, '15', array('name' => 'Germany'));

		$this->assertEquals($this->_getXmlFromFile('testPie3D'), $this->_cleanUpChartXml($chart));
	}

	public function testPie2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('Pie2D')
		      ->addGraphAttribute('showNames', '1')
		      ->addGraphAttribute('decimalPrecision', '0');

		$chart->addValue(0, '20', array('name' => 'USA', 'issliced' => '1'));
		$chart->addValue(0,  '7', array('name' => 'France'));
		$chart->addValue(0, '12', array('name' => 'India'));
		$chart->addValue(0, '11', array('name' => 'England'));
		$chart->addValue(0,  '8', array('name' => 'Italy'));
		$chart->addValue(0, '19', array('name' => 'Canada'));
		$chart->addValue(0, '15', array('name' => 'Germany'));

		$this->assertEquals($this->_getXmlFromFile('testPie2D'), $this->_cleanUpChartXml($chart));
	}

	public function testBar2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('Bar2D')
		      ->addGraphAttribute('caption', 'Monthly Unit Sales')
		      ->addGraphAttribute('xAxisName', 'Month')
		      ->addGraphAttribute('yAxisName', 'Units')
		      ->addGraphAttribute('decimalPrecision', '0')
		      ->addGraphAttribute('formatNumberScale', '0')
		      ->addGraphAttribute('chartRightMargin', '30');

		$chart->addValue(0, '462', array('name' => 'Jan', 'color' => 'AFD8F8'));
		$chart->addValue(0, '857', array('name' => 'Feb', 'color' => 'F6BD0F'));
		$chart->addValue(0, '671', array('name' => 'Mar', 'color' => '8BBA00'));
		$chart->addValue(0, '494', array('name' => 'Apr', 'color' => 'FF8E46'));
		$chart->addValue(0, '761', array('name' => 'May', 'color' => '008E8E'));
		$chart->addValue(0, '960', array('name' => 'Jun', 'color' => 'D64646'));
		$chart->addValue(0, '629', array('name' => 'Jul', 'color' => '8E468E'));
		$chart->addValue(0, '622', array('name' => 'Aug', 'color' => '588526'));
		$chart->addValue(0, '376', array('name' => 'Sep', 'color' => 'B3AA00'));
		$chart->addValue(0, '494', array('name' => 'Oct', 'color' => '008ED6'));
		$chart->addValue(0, '761', array('name' => 'Nov', 'color' => '9D080D'));
		$chart->addValue(0, '960', array('name' => 'Dec', 'color' => 'A186BE'));

		$this->assertEquals($this->_getXmlFromFile('testBar2D'), $this->_cleanUpChartXml($chart));
	}

	public function testArea2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('Area2D')
		      ->addGraphAttribute('caption', 'Monthly Sales Summary')
		      ->addGraphAttribute('subcaption', 'For the year 2006')
		      ->addGraphAttribute('xAxisName', 'Month')
		      ->addGraphAttribute('yAxisName', 'Sales')
		      ->addGraphAttribute('decimalPrecision', '0')
		      ->addGraphAttribute('numberprefix', '$')
		      ->addGraphAttribute('yAxisMinValue', '15000');

		$chart->addValue(0, '17400', array('name' => 'Jan'));
		$chart->addValue(0, '18100', array('name' => 'Feb'));
		$chart->addValue(0, '21800', array('name' => 'Mar'));
		$chart->addValue(0, '23800', array('name' => 'Apr'));
		$chart->addValue(0, '29600', array('name' => 'May'));
		$chart->addValue(0, '27600', array('name' => 'Jun'));
		$chart->addValue(0, '31800', array('name' => 'Jul'));
		$chart->addValue(0, '39700', array('name' => 'Aug'));
		$chart->addValue(0, '37800', array('name' => 'Sep'));
		$chart->addValue(0, '21900', array('name' => 'Oct'));
		$chart->addValue(0, '32900', array('name' => 'Nov'));
		$chart->addValue(0, '39800', array('name' => 'Dec'));

		$this->assertEquals($this->_getXmlFromFile('testArea2D'), $this->_cleanUpChartXml($chart));
	}

	public function testDoughnut2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('Doughnut2D')
		      ->addGraphAttribute('showNames', '1')
		      ->addGraphAttribute('decimalPrecision', '0');

		$chart->addValue(0, '20', array('name' => 'USA'));
		$chart->addValue(0,  '7', array('name' => 'France'));
		$chart->addValue(0, '12', array('name' => 'India'));
		$chart->addValue(0, '11', array('name' => 'England'));
		$chart->addValue(0,  '8', array('name' => 'Italy'));
		$chart->addValue(0, '19', array('name' => 'Canada'));
		$chart->addValue(0, '15', array('name' => 'Germany'));

		$this->assertEquals($this->_getXmlFromFile('testDoughnut2D'), $this->_cleanUpChartXml($chart));
	}

	//--------
	//
	// MS
	//
	//--------

	public function dataProviderMSColumn2D3D()
	{
		return array(
			array('MSColumn2D'),
			array('MSColumn3D'),
		);
	}

	/**
	 * @dataProvider  dataProviderMSColumn2D3D
	 */
	public function testMSColumn2D3D($graphType)
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType($graphType)
		      ->setCategoriesAttributes(array('font' => 'Arial', 'fontSize' => '11', 'fontColor' => '000000'))
		      ->addGraphAttribute('caption', 'Global Export')
		      ->addGraphAttribute('subcaption', 'In Millions Tonnes per annum pr Hectare')
		      ->addGraphAttribute('xaxisname', 'Continent')
		      ->addGraphAttribute('yaxisname', 'Export')
		      ->addGraphAttribute('hovercapbgcolor', 'DEDEBE')
		      ->addGraphAttribute('hovercapbordercolor', '889E6D')
		      ->addGraphAttribute('rotatenames', '0')
		      ->addGraphAttribute('numdivlines', '9')
		      ->addGraphAttribute('divlinecolor', 'CCCCCC')
		      ->addGraphAttribute('divlinealpha', '80')
		      ->addGraphAttribute('decimalprecision', '0')
		      ->addGraphAttribute('showalternatehgridcolor', '1')
		      ->addGraphAttribute('alternatehgridalpha', '30')
		      ->addGraphAttribute('alternatehgridcolor', 'CCCCCC');

		$chart->addCategory(array('name' => 'N. America', 'hoverText' => 'North America'));
		$chart->addCategory(array('name' => 'Asia'));
		$chart->addCategory(array('name' => 'Europe'));
		$chart->addCategory(array('name' => 'Australia'));
		$chart->addCategory(array('name' => 'Africa'));

		$chart->setDataSetCount(3);

		$chart->setDataSetAttributes(0, array('seriesname' => 'Rice',  'color' => 'FDC12E'));
		$chart->addValue(0, 30);
		$chart->addValue(0, 26);
		$chart->addValue(0, 29);
		$chart->addValue(0, 31);
		$chart->addValue(0, 34);

		$chart->setDataSetAttributes(1, array('seriesname' => 'Wheat', 'color' => '56B9F9'));
		$chart->addValue(1, 67);
		$chart->addValue(1, 98);
		$chart->addValue(1, 79);
		$chart->addValue(1, 73);
		$chart->addValue(1, 80);

		$chart->setDataSetAttributes(2, array('seriesname' => 'Grain', 'color' => 'C9198D'));
		$chart->addValue(2, 27);
		$chart->addValue(2, 25);
		$chart->addValue(2, 28);
		$chart->addValue(2, 26);
		$chart->addValue(2, 10);

		$this->assertEquals($this->_getXmlFromFile('testMSColumn3D'), $this->_cleanUpChartXml($chart));
	}

	public function testMSArea2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType('MSArea2D')
		      ->addGraphAttribute('caption', 'Sales Volume')
		      ->addGraphAttribute('subcaption', 'For the month of Aug 2004')
		      ->addGraphAttribute('divlinecolor', 'F47E00')
		      ->addGraphAttribute('numdivlines', '4')
		      ->addGraphAttribute('numberPrefix', '$')
		      ->addGraphAttribute('showNames', '1')
		      ->addGraphAttribute('numVDivLines', '29')
		      ->addGraphAttribute('vDivLineAlpha', '30')
		      ->addGraphAttribute('formatNumberScale', '1')
		      ->addGraphAttribute('rotateNames', '1')
		      ->addGraphAttribute('decimalPrecision', '0')
		      ->addGraphAttribute('showAreaBorder', '1')
		      ->addGraphAttribute('areaBorderColor', '000000');

		$chart->addCategories(array(
			array('name' => '08/01'),
			array('name' => '08/02'),
			array('name' => '08/03'),
			array('name' => '08/04'),
			array('name' => '08/05'),
			array('name' => '08/06'),
			array('name' => '08/07'),
			array('name' => '08/08'),
			array('name' => '08/09'),
			array('name' => '08/10'),
			array('name' => '08/11'),
			array('name' => '08/12'),
			array('name' => '08/13'),
			array('name' => '08/14'),
			array('name' => '08/15'),
			array('name' => '08/16'),
			array('name' => '08/17'),
			array('name' => '08/18'),
			array('name' => '08/19'),
			array('name' => '08/20'),
			array('name' => '08/21'),
			array('name' => '08/22'),
			array('name' => '08/23'),
			array('name' => '08/24'),
			array('name' => '08/25'),
			array('name' => '08/26'),
			array('name' => '08/27'),
			array('name' => '08/28'),
			array('name' => '08/29'),
			array('name' => '08/30'),
			array('name' => '08/31'),
		));

		$chart->setDataSetCount(2);

		$chart->setDataSetAttributes(0, array(
			'seriesname' => 'Product A', 
			'color' => 'FF5904',
			'showValues' => '0',
			'areaAlpha' => '50',
			'showAreaBorder' => '1',
			'areaBorderThickness' => '2',
			'areaBorderColor' => 'FF0000',
		));

		$chart->addValue(0, 36634);
		$chart->addValue(0, 43653);
		$chart->addValue(0, 55565);
		$chart->addValue(0, 49457);
		$chart->addValue(0, 64654);
		$chart->addValue(0, 58457);
		$chart->addValue(0, 66456);
		$chart->addValue(0, 48765);
		$chart->addValue(0, 52574);
		$chart->addValue(0, 49546);
		$chart->addValue(0, 42346);
		$chart->addValue(0, 51765);
		$chart->addValue(0, 78456);
		$chart->addValue(0, 53867);
		$chart->addValue(0, 38359);
		$chart->addValue(0, 63756);
		$chart->addValue(0, 45554);
		$chart->addValue(0, 6543);
		$chart->addValue(0, 7555);
		$chart->addValue(0, 4567);
		$chart->addValue(0, 7544);
		$chart->addValue(0, 6565);
		$chart->addValue(0, 6433);
		$chart->addValue(0, 3465);
		$chart->addValue(0, 3574);
		$chart->addValue(0, 6646);
		$chart->addValue(0, 4546);
		$chart->addValue(0, 9565);
		$chart->addValue(0, 5456);
		$chart->addValue(0, 5667);
		$chart->addValue(0, 4359);

		$chart->setDataSetAttributes(1, array(
			'seriesname' => 'Product B', 
			'color' => '99cc99',
			'showValues' => '0',
			'areaAlpha' => '50',
			'showAreaBorder' => '1',
			'areaBorderThickness' => '2',
			'areaBorderColor' => '006600',
		));


		$chart->addValue(1, 12152);
		$chart->addValue(1, 15349);
		$chart->addValue(1, 16442);
		$chart->addValue(1, 17551);
		$chart->addValue(1, 13478);
		$chart->addValue(1, 16553);
		$chart->addValue(1, 17338);
		$chart->addValue(1, 17263);
		$chart->addValue(1, 16552);
		$chart->addValue(1, 17649);
		$chart->addValue(1, 12442);
		$chart->addValue(1, 11151);
		$chart->addValue(1, 15478);
		$chart->addValue(1, 16553);
		$chart->addValue(1, 16538);
		$chart->addValue(1, 17663);
		$chart->addValue(1, 13252);
		$chart->addValue(1, 16549);
		$chart->addValue(1, 14342);
		$chart->addValue(1, 13451);
		$chart->addValue(1, 15378);
		$chart->addValue(1, 17853);
		$chart->addValue(1, 17638);
		$chart->addValue(1, 14363);
		$chart->addValue(1, 10952);
		$chart->addValue(1, 10049);
		$chart->addValue(1, 19442);
		$chart->addValue(1, 13951);
		$chart->addValue(1, 19778);
		$chart->addValue(1, 18453);
		$chart->addValue(1, 17338);

		$this->assertEquals($this->_getXmlFromFile('testMSArea2D'), $this->_cleanUpChartXml($chart));
	}

	public function testMSLine()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType('MSLine');


		$chart->addGraphAttributes(array(
			'caption' => 'Daily Visits',
			'subcaption' => '(from 8/6/2006 to 8/12/2006)',
			'hovercapbgcolor' => 'FFECAA',
			'hovercapbordercolor' => 'F47E00',
			'formatNumberScale' => '0',
			'decimalPrecision' => '0',
			'showvalues' => '0',
			'numdivlines' => '3',
			'numVdivlines' => '0',
			'yaxisminvalue' => '1000',
			'rotateNames' => '1',
		));

		$chart->addCategories(array(
			array('name' => '8/6/2006'),
			array('name' => '8/7/2006'),
			array('name' => '8/8/2006'),
			array('name' => '8/9/2006'),
			array('name' => '8/10/2006'),
			array('name' => '8/11/2006'),
			array('name' => '8/12/2006'),
		));

		$chart->addDataSet(array(
			'seriesname' => 'Offline Marketing',
			'color' => '1D8BD1',
			'anchorbordercolor' => '1D8BD1',
			'anchorbgcolor' => '1D8BD1',
		));
		$chart->addValue(0, 1327);
		$chart->addValue(0, 1826);
		$chart->addValue(0, 1699);
		$chart->addValue(0, 1511);
		$chart->addValue(0, 1904);
		$chart->addValue(0, 1957);
		$chart->addValue(0, 1296);

		$chart->addDataSet(array(
			'seriesname' => 'Search',
			'color' => 'F1683C',
			'anchorbordercolor' => 'F1683C',
			'anchorbgcolor' => 'F1683C',
		));
		$chart->addValue(1, 2042);
		$chart->addValue(1, 3210);
		$chart->addValue(1, 2994);
		$chart->addValue(1, 3115);
		$chart->addValue(1, 2844);
		$chart->addValue(1, 3576);
		$chart->addValue(1, 1862);

		$chart->addDataSet(array(
			'seriesname' => 'Paid Search',
			'color' => '2AD62A',
			'anchorbordercolor' => '2AD62A',
			'anchorbgcolor' => '2AD62A',
		));
		$chart->addValue(2, 850);
		$chart->addValue(2, 1010);
		$chart->addValue(2, 1116);
		$chart->addValue(2, 1234);
		$chart->addValue(2, 1210);
		$chart->addValue(2, 1054);
		$chart->addValue(2, 802);

		$chart->addDataSet(array(
			'seriesname' => 'From Mail',
			'color' => 'DBDC25',
			'anchorbordercolor' => 'DBDC25',
			'anchorbgcolor' => 'DBDC25',
		));
		$chart->addValue(3, 541);
		$chart->addValue(3, 781);
		$chart->addValue(3, 920);
		$chart->addValue(3, 754);
		$chart->addValue(3, 840);
		$chart->addValue(3, 893);
		$chart->addValue(3, 451);

		$this->assertEquals($this->_getXmlFromFile('testMSLine'), $this->_cleanUpChartXml($chart));
	}

	public function testMSBar2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType('MSBar2D');

		$chart->addGraphAttributes(array(
			'caption' => 'Business Results: 2005',
			'yaxisname' => 'Revenue (Millions)',
			'hovercapbgcolor' => 'FFFFFF',
			'divlinecolor' => '999999',
			'divlinealpha' => '80',
			'numdivlines' => '5',
			'decimalprecision' => '0',
			'numberprefix' => '$',
			'numbersuffix' => 'M',
		));

		$chart->addCategories(array(
			array('name' => 'Hardware'),
			array('name' => 'Software'),
			array('name' => 'Service'),
		));

		$chart->addDataSet(array(
			'seriesname' => 'Domestic',
			'color' => '839F2F',
		));
		$chart->addValue(0, 84);
		$chart->addValue(0, 207);
		$chart->addValue(0, 116);

		$chart->addDataSet(array(
			'seriesname' => 'International',
			'color' => '56B9F9',
		));
		$chart->addValue(1, 116);
		$chart->addValue(1, 237);
		$chart->addValue(1, 83);

		$this->assertEquals($this->_getXmlFromFile('testMSBar2D'), $this->_cleanUpChartXml($chart));
	}

	//--------
	//
	// Stacked
	//
	//--------

	public function dataProviderStackedColumn2D3DBar2D()
	{
		return array(
			array('StackedColumn2D'),
			array('StackedColumn3D'),
			array('MSBar2D'),
		);
	}

	/**
	 * @dataProvider  dataProviderStackedColumn2D3DBar2D
	 */
	public function testStackedColumn2D3DBar2D($graphType)
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType($graphType);

		$chart->addGraphAttributes(array(
			'xaxisname' => 'Products',
			'yaxisname' => 'Sales',
			'caption' => 'Cumulative Sales',
			'subcaption' => '( 2004 to 2006 )',
			'decimalprecision' => '0',
			'rotatenames' => '1',
			'numdivlines' => '3',
			'numberprefix' => '$',
			'showvalues' => '0',
			'formatnumberscale' => '0',
		));

		$chart->addCategories(array(
			array('name' => 'Product A'),
			array('name' => 'Product B'),
			array('name' => 'Product C'),
			array('name' => 'Product D'),
			array('name' => 'Product E'),
		));

		$chart->addDataSet(array(
			'seriesname' => '2004',
			'color' => 'AFD8F8',
			'showvalues' => '0',
		));
		$chart->addValue(0, 25601.34);
		$chart->addValue(0, 20148.82);
		$chart->addValue(0, 17372.76);
		$chart->addValue(0, 35407.15);
		$chart->addValue(0, 38105.68);


		$chart->addDataSet(array(
			'seriesname' => '2005',
			'color' => 'F6BD0F',
			'showvalues' => '0',
		));
		$chart->addValue(1, 57401.85);
		$chart->addValue(1, 41941.19);
		$chart->addValue(1, 45263.37);
		$chart->addValue(1, 117320.16);
		$chart->addValue(1, 114845.27);

		$chart->addDataSet(array(
			'seriesname' => '2006',
			'color' => '8BBA00',
			'showvalues' => '0',
		));
		$chart->addValue(2, 45000.65);
		$chart->addValue(2, 44835.76);
		$chart->addValue(2, 18722.18);
		$chart->addValue(2, 77557.31);
		$chart->addValue(2, 92633.68);

		$this->assertEquals($this->_getXmlFromFile('testStackedColumn2D3D'), $this->_cleanUpChartXml($chart));
	}

	public function testStackedArea2D()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType('StackedArea2D');

		$chart->addGraphAttributes(array(
			'caption' => 'Monthly Sales Summary Comparison',
			'xaxisname' => 'Month',
			'yaxisname' => 'Sales',
			'numberprefix' => '$',
			'showvalues' => '0',
			'numvdivlines' => '10',
			'showalternatevgridcolor' => '1',
			'alternatevgridcolor' => 'e1f5ff',
			'divlinecolor' => 'e1f5ff',
			'vdivlinecolor' => 'e1f5ff',
			'bgcolor' => 'E9E9E9',
			'canvasborderthickness' => '0',
			'decimalprecision' => '0',
		));

		$chart->addCategories(array(
			array('name' => 'Jan'),
			array('name' => 'Feb'),
			array('name' => 'Mar'),
			array('name' => 'Apr'),
			array('name' => 'May'),
			array('name' => 'Jun'),
			array('name' => 'Jul'),
			array('name' => 'Aug'),
			array('name' => 'Sep'),
			array('name' => 'Oct'),
			array('name' => 'Nov'),
			array('name' => 'Dec'),
		));

		$chart->addDataSet(array(
			'seriesname' => '2004',
			'color' => 'B1D1DC',
			'areaalpha' => '60',
			'showareaborder' => '1',
			'areaborderthickness' => '1',
			'areabordercolor' => '7B9D9D',
		));

		$chart->addValue(0, 27400);
		$chart->addValue(0, 29800);
		$chart->addValue(0, 25800);
		$chart->addValue(0, 26800);
		$chart->addValue(0, 29600);
		$chart->addValue(0, 32600);
		$chart->addValue(0, 31800);
		$chart->addValue(0, 36700);
		$chart->addValue(0, 29700);
		$chart->addValue(0, 31900);
		$chart->addValue(0, 32900);
		$chart->addValue(0, 34800);

		$chart->addDataSet(array(
			'seriesname' => '2003',
			'color' => 'C8A1D1',
			'areaalpha' => '60',
			'showareaborder' => '1',
			'areaborderthickness' => '1',
			'areabordercolor' => '9871a1',
		));
		$chart->addValue(1, NULL);
		$chart->addValue(1, NULL);
		$chart->addValue(1, 4500);
		$chart->addValue(1, 6500);
		$chart->addValue(1, 7600);
		$chart->addValue(1, 6800);
		$chart->addValue(1, 11800);
		$chart->addValue(1, 19700);
		$chart->addValue(1, 21700);
		$chart->addValue(1, 21900);
		$chart->addValue(1, 22900);
		$chart->addValue(1, 29800);

		$chart->addTrendLine(array(
			'startvalue' => '22000',
			'endvalue' => '58000',
			'color' => '3366FF',
			'displayvalue' => 'Target',
			'thickness' => '1',
			'alpha' => '80',
		));

		$this->assertEquals($this->_getXmlFromFile('testStackedArea2D'), $this->_cleanUpChartXml($chart));
	}

	//--------
	//
	// Combination
	//
	//--------

	public function dataProviderMSColumn2D3DLineDY()
	{
		return array(
			array('MSColumn2DLineDY'),
			array('MSColumn3DLineDY'),
		);
	}

	/**
	 * @dataProvider  dataProviderMSColumn2D3DLineDY
	 */
	public function testMSColumn2D3DLineDY($graphType)
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType($graphType);

		$chart->addGraphAttributes(array(
			'caption' => 'Sales',
			'PYAxisName' => 'Revenue',
			'SYAxisName' => 'Quantity',
			'numberPrefix' => '$',
			'showvalues' => '0',
			'numDivLines' => '4',
			'formatNumberScale' => '0',
			'decimalPrecision' => '0',
			'anchorSides' => '10',
			'anchorRadius' => '3',
			'anchorBorderColor' => '009900',
		));

		$chart->addCategories(array(
			array('name' => 'March'),
			array('name' => 'April'),
			array('name' => 'May'),
			array('name' => 'June'),
			array('name' => 'July'),
		));

		$chart->addDataSet(array(
			'seriesName' => 'Product A',
			'color' => 'AFD8F8',
			'showValues' => '0',
		));

		$chart->addValue(0, 25601.34);
		$chart->addValue(0, 20148.82);
		$chart->addValue(0, 17372.76);
		$chart->addValue(0, 35407.15);
		$chart->addValue(0, 38105.68);

		$chart->addDataSet(array(
			'seriesName' => 'Product B',
			'color' => 'F6BD0F',
			'showValues' => '0',
		));

		$chart->addValue(1, 57401.85); 
		$chart->addValue(1, 41941.19);
		$chart->addValue(1, 45263.37);
		$chart->addValue(1, 117320.16);
		$chart->addValue(1, 114845.27);

		$chart->addDataSet(array(
			'seriesname' => 'Total Quantity',
			'color' => '8BBA00',
			'showValues' => '0',
			'parentYAxis' => 'S',
		));

		$chart->addValue(2, 45000);
		$chart->addValue(2, 44835);
		$chart->addValue(2, 42835);
		$chart->addValue(2, 77557);
		$chart->addValue(2, 92633);

		$this->assertEquals($this->_getXmlFromFile('testMSColumn2D3DLineDY'), $this->_cleanUpChartXml($chart));
	}

	//--------
	//
	// Other
	//
	//--------

	public function testFunnel()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_SINGLE_SERIES)
		      ->setGraphType('Funnel');

		$chart->addGraphAttributes(array(
			'isSliced' => '1',
			'slicingDistance' => '4',
			'decimalPrecision' => '0',
		));

		$chart->addValue(0,  '41', array('name' => 'Selected', 'color' => '99CC00', 'alpha' => '85'));
		$chart->addValue(0,  '84', array('name' => 'Tested', 'color' => '333333', 'alpha' => '85'));
		$chart->addValue(0, '126', array('name' => 'Interviewed', 'color' => '99CC00', 'alpha' => '85'));
		$chart->addValue(0, '180', array('name' => 'Candidates Applied', 'color' => '333333', 'alpha' => '85'));

		$this->assertEquals($this->_getXmlFromFile('testFunnel'), $this->_cleanUpChartXml($chart));
	}

	//--------
	//
	// Miscellaenous tests
	//
	//--------
	public function testMultiSeriesChartWithSingleDataSet()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setSeriesType(Jadva_FusionCharts_Chart::SERIES_TYPE_MULTI_SERIES)
		      ->setGraphType('MSColumn2D');

		$chart->addCategories(array(
			array('name' => 'May 09'),
			array('name' => 'June 09'),
			array('name' => 'July 09'),
			array('name' => 'August 09'),
		));

		$chart->addDataSet(array(
			'seriesName' => 'Product A',
		));

		$chart->addValue(0, 100);
		$chart->addValue(0, 150);
		$chart->addValue(0, 50);
		$chart->addValue(0, 125);

		$this->assertEquals($this->_getXmlFromFile('testMultiSeriesChartWithSingleDataSet'), $this->_cleanUpChartXml($chart));
	}

	public function testNullValue()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setGraphType('Line');

		$chart->addValue(0, 17400, array('name' => 'Jan'));
		$chart->addValue(0, NULL,  array('name' => 'Feb'));
		$chart->addValue(0, 21800, array('name' => 'Mar'));

		$this->assertEquals($this->_getXmlFromFile('testNullValue'), $this->_cleanUpChartXml($chart));
	}

	public function testAutoColourSingleSeries()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setGraphType('Column2D')
		      ->setAutoColour();

		$chart->addValue(0, '100');
		$chart->addValue(0, '200');
		$chart->addValue(0, '300');

		$this->assertEquals($this->_getXmlFromFile('testAutoColourSingleSeries'), $this->_cleanUpChartXml($chart));
	}

	public function testAutoColourSingleSeriesCustomColours()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setGraphType('Column2D')
		      ->setAutoColour(array('FF00FF', '00FF00'));

		$chart->addValue(0, '100');
		$chart->addValue(0, '200');
		$chart->addValue(0, '300');

		$this->assertEquals($this->_getXmlFromFile('testAutoColourSingleSeriesCustomColours'), $this->_cleanUpChartXml($chart));
	}

	public function testAutoColourMultiSeries()
	{
		$chart = new Jadva_FusionCharts_Chart;
		$chart->setGraphType('MSColumn2D')
		      ->setAutoColour();

		$chart->addCategory(array('name' => 'Product A'))
		      ->addCategory(array('name' => 'Product B'));

		$chart->addDataSet();
		$chart->addValue(0, '100');
		$chart->addValue(0, '200');

		$chart->addDataSet();
		$chart->addValue(1, '150');
		$chart->addValue(1, '175');

		$this->assertEquals($this->_getXmlFromFile('testAutoColourMultiSeries'), $this->_cleanUpChartXml($chart));
	}

	protected function _getXmlFromFile($name)
	{
		$xml = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $name . '.xml');
		$xml = $this->_cleanUpXml($xml);
		$xml = str_replace("\t", '', $xml);

		return $xml;
	}

	protected function _cleanUpChartXml(Jadva_FusionCharts_Chart $chart, $removeHeader = TRUE)
	{
		return $this->_cleanUpXml($chart->toXml($removeHeader));
	}

	protected function _cleanUpXml($xml)
	{
		$xml = str_replace("\n", '', $xml);
		$xml = str_replace("'", '"', $xml);
		return $xml;
	}
}
//----------------------------------------------------------------------------------------------------------------------
