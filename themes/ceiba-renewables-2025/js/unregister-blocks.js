wp.domReady(() => {
    if (wp.blocks && wp.blocks.unregisterBlockStyle) {
        wp.blocks.unregisterBlockStyle('core/button', 'outline');
        wp.blocks.unregisterBlockStyle('core/button', 'fill');
    }
    wp.blocks.unregisterBlockType('core/archives');
    wp.blocks.unregisterBlockType('core/calendar');
    wp.blocks.unregisterBlockType('core/categories');
    wp.blocks.unregisterBlockType('core/classic');
    wp.blocks.unregisterBlockType('core/code');
    wp.blocks.unregisterBlockType('core/comments');
    wp.blocks.unregisterBlockType('core/freeform');
    wp.blocks.unregisterBlockType('core/latest-comments');
    wp.blocks.unregisterBlockType('core/latest-posts');
    wp.blocks.unregisterBlockType('core/legacy-widget');
    wp.blocks.unregisterBlockType('core/media-text');
    wp.blocks.unregisterBlockType('core/nextpage');
    wp.blocks.unregisterBlockType('core/preformatted');
    wp.blocks.unregisterBlockType('core/pullquote');
    wp.blocks.unregisterBlockType('core/query');
    wp.blocks.unregisterBlockType('core/quote');
    wp.blocks.unregisterBlockType('core/rss');
    wp.blocks.unregisterBlockType('core/search');
    wp.blocks.unregisterBlockType('core/tag-cloud');
    wp.blocks.unregisterBlockType('core/widget-area');
    wp.blocks.unregisterBlockType('core/verse');

    // TT5 Theme Blocks
    wp.blocks.unregisterBlockType('core/avatar');
    wp.blocks.unregisterBlockType('core/categories');
    wp.blocks.unregisterBlockType('core/loginout');
    wp.blocks.unregisterBlockType('core/post-author');
    wp.blocks.unregisterBlockType('core/post-author-biography');
    wp.blocks.unregisterBlockType('core/post-author-name');
    wp.blocks.unregisterBlockType('core/post-comments-form');
    wp.blocks.unregisterBlockType('core/post-date');
    wp.blocks.unregisterBlockType('core/post-navigation-link');
    wp.blocks.unregisterBlockType('core/post-terms');
    wp.blocks.unregisterBlockType('core/query-title');
    wp.blocks.unregisterBlockType('core/read-more');
    wp.blocks.unregisterBlockType('core/term-description');
});