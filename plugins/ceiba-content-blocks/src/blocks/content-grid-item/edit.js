import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, Button } from '@wordpress/components';

export default function Edit( { attributes, setAttributes } ) {
  const { mediaID, mediaURL, thumbURL, alt, heading, text, tag = 'h3' } = attributes;
  const blockProps = useBlockProps( { className: 'ceiba-content-grid-item' } );

  return (
    <>
      <InspectorControls>
        <PanelBody title="Heading & Image" initialOpen>
          <SelectControl
            label="Heading Level"
            value={ tag }
            options={[
              { label: 'H2', value: 'h2' },
              { label: 'H3', value: 'h3' },
              { label: 'H4', value: 'h4' },
            ]}
            onChange={ (val) => setAttributes({ tag: val }) }
          />
          <TextControl
            label="Image Alt Text"
            value={ alt }
            onChange={ (val) => setAttributes({ alt: val }) }
          />
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="ceiba-content-grid-item__image">
          <MediaUploadCheck>
            <MediaUpload
              onSelect={ ( media ) => {
                setAttributes({
                  mediaID:  media?.id,
                  mediaURL: media?.sizes?.full?.url || media?.url || '',
                  thumbURL: media?.sizes?.thumbnail?.url || media?.sizes?.medium?.url || media?.url || '',
                  alt:      media?.alt || alt || ''
                });
              } }
              allowedTypes={ [ 'image' ] }
              value={ mediaID }
              render={ ( { open } ) => (
                <div className="ceiba-content-grid-item__image-wrap">
                  { (thumbURL || mediaURL) ? (
                    <>
                      <img src={ thumbURL || mediaURL } alt={ alt || '' } loading="lazy" decoding="async" />
                      <div className="ceiba-content-grid-item__image-actions">
                        <Button variant="secondary" onClick={ open }>Replace</Button>
                        <Button variant="link" onClick={ () =>
                          setAttributes({ mediaID: undefined, mediaURL: '', thumbURL: '', alt: '' })
                        }>Remove</Button>
                      </div>
                    </>
                  ) : (
                    <Button variant="primary" onClick={ open }>Select Image</Button>
                  ) }
                </div>
              ) }
            />
          </MediaUploadCheck>
        </div>

        <div className="ceiba-content-grid-item__content">
          <RichText
            tagName={ tag }
            placeholder="Heading…"
            value={ heading }
            onChange={ ( val ) => setAttributes( { heading: val } ) }
            allowedFormats={ [ 'core/bold', 'core/italic' ] }
          />
          <RichText
            tagName="p"
            placeholder="Short paragraph…"
            value={ text }
            onChange={ ( val ) => setAttributes( { text: val } ) }
            allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
          />
        </div>
      </div>
    </>
  );
}
