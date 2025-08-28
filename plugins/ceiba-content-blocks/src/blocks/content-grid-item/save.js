import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
	const { mediaURL, mediaThumbURL, mediaMediumURL, alt, title, text } = attributes;

	const blockProps = useBlockProps.save({ className: 'ceiba-content-grid__item' });

	const frontSrc = mediaMediumURL || mediaThumbURL || mediaURL || '';

	return (
		<div {...blockProps}>
			<div className="ceiba-content-grid__media">
				{frontSrc && (
					<img
						className="ceiba-content-grid__image"
						src={frontSrc}
						alt={alt || ''}
						decoding="async"
						loading="lazy"
						width="300"
						height="300"
						data-full={mediaURL || ''}
					/>
				)}
			</div>

			<RichText.Content tagName="h3" className="ceiba-content-grid__title" value={title} />
			<RichText.Content tagName="div" className="ceiba-content-grid__text" value={text} />
		</div>
	);
}
