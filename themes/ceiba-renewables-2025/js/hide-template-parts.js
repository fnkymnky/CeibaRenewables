( function ( wp ) {
    if ( ! wp || ! wp.hooks || ! wp.compose ) {
        return;
    }

    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;

    const withHideTemplateParts = createHigherOrderComponent( ( BlockListBlock ) => {
        return ( props ) => {
            // some builds expose the block name as props.name, some as props.block.name
            const blockName = props.name || ( props.block && props.block.name );

            // Hide core/template-part blocks in the post editor canvas
            if ( blockName === 'core/template-part' ) {
                return null;
            }

            return wp.element.createElement( BlockListBlock, props );
        };
    }, 'withHideTemplateParts' );

    addFilter(
        'editor.BlockListBlock',
        'ceiba/hide-template-parts',
        withHideTemplateParts
    );
} )( window.wp );
