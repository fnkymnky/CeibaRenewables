import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { createElement as el, Fragment } from '@wordpress/element';
import { useBlockProps, RichText, InspectorControls, InnerBlocks } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import './style.scss';
import './editor.scss';
import save from './save';

// Explicit list of allowed child blocks for the editor experience
const ALLOWED_BLOCKS = [ 'core/heading', 'core/paragraph', 'core/buttons', 'core/list', 'core/list-item', 'core/separator', 'core/image', 'core/group' ];

function Edit( props ) {
  const { attributes, setAttributes, clientId } = props;
  const { title } = attributes;
  const blockProps = useBlockProps({ className: 'ceiba-hero' });

  const hasInnerBlocks = useSelect(
    (select) => select(blockEditorStore).getBlocks(clientId).length > 0,
    [clientId]
  );

  const featuredId = useSelect( (select) => select('core/editor')?.getEditedPostAttribute('featured_media'), [] );
  const featured = useSelect( (select) => featuredId ? select('core').getMedia(featuredId) : null, [featuredId] );

  const editorBgUrl = featured?.source_url || '';

  return el(Fragment, null,
    el(InspectorControls, null,
      el(PanelBody, { title: __('Background', 'ceiba'), initialOpen: true },
        el('p', null, __('This hero uses the page Featured Image. To change it, set the Featured Image in the page settings. If none is set, the background will be empty in the editor.', 'ceiba'))
      )
    ),
    el('section', blockProps,
      el('div', { className: 'ceiba-hero__top', style: editorBgUrl ? { backgroundImage: `url(${editorBgUrl})` } : undefined },
        el('div', { className: 'ceiba-hero__backdrop', 'aria-hidden': true }),
        el('div', { className: 'ceiba-hero__top__inner' },
          el(RichText, { tagName: 'h1', className: 'ceiba-hero__title', placeholder: __('Add hero title', 'ceiba'), value: title, allowedFormats: [], onChange: (val) => setAttributes({ title: val }) })
        )
      ),
      el('div', { className: 'ceiba-hero__bottom' },
        el('div', { className: 'ceiba-hero__bottom__inner' },
          el('div', { className: 'ceiba-hero__content' },
            el(InnerBlocks, {
              allowedBlocks: ALLOWED_BLOCKS,
              templateLock: false,
              renderAppender: hasInnerBlocks ? undefined : InnerBlocks.ButtonBlockAppender
            })
          )
        )
      )
    )
  );
}


registerBlockType('ceiba/hero-section', { edit: Edit, save });
