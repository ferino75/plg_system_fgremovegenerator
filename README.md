<p align="center">
  <img src="assets/logo.png" alt="FG Remove Generator logo" width="128" height="128">
</p>

<h1 align="center">FG - Remove Generator</h1>

<p align="center">
  <img src="https://img.shields.io/badge/Joomla-4%20%7C%205%20%7C%206-1a6877?logo=joomla&logoColor=white" alt="Joomla 4/5/6">
  <img src="https://img.shields.io/badge/PHP-%3E%3D8.0-777bb4?logo=php&logoColor=white" alt="PHP 8.0+">
  <img src="https://img.shields.io/github/v/tag/ferino75/plg_system_fgremovegenerator?label=version&color=ff6b4a" alt="Version">
  <img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue" alt="License">
</p>

A native Joomla 4/5/6 system plugin that removes the Joomla **generator meta tag**
(`<meta name="generator" content="Joomla! - Open Source Content Management">`) and,
optionally, common **fingerprinting HTTP response headers**:

- `X-Powered-By` (sent by PHP)
- `X-Generator` (sometimes sent by templates/extensions)
- `X-AspNet-Version` (not applicable on a PHP stack, included for completeness)

## Why

Fingerprinting headers and meta tags make it trivial for automated scanners to
identify your CMS/PHP version and target known vulnerabilities. Removing them
is a small, low-risk hardening step (**security through obscurity is not a
substitute for keeping Joomla/PHP updated**, but it does raise the bar for
casual automated scanning).

> **Note:** this plugin deliberately does **not** touch `X-Content-Type-Options`.
> That header (`nosniff`) is a genuine security control, not a fingerprinting
> leak — removing it would *reduce* security rather than improve privacy.

## Features

- Remove the generator meta tag completely, or replace it with custom text
- Optional: also remove/replace the generator meta tag in the administrator backend (the HTTP headers below are always removed everywhere when enabled, regardless of this setting)
- Optional, independent toggles for `X-Powered-By`, `X-Generator`, `X-AspNet-Version`
- Headers are removed on `onBeforeRespond` — right before Joomla sends the HTTP response, so nothing set later by a component, plugin, or template slips through
- PSR-4, `SubscriberInterface`, DI container (`services/provider.php`)
- English + Slovak (sk-SK) language files

## Installation

1. Download the latest release ZIP from the [Releases](https://github.com/ferino75/plg_system_fgremovegenerator/releases) page.
2. In Joomla admin: **System → Install → Extensions**, upload the ZIP.
3. Enable the plugin: **System → Manage → Plugins → FG - Remove Generator**.
4. Configure mode and header toggles as needed.

## Updates

This extension ships with a Joomla update server (`updates.xml`) pointing at
the `master` branch of this repository, so new versions appear under
**System → Update → Extensions** once installed.

## License

GNU General Public License version 2 or later. See [LICENSE](LICENSE).
