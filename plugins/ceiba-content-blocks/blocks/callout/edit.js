import { __ } from '@wordpress/i18n';
import {
  useBlockProps,
  InspectorControls,
  MediaUpload,
  MediaUploadCheck,
  __experimentalLinkControl as LinkControl
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  TextareaControl,
  Button,
  ToggleControl,
  FocalPointPicker,
  SelectControl
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

const disallowTestimonials = (link) =>
  link?.kind === 'post-type' && (link?.type === 'testimonial' || link?.postType === 'testimonial');

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    content,
    imageId,
    imageUrl,
    imageAlt,
    focalPoint,
    enablePrimary,
    primaryLabel,
    primaryLink,
    enableSecondary,
    secondaryLabel,
    secondaryLink,
    colorScheme
  } = attributes;

  const blockProps = useBlockProps({ className: `ceiba-callout is-${colorScheme}` });

  const media = useSelect(
    (select) => (imageId ? select(coreStore).getMedia(imageId) : null),
    [imageId]
  );

  const setImage = (media) => {
    if (!media) {
      setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' });
      return;
    }
    setAttributes({
      imageId: media.id,
      imageUrl: media.url,
      imageAlt: media.alt || media.title || ''
    });
  };

  const onPickLink = (which, next) => {
    if (disallowTestimonials(next)) return;
    const key = which === 'primary' ? 'primaryLink' : 'secondaryLink';
    setAttributes({
      [key]: {
        url: next?.url || '',
        id: next?.id || 0,
        kind: next?.kind || '',
        type: next?.type || '',
        opensInNewTab: next?.opensInNewTab || false
      }
    });
  };

  const clearLink = (which) => {
    const key = which === 'primary' ? 'primaryLink' : 'secondaryLink';
    setAttributes({ [key]: { url: '', id: 0, kind: '', type: '', opensInNewTab: false } });
  };

  const objPos = `${((focalPoint?.x ?? 0.5) * 100).toFixed(2)}% ${((focalPoint?.y ?? 0.5) * 100).toFixed(2)}%`;

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Content', 'ceiba')} initialOpen>
          <TextControl
            label={__('Title', 'ceiba')}
            value={title}
            onChange={(v) => setAttributes({ title: v })}
          />
          <TextareaControl
            label={__('Content', 'ceiba')}
            value={content}
            onChange={(v) => setAttributes({ content: v })}
            rows={4}
          />
          <SelectControl
            label={__('Color', 'ceiba')}
            value={colorScheme}
            options={[
              { label: __('Green', 'ceiba'), value: 'green' },
              { label: __('Blue', 'ceiba'), value: 'blue' }
            ]}
            onChange={(v) => setAttributes({ colorScheme: v })}
          />
        </PanelBody>

        <PanelBody title={__('Media', 'ceiba')} initialOpen={false}>
          <MediaUploadCheck>
            <MediaUpload
              onSelect={setImage}
              allowedTypes={['image']}
              value={imageId}
              render={({ open }) => (
                <div style={{ display: 'grid', gap: 8 }}>
                  <Button variant="secondary" onClick={open}>
                    {imageId ? __('Replace image', 'ceiba') : __('Select image', 'ceiba')}
                  </Button>
                  {imageId && (
                    <>
                      <FocalPointPicker
                        url={imageUrl}
                        dimensions={{ width: 1200, height: 800 }}
                        value={focalPoint}
                        onChange={(v) => setAttributes({ focalPoint: v })}
                      />
                      <Button variant="link" onClick={() => setImage(null)}>
                        {__('Remove', 'ceiba')}
                      </Button>
                    </>
                  )}
                </div>
              )}
            />
          </MediaUploadCheck>
        </PanelBody>

        <PanelBody title={__('Primary Button', 'ceiba')} initialOpen={false}>
          <ToggleControl
            label={__('Enable primary', 'ceiba')}
            checked={!!enablePrimary}
            onChange={(v) => setAttributes({ enablePrimary: !!v })}
          />
          {enablePrimary && (
            <div style={{ display: 'grid', gap: 8 }}>
              <TextControl
                label={__('Label', 'ceiba')}
                value={primaryLabel}
                onChange={(v) => setAttributes({ primaryLabel: v })}
              />
              <LinkControl
                value={primaryLink}
                onChange={(v) => onPickLink('primary', v)}
                settings={['opensInNewTab']}
              />
              {!!primaryLink?.url && (
                <Button variant="link" onClick={() => clearLink('primary')}>
                  {__('Clear link', 'ceiba')}
                </Button>
              )}
            </div>
          )}
        </PanelBody>

        <PanelBody title={__('Secondary Button', 'ceiba')} initialOpen={false}>
          <ToggleControl
            label={__('Enable secondary', 'ceiba')}
            checked={!!enableSecondary}
            onChange={(v) => setAttributes({ enableSecondary: !!v })}
          />
          {enableSecondary && (
            <div style={{ display: 'grid', gap: 8 }}>
              <TextControl
                label={__('Label', 'ceiba')}
                value={secondaryLabel}
                onChange={(v) => setAttributes({ secondaryLabel: v })}
              />
              <LinkControl
                value={secondaryLink}
                onChange={(v) => onPickLink('secondary', v)}
                settings={['opensInNewTab']}
              />
              {!!secondaryLink?.url && (
                <Button variant="link" onClick={() => clearLink('secondary')}>
                  {__('Clear link', 'ceiba')}
                </Button>
              )}
            </div>
          )}
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        {imageUrl && (
          <div className="ceiba-callout__media">
            <img src={imageUrl} alt="" style={{ objectFit: 'cover', objectPosition: objPos }} />
          </div>
        )}
        <div className="ceiba-callout__body">
          {title && <h3 className="ceiba-callout__title">{title}</h3>}
          {content && <div className="ceiba-callout__content">{content}</div>}
          <div className="ceiba-callout__actions">
            {enablePrimary && <Button variant="primary">{primaryLabel || __('Primary', 'ceiba')}</Button>}
            {enableSecondary && <Button variant="secondary">{secondaryLabel || __('Secondary', 'ceiba')}</Button>}
          </div>
        </div>
      </div>
    </>
  );
}
