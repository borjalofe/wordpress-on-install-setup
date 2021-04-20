# WordPress On-Install Setup

This script aims at easing the after-install process in which we usually do the same actions every time. As WordPress has its own on-install customization tools, we'll use them.

## Table of Contents

- [WordPress On-Install Setup](#wordpress-on-install-setup)
  - [Table of Contents](#table-of-contents)
  - [Intro](#intro)
  - [Technologies](#technologies)
  - [How to use it](#how-to-use-it)
  - [Features](#features)
  - [Sources](#sources)
  - [Status](#status)
  - [Contact](#contact)

## Intro

You might think that each project is unique and needs its own customization -and somehow you'd be right- but you just need a minute to think and get to a simple conclusion: every time you start a new WordPress project, you do the same actions:

1. Install&Setup your preferred theme
2. Install&Setup your preferred plugins
3. Setup the WordPress site's options

But you can avoid doing this actions by yourself just using the same processes used by WordPress itself: the `wp_install_defaults()` function and `WP CLI`.

In this project, I'm trying to cover both methods so I -and you, by the way- can take advantage of them to automate and speed the first site setup.

## Technologies

WordPress-On-Install is created with:

- PHP
- Shellscript -WP CLI-

## How to use it

1. Get the WordPress files
   1. DO NOT INSTALL WORDPRESS!!!
2. Download the `install.php` file and upload it to the wp-content folder
3. Install WordPress -just do the 5-minutes install process
4. Done!

## Features

This customization currently sets up the following features:

1. Categories setup:
   1. Sets default category to be "General" instead of "Uncategorized"
2. Pages setup:
   1. Sets the default content for the default page
   2. Sets the default page as the home page
   3. Creates a new page and sets it up as the blog page
   4. Sets the Privacy page -and tries to get the content from a "privacy.txt" file-
   5. Sets the Cookies page -and tries to get the content from a "cookies.txt" file-
   6. Sets the About page -and tries to get the content from a "about.txt" file-
   7. Sets the Contact page
3. Options setup:
   1. Enables de Welcome panel -this is specially useful when using this script as part of the multisite's new blog setup-
   2. Sets the permalink structure to post name -which imho is the most user-friendly permalink structure ever-
   3. Sets the language to Spanish
   4. Sets date&time formats to Spanish
   5. Sets the start of week to Monday
   6. Sets the timezone to "Europe/Madrid"
   7. Disables the year/month folder structure inside the uploads folder
   8. Disables smilies

@TODO

1. Categories setup:
   1. Sets other categories
2. Menus setup:
   1. Sets the main menu with:
      1. Homepage
      2. About page
      3. Blog page
      4. Contact page
   2. Sets the footer menu with:
      1. Legal Notice page
      2. Privacy page
      3. Cookies page
3. Options setup:
   1. Tie date&time formats to language selection
   2. Add an Ubuntu-like timezone selector
4. Other:
   1. Add capabilities to get the default setup from git

## Sources

1. My own [Custom WordPress Installation -at GitLab-](https://gitlab.com/borjalofe/custom-wordpress-installation)
2. Some ideas from [my WordPress VVV custom site template -at GitLab-](https://gitlab.com/borjalofe/custom-site-template)

## Status

This project is currently being developed, and works fine with WordPress 5.7.

## Contact

Created by [@borjalofe](https://github.com/borjalofe) - feel free to contact me!
