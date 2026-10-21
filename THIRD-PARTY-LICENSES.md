# Third-Party Licenses – EuroKeep

EuroKeep is released under the MIT License. This file lists every third-party dependency of the backend
(Composer, `backend/composer.lock`) and the frontend (npm, `frontend/package-lock.json`) in exactly the
version pinned by the lock files, together with its license. The full, machine-readable list including the
lock pins (npm `integrity` hash, Composer git commit) is in `THIRD-PARTY-LICENSES.csv`.

All licenses below are compatible with distributing EuroKeep under the MIT License. None of them is a strong
copyleft license (no GPL, LGPL, AGPL, SSPL, EUPL, CC-BY-SA, CC-NC). The only weak copyleft license (MPL-2.0)
applies to build tooling that is not part of the distributed application, and to the unmodified Mozilla CA
certificate data shipped in `composer/ca-bundle`.

Generated from the lock files and the installed packages; verified against each package's own `package.json`
/ `composer.json` and LICENSE file.

## Summary

### Backend (Composer)

| License | prod | dev | Compatible with MIT | Obligation |
|---|---:|---:|---|---|
| MIT | 19 | 44 | yes | permissive – keep copyright and license notice |
| BSD-3-Clause | 2 | 28 | yes | permissive – keep notice, no endorsement using the authors' names |

### Frontend (npm)

| License | prod | dev | Compatible with MIT | Obligation |
|---|---:|---:|---|---|
| MIT | 49 | 536 | yes | permissive – keep copyright and license notice |
| ISC | 1 | 42 | yes | permissive – keep copyright and license notice |
| BSD-2-Clause | 1 | 27 | yes | permissive – keep copyright and license notice |
| BSD-3-Clause | 1 | 16 | yes | permissive – keep notice, no endorsement using the authors' names |
| MPL-2.0 | 0 | 12 | yes | file-level weak copyleft – build tool only, not shipped |
| Apache-2.0 | 2 | 8 | yes | permissive – ship license text and NOTICE file (if any) |
| BlueOak-1.0.0 | 0 | 5 | yes | permissive – keep license notice |
| 0BSD | 3 | 0 | yes | permissive – no obligations |
| (CC-BY-4.0 AND MIT) | 3 | 0 | yes | code MIT, icons CC BY 4.0 – attribution required |
| (CC-BY-4.0 AND OFL-1.1 AND MIT) | 1 | 0 | yes | code MIT, icons CC BY 4.0, fonts OFL 1.1 – attribution required |
| CC-BY-4.0 | 0 | 1 | yes | attribution required |
| (Apache-2.0 AND BSD-3-Clause) | 0 | 1 | yes | permissive – keep license texts |
| Python-2.0 | 1 | 0 | yes | permissive (PSF) – keep license notice |
| OFL-1.1 | 1 | 0 | yes | font license – bundling with software allowed, font must not be sold on its own |

*prod* = runtime dependency (shipped), *dev* = build/test tooling only (not shipped).

## Backend – runtime dependencies (shipped in the Docker image)

| Package | Version | License | Source |
|---|---|---|---|
| cakephp/authentication | 4.2.1 | MIT | https://github.com/cakephp/authentication |
| cakephp/cakephp | 5.4.3 | MIT | https://github.com/cakephp/cakephp |
| cakephp/chronos | 3.5.1 | MIT | https://github.com/cakephp/chronos |
| cakephp/migrations | 5.2.6 | MIT | https://github.com/cakephp/migrations |
| cakephp/plugin-installer | 2.0.2 | MIT | https://github.com/cakephp/plugin-installer |
| composer/ca-bundle | 1.5.14 | MIT | https://github.com/composer/ca-bundle |
| laminas/laminas-diactoros | 3.8.0 | BSD-3-Clause | https://github.com/laminas/laminas-diactoros |
| laminas/laminas-httphandlerrunner | 2.14.0 | BSD-3-Clause | https://github.com/laminas/laminas-httphandlerrunner |
| league/container | 5.2.0 | MIT | https://github.com/thephpleague/container |
| mobiledetect/mobiledetectlib | 4.11.0 | MIT | https://github.com/serbanghita/Mobile-Detect |
| psr/clock | 1.0.0 | MIT | https://github.com/php-fig/clock |
| psr/container | 2.0.2 | MIT | https://github.com/php-fig/container |
| psr/event-dispatcher | 1.0.0 | MIT | https://github.com/php-fig/event-dispatcher |
| psr/http-client | 1.0.3 | MIT | https://github.com/php-fig/http-client |
| psr/http-factory | 1.1.0 | MIT | https://github.com/php-fig/http-factory |
| psr/http-message | 2.0 | MIT | https://github.com/php-fig/http-message |
| psr/http-server-handler | 1.0.2 | MIT | https://github.com/php-fig/http-server-handler |
| psr/http-server-middleware | 1.0.2 | MIT | https://github.com/php-fig/http-server-middleware |
| psr/link | 2.0.1 | MIT | https://github.com/php-fig/link |
| psr/log | 3.0.2 | MIT | https://github.com/php-fig/log |
| psr/simple-cache | 3.0.0 | MIT | https://github.com/php-fig/simple-cache |

## Frontend – runtime dependencies

Packages marked *(bundled)* end up in the compiled Angular application (`dist/`); the others are runtime
dependencies of tooling (e.g. the Transloco config loader) and are not part of the browser bundle.

| Package | Version | License | Source |
|---|---|---|---|
| @angular/animations *(bundled)* | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/aria | 22.2.1 | MIT | https://github.com/angular/components |
| @angular/cdk *(bundled)* | 22.2.1 | MIT | https://github.com/angular/components |
| @angular/common *(bundled)* | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/compiler | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/core *(bundled)* | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/forms *(bundled)* | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/material *(bundled)* | 22.2.1 | MIT | https://github.com/angular/components |
| @angular/platform-browser *(bundled)* | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/router *(bundled)* | 22.2.1 | MIT | https://github.com/angular/angular |
| @babel/code-frame (via parse-json) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-identifier (via parse-json) | 7.29.7 | MIT | https://github.com/babel/babel |
| @coreui/angular *(bundled)* | 5.7.34 | MIT | https://github.com/coreui/coreui-angular |
| @coreui/coreui *(bundled)* | 5.9.1 | MIT | https://github.com/coreui/coreui |
| @coreui/icons-angular | 5.7.34 | MIT | https://github.com/coreui/coreui-angular |
| @fontsource/roboto *(bundled)* | 5.3.0 | OFL-1.1 | https://github.com/fontsource/font-files |
| @fortawesome/angular-fontawesome *(bundled)* | 5.1.0 | MIT | https://github.com/FortAwesome/angular-fontawesome |
| @fortawesome/fontawesome-common-types | 7.3.1 | MIT | https://github.com/FortAwesome/Font-Awesome |
| @fortawesome/fontawesome-free *(bundled)* | 7.3.1 | (CC-BY-4.0 AND OFL-1.1 AND MIT) | https://github.com/FortAwesome/Font-Awesome |
| @fortawesome/fontawesome-svg-core *(bundled)* | 7.3.1 | MIT | https://github.com/FortAwesome/Font-Awesome |
| @fortawesome/free-brands-svg-icons *(bundled)* | 7.3.1 | (CC-BY-4.0 AND MIT) | https://github.com/FortAwesome/Font-Awesome |
| @fortawesome/free-regular-svg-icons | 7.3.1 | (CC-BY-4.0 AND MIT) | https://github.com/FortAwesome/Font-Awesome |
| @fortawesome/free-solid-svg-icons *(bundled)* | 7.3.1 | (CC-BY-4.0 AND MIT) | https://github.com/FortAwesome/Font-Awesome |
| @jsverse/transloco *(bundled)* | 8.4.0 | MIT | https://github.com/jsverse/transloco |
| @jsverse/transloco-utils | 8.4.0 | MIT | https://github.com/jsverse/transloco |
| @jsverse/utils *(bundled)* | 1.0.0-beta.5 | MIT | https://github.com/jsverse/utils |
| @ng-select/ng-option-highlight *(bundled)* | 23.6.0 | MIT | https://github.com/ng-select/ng-select |
| @ng-select/ng-select *(bundled)* | 24.2.1 | MIT | https://github.com/ng-select/ng-select |
| @openng/optimus-ui *(bundled)* | 2.0.2 | MIT | https://github.com/openng-org/optimus-ui |
| @openng/optimus-ui-motion *(bundled)* | 2.0.2 | MIT | https://github.com/openng-org/optimus-ui |
| @openng/optimus-ui-styled *(bundled)* | 2.0.2 | MIT | https://github.com/openng-org/optimus-ui |
| @openng/optimus-ui-styles *(bundled)* | 2.0.2 | MIT | https://github.com/openng-org/optimus-ui |
| @openng/optimus-ui-themes *(bundled)* | 2.0.2 | MIT | https://github.com/openng-org/optimus-ui |
| @openng/optimus-ui-utils *(bundled)* | 2.0.2 | MIT | https://github.com/openng-org/optimus-ui |
| @popperjs/core *(bundled)* | 2.11.8 | MIT | https://github.com/popperjs/popper-core |
| @rollup/rollup-linux-arm64-gnu | 4.64.1 | MIT | https://www.npmjs.com/package/@rollup/rollup-linux-arm64-gnu |
| @standard-schema/spec | 1.1.0 | MIT | https://github.com/standard-schema/standard-schema |
| argparse | 2.0.1 | Python-2.0 | https://github.com/nodeca/argparse |
| callsites | 3.1.0 | MIT | https://github.com/sindresorhus/callsites |
| cosmiconfig (via @jsverse/transloco-utils) | 8.3.6 | MIT | https://github.com/cosmiconfig/cosmiconfig |
| dropzone *(bundled)* | 6.3.5 | MIT | https://github.com/enyo/dropzone |
| echarts *(bundled)* | 6.1.0 | Apache-2.0 | https://github.com/apache/echarts |
| entities | 8.1.0 | BSD-2-Clause | https://github.com/fb55/entities |
| error-ex | 1.3.4 | MIT | https://github.com/qix-/node-error-ex |
| import-fresh | 3.3.1 | MIT | https://github.com/sindresorhus/import-fresh |
| is-arrayish | 0.2.1 | MIT | https://github.com/qix-/node-is-arrayish |
| js-tokens (via parse-json) | 4.0.0 | MIT | https://github.com/lydell/js-tokens |
| js-yaml | 4.3.2 | MIT | https://github.com/nodeca/js-yaml |
| json-parse-even-better-errors | 2.3.1 | MIT | https://github.com/npm/json-parse-even-better-errors |
| lines-and-columns | 1.2.4 | MIT | https://github.com/eventualbuddha/lines-and-columns |
| parent-module | 1.0.1 | MIT | https://github.com/sindresorhus/parent-module |
| parse-json | 5.2.0 | MIT | https://github.com/sindresorhus/parse-json |
| parse5 | 8.0.1 | MIT | https://github.com/inikulin/parse5 |
| path-type | 4.0.0 | MIT | https://github.com/sindresorhus/path-type |
| picocolors | 1.1.1 | ISC | https://github.com/alexeyraspopov/picocolors |
| resolve-from | 4.0.0 | MIT | https://github.com/sindresorhus/resolve-from |
| rxjs *(bundled)* | 7.8.2 | Apache-2.0 | https://github.com/reactivex/rxjs |
| tslib (via echarts) *(bundled)* | 2.3.0 | 0BSD | https://github.com/Microsoft/tslib |
| tslib (via zrender) *(bundled)* | 2.3.0 | 0BSD | https://github.com/Microsoft/tslib |
| tslib *(bundled)* | 2.8.1 | 0BSD | https://github.com/Microsoft/tslib |
| zod | 4.6.5 | MIT | https://github.com/colinhacks/zod |
| zone.js *(bundled)* | 0.16.3 | MIT | https://github.com/angular/angular |
| zrender *(bundled)* | 6.1.0 | BSD-3-Clause | https://github.com/ecomfe/zrender |

