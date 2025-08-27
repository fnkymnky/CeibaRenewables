import { __ } from '@wordpress/i18n';
import { useMemo, useState } from '@wordpress/element';
import {
  useBlockProps,
  InspectorControls,
  RichText,
  MediaUpload,
  MediaUploadCheck,
  __experimentalLinkControl as LinkControl,
  BlockControls
} from '@wordpress/block-editor';
import {
  PanelBody,
  Button,
  TextControl,
  ToolbarGroup,
  ToolbarButton,
  FocalPointPicker,
  SelectControl,
  ToggleControl,
  Modal,
  Spinner
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

const disallowTestimonials = (link) =>
  link?.kind === 'post-type' && (link?.type === 'testimonial' || link?.postType === 'testimonial');

export default function Edit({ attributes, setAttributes }) {
  const {
    title, content,
    imageId, imageUrl, imageAlt, imageSide, imageFit, focalPoint,
    enablePrimary, primaryLabel, primaryLink,
    enableSecondary, secondaryLabel, secondaryLink
  } = attributes;

  const blockProps = useBlockProps({ className: `ceiba-csct is-${imageSide}` });
  const [ctaOpen, setCtaOpen] = useState(false);

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

  // Inline styles for preview image fit + focal point
  const objFit = imageFit === 'cover' ? 'cover' : imageFit === 'stretch' ? 'fill' : 'contain';
  const objPos = `${((focalPoint?.x ?? 0.5) * 100).toFixed(2)}% ${((focalPoint?.y ?? 0.5) * 100).toFixed(2)}%`;

  return (
    <>
      <BlockControls>
        <ToolbarGroup>
          <ToolbarButton
            isPressed={imageSide === 'left'}
            onClick={() => setAttributes({ imageSide: 'left' })}
            icon="align-pull-left"
            label={__('Image left', 'ceiba')}
          />
          <ToolbarButton
            isPressed={imageSide === 'right'}
            onClick={() => setAttributes({ imageSide: 'right' })}
            icon="align-pull-right"
            label={__('Image right', 'ceiba')}
          />
        </ToolbarGroup>
        <ToolbarGroup>
          <ToolbarButton
            icon="admin-links"
            onClick={() => setCtaOpen(true)}
            label={__('Edit CTAs', 'ceiba')}
          />
        </ToolbarGroup>
      </BlockControls>

      <InspectorControls>
        <PanelBody title={__('Media', 'ceiba')} initialOpen>
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
                          { label: __('Stretch', 'ceiba'), value: 'stretch' }
                        ]}
                        onChange={(v) => setAttributes({ imageFit: v })}
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
                </div>
              )}
            />
          </MediaUploadCheck>

          <SelectControl
            label={__('Image side', 'ceiba')}
            value={imageSide}
            options={[
              { label: __('Left', 'ceiba'), value: 'left' },
              { label: __('Right', 'ceiba'), value: 'right' }
            ]}
            onChange={(v) => setAttributes({ imageSide: v })}
          />
        </PanelBody>
      </InspectorControls>

      {/* CTA Modal for roomy editing */}
      {ctaOpen && (
        <Modal title={__('Edit Call To Actions', 'ceiba')} onRequestClose={() => setCtaOpen(false)}>
          <div style={{ display: 'grid', gap: 16 }}>
            <section style={{ borderBottom: '1px solid var(--wp-admin-border-color,#ddd)', paddingBottom: 12 }}>
              <ToggleControl
                label={__('Enable primary', 'ceiba')}
                checked={!!enablePrimary}
                onChange={(v) => setAttributes({ enablePrimary: !!v })}
              />
              {enablePrimary && (
                <div style={{ display: 'grid', gap: 8 }}>
                  <TextControl
                    label={__('Primary label', 'ceiba')}
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
            </section>

            <section>
              <ToggleControl
                label={__('Enable secondary', 'ceiba')}
                checked={!!enableSecondary}
                onChange={(v) => setAttributes({ enableSecondary: !!v })}
              />
              {enableSecondary && (
                <div style={{ display: 'grid', gap: 8 }}>
                  <TextControl
                    label={__('Secondary label', 'ceiba')}
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
            </section>

            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 8 }}>
              <Button variant="secondary" onClick={() => setCtaOpen(false)}>{__('Close', 'ceiba')}</Button>
            </div>
          </div>
        </Modal>
      )}

      <div {...blockProps}>
        <div className="ceiba-csct__grid is-editor">
          {imageSide === 'left' && (
            <div className="ceiba-csct__col ceiba-csct__col--media">
              {imageId ? (
                <img src={imageUrl} alt="" style={{ width: '100%', height: '100%', objectFit: objFit, objectPosition: objPos, aspectRatio: '4/3' }} />
              ) : <div className="ph-img" />}
            </div>
          )}

          <div className="ceiba-csct__col ceiba-csct__col--body">
            <RichText
              tagName="h2"
              placeholder={__('Add title…', 'ceiba')}
              value={title}
              allowedFormats={[]}
              onChange={(v) => setAttributes({ title: v })}
              className="ceiba-csct__title"
            />
            <RichText
              tagName="div"
              multiline="p"
              placeholder={__('Write content…', 'ceiba')}
              value={content}
              onChange={(v) => setAttributes({ content: v })}
              className="ceiba-csct__content"
            />

            <div className="ceiba-csct__cta">
              {enablePrimary && <Button variant="primary">{primaryLabel || __('Primary', 'ceiba')}</Button>}
              {enableSecondary && <Button variant="secondary">{secondaryLabel || __('Secondary', 'ceiba')}</Button>}
            </div>
          </div>

          {imageSide === 'right' && (
            <div className="ceiba-csct__col ceiba-csct__col--media">
              {imageId ? (
                <img src={imageUrl} alt="" style={{ width: '100%', height: '100%', objectFit: objFit, objectPosition: objPos, aspectRatio: '4/3' }} />
              ) : <div className="ph-img" />}
            </div>
          )}
        </div>
      </div>
    </>
  );
}
