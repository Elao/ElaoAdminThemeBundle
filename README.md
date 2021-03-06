# elao-admin theme bundle

> Twig template set for [elao-admin](https://github.com/Elao/elao-admin)

## Installation

    composer require elao/admin-theme-bundle

## Usage

Yourbase template should extends elao admin theme base:

```twig
{# template/base.html.twig #}
{% extends '@ElaoAdminTheme/base.html.twig' %}

{# HEAD #}

{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('app') }}
{% endblock %}

{% block title 'Demo Admin' %}

{% block logo 'Demo' %}

{# MENUS #}

{% set menu_user = [
    { route: 'profile', label: 'Profil' },
    { url: '/logout', label: 'Déconnexion' },
] %}

{% set menu_primary = [
    { route: 'user_list', label: 'Utilisateurs', root: 'user', icon: 'user' },
    { route: 'bill_list', label: 'Factures', icon: 'bill' },
] %}

{# MOBILE MENU #}

{% set menu_mobile = [
    { label: 'Utilisateurs', root: 'user', children: [
        { label: 'Liste des utilisateurs', branch: 'user_list', children: [
            { route: 'user_list', label: 'Tous les utilisateurs' },
            { route: 'user_list_archived', label: 'Utilisateurs archivés' },
        ] },
        { route: 'user_create', label: 'Nouvel utilisateur' },
    ] },
    { label: 'Factures', route: 'bill_list' },
    { label: 'Profil', route: 'profile',  },
    { label: 'Déconnexion', url: '/logout',  },
] %}
```

### Page

```twig
{# templte/user/base.html.twig #}
{% extends 'base.html.twig' %}

{% set menu_secondary = [
    { route: 'user_list', label: 'Liste des utilisateurs', branch: 'user_list' },
    { route: 'user_create', label: 'Nouvel utilisateur', icon: 'plus' },
]%}
```

```twig
{# templte/user/list.html.twig #}
{% extends 'user/base.html.twig' %}

{% block title %}Utilisateurs{% endblock %}

{% set menu_tertiary = [
    { route: 'user_list', label: 'Tous les utilisateurs' },
    { route: 'user_list_archived', label: 'Utilisateurs archivés' },
]%}

{% block page_content %}
<div class="container">
    {# ... #}
</div>
{% endblock %}
```

### Drop

```twig
{% embed "@ElaoAdminTheme/components/drop.html.twig" with {
   menu: [
       { url: '#show', label: 'Consulter' },
       { url: '#edit', label: 'Éditer' },
       { url: '#delete', label: 'Supprimer' },
   ]
} only %}
    {% block drop_direction 'left' %}
    {% block tooltip_direction 'top' %}
    {% block tooltip_label 'Choisir une action' %}
{% endembed %}
```
