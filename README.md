# Scholar Theme

Scholar is an academic-focused theme, for publishing papers, articles, books, documentation, your blog, and even your resumé, with [Grav](https://getgrav.org/).

## Features

- Extensible Components, Layouts and template-partials, Styles, API
- Responsive Layouts, multiple Styles
  - Print-friendly
- Performant, light on resources
- Accessible, tested against WCAG AA, Section 508, and best practices
  - Navigable by keyboard and screen readers
  - Readable contrast across Styles
  - Clean, declarative HTML-structure with semantic labels
- Automated Evergreen-browser compatibility
- Compatible with a static setup
- Dynamic functionality for Data, Embed, Print, and Search Pages

A demonstration is available at [OleVik.me/staging/grav-skeleton-scholar](https://olevik.me/staging/grav-skeleton-scholar), and its full contents are on [GitHub](https://github.com/OleVik/grav-skeleton-scholar).

## Usage

### Configuration

| Option              | Default   | Description                                   |
|---------------------|-----------|-----------------------------------------------|
| enabled             | true      | Enable theme                                  |
| style               | metal     | Default Style to load                         |
| toolbar.breadcrumbs | true      | Enable breadcrumbs in toolbar                 |
| toolbar.search      | true      | Enable search-field in toolbar                |
| toolbar.navigation  | true      | Enable navigation-drawer in toolbar           |
| css                 | true      | Load theme's CSS                              |
| js                  | true      | Load theme's JS                               |
| itemize             | true      | Assign indices to paragraphs                  |
| linked_data         | true      | Generated Linked Data                         |
| highlighter         | true      | Highlight code                                |
| highlighter_theme   | enlighter | Theme for highlighter                         |
| components          | [List]    | List of components to enable                  |
| router              | true      | Enable dynamic routes                         |
| routes              | [Dict]    | Key-value list of routes                      |
| api                 | [Dict]    | Hierarchical key-value list of classes to use |
| flexsearch          | [Dict]    | Options for FlexSearch                        |
| flexsearch.enabled  | true      | Enable FlexSearch                             |

## Installation

### Version 3.0.0 and higher

This major-version update targets compatibility with Grav Core 1.7, and also updates and changes some internals.

### Grav Package Manager

The simplest way to install this theme is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal -- also called the command line. From the root of your Grav install type:

    bin/gpm install scholar

This will install the Scholar-theme into your `/user/themes` directory within Grav. Its files can be found under `/your/site/grav/user/themes/scholar`.

## Manual Installation

To install this theme, just download the zip version of this repository and unzip it under `/your/site/grav/user/themes`. Then, rename the folder to `scholar`.

You should now have all the theme files under

    /your/site/grav/user/themes/scholar

This theme started as a clone of Paul Hibbitt's [Learn2 with Git Sync](https://github.com/hibbitts-design/grav-theme-learn2-git-sync)-theme, which is a customized version of the [Learn2](https://github.com/getgrav/grav-theme-learn2)-theme. As this theme decouples and supercedes most of the logic in either, it is rebranded as Scholar. It also goes much further in what a documentation-theme can be and do.

## [Advanced Usage](https://github.com/OleVik/grav-theme-scholar/blob/master/ADVANCED.md)

## [Development](https://github.com/OleVik/grav-theme-scholar/blob/master/DEVELOPMENT.md)

## [Contributing](https://github.com/OleVik/grav-theme-scholar/blob/master/CONTRIBUTING.md)

## TODO

- [ ] Evaluate metadata text-wrapping
- [ ] Search: Replace with engine-agnostic approach, decoupling into a plugin