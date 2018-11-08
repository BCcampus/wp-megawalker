<?php

namespace BCcampus;

class MegaWalker extends \Walker_Nav_Menu {


	public $mege_menu_id;

	public $count;

	public function __construct() {
		$this->mege_menu_id = 0;

		$this->count = 0;
	}

	public function start_lvl( &$output, $depth = 0, $args = [] ) {
		$indent     = str_repeat( "\t", $depth );
		$submenu    = ( $depth > 0 ) ? 'sub-menu' : '';
		$parentmenu = ( $depth === 0 ) ? 'dropdown-menu' : '';
		$output     .= "\n$indent<ul class=\"$parentmenu$submenu depth_$depth\" >\n";

		if ( $this->mege_menu_id !== 0 && $depth === 0 ) {
			$output .= "<li class=\"megamenu-column\"><ul>\n";
		}

	}

	public function end_lvl( &$output, $depth = 0, $args = [] ) {
		if ( $this->mege_menu_id !== 0 && $depth === 0 ) {
			$output .= '</ul></li>';
		}

		$output .= '</ul>';
	}

	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {

		$has_mega_menu      = get_post_meta( $item->ID, 'menu-item-mm-megamenu', true );
		$has_column_divider = get_post_meta( $item->ID, 'menu-item-mm-column-divider', true );
		$has_divider        = get_post_meta( $item->ID, 'menu-item-mm-divider', true );
		$has_feature_image  = get_post_meta( $item->ID, 'menu-item-mm-featured-image', true );
		$has_description    = get_post_meta( $item->ID, 'menu-item-mm-description', true );

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$li_attributes = '';
		$class_names   = $value = '';

		$classes = empty( $item->classes ) ? [] : (array) $item->classes;

		if ( $this->mege_menu_id !== 0 && $this->mege_menu_id !== intval( $item->menu_item_parent ) && $depth === 0 ) {
			$this->mege_menu_id = 0;
		}

		if ( $has_column_divider ) {
			array_push( $classes, 'column-divider' );
			$output .= "</ul></li><li class=\"megamenu-column\"><ul>\n";
		}

		// managing divider: add divider class to an element to get a divider before it.
		if ( $has_divider ) {
			$output .= "<li class=\"divider\"></li>\n";
		}

		if ( $has_mega_menu ) {
			array_push( $classes, 'megamenu' );
			$this->mege_menu_id = $item->ID;
		}

		$classes[] = ( $args->has_children ) ? 'dropdown' : '';
		$classes[] = ( $item->current || $item->current_item_ancestor ) ? 'active' : '';
		$classes[] = 'menu-item-' . $item->ID;
		if ( $depth && $args->has_children ) {
			$classes[] = 'dropdown-submenu';
		}

		if ( $has_feature_image ) {
			array_push( $classes, 'featured-image' );
		}

		if ( $has_description ) {
			array_push( $classes, 'description' );
		}

		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

		$attributes = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
		$attributes .= ( $args->has_children && $depth === 0 ) ? ' class="dropdown-toggle parent nav-link" data-toggle="dropdown"' : ' class="nav-link"';

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';

		// Check if item has featured image
		if ( $has_feature_image && $this->mege_menu_id !== 0 ) {
			$post_id     = url_to_postid( $item->url );
			$item_output .= '<img alt="' . esc_attr( $item->attr_title ) . '" src="' . get_the_post_thumbnail_url( $post_id ) . '"/>';
		}

		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

		// add support for menu item title
		if ( strlen( $item->attr_title ) > 2 ) {
			$item_output .= '<h3 class="tit">' . $item->attr_title . '</h3>';
		}
		// add support for menu item descriptions
		if ( strlen( $item->description ) > 2 ) {
			$item_output .= '</a> <span class="sub">' . $item->description . '</span>';
		}
		$item_output .= ( ( $depth === 0 || 1 ) && $args->has_children ) ? '</a>' : '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) ) {
			$args[0]['has_children'] = ! empty( $children_elements[ $element->$id_field ] );
		} elseif ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}

		$cb_args = array_merge( [ &$output, $element, $depth ], $args );
		call_user_func_array( [ &$this, 'start_el' ], $cb_args );

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ( $max_depth === 0 || $max_depth > $depth + 1 ) && isset( $children_elements[ $id ] ) ) {
			foreach ( $children_elements[ $id ] as $child ) {
				if ( ! isset( $newlevel ) ) {
					$newlevel = true;
					//start the child delimiter
					$cb_args = array_merge( [ &$output, $depth ], $args );
					call_user_func_array( [ &$this, 'start_lvl' ], $cb_args );
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset( $newlevel ) && $newlevel ) {
			//end the child delimiter
			$cb_args = array_merge( [ &$output, $depth ], $args );
			call_user_func_array( [ &$this, 'end_lvl' ], $cb_args );
		}

		//end this element
		$cb_args = array_merge( [ &$output, $element, $depth ], $args );
		call_user_func_array( [ &$this, 'end_el' ], $cb_args );
	}
}
