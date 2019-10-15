# Scholar Theme

Scholar is a theme made especially for academics, for publishing papers, articles, books, documentation, their resumé or other content with [Grav](https://getgrav.org/).

This theme started as a clone of Paul Hibbitt's [Learn2 with Git Sync](https://github.com/hibbitts-design/grav-theme-learn2-git-sync)-theme, which is a customized version of the [Learn2](https://github.com/getgrav/grav-theme-learn2)-theme. As this theme decouples and supercedes much of the logic in either, it is rebranded as Scholar.

## Description

An academic-focused theme, for publishing papers, articles, books, documentation, your resumé or other content with Grav.

```
node-sass --watch --source-map true scss/theme.scss css/theme.css
```

## Features

[UPDATE]

### Page Types

Scholar is stricter than most themes in the types of Page Types, that is templates, it offers, and how they must be structured.

## Installation

### Grav Package Manager (Preferred)

The simplest way to install this theme is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal -- also called the command line. From the root of your Grav install type:

    bin/gpm install scholar

This will install the Scholar-theme into your `/user/themes` directory within Grav. Its files can be found under `/your/site/grav/user/themes/scholar`.

## Manual Installation

To install this theme, just download the zip version of this repository and unzip it under `/your/site/grav/user/themes`. Then, rename the folder to `scholar`.

You should now have all the theme files under

    /your/site/grav/user/themes/scholar

## Updating

As development for the Scholar-theme continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating Scholar is easy, and can be done through Grav's GPM system, as well as manually.

### GPM Update (Preferred)

The simplest way to update this theme is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). You can do this by navigating to the root directory of your Grav install using your system's Terminal (also called command-line) and typing the following:

    bin/gpm update scholar

This command will check your Grav-installation to see if your Scholar theme is due for an update. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The theme will automatically update and clear Grav's cache.

### Manual Update

Manually updating Scholar is pretty simple. Here is what you will need to do to get this done:

