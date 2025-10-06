import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { createElement as el, Fragment } from '@wordpress/element';
import {
  useBlockProps,
  InspectorControls,
  InnerBlocks,
  RichText,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import './style.scss';
import './editor.scss';
import save from './save';

// Keep this list sane for authors; expand if needed
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

/**
 * Two logical “slots” implemented as Groups inside a single InnerBlocks tree.
 * PHP will render each slot where it belongs.
 * The metadata.name shows up in List View for nicer authoring.
 */
const TEMPLATE = [
  [
    'core/group',
    {
      className: 'ceiba-hero__top-extra',
      metadata: { name: 'Hero: Top content (under H1)' },
    },
  ],
  [
    'core/group',
    {
      className: 'ceiba-hero__bottom-content',
      metadata: { name: 'Hero: Bottom content' },
    },
  ],
];

function Edit( props ) {
  const { attributes, setAttributes } = props;
  const { title = '' } = attributes;

  const blockProps = useBlockProps({
    className: 'ceiba-hero ceiba-hero--editor',
  });

  return el(
    Fragment,
    null,
    el(
      'section',
      blockProps,
      // Top area - editor preview of the H1 (front-end actual markup is in PHP)
      el(
        'div',
        { className: 'ceiba-hero__top' },
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
          // We show a small hint where the top content will be placed on the front-end
          el('div', { className: 'ceiba-hero__top__hint' }, __('Top content renders here on the front-end', 'ceiba'))
        )
      ),

      el('div', { className: 'ceiba-hero__top__green-border' }),

      // Bottom area in the editor hosts the InnerBlocks; template contains both slots.
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
