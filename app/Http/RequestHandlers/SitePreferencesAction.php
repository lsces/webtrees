<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function e;
use function file_get_contents;
use function file_put_contents;
use function is_writable;
use function preg_replace;
use function redirect;
use function route;
use function str_contains;

final class SitePreferencesAction implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $index_directory     = Validator::parsedBody($request)->string('INDEX_DIRECTORY');
        $allow_change_gedcom = Validator::parsedBody($request)->boolean('ALLOW_CHANGE_GEDCOM');
        $language            = Validator::parsedBody($request)->string('LANGUAGE');
        $theme_dir           = Validator::parsedBody($request)->string('THEME_DIR');
        $timezone            = Validator::parsedBody($request)->string('TIMEZONE');

        if (!str_ends_with($index_directory, '/')) {
            $index_directory .= '/';
        }

        if (is_dir($index_directory)) {
            if (is_writable($index_directory)) {
                $this->updateConfigDataDir($index_directory);
                Site::$config_overrides['INDEX_DIRECTORY'] = $index_directory;
            } else {
                FlashMessages::addMessage(I18N::translate('Cannot write to the folder “%s”.', e($index_directory)), 'danger');
            }
        } else {
            FlashMessages::addMessage(I18N::translate('The folder “%s” does not exist.', e($index_directory)), 'danger');
        }

        Site::setPreference('ALLOW_CHANGE_GEDCOM', (string) $allow_change_gedcom);
        Site::setPreference('LANGUAGE', $language);
        Site::setPreference('THEME_DIR', $theme_dir);
        Site::setPreference('TIMEZONE', $timezone);

        FlashMessages::addMessage(I18N::translate('The website preferences have been updated.'), 'success');
        $url = route(ControlPanel::class);

        return redirect($url);
    }

    private function updateConfigDataDir(string $data_dir): void
    {
        $config_file = Webtrees::CONFIG_FILE;
        $content     = file_get_contents($config_file);

        $escaped = addcslashes($data_dir, '"');

        if (str_contains($content, 'data_dir=')) {
            $content = preg_replace('/^data_dir=".*"$/m', 'data_dir="' . $escaped . '"', $content);
        } else {
            $content .= 'data_dir="' . $escaped . '"' . "\n";
        }

        file_put_contents($config_file, $content);
    }
}