- Delete the `your/site/user/themes/scholar`-folder.
- Download the new version of the Scholar-theme from either [GitHub](https://github.com/OleVik/grav-theme-scholar) or [GetGrav.org](http://getgrav.org/downloads/themes#extras).
- Unzip the zip-file in `your/site/user/themes` and rename the resulting folder to `scholar`.
- Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/themes`) will remain intact.

## Usage

### Git Sync

The Scholar-theme natively supports the [Git Sync](https://github.com/trilbymedia/grav-plugin-git-sync)-plugin. Before setting up Git Sync, please make sure to remove the `ReadMe.md` file in your Grav site `user` folder (if one exists). This will prevent a possible sync issue when creating a default `ReadMe.md` file in your new Git repository.

If you want to set Scholar as the default theme, you can do so by following these steps:

- Navigate to `/your/site/grav/user/config`.
- Open the **system.yaml** file.
- Change the `theme:` setting to `theme: scholar`.
- Save your changes.
- Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in Terminal and typing `bin/grav clear-cache`.

Once this is done, you should be able to see the new theme on the frontend. Keep in mind any customizations made to the previous theme will not be reflected as all of the theme- and templating-information is now being pulled from the **scholar** folder.

### Components

The Scholar theme uses modular components to let you choose what features you want. These are not the same as as [Modular Pages](https://learn.getgrav.org/16/content/modular) in Grav, but rather standalone Page types. The `components`-setting in the theme's configuration-file is a plain list of names of components to load.

#### Architecture

Each component exists in the theme, in the `/components`-folder, and contains needed templates, a schema, and any assets. Extensions to the theme, or child-themes, can deliver their own components by replicating this structure or overriding the existing structure. For example, the Tufte-article looks like this, in `/components/tufte`:

```
│   schema.yaml
│   tufte.html.twig
│
├───assets
│       tufte.min.css
│
├───partials
│   └───tufte
│           note.html.twig
│
└───shortcodes
        CiteShortcode.php
        NoteShortcode.php
```

Wherein `schema.yaml` holds basic data used for Linked Data and ARIA-attributes:

```yaml
tufte:
  name: tufte
  schema: ScholarlyArticle
```

`tufte.html.twig` defines how a `tufte.md`-file is rendered, `/assets` holds the necessary style in `tufte.min.css`, `/partials` holds template-pieces specific to this template, and `/shortcodes` shortcodes that can be used in `tufte.md`.

## Development

## TODO

- [x] Global search-page, adapting to root templates
  - [x] Meta-search Page (query params)
    - [x] Integrate into Global search-page
  - [ ] Generate data via Enduring, and in Admin
    - [x] Generate static, eg. ekstern.php onPageContentProcessed()
      - [ ] Needs testing with broader collections
  - [ ] Taxonomy
    - [ ] Versions alá Translations-plugin (/lang/version/slug)
  - [x] Make optional
- [x] Book root template
  - [ ] Paged.js, somewhat too niche for general applicability
  - [ ] Render all
  - [ ] Render singular
  - [x] Listing
- [x] Docs keyboard navigation (prev next, accessibility)
  - [x] Design for listing template
- [x] Blog Post template (post.html.twig) extends page.html.twig - does this cause a semantic conflict?
- [x] Responsive styling
- [x] Styles
  - [x] Integrate Type specific variants into common base
  - [x] Minimize conflicts
    - [x] Resolve header and primary color lack of contrast
    - [ ] Across styles
  - [x] Print style
    - [ ] Print all, subset, or collection
  - [x] Remove dependency on color-mod-function
- [x] API standardisation
  - [x] All Page's must implement Linked Data
  - [x] All Page's should implement a REST endpoint? No:
    - Better to test with API-plugin and leave it to that
  - [x] WIP: Determine Schema from templates? Yes
    - [x] Schema must be a separate file, otherwise API-changes are never reflect after editing
    - [x] **Move components into root-level directory, keeping templates, Schema, and assets separate**
      - Theme.css still remains collective
    - [x] Schema-type in ARIA determined dynamically
- [x] Components loader for Page Types (templates)
  - [ ] Separate metadata.html.twig into generalized and specific for templates
  - [ ] Admin: List or checkboxes?
- [ ] Map features to settings
  - [ ] Admin blueprints

### Current

1. Recast as Scholar
   - Maintain Grav-terminology of extensions as Themes or Plugins
     - Customizations _within_ this theme are Styles (color schemes) and Layouts (templates)
     - Extensions can add either, or features not strictly necessary for Scholar "Core"
   - [ ] Implement hierarchical-taxonomy, per https://towardsdatascience.com/https-medium-com-noa-weiss-the-hitchhikers-guide-to-hierarchical-classification-f8428ea1e076
1. Optimize blueprints and languages
   - [x] Optimize fields
   - [x] String-hierarchy in languages.yaml
1. Layouts
   - [x] Must comply with [tota11y](https://github.com/Khan/tota11y)
     - See https://a11yproject.com/resources for ARIA-WAI
   - [x] Optimize templates for docs
   - [x] Add templates for pages
     - [x] https://html5up.net/uploads/demos/future-imperfect/
   - [x] Add templates for book/papers/articles
     - **Structure/Article**:
       - https://w3c.github.io/scholarly-html/ (outdated, but best reasoned)
       - https://github.com/scienceai/scholarly.vernacular.io/blob/master/index.html (deprecated example)
       - https://github.com/thomaspark/pubcss (implements some common formats)
       - https://essepuntato.github.io/papers/rash-peerj2016.html (RASH)
       - https://github.com/rubensworks/ScholarMarkdown (Ruby, but up to date)
       - https://github.com/linkeddata/dokieli (Alternative to Rash, bloated)
       - http://scholarlymarkdown.com/ (outdated, poor semantics)
       - Example HTML structures:
         - https://github.com/scienceai/scholarly.vernacular.io/blob/master/index.html (https://htmlpreview.github.io/?https://raw.githubusercontent.com/scienceai/scholarly.vernacular.io/master/index.html#scienceai)
         - https://github.com/linkeddata/dokieli/blob/master/new
         - https://github.com/linkeddata/dokieli/blob/master/docs
         - https://github.com/edwardtufte/tufte-css/blob/gh-pages/index.html
         - https://github.com/jakobib/hypertext2019/blob/master/template.html (https://github.com/jakobib/hypertext2019/blob/master/metadata.yaml, https://jakobib.github.io/hypertext2019/)
       - RFD generators (https://stackoverflow.com/questions/49252518/what-is-the-relation-between-schema-org-goodrelations-vocabulary-org-and-produc/49255326#49255326):
         - https://github.com/spatie/schema-org
         - https://github.com/pietercolpaert/hardf
     - **Structure/Book**:
       - https://github.com/oreillymedia/HTMLBook/
     - https://github.com/sachsmc/pandoc-journal-templates
     - https://www.overleaf.com/latex/templates/
     - https://latex.org/forum/viewtopic.php?t=26165
     - CSS3 Multiple Column Layout: https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Columns/Using_multi-column_layouts
       - https://github.com/futurepress/epub.js/
       - https://github.com/vivliostyle/vivliostyle.js
       - https://github.com/vivliostyle/vivliostyle-print
     - Print:
       - W3C drafts:
         - https://www.w3.org/TR/css-gcpm-3/
         - https://drafts4.csswg.org/css-gcpm-4/
       - Paged Media: https://www.pagedmedia.org/paged-media-approaches-part-1-of-2/
         - https://gitlab.pagedmedia.org/tools/pagedjs
       - Breaking HTML into pages: https://alistapart.com/article/boom
       - Print Stylessheets: https://www.smashingmagazine.com/2015/01/designing-for-print-with-css/
1. Styles

   - [ ] Must comply with [tota11y](https://github.com/Khan/tota11y)
   - [x] Unify to reduce customizations in core Styles
     - [x] Arctic
     - [x] Dark Ocean
     - [x] Gold
     - [x] Grey
     - [x] Longyearbyen
     - [x] Metal
     - [x] Navy Sunrise
     - [x] Spitsbergen
     - [x] Sunrise
   - [x] Join docs, pages, and book styles into one sheet
     - Color-customization should be available globally, for page-hierarchies, and single pages
   - [x] Add styles for docs
     - Missing GitBooks-like, something like [Simon Halimonov](http://learn.simonhalimonov.de/)
   - [x] Add styles for pages
   - [x] Add styles for book
     - See [GitBook](https://docs.gitbook.com/), [PressBooks](https://pressbooks.com/themes/)

1. Extensions
   - [ ] Document modular-approach: Scholar as Core
   - [ ] Zen Editor
     - WYSIWYG, not WYSIWYM. Currently only [CKEditor](https://ckeditor.com/docs/ckeditor5/latest/features/markdown.html)?
       - Features like [Gutenberg](https://wordpress.org/gutenberg/), [VisualEditor](https://www.mediawiki.org/wiki/Extension:VisualEditor)?
     - Lightweight Admin-implementation
   - [ ] Port [PAW](https://github.com/OleVik/personal-academic-website) as personal page
   - [ ] Implement a PowerPoint-equivalent using [Fullpage](https://github.com/OleVik/grav-plugin-fullpage)
