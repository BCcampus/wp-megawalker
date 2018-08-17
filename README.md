

# WordPress MegaWalker

A custom WordPress nav walker, creates a Bootstrap "MegaMenu" by adding new option fields to the WordPress built in menu manager. 

## Notes
The custom fields and styling required for this to work are already included included in the [bcc-sage](https://github.com/BCcampus/bcc-sage) theme. If you aren't using the `bcc-sage` theme, use [wp-megawalker](https://github.com/alex-418/wp-megawalker) instead. 

## Screenshots 
Coming soon. 

## Installation
`composer require BCcampus/wp-megawalker` after adding this repository to bcc-sage theme's `composer.json` file ```  "repositories": [
                                                                                                     {
                                                                                                       "type": "vcs",
                                                                                                       "url": "https://github.com/BCcampus/wp-megawalker.git"
                                                                                                     }```
## Displaying the Menu
Visit Appearance > Menus in your dashboard. You'll see the following extra fields in your menu editor:
Create and assign a menu to the bcc-sage "primary menu" location. 
 
* **Activate Megamenu** - Turns a menu item into a MegaMenu. 
* **Column Divider** - Adding this to a sub-menu item in your mega menu to start a new column
* **Featured Image** - Pulls the featured image from a page
* **Inline Divider** - Adds inline divider, a horizontal line
* **Description** - Choose to display the built-in Descriptions field, can be turned on in Screen Options (top right of your screen)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Originally based on [WordPress-MegaMenu](https://github.com/Alecaddd/WordPress-MegaMenu) by [Alecaddd](https://github.com/Alecaddd)
