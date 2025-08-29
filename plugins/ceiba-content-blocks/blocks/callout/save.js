import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
  const {
    title,
    content,
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

  const objPos = `${((focalPoint?.x ?? 0.5) * 100).toFixed(2)}% ${((focalPoint?.y ?? 0.5) * 100).toFixed(2)}%`;

  return (
    <div {...useBlockProps.save({ className: `ceiba-callout is-${colorScheme}` })}>
      {imageUrl && (
        <div className="ceiba-callout__media">
          <img src={imageUrl} alt={imageAlt || ''} style={{ objectFit: 'cover', objectPosition: objPos }} />
        </div>
      )}
      <div className="ceiba-callout__body">
        {title && <h3 className="ceiba-callout__title">{title}</h3>}
        {content && <div className="ceiba-callout__content">{content}</div>}
        <div className="ceiba-callout__actions">
          {enablePrimary && primaryLink?.url && (
            <a
              className="ceiba-callout__btn ceiba-callout__btn--primary"
              href={primaryLink.url}
              target={primaryLink.opensInNewTab ? '_blank' : undefined}
              rel={primaryLink.opensInNewTab ? 'noopener' : undefined}
            >
              {primaryLabel}
            </a>
          )}
          {enableSecondary && secondaryLink?.url && (
            <a
              className="ceiba-callout__btn ceiba-callout__btn--secondary"
              href={secondaryLink.url}
              target={secondaryLink.opensInNewTab ? '_blank' : undefined}
              rel={secondaryLink.opensInNewTab ? 'noopener' : undefined}
            >
              {secondaryLabel}
            </a>
          )}
        </div>
      </div>
    </div>
  );
}
