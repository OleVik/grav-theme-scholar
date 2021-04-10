# v3.0.0
## 10-04-2021

1. [](#new)
    * Stable release

# v3.0.0-beta.2
## 12-03-2021

1. [](#new)
    * `render`-variables for `templates/partials/header.html.twig` and `components/docs/partials/docs/toolbar/menu.html.twig`
2. [](#improved)
    * Search-template
    * Expect metadata in `user://data/persist/index.js`
    * Expect full data in `user://data/persist/static/index.full.js`
3. [](#bugfix)
    * API-alignment to use `Grav\Common\Page\Interfaces\PageInterface`
    * ClassNames-getter
    * Patch Router Page-instance

# v3.0.0-beta.1
## 08-03-2021

1. [](#new)
    * Compatibility with Grav Core 1.7
    * Bundler changed from Parcel to Rollup
    * `filesExist` Twig-function
    * Dependencies strictness
2. [](#bugfix)
    * Relocate search-results for correct keyboard-navigation
    * Forcibly cast Linked Data as strings
    * Fall back to `en` for Linked Data
    * Search-mechanisms
    * Remove redundant `aria-checked`-attribute

# v2.2.0
## 28-12-2020

1. [](#improved)
    * Only include Published and Visible Pages in header
    * Tests
2. [](#bugfix)
    * CV-template
    * Slight padding
    * Router

# v2.1.0
## 22-09-2020

1. [](#new)
    * v2.1.0 stable release

# v2.1.0-beta.3
## 25-08-2020

1. [](#bugfix)
    * "Tufte" component: Variable-location

# v2.1.0-beta.2
## 25-08-2020

1. [](#improved)
    * "Tufte" component: Header-links, styling

# v2.1.0-beta.1
## 21-07-2020

1. [](#new)
    * "What Links Here" component

# v2.0.0
## 12-05-2020

1. [](#new)
    * Generalized Listing-template
    * Generalized margin- and side-notes
    * Header enabled in all templates, option to disable in `toolbar.enabled`
    * Related Pages links optimized, option to disable in `related.enabled`
2. [](#improved)
    * Smaller type for related Pages, enabled on mobile
    * Listing fallback for Page.summary()
    * Explicit version constraint for Composer (`>=7.1.3 <7.4`), with extensions
    * README-note about PHP-version
3. [](#bugfix)
    * Heading-borders
    * Related links alignment and size
    * Responsive sizes

# v1.0.4
## 09-05-2020

1. [](#bugfix)
    * Use onGetPageTemplates-event in Admin
    * README-link

# v1.0.3
## 20-02-2020

1. [](#new)
    * Lock version to Grav 1.6.* until 1.7 stabilizes
    * Silently fail on Flex
2. [](#bugfix)
    * Gracefully fail lookups for Blueprints
    * Fallback to Page titles if no menu-property is set
    * Temporarily revert Blueprint-helpers

# v1.0.2
## 28-12-2019

1. [](#improved)
    * Test-coverage
    * Translation-coverage
    * Templates
2. [](#bugfix)
    * Article Search-field

# v1.0.1
## 27-12-2019

1. [](#improved)
    * Next- and previous-linking
    * Cross-browser CSS for anchor and search-field
    * Book-component
2. [](#bugfix)
    * Highlighter
    * Advanced Search link in Docs

# v1.0.0
## 25-12-2019

1. [](#new)
    * Initial public release
