import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button, FocalPointPicker, SelectControl, TextControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
  const { imageId, imageUrl, imageAlt, imageFit = 'cover', focalPoint = { x: 0.5, y: 0.5 }, proportion = 'wide' } = attributes;
  const blockProps = useBlockProps({ className: `ceiba-image-frame is-edit is-${proportion}` });

  const setImage = (media) => {
    if (!media) {
      setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' });
      return;
    }
    setAttributes({ imageId: media.id, imageUrl: media.url, imageAlt: media.alt || media.title || '' });
  };

  const objFit = imageFit === 'cover' ? 'cover' : imageFit === 'stretch' ? 'fill' : 'contain';
  const objPos = `${((focalPoint?.x ?? 0.5) * 100).toFixed(2)}% ${((focalPoint?.y ?? 0.5) * 100).toFixed(2)}%`;

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Image Settings', 'ceiba')} initialOpen>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={setImage}
              allowedTypes={['image']}
              value={imageId}
              render={({ open }) => (
                <Button variant="secondary" onClick={open}>
                  {imageId ? __('Replace image', 'ceiba') : __('Select image', 'ceiba')}
                </Button>
              )}
            />
          </MediaUploadCheck>

          {imageId ? (
            <>
              <FocalPointPicker
                url={imageUrl}
                dimensions={{ width: 1200, height: 800 }}
                value={focalPoint}
                onChange={(fp) => setAttributes({ focalPoint: fp })}
                __nextHasNoMargin
              />
              <SelectControl
                label={__('Image fit', 'ceiba')}
                value={imageFit}
                options={[
                  { label: __('Cover (crop to fill)', 'ceiba'), value: 'cover' },
                  { label: __('Fill (contain)', 'ceiba'), value: 'fill' },
                  { label: __('Stretch', 'ceiba'), value: 'stretch' },
                ]}
                onChange={(v) => setAttributes({ imageFit: v })}
              />
              <SelectControl
                label={__('Proportion', 'ceiba')}
                value={proportion}
                options={[
                  { label: __('Square', 'ceiba'), value: 'square' },
                  { label: __('Slim', 'ceiba'), value: 'slim' },
                  { label: __('Wide', 'ceiba'), value: 'wide' }
                ]}
                onChange={(v) => setAttributes({ proportion: v })}
              />
              <TextControl
                label={__('Alt text', 'ceiba')}
                value={imageAlt || ''}
                onChange={(v) => setAttributes({ imageAlt: v })}
              />
              <Button isDestructive variant="link" onClick={() => setImage(null)}>
                {__('Remove image', 'ceiba')}
              </Button>
            </>
          ) : null}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        {imageUrl ? (
          <img
            className="ceiba-image-frame__img"
            src={imageUrl}
            alt=""
            style={{ width: '100%', objectFit: objFit, objectPosition: objPos }}
          />
        ) : (
          <div className="ceiba-image-frame__placeholder">{__('Select an image…', 'ceiba')}</div>
        )}
      </div>
    </>
  );
}
