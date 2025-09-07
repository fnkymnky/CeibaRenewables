import { __ } from '@wordpress/i18n';
import {
  useBlockProps, MediaUpload, InspectorControls, RichText, URLInputButton
} from '@wordpress/block-editor';
import { PanelBody, Button, SelectControl, TextControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

export default function Edit( { attributes, setAttributes } ) {
  const { heading, body, linkText, linkUrl, imgId, imgUrl, imgAlt, layout, ctaVariant } = attributes;
  const blockProps = useBlockProps( { className: `cb-card layout-${ layout }` } );

  const onSelectImage = ( media ) => {
    setAttributes({
      imgId: media?.id || 0,
      imgUrl: media?.sizes?.large?.url || media?.url || '',
      imgAlt: media?.alt || ''
    });
  };

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={ __('Layout', 'ceiba') } initialOpen>
          <SelectControl
            label={ __('Select layout', 'ceiba') }
            value={ layout }
            options={[
              { label: __('Image Left', 'ceiba'), value: 'image-left' },
              { label: __('Image Right','ceiba'), value: 'image-right' },
              { label: __('Image Top',  'ceiba'), value: 'image-top' }
            ]}
            onChange={(value) => setAttributes({ layout: value })}
          />
        </PanelBody>

        <PanelBody title={ __('Link / CTA', 'ceiba') } initialOpen>
          <TextControl
            label={ __('Link text', 'ceiba') }
            value={ linkText }
            onChange={(value) => setAttributes({ linkText: value })}
            placeholder={ __('Read more', 'ceiba') }
          />
          <div style={{ marginTop: 8 }}>
            <URLInputButton
              url={ linkUrl }
              onChange={(url) => setAttributes({ linkUrl: url })}
              label={ __('Select or paste URL', 'ceiba') }
            />
          </div>
          <SelectControl
            label={ __('CTA style', 'ceiba') }
            value={ ctaVariant }
            options={[
              { label: __('Primary (Navy)', 'ceiba'), value: 'primary' },
              { label: __('Accent (Green)', 'ceiba'), value: 'accent' }
            ]}
            onChange={(value) => setAttributes({ ctaVariant: value })}
            help={ __('Accent uses black text for readability on green.', 'ceiba') }
          />
        </PanelBody>
      </InspectorControls>

      <div { ...blockProps }>
        <div className="cb-media">
          { imgUrl ? (
            <img src={ imgUrl } alt={ imgAlt || '' } />
          ) : (
            <MediaUpload
              onSelect={ onSelectImage }
              allowedTypes={ ['image'] }
              value={ imgId }
              render={ ({ open }) => <Button variant="secondary" onClick={ open }>{ __('Choose image', 'ceiba') }</Button> }
            />
          ) }
          { imgUrl && (
            <div className="cb-media-actions">
              <MediaUpload
                onSelect={ onSelectImage }
                allowedTypes={ ['image'] }
                value={ imgId }
                render={ ({ open }) => <Button variant="secondary" onClick={ open }>{ __('Replace image', 'ceiba') }</Button> }
              />
              <Button variant="tertiary" onClick={ () => setAttributes({ imgId: 0, imgUrl: '', imgAlt: '' }) }>
                { __('Remove', 'ceiba') }
              </Button>
            </div>
          ) }
        </div>

        <div className="cb-content">
          <RichText
            tagName="h3"
            value={ heading }
            onChange={ (value) => setAttributes({ heading: value }) }
            placeholder={ __('Add heading', 'ceiba') }
            allowedFormats={[ 'core/bold','core/italic','core/link' ]}
          />
          <RichText
            tagName="div"
            className="cb-body"
            value={ body }
            onChange={ (value) => setAttributes({ body: value }) }
            placeholder={ __('Start writing', 'ceiba') }
            allowedFormats={[ 'core/bold','core/italic','core/underline','core/link','core/strikethrough','core/code' ]}
          />
          {(linkText || linkUrl) && (
            <div className="cb-cta">
              <a
                className={ ctaVariant === 'accent' ? 'is-alt' : 'is-primary' }
                href={ linkUrl || '#' }
                onClick={(e) => !linkUrl && e.preventDefault()}
              >
                { linkText || __('Learn more', 'ceiba') }
              </a>
            </div>
          )}
        </div>
      </div>
    </Fragment>
  );
}

