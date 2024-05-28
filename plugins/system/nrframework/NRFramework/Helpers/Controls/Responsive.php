<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Helpers\Controls;

defined('_JEXEC') or die;

use NRFramework\Helpers\Controls\Control;

class Responsive
{
	/**
	 * Responsive breakpoints
	 * 
	 * @var  array
	 */
	public static $breakpoints = [
		'desktop',
		'tablet',
		'mobile'
	];
    
    /**
     * Given a responsive value, we prepare its CSS for each breakpoint.
     * 
     * @param   array   $value
     * @param   string  $prefix
     * @param   string  $unit
     * 
     * @return  mixed
     */
    public static function getResponsiveControlValue($value, $prefix = '', $unit = '')
    {
        if (!$value)
        {
            return;
        }

        if (!is_string($unit))
        {
            return;
        }
        
        if (!is_string($prefix) || empty($prefix))
        {
            return;
        }
        
        if (is_string($value) && trim($value) === '')
        {
            return;
        }

        if (!is_array($value))
        {
            $value = self::prepareResponsiveControlValue($value);
        }

        $return = [];
        // Validate breakpoints
        foreach ($value as $breakpoint => $_value)
        {
            $validatedValue = Control::getParsedValue($_value, $unit);
            if ($validatedValue === null || $validatedValue === false || $validatedValue === '' || (is_array($validatedValue) && empty($validatedValue)))
            {
                continue;
            }
            
            $return[$breakpoint] = preg_filter('/^/', $prefix . ':', $validatedValue) . ';';
        }

        if (!$return)
        {
            return;
        }
        
        if (count(array_unique($return)) === 1)
        {
            $value = reset($return);
            if ($value_data = Control::findUnitinValue($return))
            {
                $value = $value_data['value'];
                $unit = $value_data['unit'];
            }
            return [
                key($return) => $value
            ];
        }

        return $return;
    }

    /**
     * Prepares the value
     * 
     * @param   mixed  $value
     * 
     * @return  array
     */
    public static function prepareResponsiveControlValue($value)
    {
        if (!$value)
        {
            return;
        }
        
        if (!is_array($value))
        {
            return [
                'desktop' => $value
            ];
        }
        
        return $value;
    }
}