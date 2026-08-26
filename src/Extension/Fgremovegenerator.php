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
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\EventInterface;
use Joomla\Event\SubscriberInterface;

/**
 * Removes the Joomla generator meta tag and optional fingerprinting HTTP headers
 * (X-Powered-By, X-Generator, X-AspNet-Version).
 */
final class Fgremovegenerator extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise'   => 'onAfterInitialise',
            'onBeforeCompileHead' => 'onBeforeCompileHead',
        ];
    }

    /**
     * Removes selected fingerprinting HTTP headers as early as possible in the
     * application lifecycle, before headers have been sent to the client.
     */
    public function onAfterInitialise(EventInterface $event): void
    {
        if (headers_sent()) {
            return;
        }

        $headersToRemove = [
            'remove_x_powered_by'     => 'X-Powered-By',
            'remove_x_generator'      => 'X-Generator',
            'remove_x_aspnet_version' => 'X-AspNet-Version',
        ];

        foreach ($headersToRemove as $param => $header) {
            if ((int) $this->params->get($param, 0)) {
                header_remove($header);
            }
        }
    }

    public function onBeforeCompileHead(EventInterface $event): void
    {
        $app = $this->getApplication();

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

        $document = $app->getDocument();

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
