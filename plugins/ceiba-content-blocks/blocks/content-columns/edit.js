import { __, sprintf } from '@wordpress/i18n';
import {
  useBlockProps,
  InspectorControls,
  RichText,
  MediaUpload,
  MediaUploadCheck,
  __experimentalLinkControl as LinkControl,
} from '@wordpress/block-editor';
import {
  PanelBody,
  PanelRow,
  RangeControl,
  TextControl,
  Button,
  ToggleControl,
} from '@wordpress/components';
import { useMemo } from '@wordpress/element';

const disallowTestimonials = (link) =>
  link?.kind === 'post-type' && (link?.type === 'testimonial' || link?.postType === 'testimonial');

// Safe default for legacy content that didn’t have primaryLink yet
const emptyLink = { url: '', id: 0, kind: '', type: '', opensInNewTab: false };

export default function Edit({ attributes, setAttributes }) {
  const {
    title,
    introContent,
    outroContent,
    columnsCount = 3,
    columns = [],
    enablePrimary = false,
    primaryLabel = '',
    primaryLink,
  } = attributes;

  // Always use a safe object for LinkControl; never pass undefined/null
  const safePrimaryLink =
    primaryLink && typeof primaryLink === 'object'
      ? { ...emptyLink, ...primaryLink }
      : { ...emptyLink };

  const hasBg = !!attributes?.backgroundColor || !!attributes?.style?.color?.background || !!attributes?.style?.color?.gradient;
  const alignSuffix = hasBg ? ' alignfull' : (attributes?.align ? ` align${attributes.align}` : '');
  const blockProps = useBlockProps({
    className: `ceiba-mcols is-edit cols-${columnsCount}${alignSuffix}`,
  });

  // Ensure we always have 4 column slots stored, but only render 1..columnsCount
  const cols = useMemo(() => {
    const next = [...(columns || [])];
    for (let i = 0; i < 4; i++) {
      if (!next[i]) next[i] = { imageId: 0, imageUrl: '', imageAlt: '', heading: '', text: '' };
    }
    return next;
  }, [columns]);

  const updateCol = (i, patch) => {
    const next = [...cols];
    next[i] = { ...next[i], ...patch };
    setAttributes({ columns: next });
  };

  const setImage = (i, media) => {
    if (!media) {
      updateCol(i, { imageId: 0, imageUrl: '', imageAlt: '' });
      return;
    }
    updateCol(i, {
      imageId: media.id,
      imageUrl: media.url,
      imageAlt: media.alt || media.title || '',
    });
  };

  const setPrimaryLink = (v) => {
    if (disallowTestimonials(v)) return;
    const next = {
      url: v?.url || '',
      id: v?.id || 0,
      kind: v?.kind || '',
      type: v?.type || '',
      opensInNewTab: !!v?.opensInNewTab,
    };
    setAttributes({ primaryLink: next });
  };

  const onTogglePrimary = (v) => {
    // When enabling, also ensure a safe link object is present to avoid crashes
    if (v && (!primaryLink || typeof primaryLink !== 'object')) {
      setAttributes({ enablePrimary: !!v, primaryLink: { ...emptyLink } });
      return;
    }
    setAttributes({ enablePrimary: !!v });
  };

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Layout', 'ceiba')} initialOpen>
          <RangeControl
            label={__('Number of columns', 'ceiba')}
            value={columnsCount}
            onChange={(val) => setAttributes({ columnsCount: val })}
            min={1}
            max={4}
          />
        </PanelBody>

        <PanelBody title={__('Primary CTA', 'ceiba')} initialOpen={false}>
          <ToggleControl
            label={__('Enable primary button', 'ceiba')}
            checked={!!enablePrimary}
            onChange={onTogglePrimary}
          />
          {enablePrimary && (
            <>
              <TextControl
                label={__('Button label', 'ceiba')}
                value={primaryLabel || ''}
                onChange={(v) => setAttributes({ primaryLabel: v })}
              />
              {/* LinkControl must always receive a valid object */}
              <LinkControl
                value={safePrimaryLink}
                onChange={setPrimaryLink}
                settings={['opensInNewTab']}
              />
            </>
          )}
        </PanelBody>

        {[0, 1, 2, 3].map((i) => (
          <PanelBody key={i} title={sprintf(__('Column %d', 'ceiba'), i + 1)} initialOpen={i === 0}>
            <PanelRow>
              <MediaUploadCheck>
                <MediaUpload
                  onSelect={(m) => setImage(i, m)}
                  allowedTypes={['image']}
                  value={cols[i]?.imageId}
                  render={({ open }) => (
                    <Button variant="secondary" onClick={open}>
                      {cols[i]?.imageId
                        ? __('Replace thumbnail', 'ceiba')
                        : __('Select thumbnail', 'ceiba')}
                    </Button>
                  )}
                />
              </MediaUploadCheck>
            </PanelRow>
            <TextControl
              label={__('Alt text', 'ceiba')}
              value={cols[i]?.imageAlt || ''}
              onChange={(v) => updateCol(i, { imageAlt: v })}
            />
            <TextControl
              label={__('Heading (H3)', 'ceiba')}
              value={cols[i]?.heading || ''}
              onChange={(v) => updateCol(i, { heading: v })}
            />
            <TextControl
              label={__('Short paragraph', 'ceiba')}
              value={cols[i]?.text || ''}
              onChange={(v) => updateCol(i, { text: v })}
            />
            {i >= columnsCount && (
              <p style={{ color: '#777', fontSize: 12 }}>
                {__('This column is hidden because the layout has fewer columns selected.', 'ceiba')}
              </p>
            )}
          </PanelBody>
        ))}
      </InspectorControls>

      <div {...blockProps}>
        <section className="ceiba-mcols__inner" style={{ maxWidth: 'var(--wp--style--global--content-size)', width: '100%', marginLeft: 'auto', marginRight: 'auto' }}>
        <div className="ceiba-mcols__head">
          <RichText
            tagName="h2"
            placeholder={__('Add a title…', 'ceiba')}
            value={title}
            allowedFormats={[]}
            onChange={(v) => setAttributes({ title: v })}
            className="ceiba-mcols__title"
          />
          <RichText
            tagName="div"
            placeholder={__('Intro text…', 'ceiba')}
            value={introContent}
            onChange={(v) => setAttributes({ introContent: v })}
            className="ceiba-mcols__intro"
          />
        </div>

        <div className={`ceiba-mcols__grid cols-${columnsCount}`}>
          {cols.slice(0, columnsCount).map((c, i) => (
            <article key={i} className="ceiba-mcols__item">
              <div className="ceiba-mcols__media">
                {c.imageUrl ? <img className="ceiba-mcols__thumb" src={c.imageUrl} alt="" /> : <div className="ph-thumb" />}
              </div>
              <RichText
                tagName="h3"
                placeholder={__('Heading…', 'ceiba')}
                value={c.heading}
                allowedFormats={[]}
                onChange={(v) => updateCol(i, { heading: v })}
                className="ceiba-mcols__heading"
              />
              <RichText
                tagName="p"
                placeholder={__('Short paragraph…', 'ceiba')}
                value={c.text}
                onChange={(v) => updateCol(i, { text: v })}
                className="ceiba-mcols__text"
              />
            </article>
          ))}
        </div>

        <RichText
          tagName="div"
          placeholder={__('Outro text…', 'ceiba')}
          value={outroContent}
          onChange={(v) => setAttributes({ outroContent: v })}
          className="ceiba-mcols__outro"
        />

        {enablePrimary && (
          <div className="ceiba-mcols__cta">
            <Button variant="primary">{primaryLabel || __('Primary', 'ceiba')}</Button>
          </div>
        )}
        </section>
      </div>
    </>
  );
}
