<?php

/**
 * @copyright  Daniel Gaußmann 2018
 * @author     Daniel Gaußmann (Gausi)
 * @package    Calendar_Editor
 * @license    LGPL-3.0+
 * @see	       https://github.com/DanielGausi/Contao-CalendarEditor
 *
 */

namespace DanielGausi\CalendarEditorBundle\ContaoManager;

use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use DanielGausi\CalendarEditorBundle\DanielGausiCalendarEditorBundle;
use Contao\CoreBundle\ContaoCoreBundle;


/**
 * Plugin for the Contao Manager.
 */
class Plugin implements BundlePluginInterface    
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(DanielGausiCalendarEditorBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['calendareditor'])
        ];
    }
}
