import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { createElement as el, Fragment } from '@wordpress/element';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls, InnerBlocks, BlockControls, __experimentalBlockAlignmentMatrixControl as BlockAlignmentControl } from '@wordpress/block-editor';
import { PanelBody, Button, TextControl, RadioControl } from '@wordpress/components';
import './style.scss';
import save from './save';

const ALLOWED_BLOCKS = [ 'core/heading', 'core/paragraph', 'core/buttons', 'core/list', 'core/list-item', 'core/separator' ];

registerBlockType('ceiba/hero-section', {
  title: __('Hero Section', 'ceiba'),
  description: __('Full-width hero with title, content, and background image.', 'ceiba'),
  icon: 'cover-image',
  category: 'layout',
  supports: { anchor: true, align: ['full'], spacing: { padding: true, margin: true } },
  edit(props) {
    const { attributes, setAttributes } = props;
    const { title, backgroundId, backgroundUrl, backgroundAlt, align } = attributes;
    const blockProps = useBlockProps({ className: 'ceiba-hero' });

    const onSelectBg = (media) => {
      if (!media) { setAttributes({ backgroundId: 0, backgroundUrl: '', backgroundAlt: '' }); return; }
      setAttributes({ backgroundId: media.id || 0, backgroundUrl: media.url || '', backgroundAlt: media.alt || media.title || '' });
    };

    return el(Fragment, null,
      el(BlockControls, null,
        el(BlockAlignmentControl, { value: align, onChange: (next) => setAttributes({ align: next }), controls: ['full'] })
      ),
      el(InspectorControls, null,
        el(PanelBody, { title: __('Background', 'ceiba'), initialOpen: true },
          el(MediaUploadCheck, null,
            el(MediaUpload, {
              onSelect: onSelectBg,
              allowedTypes: ['image'],
              value: backgroundId,
              render: ({ open }) => el('div', { style: { display: 'grid', gap: 8 } },
                el(Button, { variant: 'secondary', onClick: open }, backgroundId ? __('Replace image', 'ceiba') : __('Select image', 'ceiba')),
                backgroundUrl && el(TextControl, { label: __('Alt text', 'ceiba'), value: backgroundAlt || '', onChange: (val) => setAttributes({ backgroundAlt: val }) }),
                backgroundId ? el(Button, { variant: 'link', isDestructive: true, onClick: () => onSelectBg(null) }, __('Remove image', 'ceiba')) : null
              )
            })
          )
        ),
        el(PanelBody, { title: __('Layout', 'ceiba'), initialOpen: false },
          el(RadioControl, {
            label: __('Width', 'ceiba'),
            selected: align || 'full',
            options: [ { label: __('Contained', 'ceiba'), value: 'contained' }, { label: __('Full', 'ceiba'), value: 'full' } ],
            onChange: (val) => setAttributes({ align: val === 'contained' ? undefined : 'full' })
          })
        )
      ),
      el('section', blockProps,
        el('div', { className: 'ceiba-hero__top', style: backgroundUrl ? { backgroundImage: `url(${backgroundUrl})` } : undefined },
          el('div', { className: 'ceiba-hero__backdrop', 'aria-hidden': true }),
          el('div', { className: 'ceiba-hero__inner' },
            el(RichText, { tagName: 'h1', className: 'ceiba-hero__title', placeholder: __('Add hero title…', 'ceiba'), value: title, allowedFormats: [], onChange: (val) => setAttributes({ title: val }) })
          )
        ),
        el('div', { className: 'ceiba-hero__bottom' },
          el('div', { className: 'ceiba-hero__inner' },
            el('div', { className: 'ceiba-hero__content' },
              el(InnerBlocks, { allowedBlocks: ALLOWED_BLOCKS, templateLock: false, renderAppender: InnerBlocks.ButtonBlockAppender })
            )
          )
        )
      )
    );
  },
  save
});
