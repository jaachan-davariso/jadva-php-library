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
 * @version    $Id: AggregateInterface.php 269 2009-08-26 17:11:14Z jaachan $
 */
//----------------------------------------------------------------------------------------------------------------------
/**
 * Interface to generate an external {@link Jadva_FusionCharts_Chart}
 *
 * @category   JAdVA
 * @package    Jadva_FusionCharts
 * @subpackage Jadva_FusionCharts_Chart
 */
interface Jadva_FusionCharts_Chart_AggregateInterface
{
	//------------------------------------------------
	/**
	 * Returns the FusionCharts Chart that displays the information of the object
	 *
	 * @return  Jadva_FusionCharts_Chart  The chart that displays the information of the object
	 */
	public function getFusionChartsChart();
	//------------------------------------------------
}
//----------------------------------------------------------------------------------------------------------------------
