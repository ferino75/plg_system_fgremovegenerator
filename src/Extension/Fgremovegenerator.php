<?php

/**
 * @package     plg_system_fgremovegenerator
 * @copyright   (C) 2026 Fero
 * @license     GNU General Public License version 2 or later
 */

declare(strict_types=1);

namespace FG\Plugin\System\Fgremovegenerator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Event\Application\BeforeCompileHeadEvent;
use Joomla\CMS\Event\Application\BeforeRespondEvent;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

/**
 * Removes the Joomla generator meta tag and optional fingerprinting HTTP headers
 * (X-Powered-By, X-Generator).
 *
 * Requires Joomla 5.0+ (uses the concrete Application event classes introduced
 * in Joomla 5.0.0; these do not exist in Joomla 4).
 */
final class Fgremovegenerator extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onBeforeCompileHead' => 'onBeforeCompileHead',
            'onBeforeRespond'     => 'onBeforeRespond',
        ];
    }

    /**
     * Removes selected fingerprinting HTTP headers as late as possible in the
     * application lifecycle — right before Joomla sends the HTTP response —
     * so that headers set later by any component, plugin or template cannot
     * slip through after this plugin already ran.
     */
    public function onBeforeRespond(BeforeRespondEvent $event): void
    {
        if (headers_sent()) {
            return;
        }

        $headersToRemove = [
            'remove_x_powered_by' => 'X-Powered-By',
            'remove_x_generator'  => 'X-Generator',
        ];

        foreach ($headersToRemove as $param => $header) {
            if ((int) $this->params->get($param, 0)) {
                header_remove($header);
            }
        }
    }

    public function onBeforeCompileHead(BeforeCompileHeadEvent $event): void
    {
        $app = $event->getApplication();

        if (!$app instanceof CMSApplicationInterface) {
            return;
        }

        // Always on the frontend; on the backend only if explicitly enabled
        if ($app->isClient('administrator') && !(int) $this->params->get('apply_admin', 0)) {
            return;
        }

        if (!$app->isClient('site') && !$app->isClient('administrator')) {
            return;
        }

        $document = $event->getDocument();

        if (!$document instanceof HtmlDocument) {
            return;
        }

        $mode = (string) $this->params->get('mode', 'remove');

        if ($mode === 'custom') {
            $document->setGenerator((string) $this->params->get('custom_text', ''));
        } else {
            // Empty generator string = Joomla renders no generator meta tag at all
            $document->setGenerator('');
        }
    }
}
