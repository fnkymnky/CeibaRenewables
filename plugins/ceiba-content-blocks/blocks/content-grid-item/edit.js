import { __ } from '@wordpress/i18n';
import {
        useBlockProps,
        MediaUpload,
        InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
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
			mediaURL: media.url,
			mediaThumbURL: sizes?.thumbnail?.url || sizes?.medium?.url || media.url,
			mediaMediumURL: sizes?.medium?.url || sizes?.large?.url || media.url,
			alt: media.alt || media.alt_text || ''
		});
	};

        const blockProps = useBlockProps({ className: 'content-grid__item' });

        return (
                <>
                        <InspectorControls>
                                <PanelBody title={ __('Content', 'ceiba') } initialOpen>
                                        <MediaUpload
                                                onSelect={ onSelectImage }
                                                allowedTypes={ ['image'] }
                                                value={ mediaID }
                                                render={ ({ open }) => (
                                                        <Button variant="secondary" onClick={ open }>
                                                                { mediaThumbURL ? __('Replace image', 'ceiba') : __('Select image', 'ceiba') }
                                                        </Button>
                                                ) }
                                        />
                                        { mediaThumbURL && (
                                                <Button
                                                        variant="tertiary"
                                                        onClick={() => setAttributes({
                                                                mediaID: undefined,
                                                                mediaURL: '',
                                                                mediaThumbURL: '',
                                                                mediaMediumURL: '',
                                                                alt: ''
                                                        })}
                                                        style={{ marginTop: '8px' }}
                                                >
                                                        { __('Remove image', 'ceiba') }
                                                </Button>
                                        ) }
                                        <TextControl
                                                label={ __('Heading', 'ceiba') }
                                                value={ title }
                                                onChange={(v) => setAttributes({ title: v })}
                                                placeholder={ __('Add heading', 'ceiba') }
                                                style={{ marginTop: '16px' }}
                                        />
                                        <TextareaControl
                                                label={ __('Content', 'ceiba') }
                                                value={ text }
                                                onChange={(v) => setAttributes({ text: v })}
                                                placeholder={ __('Add content', 'ceiba') }
                                        />
                                </PanelBody>
                        </InspectorControls>

                        <div { ...blockProps }>
                                <div className="content-grid__media">
                                        { mediaThumbURL && (
                                                <img
                                                        className="content-grid__image"
                                                        src={ mediaThumbURL }
                                                        alt={ alt || '' }
                                                        decoding="async"
                                                        loading="lazy"
                                                />
                                        ) }
                                </div>
                                { title && (
                                        <h3 className="content-grid__title">{ title }</h3>
                                ) }
                                { text && (
                                        <div className="content-grid__text">{ text }</div>
                                ) }
                        </div>
                </>
        );
}

