import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { createElement as el, Fragment, useMemo } from '@wordpress/element';
import { useBlockProps, InnerBlocks, RichText } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import './style.scss';
import './editor.scss';
import save from './save';

const ALLOWED_BLOCKS = [
  'core/heading',
  'core/paragraph',
  'core/list',
  'core/list-item',
  'core/separator',
  'core/buttons',
  'core/button',
  'core/image',
  'core/group',
];

const TEMPLATE = [
  [ 'core/group', { className: 'ceiba-hero__top-extra', metadata: { name: 'Hero: Top content (under H1)' } } ],
  [ 'core/group', { className: 'ceiba-hero__bottom-content', metadata: { name: 'Hero: Bottom content' } } ],
];

// Helper to pick the best URL from a media entity
function pickSize(media, names) {
  const sizes = media?.media_details?.sizes || {};
  for (const n of names) {
    if (sizes[n]?.source_url) return sizes[n].source_url;
  }
  return media?.source_url || '';
}

function Edit(props) {
  const { attributes, setAttributes, clientId } = props;
  const { title = '', backgroundUrl = '' } = attributes;

  // Get featured image entity (async-safe)
  const { featuredId, featuredMedia } = useSelect((select) => {
    const coreEditor = select('core/editor');
    const core = select('core');
    const id = coreEditor?.getEditedPostAttribute?.('featured_media');
    return { featuredId: id, featuredMedia: id ? core.getMedia(id) : null };
  }, []);

  // Compute desktop + mobile URLs like render.php does
  const { bgDesktop, bgMobile } = useMemo(() => {
    if (featuredMedia) {
      const desktop = pickSize(featuredMedia, ['full']);
      const mobile =
        pickSize(featuredMedia, ['hero-mobile']) ||
        pickSize(featuredMedia, ['medium_large', 'large', 'full']);
      return { bgDesktop: desktop || '', bgMobile: mobile || desktop || '' };
    }
    if (backgroundUrl) {
      return { bgDesktop: backgroundUrl, bgMobile: backgroundUrl };
    }
    return { bgDesktop: '', bgMobile: '' };
  }, [featuredId, featuredMedia, backgroundUrl]);

  // Style that EXACT element in the editor
  const topStyle = bgDesktop ? { backgroundImage: `url(${bgDesktop})` } : undefined;

  // Mobile override via an inline <style>
  const uniqueId = useMemo(() => `ceiba-hero-${clientId}`, [clientId]);
  const inlineCss = useMemo(() => {
    if (!bgMobile) return '';
    return `@media (max-width: 768px){#${uniqueId} .ceiba-hero__top{background-image:url(${bgMobile}) !important;}}`;
  }, [bgMobile, uniqueId]);

  const blockProps = useBlockProps({
    id: uniqueId,
    className: 'ceiba-hero ceiba-hero--editor',
  });

  return el(
    Fragment,
    null,
    // Mobile media-query override
    inlineCss ? el('style', null, inlineCss) : null,

    el(
      'section',
      blockProps,

      // TOP: set background directly on the element so it actually shows up in the editor
      el(
        'div',
        { className: 'ceiba-hero__top', style: topStyle },
        el('div', { className: 'ceiba-hero__backdrop', 'aria-hidden': 'true' }),
        el(
          'div',
          { className: 'ceiba-hero__top__inner' },
          el(RichText, {
            tagName: 'h1',
            className: 'ceiba-hero__title',
            value: title,
            placeholder: __('Add title…', 'ceiba'),
            onChange: (val) => setAttributes({ title: val }),
            allowedFormats: [],
          }),
          el('div', { className: 'ceiba-hero__top__hint' }, __('Top content renders here on the front-end', 'ceiba'))
        )
      ),

      el('div', { className: 'ceiba-hero__top__green-border' }),

      // BOTTOM: both slots live inside one InnerBlocks tree (template)
      el(
        'div',
        { className: 'ceiba-hero__bottom' },
        el(
          'div',
          { className: 'ceiba-hero__bottom__inner' },
          el('div', { className: 'ceiba-hero__content' },
            el(InnerBlocks, {
              template: TEMPLATE,
              templateLock: false,
              allowedBlocks: ALLOWED_BLOCKS,
              renderAppender: InnerBlocks.ButtonBlockAppender,
            })
          )
        )
      )
    )
  );
}

registerBlockType('ceiba/hero-section', {
  edit: Edit,
  save,
});
