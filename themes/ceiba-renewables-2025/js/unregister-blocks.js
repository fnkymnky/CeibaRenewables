wp.domReady(() => {
    if (wp.blocks && wp.blocks.unregisterBlockStyle) {
        wp.blocks.unregisterBlockStyle('core/button', 'outline');
        wp.blocks.unregisterBlockStyle('core/button', 'fill');
    }
    wp.blocks.unregisterBlockType('core/quote');
    wp.blocks.unregisterBlockType('core/pullquote');
    wp.blocks.unregisterBlockType('core/verse');
    wp.blocks.unregisterBlockType('core/query');
    wp.blocks.unregisterBlockType('core/archives');
    wp.blocks.unregisterBlockType('core/comments');
    wp.blocks.unregisterBlockType('core/preformatted');
    wp.blocks.unregisterBlockType('core/freeform');
    wp.blocks.unregisterBlockType('core/media-text');
});