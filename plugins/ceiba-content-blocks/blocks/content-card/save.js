import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
  const { heading, body, linkText, linkUrl, imgUrl, imgAlt, layout, ctaVariant } = attributes;

  return (
    <div { ...useBlockProps.save({ className: `cb-card layout-${ layout }` }) }>
      <div className="cb-media">{ imgUrl ? <img src={ imgUrl } alt={ imgAlt || '' } /> : null }</div>
      <div className="cb-content">
        <RichText.Content tagName="h3" value={ heading } />
        <RichText.Content tagName="div" className="cb-body" value={ body } />
        {(linkText || linkUrl) && (
          <div className="cb-cta">
            <a className={ ctaVariant === 'accent' ? 'is-alt' : 'is-primary' } href={ linkUrl || '#' }>
              { linkText || 'Learn more' }
            </a>
          </div>
        )}
      </div>
    </div>
  );
}
