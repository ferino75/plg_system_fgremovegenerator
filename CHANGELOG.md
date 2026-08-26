# Changelog – plg_system_fgremovegenerator

## 1.3.0 (2026-08-26)
- Rebranded into the FG series as `plg_system_fgremovegenerator`
- Namespace changed to `FG\Plugin\System\Fgremovegenerator`, class renamed to `Fgremovegenerator`
- Added `declare(strict_types=1)`; `onAfterInitialise`/`onBeforeCompileHead` now type-hinted against `Joomla\Event\EventInterface` instead of the concrete `Event` class
- Added `<updateservers>` block (points at `updates.xml` on the `master` branch of `ferino75/plg_system_fgremovegenerator`), with `<client>site</client>` declared on the plugin's `<update>` entry
- Declared `<php_minimum>8.0.0</php_minimum>`
- Added GitHub-ready repo scaffolding: README.md with shields.io badges, LICENSE (GPL-2.0-or-later), .gitignore, assets/logo.png (teal/coral FG brand style) + its generation script
- No functional/behavioral change to header or meta-tag removal logic

## 1.2.0 (2026-07-27)
- Added independent toggles for removing the `X-Generator` and `X-AspNet-Version` HTTP headers (both default off)
- Refactored `onAfterInitialise` to remove headers via a param → header map instead of separate conditionals
- Deliberately did **not** add removal of `X-Content-Type-Options` — that is a genuine security header (`nosniff`), not a fingerprinting leak; removing it would reduce security

## 1.1.0 (2026-07-27)
- Added removal of the `X-Powered-By` HTTP header (PHP) via `header_remove()` on `onAfterInitialise`
- New parameter: "Remove X-Powered-By header" (default on)

## 1.0.0 (2026-07-17)
- Initial release: native Joomla 6 system plugin (PSR-4, `SubscriberInterface`, DI container)
- Removes the Joomla generator meta tag via `onBeforeCompileHead` / `setGenerator('')`
- Optional mode: remove completely, or replace with custom text
- Optional: also apply in the administrator backend
- Languages: en-GB, sk-SK