## Attribution notices required by the licenses

The full license texts of everything shipped in the frontend are delivered with the application as
`/a/3rdpartylicenses.txt` (generated by `ng build`, completed by `frontend/complete-3rdpartylicenses.js`).
The backend Docker image contains the LICENSE file of every Composer package under `vendor/`.

- **Font Awesome Free 7.3.1** by Fonticons, Inc. – https://fontawesome.com – Icons: CC BY 4.0
  (https://creativecommons.org/licenses/by/4.0/), Fonts: SIL OFL 1.1, Code: MIT. "Font Awesome" is a
  Reserved Font Name. Brand icons are trademarks of their respective owners.
- **Roboto** (via @fontsource/roboto 5.3.0) – Copyright The Roboto Project Authors – SIL OFL 1.1.
- **Apache ECharts 6.1.0** – Copyright 2017-2026 The Apache Software Foundation. This product includes
  software developed at The Apache Software Foundation (https://www.apache.org/). Apache-2.0.
  ECharts embeds parts of d3.js – Copyright Mike Bostock – BSD-3-Clause.
- **RxJS 7.8.2** – Apache-2.0.
- **Mozilla CA certificate bundle** (in composer/ca-bundle) – MPL-2.0, unmodified.
- **CakePHP application skeleton** (cakephp/app) – Copyright (c) Cake Software Foundation, Inc. – MIT.
  Several files in `backend/src` and `backend/config` originate from it and keep their original headers.
- **Adminator** (`frontend/src/assets/login/adminator.min.css`) – Copyright (c) 2018 Aigars Silkalns – MIT.
- **Nestable** (styles in `frontend/src/assets/coreui/nestable.scss`) – Copyright (c) 2012 David Bushell –
  dual-licensed BSD / MIT (used under MIT).

## Not part of the distribution: runtime platform

The Docker images are built on official base images that are separate programs with their own licenses
(aggregation, not linked into EuroKeep): Alpine Linux (various, incl. GPL-2.0 for BusyBox / MIT for musl),
PHP 8.5 (PHP License 3.01), nginx (BSD-2-Clause), Node.js 26 (MIT, build stage only), Composer 2 (MIT, build
stage only), MariaDB 11.8 (GPL-2.0, separate container, not distributed with EuroKeep).

## Development dependencies (not shipped)

### Backend (Composer, require-dev)

| Package | Version | License | Source |
|---|---|---|---|
| brick/varexporter | 0.7.0 | MIT | https://github.com/brick/varexporter |
| cakephp/bake | 3.8.1 | MIT | https://github.com/cakephp/bake |
| cakephp/cakephp-codesniffer | 5.3.2 | MIT | https://github.com/cakephp/cakephp-codesniffer |
| cakephp/debug_kit | 5.2.4 | MIT | https://github.com/cakephp/debug_kit |
| cakephp/twig-view | 2.1.2 | MIT | https://github.com/cakephp/twig-view |
| composer/class-map-generator | 1.8.0 | MIT | https://github.com/composer/class-map-generator |
| composer/composer | 2.10.3 | MIT | https://github.com/composer/composer |
| composer/metadata-minifier | 1.0.1 | MIT | https://github.com/composer/metadata-minifier |
| composer/pcre | 3.4.0 | MIT | https://github.com/composer/pcre |
| composer/semver | 3.5.1 | MIT | https://github.com/composer/semver |
| composer/spdx-licenses | 1.6.0 | MIT | https://github.com/composer/spdx-licenses |
| composer/xdebug-handler | 3.0.5 | MIT | https://github.com/composer/xdebug-handler |
| dealerdirect/phpcodesniffer-composer-installer | 1.2.1 | MIT | https://github.com/PHPCSStandards/composer-installer |
| doctrine/sql-formatter | 1.5.4 | MIT | https://github.com/doctrine/sql-formatter |
| jasny/twig-extensions | 1.3.1 | MIT | https://github.com/jasny/twig-extensions |
| justinrainbow/json-schema | 6.13.1 | MIT | https://github.com/jsonrainbow/json-schema |
| marc-mabe/php-enum | 4.7.2 | BSD-3-Clause | https://github.com/marc-mabe/php-enum |
| myclabs/deep-copy | 1.14.0 | MIT | https://github.com/myclabs/DeepCopy |
| nikic/php-parser | 5.9.0 | BSD-3-Clause | https://github.com/nikic/PHP-Parser |
| phar-io/manifest | 2.0.4 | BSD-3-Clause | https://github.com/phar-io/manifest |
| phar-io/version | 3.2.1 | BSD-3-Clause | https://github.com/phar-io/version |
| phpstan/phpdoc-parser | 2.3.6 | MIT | https://github.com/phpstan/phpdoc-parser |
| phpstan/phpstan | 2.3.0 | MIT | https://apihub.com/repos/phpstan/phpstan/zipball/b1f9890d9af53a25c58129e78f0aadd31f90d7f4 |
| phpstan/phpstan-deprecation-rules | 2.0.5 | MIT | https://github.com/phpstan/phpstan-deprecation-rules |
| phpstan/phpstan-strict-rules | 2.1.0 | MIT | https://github.com/phpstan/phpstan-strict-rules |
| phpunit/php-code-coverage | 14.4.1 | BSD-3-Clause | https://github.com/sebastianbergmann/php-code-coverage |
| phpunit/php-file-iterator | 7.0.2 | BSD-3-Clause | https://github.com/sebastianbergmann/php-file-iterator |
| phpunit/php-invoker | 7.0.0 | BSD-3-Clause | https://github.com/sebastianbergmann/php-invoker |
| phpunit/php-text-template | 6.0.1 | BSD-3-Clause | https://github.com/sebastianbergmann/php-text-template |
| phpunit/php-timer | 9.0.0 | BSD-3-Clause | https://github.com/sebastianbergmann/php-timer |
| phpunit/phpunit | 13.4.1 | BSD-3-Clause | https://github.com/sebastianbergmann/phpunit |
| react/promise | 3.3.0 | MIT | https://github.com/reactphp/promise |
| sebastian/cli-parser | 5.0.1 | BSD-3-Clause | https://github.com/sebastianbergmann/cli-parser |
| sebastian/comparator | 8.4.0 | BSD-3-Clause | https://github.com/sebastianbergmann/comparator |
| sebastian/complexity | 6.0.0 | BSD-3-Clause | https://github.com/sebastianbergmann/complexity |
| sebastian/diff | 9.0.1 | BSD-3-Clause | https://github.com/sebastianbergmann/diff |
| sebastian/environment | 9.3.3 | BSD-3-Clause | https://github.com/sebastianbergmann/environment |
| sebastian/exporter | 8.2.1 | BSD-3-Clause | https://github.com/sebastianbergmann/exporter |
| sebastian/file-filter | 1.0.0 | BSD-3-Clause | https://github.com/sebastianbergmann/file-filter |
| sebastian/git-state | 1.0.0 | BSD-3-Clause | https://github.com/sebastianbergmann/git-state |
| sebastian/global-state | 9.0.1 | BSD-3-Clause | https://github.com/sebastianbergmann/global-state |
| sebastian/lines-of-code | 5.0.2 | BSD-3-Clause | https://github.com/sebastianbergmann/lines-of-code |
| sebastian/object-enumerator | 8.1.0 | BSD-3-Clause | https://github.com/sebastianbergmann/object-enumerator |
| sebastian/object-reflector | 6.1.0 | BSD-3-Clause | https://github.com/sebastianbergmann/object-reflector |
| sebastian/recursion-context | 8.0.1 | BSD-3-Clause | https://github.com/sebastianbergmann/recursion-context |
| sebastian/type | 7.0.2 | BSD-3-Clause | https://github.com/sebastianbergmann/type |
| sebastian/version | 7.0.1 | BSD-3-Clause | https://github.com/sebastianbergmann/version |
| seld/jsonlint | 1.12.1 | MIT | https://github.com/Seldaek/jsonlint |
| seld/phar-utils | 1.2.3 | MIT | https://github.com/Seldaek/phar-utils |
| seld/signal-handler | 2.0.2 | MIT | https://github.com/Seldaek/signal-handler |
| slevomat/coding-standard | 8.31.1 | MIT | https://github.com/slevomat/coding-standard |
| squizlabs/php_codesniffer | 4.0.4 | BSD-3-Clause | https://github.com/PHPCSStandards/PHP_CodeSniffer |
| staabm/side-effects-detector | 1.0.5 | MIT | https://github.com/staabm/side-effects-detector |
| symfony/console | 8.1.8 | MIT | https://github.com/symfony/console |
| symfony/deprecation-contracts | 3.7.1 | MIT | https://github.com/symfony/deprecation-contracts |
| symfony/filesystem | 8.1.6 | MIT | https://github.com/symfony/filesystem |
| symfony/finder | 8.1.8 | MIT | https://github.com/symfony/finder |
| symfony/polyfill-ctype | 1.37.0 | MIT | https://github.com/symfony/polyfill-ctype |
| symfony/polyfill-intl-grapheme | 1.43.0 | MIT | https://github.com/symfony/polyfill-intl-grapheme |
| symfony/polyfill-intl-normalizer | 1.43.0 | MIT | https://github.com/symfony/polyfill-intl-normalizer |
| symfony/polyfill-mbstring | 1.43.0 | MIT | https://github.com/symfony/polyfill-mbstring |
| symfony/polyfill-php73 | 1.37.0 | MIT | https://github.com/symfony/polyfill-php73 |
| symfony/polyfill-php80 | 1.43.0 | MIT | https://github.com/symfony/polyfill-php80 |
| symfony/polyfill-php81 | 1.38.1 | MIT | https://github.com/symfony/polyfill-php81 |
| symfony/polyfill-php84 | 1.43.0 | MIT | https://github.com/symfony/polyfill-php84 |
| symfony/polyfill-php85 | 1.43.0 | MIT | https://github.com/symfony/polyfill-php85 |
| symfony/process | 8.1.7 | MIT | https://github.com/symfony/process |
| symfony/service-contracts | 3.7.3 | MIT | https://github.com/symfony/service-contracts |
| symfony/string | 8.1.7 | MIT | https://github.com/symfony/string |
| theseer/tokenizer | 2.0.1 | BSD-3-Clause | https://github.com/theseer/tokenizer |
| twig/markdown-extra | 3.29.0 | MIT | https://github.com/twigphp/markdown-extra |
| twig/twig | 3.30.0 | BSD-3-Clause | https://github.com/twigphp/Twig |

### Frontend (npm, devDependencies incl. transitive)

| Package | Version | License | Source |
|---|---|---|---|
| @ampproject/remapping | 2.3.0 | Apache-2.0 | https://github.com/ampproject/remapping |
| @angular-devkit/architect | 0.2202.1 | MIT | https://github.com/angular/angular-cli |
| @angular-devkit/core | 22.2.1 | MIT | https://github.com/angular/angular-cli |
| @angular-devkit/schematics | 22.2.1 | MIT | https://github.com/angular/angular-cli |
| @angular/build | 22.2.1 | MIT | https://github.com/angular/angular-cli |
| @angular/cli | 22.2.1 | MIT | https://github.com/angular/angular-cli |
| @angular/compiler-cli | 22.2.1 | MIT | https://github.com/angular/angular |
| @angular/localize | 22.2.1 | MIT | https://github.com/angular/angular |
| @babel/code-frame (via @babel/helper-module-imports) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/code-frame (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/code-frame (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/code-frame | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/compat-data (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/compat-data (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/compat-data | 8.0.5 | MIT | https://github.com/babel/babel |
| @babel/core (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/core (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/core (via @angular/compiler-cli) | 8.0.1 | MIT | https://github.com/babel/babel |
| @babel/core (via @angular/localize) | 8.0.1 | MIT | https://github.com/babel/babel |
| @babel/core | 8.0.5 | MIT | https://github.com/babel/babel |
| @babel/generator (via @babel/helper-module-imports) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/generator (via istanbul-lib-instrument) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/generator (via karma-coverage) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/generator | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/helper-compilation-targets (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-compilation-targets (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-compilation-targets | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/helper-globals (via @babel/helper-module-imports) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-globals (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-globals (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-globals | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/helper-module-imports | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-module-transforms (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-module-transforms (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-string-parser (via @babel/helper-module-imports) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-string-parser (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-string-parser (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-string-parser | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-identifier (via @babel/helper-module-imports) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-identifier (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-identifier (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-identifier | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-option (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-option (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helper-validator-option | 8.0.0 | MIT | https://github.com/babel/babel |
| @babel/helpers (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helpers (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/helpers | 8.0.5 | MIT | https://github.com/babel/babel |
| @babel/parser (via @babel/helper-module-imports) | 7.29.9 | MIT | https://github.com/babel/babel |
| @babel/parser (via istanbul-lib-instrument) | 7.29.9 | MIT | https://github.com/babel/babel |
| @babel/parser (via karma-coverage) | 7.29.9 | MIT | https://github.com/babel/babel |
| @babel/parser | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/template (via @babel/helper-module-imports) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/template (via istanbul-lib-instrument) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/template (via karma-coverage) | 7.29.7 | MIT | https://github.com/babel/babel |
| @babel/template | 8.0.0 | MIT | https://github.com/babel/babel |
| @babel/traverse (via @babel/helper-module-imports) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/traverse (via istanbul-lib-instrument) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/traverse (via karma-coverage) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/traverse | 8.0.6 | MIT | https://github.com/babel/babel |
| @babel/types (via @babel/helper-module-imports) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/types (via istanbul-lib-instrument) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/types (via karma-coverage) | 7.29.8 | MIT | https://github.com/babel/babel |
| @babel/types | 8.0.6 | MIT | https://github.com/babel/babel |
| @bufbuild/protobuf | 2.16.0 | (Apache-2.0 AND BSD-3-Clause) | https://github.com/bufbuild/protobuf-es |
| @colors/colors | 1.5.0 | MIT | http://github.com/DABH/colors.js |
| @esbuild/aix-ppc64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/aix-ppc64 |
| @esbuild/android-arm | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/android-arm |
| @esbuild/android-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/android-arm64 |
| @esbuild/android-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/android-x64 |
| @esbuild/darwin-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/darwin-arm64 |
| @esbuild/darwin-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/darwin-x64 |
| @esbuild/freebsd-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/freebsd-arm64 |
| @esbuild/freebsd-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/freebsd-x64 |
| @esbuild/linux-arm | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-arm |
| @esbuild/linux-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-arm64 |
| @esbuild/linux-ia32 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-ia32 |
| @esbuild/linux-loong64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-loong64 |
| @esbuild/linux-mips64el | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-mips64el |
| @esbuild/linux-ppc64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-ppc64 |
| @esbuild/linux-riscv64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-riscv64 |
| @esbuild/linux-s390x | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/linux-s390x |
| @esbuild/linux-x64 | 0.28.2 | MIT | https://github.com/evanw/esbuild |
| @esbuild/netbsd-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/netbsd-arm64 |
| @esbuild/netbsd-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/netbsd-x64 |
| @esbuild/openbsd-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/openbsd-arm64 |
| @esbuild/openbsd-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/openbsd-x64 |
| @esbuild/openharmony-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/openharmony-arm64 |
| @esbuild/sunos-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/sunos-x64 |
| @esbuild/win32-arm64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/win32-arm64 |
| @esbuild/win32-ia32 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/win32-ia32 |
| @esbuild/win32-x64 | 0.28.2 | MIT | https://www.npmjs.com/package/@esbuild/win32-x64 |
| @harperfast/extended-iterable | 1.0.3 | Apache-2.0 | ssh://git@github.com/harperdb/extended-iterable |
| @inquirer/ansi | 2.0.9 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/checkbox | 5.2.6 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/confirm | 6.3.2 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/core | 12.0.4 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/editor | 5.3.4 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/expand | 5.1.6 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/external-editor | 3.0.6 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/figures | 2.0.9 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/input | 5.1.7 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/number | 4.2.4 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/password | 5.2.3 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/prompts | 8.7.2 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/rawlist | 5.3.6 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/search | 4.3.4 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/select | 5.2.6 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type | 4.1.1 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/checkbox) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/core) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/editor) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/expand) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/input) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/number) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/password) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/rawlist) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/search) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @inquirer/type (via @inquirer/select) | 4.2.0 | MIT | https://github.com/SBoudrias/Inquirer.js |
| @istanbuljs/schema | 0.1.6 | MIT | https://github.com/istanbuljs/schema |
| @jridgewell/gen-mapping | 0.3.13 | MIT | https://github.com/jridgewell/sourcemaps |
| @jridgewell/gen-mapping (via @babel/generator) | 0.4.0-beta.0 | MIT | https://github.com/jridgewell/sourcemaps |
| @jridgewell/remapping | 2.3.5 | MIT | https://github.com/jridgewell/sourcemaps |
| @jridgewell/resolve-uri | 3.1.2 | MIT | https://github.com/jridgewell/resolve-uri |
| @jridgewell/sourcemap-codec | 1.6.0 | MIT | https://github.com/jridgewell/sourcemaps |
| @jridgewell/trace-mapping | 0.3.31 | MIT | https://github.com/jridgewell/sourcemaps |
| @jsverse/angular-utils | 1.0.0-beta.6 | MIT | https://github.com/jsverse/utils |
| @jsverse/transloco-keys-manager | 8.1.1 | MIT | https://github.com/jsverse/transloco-keys-manager |
| @listr2/prompt-adapter-inquirer | 4.2.7 | MIT | https://github.com/listr2/listr2 |
| @lmdb/lmdb-darwin-arm64 | 3.5.6 | MIT | https://www.npmjs.com/package/@lmdb/lmdb-darwin-arm64 |
| @lmdb/lmdb-darwin-x64 | 3.5.6 | MIT | https://www.npmjs.com/package/@lmdb/lmdb-darwin-x64 |
| @lmdb/lmdb-linux-arm | 3.5.6 | MIT | https://www.npmjs.com/package/@lmdb/lmdb-linux-arm |
| @lmdb/lmdb-linux-arm64 | 3.5.6 | MIT | https://www.npmjs.com/package/@lmdb/lmdb-linux-arm64 |
| @lmdb/lmdb-linux-x64 | 3.5.6 | MIT | ssh://git@github.com/kriszyp/lmdb-js |
| @lmdb/lmdb-win32-arm64 | 3.5.6 | MIT | https://www.npmjs.com/package/@lmdb/lmdb-win32-arm64 |
| @lmdb/lmdb-win32-x64 | 3.5.6 | MIT | https://www.npmjs.com/package/@lmdb/lmdb-win32-x64 |
| @modelcontextprotocol/core | 2.0.0 | MIT | https://github.com/modelcontextprotocol/typescript-sdk |
| @modelcontextprotocol/server | 2.0.0 | MIT | https://github.com/modelcontextprotocol/typescript-sdk |
| @msgpackr-extract/msgpackr-extract-darwin-arm64 | 3.0.4 | MIT | https://www.npmjs.com/package/@msgpackr-extract/msgpackr-extract-darwin-arm64 |
| @msgpackr-extract/msgpackr-extract-darwin-x64 | 3.0.4 | MIT | https://www.npmjs.com/package/@msgpackr-extract/msgpackr-extract-darwin-x64 |
| @msgpackr-extract/msgpackr-extract-linux-arm | 3.0.4 | MIT | https://www.npmjs.com/package/@msgpackr-extract/msgpackr-extract-linux-arm |
| @msgpackr-extract/msgpackr-extract-linux-arm64 | 3.0.4 | MIT | https://www.npmjs.com/package/@msgpackr-extract/msgpackr-extract-linux-arm64 |
| @msgpackr-extract/msgpackr-extract-linux-x64 | 3.0.4 | MIT | http://github.com/kriszyp/msgpackr-extract |
| @msgpackr-extract/msgpackr-extract-win32-x64 | 3.0.4 | MIT | https://www.npmjs.com/package/@msgpackr-extract/msgpackr-extract-win32-x64 |
| @napi-rs/nice | 1.1.1 | MIT | ssh://git@github.com/Brooooooklyn/nice |
| @napi-rs/nice-android-arm-eabi | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-android-arm-eabi |
| @napi-rs/nice-android-arm64 | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-android-arm64 |
| @napi-rs/nice-darwin-arm64 | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-darwin-arm64 |
| @napi-rs/nice-darwin-x64 | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-darwin-x64 |
| @napi-rs/nice-freebsd-x64 | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-freebsd-x64 |
| @napi-rs/nice-linux-arm-gnueabihf | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-arm-gnueabihf |
| @napi-rs/nice-linux-arm64-gnu | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-arm64-gnu |
| @napi-rs/nice-linux-arm64-musl | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-arm64-musl |
| @napi-rs/nice-linux-ppc64-gnu | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-ppc64-gnu |
| @napi-rs/nice-linux-riscv64-gnu | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-riscv64-gnu |
| @napi-rs/nice-linux-s390x-gnu | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-s390x-gnu |
| @napi-rs/nice-linux-x64-gnu | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-linux-x64-gnu |
| @napi-rs/nice-linux-x64-musl | 1.1.1 | MIT | ssh://git@github.com/Brooooooklyn/nice |
| @napi-rs/nice-openharmony-arm64 | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-openharmony-arm64 |
| @napi-rs/nice-win32-arm64-msvc | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-win32-arm64-msvc |
| @napi-rs/nice-win32-ia32-msvc | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-win32-ia32-msvc |
| @napi-rs/nice-win32-x64-msvc | 1.1.1 | MIT | https://www.npmjs.com/package/@napi-rs/nice-win32-x64-msvc |
| @oxc-parser/binding-android-arm-eabi | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-android-arm-eabi |
| @oxc-parser/binding-android-arm64 | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-android-arm64 |
| @oxc-parser/binding-darwin-arm64 | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-darwin-arm64 |
| @oxc-parser/binding-darwin-x64 | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-darwin-x64 |
| @oxc-parser/binding-freebsd-x64 | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-freebsd-x64 |
| @oxc-parser/binding-linux-arm-gnueabihf | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-arm-gnueabihf |
| @oxc-parser/binding-linux-arm-musleabihf | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-arm-musleabihf |
| @oxc-parser/binding-linux-arm64-gnu | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-arm64-gnu |
| @oxc-parser/binding-linux-arm64-musl | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-arm64-musl |
| @oxc-parser/binding-linux-ppc64-gnu | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-ppc64-gnu |
| @oxc-parser/binding-linux-riscv64-gnu | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-riscv64-gnu |
| @oxc-parser/binding-linux-riscv64-musl | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-riscv64-musl |
| @oxc-parser/binding-linux-s390x-gnu | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-s390x-gnu |
| @oxc-parser/binding-linux-x64-gnu | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-linux-x64-gnu |
| @oxc-parser/binding-linux-x64-musl | 0.150.0 | MIT | https://github.com/oxc-project/oxc |
| @oxc-parser/binding-openharmony-arm64 | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-openharmony-arm64 |
| @oxc-parser/binding-win32-arm64-msvc | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-win32-arm64-msvc |
| @oxc-parser/binding-win32-ia32-msvc | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-win32-ia32-msvc |
| @oxc-parser/binding-win32-x64-msvc | 0.150.0 | MIT | https://www.npmjs.com/package/@oxc-parser/binding-win32-x64-msvc |
| @oxc-project/types (via rolldown) | 0.149.0 | MIT | https://github.com/oxc-project/oxc |
| @oxc-project/types | 0.150.0 | MIT | https://github.com/oxc-project/oxc |
| @parcel/watcher | 2.6.0 | MIT | https://github.com/parcel-bundler/watcher |
| @parcel/watcher-android-arm64 | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-android-arm64 |
| @parcel/watcher-darwin-arm64 | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-darwin-arm64 |
| @parcel/watcher-darwin-x64 | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-darwin-x64 |
| @parcel/watcher-freebsd-x64 | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-freebsd-x64 |
| @parcel/watcher-linux-arm-glibc | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-linux-arm-glibc |
| @parcel/watcher-linux-arm-musl | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-linux-arm-musl |
| @parcel/watcher-linux-arm64-glibc | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-linux-arm64-glibc |
| @parcel/watcher-linux-arm64-musl | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-linux-arm64-musl |
| @parcel/watcher-linux-x64-glibc | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-linux-x64-glibc |
| @parcel/watcher-linux-x64-musl | 2.6.0 | MIT | https://github.com/parcel-bundler/watcher |
| @parcel/watcher-win32-arm64 | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-win32-arm64 |
| @parcel/watcher-win32-x64 | 2.6.0 | MIT | https://www.npmjs.com/package/@parcel/watcher-win32-x64 |
| @phenomnomnominal/tsquery | 6.2.0 | MIT | https://github.com/phenomnomnominal/tsquery |
| @rolldown/binding-android-arm-eabi | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-android-arm-eabi |
| @rolldown/binding-android-arm64 | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-android-arm64 |
| @rolldown/binding-darwin-arm64 | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-darwin-arm64 |
| @rolldown/binding-darwin-x64 | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-darwin-x64 |
| @rolldown/binding-freebsd-x64 | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-freebsd-x64 |
| @rolldown/binding-linux-arm-gnueabihf | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-linux-arm-gnueabihf |
| @rolldown/binding-linux-arm64-gnu | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-linux-arm64-gnu |
| @rolldown/binding-linux-arm64-musl | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-linux-arm64-musl |
| @rolldown/binding-linux-ppc64-gnu | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-linux-ppc64-gnu |
| @rolldown/binding-linux-s390x-gnu | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-linux-s390x-gnu |
| @rolldown/binding-linux-x64-gnu | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-linux-x64-gnu |
| @rolldown/binding-linux-x64-musl | 1.2.8 | MIT | https://github.com/rolldown/rolldown |
| @rolldown/binding-openharmony-arm64 | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-openharmony-arm64 |
| @rolldown/binding-win32-arm64-msvc | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-win32-arm64-msvc |
| @rolldown/binding-win32-x64-msvc | 1.2.8 | MIT | https://www.npmjs.com/package/@rolldown/binding-win32-x64-msvc |
| @rolldown/pluginutils | 1.0.1 | MIT | https://github.com/rolldown/plugins |
| @schematics/angular | 22.2.1 | MIT | https://github.com/angular/angular-cli |
| @socket.io/component-emitter | 3.1.2 | MIT | https://github.com/socketio/emitter |
| @types/cors | 2.8.19 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/esquery | 1.5.4 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/estree | 1.0.9 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/gensync | 1.0.5 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/jasmine | 7.0.0 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/jsesc | 2.5.1 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/node | 26.6.4 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @types/ws | 8.18.2 | MIT | https://github.com/DefinitelyTyped/DefinitelyTyped |
| @vitejs/plugin-basic-ssl | 2.3.0 | MIT | https://github.com/vitejs/vite-plugin-basic-ssl |
| abort-controller | 3.0.0 | MIT | https://github.com/mysticatea/abort-controller |
| accepts | 1.3.8 | MIT | https://github.com/jshttp/accepts |
| agent-base | 9.0.0 | MIT | https://github.com/TooTallNate/proxy-agents |
| ajv | 8.20.0 | MIT | https://github.com/ajv-validator/ajv |
| ajv-formats | 3.0.1 | MIT | https://github.com/ajv-validator/ajv-formats |
| ansi-escapes | 7.3.0 | MIT | https://github.com/sindresorhus/ansi-escapes |
| ansi-regex (via karma) | 5.0.1 | MIT | https://github.com/chalk/ansi-regex |
| ansi-regex (via string-width) | 5.0.1 | MIT | https://github.com/chalk/ansi-regex |
| ansi-regex | 6.4.0 | MIT | https://github.com/chalk/ansi-regex |
| ansi-styles (via chalk-template) | 4.3.0 | MIT | https://github.com/chalk/ansi-styles |
| ansi-styles (via karma) | 4.3.0 | MIT | https://github.com/chalk/ansi-styles |
| ansi-styles | 6.2.3 | MIT | https://github.com/chalk/ansi-styles |
| anymatch | 3.1.3 | ISC | https://github.com/micromatch/anymatch |
| array-back | 6.2.3 | MIT | https://github.com/75lb/array-back |
| balanced-match (via karma-coverage) | 1.0.2 | MIT | https://github.com/juliangruber/balanced-match |
| balanced-match (via karma) | 1.0.2 | MIT | https://github.com/juliangruber/balanced-match |
| balanced-match (via rimraf) | 1.0.2 | MIT | https://github.com/juliangruber/balanced-match |
| balanced-match | 4.0.4 | MIT | https://github.com/juliangruber/balanced-match |
| base64-js | 1.5.1 | MIT | https://github.com/beatgammit/base64-js |
| baseline-browser-mapping | 2.11.27 | Apache-2.0 | https://github.com/web-platform-dx/baseline-browser-mapping |
| beasties | 0.5.4 | Apache-2.0 | https://github.com/danielroe/beasties |
| binary-extensions | 2.3.0 | MIT | https://github.com/sindresorhus/binary-extensions |
| body-parser | 1.20.8 | MIT | https://github.com/expressjs/body-parser |
| boolbase | 1.0.0 | ISC | https://github.com/fb55/boolbase |
| boolbase (via css-select) | 2.0.0 | ISC | https://github.com/fb55/boolbase |
| boolbase (via nth-check) | 2.0.0 | ISC | https://github.com/fb55/boolbase |
| brace-expansion (via karma-coverage) | 1.1.21 | MIT | https://github.com/juliangruber/brace-expansion |
| brace-expansion (via karma) | 1.1.21 | MIT | https://github.com/juliangruber/brace-expansion |
| brace-expansion (via rimraf) | 1.1.21 | MIT | https://github.com/juliangruber/brace-expansion |
| brace-expansion | 5.0.12 | MIT | https://github.com/juliangruber/brace-expansion |
| braces | 3.0.3 | MIT | https://github.com/micromatch/braces |
| browserslist | 4.29.3 | MIT | https://github.com/browserslist/browserslist |
| buffer | 6.0.3 | MIT | https://github.com/feross/buffer |
| buffer-from | 1.1.2 | MIT | https://github.com/LinusU/buffer-from |
| bytes | 3.1.2 | MIT | https://github.com/visionmedia/bytes.js |
| call-bind-apply-helpers | 1.0.2 | MIT | https://github.com/ljharb/call-bind-apply-helpers |
| call-bound | 1.0.4 | MIT | https://github.com/ljharb/call-bound |
| caniuse-lite | 1.0.30001814 | CC-BY-4.0 | https://github.com/browserslist/caniuse-lite |
| chalk (via chalk-template) | 4.1.2 | MIT | https://github.com/chalk/chalk |
| chalk | 5.6.2 | MIT | https://github.com/chalk/chalk |
| chalk-template | 0.4.0 | MIT | https://github.com/chalk/chalk-template |
| chardet | 2.2.0 | MIT | https://github.com/runk/node-chardet |
| cheerio | 1.2.0 | MIT | https://github.com/cheeriojs/cheerio |
| cheerio-select | 2.1.0 | BSD-2-Clause | https://github.com/cheeriojs/cheerio-select |
| chokidar (via karma) | 3.6.0 | MIT | https://github.com/paulmillr/chokidar |
| chokidar | 5.0.0 | MIT | https://github.com/paulmillr/chokidar |
| cli-cursor | 5.0.0 | MIT | https://github.com/sindresorhus/cli-cursor |
| cli-spinners | 3.4.0 | MIT | https://github.com/sindresorhus/cli-spinners |
| cli-table3 | 0.6.1 | MIT | https://github.com/cli-table/cli-table3 |
| cli-truncate | 6.1.1 | MIT | https://github.com/sindresorhus/cli-truncate |
| cli-width | 4.1.0 | ISC | https://github.com/git@github.com:knownasilya/cli-width |
| cliui (via karma) | 7.0.4 | ISC | https://github.com/yargs/cliui |
| cliui | 9.0.1 | ISC | https://github.com/yargs/cliui |
| color-convert | 2.0.1 | MIT | https://github.com/Qix-/color-convert |
| color-name | 1.1.4 | MIT | https://github.com/git@github.com:colorjs/color-name |
| colorjs.io | 0.7.1 | MIT | https://github.com/color-js/color.js |
| colors | 1.4.0 | MIT | http://github.com/Marak/colors.js |
| command-line-args | 6.0.2 | MIT | https://github.com/75lb/command-line-args |
| command-line-usage | 7.0.4 | MIT | https://github.com/75lb/command-line-usage |
| concat-map | 0.0.1 | MIT | https://github.com/substack/node-concat-map |
| connect | 3.7.0 | MIT | https://github.com/senchalabs/connect |
| content-type | 1.0.5 | MIT | https://github.com/jshttp/content-type |
| convert-source-map | 1.9.0 | MIT | https://github.com/thlorenz/convert-source-map |
| convert-source-map (via @babel/core) | 2.0.0 | MIT | https://github.com/thlorenz/convert-source-map |
| convert-source-map (via @angular/localize) | 2.0.0 | MIT | https://github.com/thlorenz/convert-source-map |
| convert-source-map (via @babel/core) | 2.0.0 | MIT | https://github.com/thlorenz/convert-source-map |
| convert-source-map (via istanbul-lib-instrument) | 2.0.0 | MIT | https://github.com/thlorenz/convert-source-map |
| convert-source-map (via karma-coverage) | 2.0.0 | MIT | https://github.com/thlorenz/convert-source-map |
| cookie | 0.7.2 | MIT | https://github.com/jshttp/cookie |
| cors | 2.8.6 | MIT | https://github.com/expressjs/cors |
| cosmiconfig | 9.0.2 | MIT | https://github.com/cosmiconfig/cosmiconfig |
| css-select (via cheerio-select) | 5.2.2 | BSD-2-Clause | https://github.com/fb55/css-select |
| css-select | 7.0.0 | BSD-2-Clause | https://github.com/fb55/css-select |
| css-what (via cheerio-select) | 6.2.2 | BSD-2-Clause | https://github.com/fb55/css-what |
| css-what | 8.0.0 | BSD-2-Clause | https://github.com/fb55/css-what |
| custom-event | 1.0.1 | MIT | https://github.com/webmodules/custom-event |
| date-format | 4.0.14 | MIT | https://github.com/nomiddlename/date-format |
| debug (via body-parser) | 2.6.9 | MIT | https://github.com/visionmedia/debug |
| debug (via connect) | 2.6.9 | MIT | https://github.com/visionmedia/debug |
| debug (via finalhandler) | 2.6.9 | MIT | https://github.com/visionmedia/debug |
| debug | 4.4.3 | MIT | https://github.com/debug-js/debug |
| deep-diff | 1.0.2 | MIT | https://github.com/flitbit/diff |
| depd | 2.0.0 | MIT | https://github.com/dougwilson/nodejs-depd |
| destroy | 1.2.0 | MIT | https://github.com/stream-utils/destroy |
| detect-libc | 2.1.2 | Apache-2.0 | https://github.com/lovell/detect-libc |
| di | 0.0.1 | MIT | https://github.com/vojtajina/node-di |
| dom-serialize | 2.2.1 | MIT | https://github.com/webmodules/dom-serialize |
| dom-serializer (via cheerio) | 2.0.0 | MIT | https://github.com/cheeriojs/dom-serializer |
| dom-serializer (via domutils) | 2.0.0 | MIT | https://github.com/cheeriojs/dom-serializer |
| dom-serializer | 3.1.1 | MIT | https://github.com/cheeriojs/dom-serializer |
| domelementtype | 2.3.0 | BSD-2-Clause | https://github.com/fb55/domelementtype |
| domelementtype (via css-select) | 3.0.0 | BSD-2-Clause | https://github.com/fb55/domelementtype |
| domelementtype (via dom-serializer) | 3.0.0 | BSD-2-Clause | https://github.com/fb55/domelementtype |
| domelementtype (via domhandler) | 3.0.0 | BSD-2-Clause | https://github.com/fb55/domelementtype |
| domelementtype (via htmlparser2) | 3.0.0 | BSD-2-Clause | https://github.com/fb55/domelementtype |
| domhandler (via cheerio-select) | 5.0.3 | BSD-2-Clause | https://github.com/fb55/domhandler |
| domhandler (via cheerio) | 5.0.3 | BSD-2-Clause | https://github.com/fb55/domhandler |
| domhandler (via domutils) | 5.0.3 | BSD-2-Clause | https://github.com/fb55/domhandler |
| domhandler (via parse5-htmlparser2-tree-adapter) | 5.0.3 | BSD-2-Clause | https://github.com/fb55/domhandler |
| domhandler | 6.0.1 | BSD-2-Clause | https://github.com/fb55/domhandler |
| domutils | 3.2.2 | BSD-2-Clause | https://github.com/fb55/domutils |
| domutils (via css-select) | 4.0.2 | BSD-2-Clause | https://github.com/fb55/domutils |
| domutils (via htmlparser2) | 4.0.2 | BSD-2-Clause | https://github.com/fb55/domutils |
| dunder-proto | 1.0.1 | MIT | https://github.com/es-shims/dunder-proto |
| ee-first | 1.1.1 | MIT | https://github.com/jonathanong/ee-first |
| electron-to-chromium | 1.5.448 | ISC | https://github.com/Kilian/electron-to-chromium |
| emoji-regex (via cliui) | 10.6.0 | MIT | https://github.com/mathiasbynens/emoji-regex |
| emoji-regex | 8.0.0 | MIT | https://github.com/mathiasbynens/emoji-regex |
| empathic | 2.1.0 | MIT | https://github.com/lukeed/empathic |
| encodeurl | 1.0.2 | MIT | https://github.com/pillarjs/encodeurl |
| encoding | 0.1.13 | MIT | https://github.com/andris9/encoding |
| encoding-sniffer | 0.2.1 | MIT | https://github.com/fb55/encoding-sniffer |
| engine.io | 6.6.11 | MIT | https://github.com/socketio/socket.io |
| engine.io-parser | 5.2.3 | MIT | https://github.com/socketio/socket.io |
| ent | 2.2.2 | MIT | https://github.com/ljharb/ent |
| entities (via cheerio) | 4.5.0 | BSD-2-Clause | https://github.com/fb55/entities |
| entities (via domutils) | 4.5.0 | BSD-2-Clause | https://github.com/fb55/entities |
| entities (via parse5) | 6.0.1 | BSD-2-Clause | https://github.com/fb55/entities |
| entities (via parse5-htmlparser2-tree-adapter) | 6.0.1 | BSD-2-Clause | https://github.com/fb55/entities |
| entities (via parse5-parser-stream) | 6.0.1 | BSD-2-Clause | https://github.com/fb55/entities |
| entities (via htmlparser2) | 7.0.1 | BSD-2-Clause | https://github.com/fb55/entities |
| env-paths | 2.2.1 | MIT | https://github.com/sindresorhus/env-paths |
| environment | 1.1.0 | MIT | https://github.com/sindresorhus/environment |
| es-define-property | 1.0.1 | MIT | https://github.com/ljharb/es-define-property |
| es-errors | 1.3.0 | MIT | https://github.com/ljharb/es-errors |
| es-object-atoms | 1.1.2 | MIT | https://github.com/ljharb/es-object-atoms |
| esbuild | 0.28.2 | MIT | https://github.com/evanw/esbuild |
| escalade | 3.2.0 | MIT | https://github.com/lukeed/escalade |
| escape-html | 1.0.3 | MIT | https://github.com/component/escape-html |
| esquery | 1.7.0 | BSD-3-Clause | https://github.com/estools/esquery |
| estraverse | 5.3.0 | BSD-2-Clause | http://github.com/estools/estraverse |
| event-target-shim | 5.0.1 | MIT | https://github.com/mysticatea/event-target-shim |
| eventemitter3 | 4.0.7 | MIT | https://github.com/primus/eventemitter3 |
| events | 3.3.0 | MIT | https://github.com/Gozala/events |
| extend | 3.0.2 | MIT | https://github.com/justmoon/node-extend |
| fast-deep-equal | 3.1.3 | MIT | https://github.com/epoberezkin/fast-deep-equal |
| fast-string-truncated-width | 3.0.3 | MIT | https://github.com/fabiospampinato/fast-string-truncated-width |
| fast-string-width | 3.0.2 | MIT | https://github.com/fabiospampinato/fast-string-width |
| fast-uri | 3.1.8 | BSD-3-Clause | https://github.com/fastify/fast-uri |
| fast-wrap-ansi | 0.2.2 | MIT | https://github.com/43081j/fast-wrap-ansi |
| fdir | 6.5.0 | MIT | https://github.com/thecodrr/fdir |
| fill-range | 7.1.1 | MIT | https://github.com/jonschlinkert/fill-range |
| finalhandler | 1.1.2 | MIT | https://github.com/pillarjs/finalhandler |
| find-replace | 5.0.2 | MIT | https://github.com/75lb/find-replace |
| flat | 6.0.1 | BSD-3-Clause | https://github.com/hughsk/flat |
| flatted | 3.4.4 | ISC | https://github.com/WebReflection/flatted |
| flru | 1.0.2 | MIT | https://github.com/lukeed/flru |
| follow-redirects | 1.16.1 | MIT | ssh://git@github.com/follow-redirects/follow-redirects |
| fs-extra | 11.4.0 | MIT | https://github.com/jprichardson/node-fs-extra |
| fs-extra (via streamroller) | 8.1.0 | MIT | https://github.com/jprichardson/node-fs-extra |
| fs.realpath | 1.0.0 | ISC | https://github.com/isaacs/fs.realpath |
| fsevents | 2.3.3 | MIT | https://www.npmjs.com/package/fsevents |
| function-bind | 1.1.2 | MIT | https://github.com/Raynos/function-bind |
| gensync | 1.0.0-beta.2 | MIT | https://github.com/loganfsmyth/gensync |
| get-caller-file | 2.0.5 | ISC | https://github.com/stefanpenner/get-caller-file |
| get-east-asian-width | 1.7.0 | MIT | https://github.com/sindresorhus/get-east-asian-width |
| get-intrinsic | 1.3.0 | MIT | https://github.com/ljharb/get-intrinsic |
| get-proto | 1.0.1 | MIT | https://github.com/ljharb/get-proto |
| gettext-parser | 8.0.0 | MIT | http://github.com/smhg/gettext-parser |
| glob | 13.0.6 | BlueOak-1.0.0 | https://github.com/git@github.com:isaacs/node-glob |
| glob (via karma) | 7.2.3 | ISC | https://github.com/isaacs/node-glob |
| glob (via rimraf) | 7.2.3 | ISC | https://github.com/isaacs/node-glob |
| glob-parent | 5.1.2 | ISC | https://github.com/gulpjs/glob-parent |
| gopd | 1.2.0 | MIT | https://github.com/ljharb/gopd |
| graceful-fs | 4.2.11 | ISC | https://github.com/isaacs/node-graceful-fs |
| has-flag | 4.0.0 | MIT | https://github.com/sindresorhus/has-flag |
| has-symbols | 1.1.0 | MIT | https://github.com/inspect-js/has-symbols |
| has-tostringtag | 1.0.2 | MIT | https://github.com/inspect-js/has-tostringtag |
| hasown | 2.0.4 | MIT | https://github.com/inspect-js/hasOwn |
| hosted-git-info | 10.1.1 | ISC | https://github.com/npm/hosted-git-info |
| html-escaper | 2.0.2 | MIT | https://github.com/WebReflection/html-escaper |
| htmlparser2 (via cheerio) | 10.1.0 | MIT | https://github.com/fb55/htmlparser2 |
| htmlparser2 | 12.0.0 | MIT | https://github.com/fb55/htmlparser2 |
| http-errors | 2.0.1 | MIT | https://github.com/jshttp/http-errors |
| http-proxy | 1.18.1 | MIT | https://github.com/http-party/node-http-proxy |
| https-proxy-agent | 9.1.0 | MIT | https://github.com/TooTallNate/proxy-agents |
| iconv-lite (via body-parser) | 0.4.24 | MIT | https://github.com/ashtuchkin/iconv-lite |
| iconv-lite (via raw-body) | 0.4.24 | MIT | https://github.com/ashtuchkin/iconv-lite |
| iconv-lite (via encoding-sniffer) | 0.6.3 | MIT | https://github.com/ashtuchkin/iconv-lite |
| iconv-lite (via encoding) | 0.6.3 | MIT | https://github.com/ashtuchkin/iconv-lite |
| iconv-lite (via whatwg-encoding) | 0.6.3 | MIT | https://github.com/ashtuchkin/iconv-lite |
| iconv-lite | 0.7.3 | MIT | https://github.com/pillarjs/iconv-lite |
| ieee754 | 1.2.1 | BSD-3-Clause | https://github.com/feross/ieee754 |
| immutable | 5.1.9 | MIT | https://github.com/immutable-js/immutable-js |
| import-meta-resolve | 4.2.0 | MIT | https://github.com/wooorm/import-meta-resolve |
| inflight | 1.0.6 | ISC | https://github.com/npm/inflight |
| inherits | 2.0.4 | ISC | https://github.com/isaacs/inherits |
| is-binary-path | 2.1.0 | MIT | https://github.com/sindresorhus/is-binary-path |
| is-extglob | 2.1.1 | MIT | https://github.com/jonschlinkert/is-extglob |
| is-fullwidth-code-point (via string-width) | 3.0.0 | MIT | https://github.com/sindresorhus/is-fullwidth-code-point |
| is-fullwidth-code-point | 5.1.0 | MIT | https://github.com/sindresorhus/is-fullwidth-code-point |
| is-glob | 4.0.3 | MIT | https://github.com/micromatch/is-glob |
| is-interactive | 2.0.0 | MIT | https://github.com/sindresorhus/is-interactive |
| is-number | 7.0.0 | MIT | https://github.com/jonschlinkert/is-number |
| is-regex | 1.2.1 | MIT | https://github.com/inspect-js/is-regex |
| is-unicode-supported | 2.1.0 | MIT | https://github.com/sindresorhus/is-unicode-supported |
| isbinaryfile | 4.0.10 | MIT | https://github.com/gjtorikian/isBinaryFile |
| isexe | 2.0.0 | ISC | https://github.com/isaacs/isexe |
| istanbul-lib-coverage | 3.2.2 | BSD-3-Clause | ssh://git@github.com/istanbuljs/istanbuljs |
| istanbul-lib-instrument (via karma-coverage) | 5.2.1 | BSD-3-Clause | ssh://git@github.com/istanbuljs/istanbuljs |
| istanbul-lib-instrument | 6.0.3 | BSD-3-Clause | ssh://git@github.com/istanbuljs/istanbuljs |
| istanbul-lib-report | 3.0.1 | BSD-3-Clause | ssh://git@github.com/istanbuljs/istanbuljs |
| istanbul-lib-source-maps | 4.0.1 | BSD-3-Clause | ssh://git@github.com/istanbuljs/istanbuljs |
| istanbul-reports | 3.2.0 | BSD-3-Clause | ssh://git@github.com/istanbuljs/istanbuljs |
| jasmine-core (via karma-jasmine) | 4.6.1 | MIT | https://github.com/jasmine/jasmine |
| jasmine-core | 7.0.2 | MIT | https://github.com/jasmine/jasmine |
| js-tokens | 10.0.0 | MIT | https://github.com/lydell/js-tokens |
| js-tokens (via @babel/helper-module-imports) | 4.0.0 | MIT | https://github.com/lydell/js-tokens |
| js-tokens (via istanbul-lib-instrument) | 4.0.0 | MIT | https://github.com/lydell/js-tokens |
| js-tokens (via karma-coverage) | 4.0.0 | MIT | https://github.com/lydell/js-tokens |
| jsesc | 3.1.0 | MIT | https://github.com/mathiasbynens/jsesc |
| json-schema-traverse | 1.0.0 | MIT | https://github.com/epoberezkin/json-schema-traverse |
| json5 | 2.2.3 | MIT | https://github.com/json5/json5 |
| jsonc-parser | 3.3.1 | MIT | https://github.com/microsoft/node-jsonc-parser |
| jsonfile (via streamroller) | 4.0.0 | MIT | https://github.com/git@github.com:jprichardson/node-jsonfile |
| jsonfile | 6.2.1 | MIT | https://github.com/git@github.com:jprichardson/node-jsonfile |
| karma | 6.4.4 | MIT | https://github.com/karma-runner/karma |
| karma-chrome-launcher | 3.2.0 | MIT | https://github.com/karma-runner/karma-chrome-launcher |
| karma-coverage | 2.2.1 | MIT | https://github.com/karma-runner/karma-coverage |
| karma-jasmine | 5.1.0 | MIT | https://github.com/karma-runner/karma-jasmine |
| karma-jasmine-html-reporter | 2.3.0 | MIT | https://github.com/dfederm/karma-jasmine-html-reporter |
| lightningcss | 1.33.0 | MPL-2.0 | https://github.com/parcel-bundler/lightningcss |
| lightningcss-android-arm64 | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-android-arm64 |
| lightningcss-darwin-arm64 | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-darwin-arm64 |
| lightningcss-darwin-x64 | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-darwin-x64 |
| lightningcss-freebsd-x64 | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-freebsd-x64 |
| lightningcss-linux-arm-gnueabihf | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-linux-arm-gnueabihf |
| lightningcss-linux-arm64-gnu | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-linux-arm64-gnu |
| lightningcss-linux-arm64-musl | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-linux-arm64-musl |
| lightningcss-linux-x64-gnu | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-linux-x64-gnu |
| lightningcss-linux-x64-musl | 1.33.0 | MPL-2.0 | https://github.com/parcel-bundler/lightningcss |
| lightningcss-win32-arm64-msvc | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-win32-arm64-msvc |
| lightningcss-win32-x64-msvc | 1.33.0 | MPL-2.0 | https://www.npmjs.com/package/lightningcss-win32-x64-msvc |
| listr2 | 11.1.0 | MIT | https://github.com/listr2/listr2 |
| lmdb | 3.5.6 | MIT | ssh://git@github.com/kriszyp/lmdb-js |
| lodash | 4.18.1 | MIT | https://github.com/lodash/lodash |
| lodash.camelcase | 4.3.0 | MIT | https://github.com/lodash/lodash |
| log-symbols | 7.0.1 | MIT | https://github.com/sindresorhus/log-symbols |
| log-update | 8.0.0 | MIT | https://github.com/sindresorhus/log-update |
| log4js | 6.9.1 | Apache-2.0 | https://github.com/log4js-node/log4js-node |
| lru-cache | 11.5.3 | BlueOak-1.0.0 | ssh://git@github.com/isaacs/node-lru-cache |
| lru-cache (via istanbul-lib-instrument) | 5.1.1 | ISC | https://github.com/isaacs/node-lru-cache |
| lru-cache (via karma-coverage) | 5.1.1 | ISC | https://github.com/isaacs/node-lru-cache |
| magic-string | 1.4.1 | MIT | https://github.com/Rich-Harris/magic-string |
| make-dir | 4.0.0 | MIT | https://github.com/sindresorhus/make-dir |
| math-intrinsics | 1.1.0 | MIT | https://github.com/es-shims/math-intrinsics |
| media-typer | 0.3.0 | MIT | https://github.com/jshttp/media-typer |
| mime | 2.6.0 | MIT | https://github.com/broofa/mime |
| mime-db | 1.52.0 | MIT | https://github.com/jshttp/mime-db |
| mime-types | 2.1.35 | MIT | https://github.com/jshttp/mime-types |
| mimic-function | 5.0.1 | MIT | https://github.com/sindresorhus/mimic-function |
| minimatch | 10.2.6 | BlueOak-1.0.0 | https://github.com/git@github.com:isaacs/minimatch |
| minimatch (via karma-coverage) | 3.1.5 | ISC | https://github.com/isaacs/minimatch |
| minimatch (via karma) | 3.1.5 | ISC | https://github.com/isaacs/minimatch |
| minimatch (via rimraf) | 3.1.5 | ISC | https://github.com/isaacs/minimatch |
| minimist | 1.2.8 | MIT | https://github.com/minimistjs/minimist |
| minipass | 7.1.3 | BlueOak-1.0.0 | https://github.com/isaacs/minipass |
| mkdirp | 0.5.6 | MIT | https://github.com/substack/node-mkdirp |
| mrmime | 2.0.1 | MIT | https://github.com/lukeed/mrmime |
| ms (via body-parser) | 2.0.0 | MIT | https://github.com/zeit/ms |
| ms (via connect) | 2.0.0 | MIT | https://github.com/zeit/ms |
| ms (via finalhandler) | 2.0.0 | MIT | https://github.com/zeit/ms |
| ms | 2.1.3 | MIT | https://github.com/vercel/ms |
| msgpackr | 1.12.1 | MIT | http://github.com/kriszyp/msgpackr |
| msgpackr-extract | 3.0.4 | MIT | http://github.com/kriszyp/msgpackr-extract |
| mute-stream | 3.0.0 | ISC | https://github.com/npm/mute-stream |
| nanoid | 3.3.20 | MIT | https://github.com/ai/nanoid |
| negotiator | 0.6.3 | MIT | https://github.com/jshttp/negotiator |
| node-addon-api (via lmdb) | 6.1.0 | MIT | https://github.com/nodejs/node-addon-api |
| node-addon-api | 7.1.1 | MIT | https://github.com/nodejs/node-addon-api |
| node-gyp-build-optional-packages | 5.2.2 | MIT | https://github.com/prebuild/node-gyp-build |
| node-releases | 2.0.57 | MIT | https://github.com/chicoxyzzy/node-releases |
| normalize-path | 3.0.0 | MIT | https://github.com/jonschlinkert/normalize-path |
| npm-package-arg | 14.0.0 | ISC | https://github.com/npm/npm-package-arg |
| nth-check (via cheerio-select) | 2.1.1 | BSD-2-Clause | https://github.com/fb55/nth-check |
| nth-check | 3.0.1 | BSD-2-Clause | https://github.com/fb55/nth-check |
| object-assign | 4.1.1 | MIT | https://github.com/sindresorhus/object-assign |
| object-inspect | 1.13.4 | MIT | https://github.com/inspect-js/object-inspect |
| obug | 2.2.1 | MIT | https://github.com/sxzz/obug |
| on-finished (via finalhandler) | 2.3.0 | MIT | https://github.com/jshttp/on-finished |
| on-finished | 2.4.1 | MIT | https://github.com/jshttp/on-finished |
| once | 1.4.0 | ISC | https://github.com/isaacs/once |
| onetime | 7.0.0 | MIT | https://github.com/sindresorhus/onetime |
| ora | 9.4.1 | MIT | https://github.com/sindresorhus/ora |
| ordered-binary | 1.6.2 | MIT | http://github.com/kriszyp/ordered-binary |
| oxc-parser | 0.150.0 | MIT | https://github.com/oxc-project/oxc |
| parse5 (via cheerio) | 7.3.0 | MIT | https://github.com/inikulin/parse5 |
| parse5 (via parse5-htmlparser2-tree-adapter) | 7.3.0 | MIT | https://github.com/inikulin/parse5 |
| parse5 (via parse5-parser-stream) | 7.3.0 | MIT | https://github.com/inikulin/parse5 |
| parse5-html-rewriting-stream | 8.0.1 | MIT | https://github.com/inikulin/parse5 |
| parse5-htmlparser2-tree-adapter | 7.1.0 | MIT | https://github.com/inikulin/parse5 |
| parse5-parser-stream | 7.1.2 | MIT | https://github.com/inikulin/parse5 |
| parse5-sax-parser | 8.0.0 | MIT | https://github.com/inikulin/parse5 |
| parseurl | 1.3.3 | MIT | https://github.com/pillarjs/parseurl |
| path-is-absolute | 1.0.1 | MIT | https://github.com/sindresorhus/path-is-absolute |
| path-scurry | 2.0.2 | BlueOak-1.0.0 | https://github.com/isaacs/path-scurry |
| picomatch (via anymatch) | 2.3.2 | MIT | https://github.com/micromatch/picomatch |
| picomatch (via karma) | 2.3.2 | MIT | https://github.com/micromatch/picomatch |
| picomatch | 4.0.7 | MIT | https://github.com/micromatch/picomatch |
| piscina | 5.3.2 | MIT | https://github.com/piscinajs/piscina |
| postcss | 8.5.29 | MIT | https://github.com/postcss/postcss |
| postcss-media-query-parser | 0.2.3 | MIT | https://github.com/dryoma/postcss-media-query-parser |
| postcss-safe-parser | 7.1.0 | MIT | https://github.com/postcss/postcss-safe-parser |
| proc-log | 7.0.0 | ISC | https://github.com/npm/proc-log |
| process | 0.11.10 | MIT | https://github.com/shtylman/node-process |
| proxy-agent-negotiate | 1.1.0 | MIT | https://github.com/TooTallNate/proxy-agents |
| punycode | 1.4.1 | MIT | https://github.com/bestiejs/punycode.js |
| qjobs | 1.2.0 | MIT | https://github.com/franck34/qjobs |
| qs | 6.16.0 | BSD-3-Clause | https://github.com/ljharb/qs |
| range-parser | 1.3.0 | MIT | https://github.com/jshttp/range-parser |
| raw-body | 2.5.3 | MIT | https://github.com/stream-utils/raw-body |
| readable-stream | 4.7.0 | MIT | https://github.com/nodejs/readable-stream |
| readdirp (via karma) | 3.6.0 | MIT | https://github.com/paulmillr/readdirp |
| readdirp | 5.1.1 | MIT | https://github.com/paulmillr/readdirp |
| reflect-metadata | 0.2.2 | Apache-2.0 | https://github.com/rbuckton/reflect-metadata |
| require-directory | 2.1.1 | MIT | https://github.com/troygoode/node-require-directory |
| require-from-string | 2.0.2 | MIT | https://github.com/floatdrop/require-from-string |
| requires-port | 1.0.0 | MIT | https://github.com/unshiftio/requires-port |
| restore-cursor | 5.1.0 | MIT | https://github.com/sindresorhus/restore-cursor |
| rfdc | 1.4.1 | MIT | https://github.com/davidmarkclements/rfdc |
| rimraf | 3.0.2 | ISC | https://github.com/isaacs/rimraf |
| rolldown | 1.2.8 | MIT | https://github.com/rolldown/rolldown |
| safe-buffer | 5.2.1 | MIT | https://github.com/feross/safe-buffer |
| safe-regex-test | 1.1.0 | MIT | https://github.com/ljharb/safe-regex-test |
| safer-buffer | 2.1.2 | MIT | https://github.com/ChALkeR/safer-buffer |
| sass | 1.105.1 | MIT | https://github.com/sass/dart-sass |
| sass-embedded | 1.104.1 | MIT | https://github.com/sass/embedded-host-node |
| sass-embedded-all-unknown | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-all-unknown |
| sass-embedded-android-arm | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-android-arm |
| sass-embedded-android-arm64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-android-arm64 |
| sass-embedded-android-riscv64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-android-riscv64 |
| sass-embedded-android-x64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-android-x64 |
| sass-embedded-darwin-arm64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-darwin-arm64 |
| sass-embedded-darwin-x64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-darwin-x64 |
| sass-embedded-linux-arm | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-arm |
| sass-embedded-linux-arm64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-arm64 |
| sass-embedded-linux-musl-arm | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-musl-arm |
| sass-embedded-linux-musl-arm64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-musl-arm64 |
| sass-embedded-linux-musl-riscv64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-musl-riscv64 |
| sass-embedded-linux-musl-x64 | 1.104.1 | MIT | https://github.com/sass/embedded-host-node |
| sass-embedded-linux-riscv64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-riscv64 |
| sass-embedded-linux-x64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-linux-x64 |
| sass-embedded-unknown-all | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-unknown-all |
| sass-embedded-win32-arm64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-win32-arm64 |
| sass-embedded-win32-x64 | 1.104.1 | MIT | https://www.npmjs.com/package/sass-embedded-win32-x64 |
| semver (via @babel/core) | 6.3.1 | ISC | https://github.com/npm/node-semver |
| semver (via @babel/helper-compilation-targets) | 6.3.1 | ISC | https://github.com/npm/node-semver |
| semver (via karma-coverage) | 6.3.1 | ISC | https://github.com/npm/node-semver |
| semver | 7.8.5 | ISC | https://github.com/npm/node-semver |
| setprototypeof | 1.2.0 | ISC | https://github.com/wesleytodd/setprototypeof |
| side-channel | 1.1.1 | MIT | https://github.com/ljharb/side-channel |
| side-channel-list | 1.0.1 | MIT | https://github.com/ljharb/side-channel-list |
| side-channel-map | 1.0.1 | MIT | https://github.com/ljharb/side-channel-map |
| side-channel-weakmap | 1.0.2 | MIT | https://github.com/ljharb/side-channel-weakmap |
| signal-exit | 4.1.0 | ISC | https://github.com/tapjs/signal-exit |
| slice-ansi | 9.0.1 | MIT | https://github.com/chalk/slice-ansi |
| socket.io | 4.8.4 | MIT | https://github.com/socketio/socket.io |
| socket.io-adapter | 2.5.8 | MIT | https://github.com/socketio/socket.io |
| socket.io-parser | 4.2.7 | MIT | https://github.com/socketio/socket.io |
| source-map (via istanbul-lib-source-maps) | 0.6.1 | BSD-3-Clause | http://github.com/mozilla/source-map |
| source-map (via karma) | 0.6.1 | BSD-3-Clause | http://github.com/mozilla/source-map |
| source-map (via source-map-support) | 0.6.1 | BSD-3-Clause | http://github.com/mozilla/source-map |
| source-map | 0.8.0 | BSD-3-Clause | ssh://git@github.com/mozilla/source-map |
| source-map-js | 1.2.2 | BSD-3-Clause | https://github.com/7rulnik/source-map-js |
| source-map-support | 0.5.21 | MIT | https://github.com/evanw/node-source-map-support |
| statuses | 1.5.0 | MIT | https://github.com/jshttp/statuses |
| statuses (via http-errors) | 2.0.2 | MIT | https://github.com/jshttp/statuses |
| stdin-discarder | 0.3.2 | MIT | https://github.com/sindresorhus/stdin-discarder |
| streamroller | 3.1.5 | MIT | https://github.com/log4js-node/streamroller |
| string-width | 4.2.3 | MIT | https://github.com/sindresorhus/string-width |
| string-width (via cliui) | 7.2.0 | MIT | https://github.com/sindresorhus/string-width |
| string-width (via cli-truncate) | 8.3.0 | MIT | https://github.com/sindresorhus/string-width |
| string-width (via log-update) | 8.3.0 | MIT | https://github.com/sindresorhus/string-width |
| string-width (via ora) | 8.3.0 | MIT | https://github.com/sindresorhus/string-width |
| string-width (via wrap-ansi) | 8.3.0 | MIT | https://github.com/sindresorhus/string-width |
| string-width (via yargs) | 8.3.0 | MIT | https://github.com/sindresorhus/string-width |
| string_decoder | 1.3.0 | MIT | https://github.com/nodejs/string_decoder |
| strip-ansi (via karma) | 6.0.1 | MIT | https://github.com/chalk/strip-ansi |
| strip-ansi (via string-width) | 6.0.1 | MIT | https://github.com/chalk/strip-ansi |
| strip-ansi | 7.2.0 | MIT | https://github.com/chalk/strip-ansi |
| supports-color | 7.2.0 | MIT | https://github.com/chalk/supports-color |
| supports-color (via sass-embedded) | 8.1.1 | MIT | https://github.com/chalk/supports-color |
| sync-child-process | 1.0.2 | MIT | https://github.com/sass/sync-child-process |
| sync-message-port | 1.2.0 | MIT | https://github.com/sass/sync-message-port |
| table-layout | 4.1.1 | MIT | https://github.com/75lb/table-layout |
| tinyglobby | 0.2.17 | MIT | https://github.com/SuperchupuDev/tinyglobby |
| tmp | 0.2.7 | MIT | https://github.com/raszi/node-tmp |
| to-regex-range | 5.0.1 | MIT | https://github.com/micromatch/to-regex-range |
| toidentifier | 1.0.1 | MIT | https://github.com/component/toidentifier |
| type-is | 1.6.18 | MIT | https://github.com/jshttp/type-is |
| typescript | 6.0.3 | Apache-2.0 | https://github.com/microsoft/TypeScript |
| typical | 7.3.0 | MIT | https://github.com/75lb/typical |
| ua-parser-js | 0.7.41 | MIT | https://github.com/faisalman/ua-parser-js |
| undici | 7.30.0 | MIT | https://github.com/nodejs/undici |
| undici-types | 8.9.0 | MIT | https://github.com/nodejs/undici |
| universalify (via streamroller) | 0.1.2 | MIT | https://github.com/RyanZim/universalify |
| universalify | 2.0.1 | MIT | https://github.com/RyanZim/universalify |
| unpipe | 1.0.0 | MIT | https://github.com/stream-utils/unpipe |
| update-browserslist-db | 1.3.3 | MIT | https://github.com/browserslist/update-db |
| utils-merge | 1.0.1 | MIT | https://github.com/jaredhanson/utils-merge |
| validate-npm-package-name | 8.0.0 | ISC | https://github.com/npm/validate-npm-package-name |
| varint | 6.0.0 | MIT | https://github.com/chrisdickinson/varint |
| vary | 1.1.2 | MIT | https://github.com/jshttp/vary |
| verkit | 0.3.2 | MIT | https://github.com/sxzz/verkit |
| vite | 8.3.0 | MIT | https://github.com/vitejs/vite |
| void-elements | 2.0.1 | MIT | https://github.com/hemanth/void-elements |
| weak-lru-cache | 1.2.2 | MIT | http://github.com/kriszyp/weak-lru-cache |
| whatwg-encoding | 3.1.1 | MIT | https://github.com/jsdom/whatwg-encoding |
| whatwg-mimetype | 4.0.0 | MIT | https://github.com/jsdom/whatwg-mimetype |
| which | 1.3.1 | ISC | https://github.com/isaacs/node-which |
| wordwrapjs | 5.1.1 | MIT | https://github.com/75lb/wordwrapjs |
| wrap-ansi | 10.0.2 | MIT | https://github.com/chalk/wrap-ansi |
| wrap-ansi (via karma) | 7.0.0 | MIT | https://github.com/chalk/wrap-ansi |
| wrap-ansi (via cliui) | 9.0.2 | MIT | https://github.com/chalk/wrap-ansi |
| wrappy | 1.0.2 | ISC | https://github.com/npm/wrappy |
| ws | 8.21.3 | MIT | https://github.com/websockets/ws |
| xxhash-wasm | 1.1.0 | MIT | https://github.com/jungomi/xxhash-wasm |
| y18n | 5.0.8 | ISC | https://github.com/yargs/y18n |
| yallist | 3.1.1 | ISC | https://github.com/isaacs/yallist |
| yargs (via karma) | 16.2.2 | MIT | https://github.com/yargs/yargs |
| yargs | 18.1.0 | MIT | https://github.com/yargs/yargs |
| yargs-parser (via karma) | 20.2.9 | ISC | https://github.com/yargs/yargs-parser |
| yargs-parser | 22.0.0 | ISC | https://github.com/yargs/yargs-parser |
| yoctocolors | 2.2.0 | MIT | https://github.com/sindresorhus/yoctocolors |
