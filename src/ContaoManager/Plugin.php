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

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create('DanielGausi\CalendarEditorBundle\DanielGausiCalendarEditorBundle')
                ->setLoadAfter(['Contao\CoreBundle\ContaoCoreBundle'])
                ->setReplace(['calendareditor']),
        ];
    }
}