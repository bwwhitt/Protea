<?php
/**
 * Server-side rendering of the `core/gallery` block.
 *
 * @package WordPress
 */

/**
 * Handles backwards compatibility for Gallery Blocks,
 * whose images feature a `data-id` attribute.
 *
 * Now that the Gallery Block contains inner Image Blocks,
 * we add a custom `data-id` attribute before rendering the gallery
 * so that the Image Block can pick it up in its render_callback.
 *
 * @param array $parsed_block The block being rendered.
 * @return array The migrated block object.
 */
function gutenberg_block_core_gallery_data_id_backcompatibility( $parsed_block ) {
	if ( 'core/gallery' === $parsed_block['blockName'] ) {
		foreach ( $parsed_block['innerBlocks'] as $key => $inner_block ) {
			if ( 'core/image' === $inner_block['blockName'] ) {
				if ( ! isset( $parsed_block['innerBlocks'][ $key ]['attrs']['data-id'] ) && isset( $inner_block['attrs']['id'] ) ) {
					$parsed_block['innerBlocks'][ $key ]['attrs']['data-id'] = esc_attr( $inner_block['attrs']['id'] );
				}
			}
		}
	}

	return $parsed_block;
}

add_filter( 'render_block_data', 'gutenberg_block_core_gallery_data_id_backcompatibility' );

/**
 * Registers the `core/gallery` block on server.
 */
function gutenberg_register_block_core_gallery() {
	register_block_type_from_metadata(
		__DIR__ . '/gallery'
	);
}

add_action( 'init', 'gutenberg_register_block_core_gallery', 20 );
