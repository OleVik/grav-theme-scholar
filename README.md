# Scholar

Scholar is a theme made especially for academics, for publishing papers, articles, books, documentation, their resumé or other content with [Grav](https://getgrav.org/). This theme started as a clone of Paul Hibbitt's [Learn2 with Git Sync](https://github.com/hibbitts-design/grav-theme-learn2-git-sync)-theme, which is a customized version of the [Learn2](https://github.com/getgrav/grav-theme-learn2)-theme. As this theme decouples and supercedes much of the logic in either, it is rebranded as Scholar.

# Installation

Installing the Scholar theme can be done in one of two ways. Our GPM (Grav Package Manager) installation method enables you to quickly and easily install the theme with a simple terminal command, while the manual method enables you to do so via a zip file.

## Features

[UPDATE]

### Supported Page Templates

[UPDATE]

## GPM Installation (Preferred)

The simplest way to install this theme is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install scholar

This will install the Scholar-theme into your `/user/themes` directory within Grav. Its files can be found under `/your/site/grav/user/themes/scholar`.

## Manual Installation

To install this theme, just download the zip version of this repository and unzip it under `/your/site/grav/user/themes`. Then, rename the folder to `scholar`.

You should now have all the theme files under

    /your/site/grav/user/themes/scholar


# Updating

As development for the Scholar-theme continues, new versions may become available that add additional features and functionality, improve compatibility with newer Grav releases, and generally provide a better user experience. Updating Scholar is easy, and can be done through Grav's GPM system, as well as manually.

## GPM Update (Preferred)

The simplest way to update this theme is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm). You can do this by navigating to the root directory of your Grav install using your system's Terminal (also called command-line) and typing the following:

    bin/gpm update scholar

This command will check your Grav-installation to see if your Scholar theme is due for an update. If a newer release is found, you will be asked whether or not you wish to update. To continue, type `y` and hit enter. The theme will automatically update and clear Grav's cache.

## Manual Update

Manually updating Scholar is pretty simple. Here is what you will need to do to get this done:

* Delete the `your/site/user/themes/scholar`-folder.
* Download the new version of the Scholar-theme from either [GitHub](https://github.com/OleVik/grav-theme-scholar) or [GetGrav.org](http://getgrav.org/downloads/themes#extras).
* Unzip the zip-file in `your/site/user/themes` and rename the resulting folder to `scholar`.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in terminal and typing `bin/grav clear-cache`.

> Note: Any changes you have made to any of the files listed under this directory will also be removed and replaced by the new set. Any files located elsewhere (for example a YAML settings file placed in `user/config/themes`) will remain intact.

## Setup

### Git Sync

The Scholar-theme natively supports the [Git Sync](https://github.com/trilbymedia/grav-plugin-git-sync)-plugin. Before setting up Git Sync, please make sure to remove the `ReadMe.md` file in your Grav site `user` folder (if one exists). This will prevent a possible sync issue when creating a default `ReadMe.md` file in your new Git repository.

If you want to set Scholar as the default theme, you can do so by following these steps:

* Navigate to `/your/site/grav/user/config`.
* Open the **system.yaml** file.
* Change the `theme:` setting to `theme: scholar`.
* Save your changes.
* Clear the Grav cache. The simplest way to do this is by going to the root Grav directory in Terminal and typing `bin/grav clear-cache`.

Once this is done, you should be able to see the new theme on the frontend. Keep in mind any customizations made to the previous theme will not be reflected as all of the theme- and templating-information is now being pulled from the **scholar** folder.

## Development

### Recompile CSS from SCSS

To recompile default style using a Sass-compiler, run it on /scss/theme.scss and output to /css-compiled/theme.css, like `node-sass --watch --source-map true scss/theme.scss css/theme.css`. To do the same for custom styles, run it on /scss/custom and output to /css-compiled/custom, like `node-sass --watch --source-map true scss/styles --output css/styles`.

## Todo

### Current

1. Recast as Scholar
    - Maintain Grav-terminology of extensions as Themes or Plugins
        - Customizations _within_ this theme are Styles (color schemes) and Layouts (templates)
        - Extensions can add either, or features not strictly necessary for Scholar "Core"
1. Optimize blueprints and languages
    - [X] Optimize fields
    - [X] String-hierarchy in languages.yaml
1. Layouts
    - [ ] Must comply with [tota11y](https://github.com/Khan/tota11y)
        - See https://a11yproject.com/resources for ARIA-WAI
    - [ ] Optimize templates for docs
    - [ ] Add templates for pages
    - [ ] Add templates for book/papers/articles
1. Styles
    - [ ] Must comply with [tota11y](https://github.com/Khan/tota11y)
    - [ ] Unify to reduce customizations in core Styles
        - [ ] Arctic
        - [ ] Dark Ocean
        - [ ] Gold
        - [ ] Grey
        - [X] Longyearbyen
        - [ ] Metal
        - [ ] Navy Sunrise
        - [ ] Spitsbergen
        - [ ] Sunrise
    - [ ] Join docs, pages, and book styles into one sheet
        - Color-customization should be available globally, for page-hierarchies, and single pages
    - [ ] Add styles for docs
        - Missing GitBooks-like, something like [Simon Halimonov](http://learn.simonhalimonov.de/)
    - [ ] Add styles for pages
    - [ ] Add styles for book
        - See [GitBook](https://docs.gitbook.com/), [PressBooks](https://pressbooks.com/themes/)

### Future

1. Extensions
    - [ ] Document modular-approach: Scholar as Core
    - [ ] Zen Editor
        - WYSIWYG, not WYSIWYM. Currently only [CKEditor](https://ckeditor.com/docs/ckeditor5/latest/features/markdown.html)?
            - Features like [Gutenberg](https://wordpress.org/gutenberg/), [VisualEditor](https://www.mediawiki.org/wiki/Extension:VisualEditor)?
        - Lightweight Admin-implementation
    - [ ] Port [PAW](https://github.com/OleVik/personal-academic-website) as personal page
    - [ ] Implement a PowerPoint-equivalent using [Fullpage](https://github.com/OleVik/grav-plugin-fullpage)
