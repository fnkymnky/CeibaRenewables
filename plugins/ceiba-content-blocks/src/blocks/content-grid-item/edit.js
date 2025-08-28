import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	MediaUpload,
	MediaReplaceFlow,
	BlockControls,
} from '@wordpress/block-editor';
import { ToolbarGroup, Button } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

export default function Edit({ attributes, setAttributes }) {
	const { mediaID, mediaURL, mediaThumbURL, mediaMediumURL, alt, title, text } = attributes;

	useSelect(
		(select) => (mediaID ? select(coreStore).getMedia(mediaID) : null),
		[mediaID]
	);

	const onSelectImage = (media) => {
		if (!media || !media.url) {
			setAttributes({
				mediaID: undefined,
				mediaURL: '',
				mediaThumbURL: '',
				mediaMediumURL: '',
				alt: ''
			});
			return;
		}

		const sizes = media?.sizes || {};
		setAttributes({
			mediaID: media.id,
			mediaURL: media.url, // full
			mediaThumbURL: sizes?.thumbnail?.url || sizes?.medium?.url || media.url,
			mediaMediumURL: sizes?.medium?.url || sizes?.large?.url || media.url,
			alt: media.alt || media.alt_text || ''
		});
	};

	const blockProps = useBlockProps({ className: 'ceiba-content-grid__item' });

	return (
		<div {...blockProps}>
			<BlockControls>
				<ToolbarGroup>
					<MediaReplaceFlow
						mediaId={mediaID}
						mediaURL={mediaURL || mediaMediumURL || mediaThumbURL}
						accept="image/*"
						allowedTypes={['image']}
						onSelect={onSelectImage}
						name={__('Replace image', 'ceiba')}
					/>
					{(mediaURL || mediaMediumURL || mediaThumbURL) && (
						<Button
							variant="tertiary"
							onClick={() => setAttributes({
								mediaID: undefined,
								mediaURL: '',
								mediaThumbURL: '',
								mediaMediumURL: '',
								alt: ''
							})}
						>
							{__('Remove image', 'ceiba')}
						</Button>
					)}
				</ToolbarGroup>
			</BlockControls>

			<div className="ceiba-content-grid__media">
				{mediaThumbURL ? (
					<img
						className="ceiba-content-grid__image is-editor"
						src={mediaThumbURL}
						alt={alt || ''}
						decoding="async"
						loading="lazy"
					/>
				) : (
					<MediaUpload
						onSelect={onSelectImage}
						allowedTypes={['image']}
						render={({ open }) => (
							<Button variant="secondary" onClick={open}>
								{__('Select image', 'ceiba')}
							</Button>
						)}
					/>
				)}
			</div>

			<RichText
				tagName="h3"
				className="ceiba-content-grid__title"
				placeholder={__('Add heading…', 'ceiba')}
				value={title}
				onChange={(v) => setAttributes({ title: v })}
				allowedFormats={['core/bold', 'core/italic', 'core/link']}
			/>

			<RichText
				tagName="div"
				className="ceiba-content-grid__text"
				multiline="p"
				placeholder={__('Add content…', 'ceiba')}
				value={text}
				onChange={(v) => setAttributes({ text: v })}
				allowedFormats={['core/bold', 'core/italic', 'core/link']}
			/>
		</div>
	);
}
