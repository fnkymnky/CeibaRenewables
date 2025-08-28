import { useMemo, useRef } from '@wordpress/element';
import { useBlockProps, InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, Notice } from '@wordpress/components';

const ALLOWED = [ 'ceiba/content-grid-item' ];

export default function Edit( { attributes, setAttributes } ) {
  const { columns = 3, itemCount = 4, gap = '1.25rem' } = attributes;

  const safeCols = Math.min( Math.max( Number( columns ) || 3, 1 ), 6 );

  // Build the initial template once using the current itemCount (at insert time).
  const initialTemplate = useMemo(
    () => Array.from( { length: itemCount }, () => [ 'ceiba/content-grid-item', {} ] ),
    [] // <-- IMPORTANT: empty deps; never re-create per render
  );

  // We only want to pass a template to InnerBlocks on the very first mount.
  const hasProvidedInitialTemplate = useRef( false );
  const templateForThisRender = hasProvidedInitialTemplate.current ? undefined : initialTemplate;

  const blockProps = useBlockProps( {
    className: `columns-${ safeCols }`,
    style: {
      '--ceiba-grid-gap': gap,
      '--ceiba-grid-columns': safeCols,
    },
  } );

  // After the first render, consider the initial template "used"
  if ( !hasProvidedInitialTemplate.current ) {
    hasProvidedInitialTemplate.current = true;
  }

  return (
    <>
      <InspectorControls>
        <PanelBody title="Layout" initialOpen>
          <RangeControl
            label="Columns"
            min={ 1 }
            max={ 6 }
            value={ safeCols }
            onChange={ (val) => setAttributes({ columns: val }) }
          />
          <SelectControl
            label="Gap"
            value={ gap }
            options={[
              { label: 'Small (0.75rem)', value: '0.75rem' },
              { label: 'Default (1.25rem)', value: '1.25rem' },
              { label: 'Comfortable (2rem)', value: '2rem' },
            ]}
            onChange={ (val) => setAttributes({ gap: val }) }
          />
          <Notice status="info" isDismissible={ false }>
            Use the + button to add items or delete to remove.  
            (The initial “Items” count is applied only when you insert the block.)
          </Notice>
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <InnerBlocks
          allowedBlocks={ ALLOWED }
          template={ templateForThisRender }
          /* no templateLock so editors can add/remove items freely */
          renderAppender={ InnerBlocks.ButtonBlockAppender }
        />
      </div>
    </>
  );
}
