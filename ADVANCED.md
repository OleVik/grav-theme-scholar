# Advanced

## Components and Page Types

The Scholar theme is stricter than most themes in how the Pages and Page Types, that is templates it offers, must be structured. The theme expects Page Types to be declared as high-level structures, with lower-level structures beneath. For example, for a set of Documentation Pages, `docs.html.twig` would be the uppermost template. Below there will be hierarchical folders of Pages, using `page.html.twig` for each.

The theme uses modular components to let you choose what features you want. These are not the same as as [Modular Pages](https://learn.getgrav.org/16/content/modular) in Grav. The `components`-setting in the theme's configuration-file is a plain list of names of Components to load.

Each Component exists in the theme, in the `/components`-folder, and contains needed templates, a schema, and any assets needed to render it. Extensions to the theme, or child-themes, can deliver their own Components by replicating this structure or overriding the existing structure. For example, the Tufte-article looks like this, in `/components/tufte`:

```
│  schema.yaml
│  tufte.html.twig
├──assets/
│    tufte.min.css
├──partials/
│   └──tufte
│        note.html.twig
└──shortcodes/
      CiteShortcode.php
      NoteShortcode.php
```

Wherein `schema.yaml` holds basic data used for Linked Data and ARIA-attributes:

```yaml
tufte:
  name: tufte
  schema: ScholarlyArticle
```

`tufte.html.twig` defines how a `tufte.md`-file is rendered, `/components/tufte/assets` holds the necessary style in `tufte.min.css`, `/components/tufte/partials` holds template-pieces specific to this template, and `/components/tufte/shortcodes` the shortcodes that can be used in `tufte.md`.

## Git Sync plugin

The Scholar-theme natively supports the [Git Sync](https://github.com/trilbymedia/grav-plugin-git-sync)-plugin. Before setting up Git Sync, please make sure to remove the `ReadMe.md` file in your Grav site `user` folder if one exists. This will prevent a possible sync issue when creating a default `ReadMe.md` file in your new Git repository.