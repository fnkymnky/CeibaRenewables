import { __ } from '@wordpress/i18n';
import { Fragment, useMemo } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import {
  InnerBlocks,
  useBlockProps,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
  BlockControls,
  AlignmentControl,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, Button, SelectControl } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'core/heading', 'core/paragraph', 'core/buttons' ];
const TEMPLATE = [
  [ 'core/heading', { level: 2, placeholder: __('Add heading…', 'ceiba') } ],
  [ 'core/paragraph', { placeholder: __('Add content…', 'ceiba') } ],
];

export default function Edit({ attributes, setAttributes }) {
  const { imageId = 0, imageSize = 'large', imageLeft = false } = attributes;

  const blockProps = useBlockProps({
    className: [ 'ceiba-callout', 'is-edit', imageLeft ? 'is-image-left' : 'is-image-right' ].join(' '),
  });

  const media = useSelect(
    (select) => (imageId ? select(coreStore).getMedia(imageId) : null),
    [imageId]
  );

  const imageUrl = useMemo(() => {
    if (!media) return '';
    if (media?.media_details?.sizes?.[imageSize]?.source_url) {
      return media.media_details.sizes[imageSize].source_url;
    }
    return media?.source_url || '';
  }, [media, imageSize]);

  const onSelectImage = (img) => {
    setAttributes({ imageId: img?.id || 0 });
  };

  const clearImage = () => setAttributes({ imageId: 0 });

  return (
    <Fragment>
      <InspectorControls>
        <PanelBody title={__('Layout', 'ceiba')} initialOpen>
          <SelectControl
            label={__('Image position', 'ceiba')}
            value={imageLeft ? 'left' : 'right'}
            options={[
              { label: __('Image on left', 'ceiba'), value: 'left' },
              { label: __('Image on right', 'ceiba'), value: 'right' },
            ]}
            onChange={(v) => setAttributes({ imageLeft: v === 'left' })}
          />
        </PanelBody>
        <PanelBody title={__('Image', 'ceiba')} initialOpen={false}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={onSelectImage}
              allowedTypes={[ 'image' ]}
              value={imageId || undefined}
              render={({ open }) => (
                <div>
                  <Button variant="secondary" onClick={open}>
                    { imageId ? __('Replace image', 'ceiba') : __('Select image', 'ceiba') }
                  </Button>
                  {imageId ? (
                    <Button variant="link" onClick={clearImage} style={{ marginLeft: 8 }}>
                      {__('Clear', 'ceiba')}
                    </Button>
                  ) : null}
                </div>
              )}
            />
          </MediaUploadCheck>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className="ceiba-callout__inner">
          <div className="ceiba-callout__media" aria-hidden={imageId ? undefined : true}>
            {imageId && imageUrl ? (
              <div className="ceiba-callout__media__frame">
                <img className="ceiba-callout__img" src={imageUrl} alt={media?.alt_text || ''} />
              </div>
            ) : (
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={onSelectImage}
                  allowedTypes={[ 'image' ]}
                  value={undefined}
                  render={({ open }) => (
                    <Button variant="secondary" onClick={open}>
                      {__('Select image', 'ceiba')}
                    </Button>
                  )}
                />
              </MediaUploadCheck>
            )}
          </div>

          <div className="ceiba-callout__content">
            <InnerBlocks
              allowedBlocks={ALLOWED_BLOCKS}
              template={TEMPLATE}
              templateLock={false}
            />
          </div>
        </div>
      </div>
    </Fragment>
  );
}
